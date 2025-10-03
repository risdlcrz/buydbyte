<?php
require_once 'config/database_connection.php';

// Check if user is admin
requireAdmin();

$db = getDB();
$errors = [];
$success = '';
$product = null;

// Get product ID from URL
$product_id = $_GET['id'] ?? '';
if (empty($product_id)) {
    header('Location: products.php');
    exit();
}

// Fetch product data
$stmt = $db->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: products.php');
    exit();
}

// Create uploads directory if it doesn't exist
$uploadDir = 'uploads/products/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $price = $_POST['price'] ?? '';
    $stock = $_POST['stock'] ?? '';
    $description = trim($_POST['description'] ?? '');
    $remove_image = isset($_POST['remove_image']);
    $image = $product['image']; // Keep existing image by default
    
    // Validation
    if (empty($name)) {
        $errors[] = 'Product name is required.';
    }
    
    if (empty($category)) {
        $errors[] = 'Category is required.';
    }
    
    if (empty($price) || !is_numeric($price) || $price < 0) {
        $errors[] = 'Please enter a valid price.';
    }
    
    if (empty($stock) || !is_numeric($stock) || $stock < 0) {
        $errors[] = 'Please enter a valid stock quantity.';
    }
    
    // Handle image removal
    if ($remove_image && !empty($product['image'])) {
        $oldImagePath = $uploadDir . $product['image'];
        if (file_exists($oldImagePath)) {
            unlink($oldImagePath);
        }
        $image = null;
    }
    
    // Handle new image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($_FILES['image']['type'], $allowedTypes)) {
            $errors[] = 'Invalid image type. Please upload JPEG, PNG, GIF, or WebP images only.';
        } elseif ($_FILES['image']['size'] > $maxSize) {
            $errors[] = 'Image size must be less than 5MB.';
        } else {
            // Remove old image if exists
            if (!empty($product['image'])) {
                $oldImagePath = $uploadDir . $product['image'];
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            
            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image = uniqid() . '.' . $extension;
            $uploadPath = $uploadDir . $image;
            
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                $errors[] = 'Failed to upload image.';
                $image = $product['image']; // Keep existing image on failure
            }
        }
    }
    
    // If no errors, update product
    if (empty($errors)) {
        try {
            $stmt = $db->prepare("
                UPDATE products 
                SET name = ?, category = ?, price = ?, stock = ?, description = ?, image = ?, updated_at = CURRENT_TIMESTAMP
                WHERE product_id = ?
            ");
            
            $stmt->execute([
                $name,
                $category,
                $price,
                $stock,
                $description ?: null,
                $image,
                $product_id
            ]);
            
            $success = 'Product has been successfully updated!';
            
            // Refresh product data
            $stmt = $db->prepare("SELECT * FROM products WHERE product_id = ?");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch();
            
        } catch (PDOException $e) {
            $errors[] = 'Database error: ' . $e->getMessage();
        }
    }
}

$currentUser = getCurrentUser();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - BuyDByte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .navbar-brand { font-weight: bold; }
        .form-section { background-color: #f8f9fa; }
        .image-preview { max-width: 200px; max-height: 200px; object-fit: cover; }
        .current-image { max-width: 150px; max-height: 150px; object-fit: cover; }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-shopping-cart me-2"></i>BuyDByte
            </a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">
                    Welcome, <?php echo htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name']); ?>
                </span>
                <a class="nav-link" href="logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h2><i class="fas fa-edit me-2"></i>Edit Product</h2>
                <p class="text-muted">Update product information and details</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="products.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Products
                </a>
            </div>
        </div>

        <!-- Success/Error Messages -->
        <?php if (!empty($success)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Edit Product Form -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-box me-2"></i>Product Information
                            <span class="badge bg-secondary ms-2">ID: <?php echo htmlspecialchars($product['product_id']); ?></span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data" novalidate>
                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?php echo htmlspecialchars($product['name']); ?>" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="category" name="category" 
                                           value="<?php echo htmlspecialchars($product['category']); ?>" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="price" name="price" 
                                               value="<?php echo htmlspecialchars($product['price']); ?>" 
                                               step="0.01" min="0" required>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="stock" class="form-label">Stock Quantity <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="stock" name="stock" 
                                           value="<?php echo htmlspecialchars($product['stock']); ?>" 
                                           min="0" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Product Image</label>
                                
                                <?php if (!empty($product['image'])): ?>
                                <div class="mb-3">
                                    <label class="form-label">Current Image</label>
                                    <div class="d-flex align-items-center">
                                        <img src="uploads/products/<?php echo htmlspecialchars($product['image']); ?>" 
                                             alt="Current image" class="current-image border rounded me-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="remove_image" name="remove_image">
                                            <label class="form-check-label" for="remove_image">
                                                Remove current image
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                                <div class="mb-3">
                                    <label for="image" class="form-label"><?php echo !empty($product['image']) ? 'Replace Image' : 'Upload Image'; ?></label>
                                    <input type="file" class="form-control" id="image" name="image" 
                                           accept="image/jpeg,image/png,image/gif,image/webp">
                                    <div class="form-text">Upload JPEG, PNG, GIF, or WebP image (max 5MB)</div>
                                    <div id="imagePreview" class="mt-2" style="display: none;">
                                        <img id="previewImg" class="image-preview border rounded" alt="Preview">
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="products.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update Product
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Product Info Panel -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Product Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Product ID:</strong>
                            <p class="small mb-0"><?php echo htmlspecialchars($product['product_id']); ?></p>
                        </div>
                        <div class="mb-3">
                            <strong>Created:</strong>
                            <p class="small mb-0"><?php echo date('M j, Y g:i A', strtotime($product['created_at'])); ?></p>
                        </div>
                        <div class="mb-3">
                            <strong>Last Updated:</strong>
                            <p class="small mb-0"><?php echo date('M j, Y g:i A', strtotime($product['updated_at'])); ?></p>
                        </div>
                        <div class="mb-3">
                            <strong>Current Price:</strong>
                            <p class="small mb-0">$<?php echo number_format($product['price'], 2); ?></p>
                        </div>
                        <div class="mb-3">
                            <strong>Current Stock:</strong>
                            <p class="small mb-0">
                                <span class="<?php 
                                    echo $product['stock'] <= 5 ? 'text-danger' : 
                                        ($product['stock'] <= 20 ? 'text-warning' : 'text-success'); 
                                ?>">
                                    <?php echo $product['stock']; ?> units
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Stock Alert -->
                <?php if ($product['stock'] <= 5): ?>
                <div class="card mt-3 border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Low Stock Alert</h6>
                    </div>
                    <div class="card-body">
                        <p class="small mb-0">This product is running low on stock. Consider reordering soon.</p>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Quick Actions -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="document.getElementById('stock').value = '<?php echo $product['stock'] + 10; ?>'">
                                <i class="fas fa-plus me-2"></i>Add 10 to Stock
                            </button>
                            <button type="button" class="btn btn-outline-warning btn-sm" onclick="document.getElementById('price').value = (parseFloat(document.getElementById('price').value) * 1.1).toFixed(2)">
                                <i class="fas fa-arrow-up me-2"></i>Increase Price 10%
                            </button>
                            <button type="button" class="btn btn-outline-success btn-sm" onclick="document.getElementById('price').value = (parseFloat(document.getElementById('price').value) * 0.9).toFixed(2)">
                                <i class="fas fa-arrow-down me-2"></i>Decrease Price 10%
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Image preview functionality
        document.getElementById('image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewImg').src = e.target.result;
                    document.getElementById('imagePreview').style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                document.getElementById('imagePreview').style.display = 'none';
            }
        });
    </script>
</body>
</html>
