<?php
require_once 'config/database_connection.php';

// Check if user is admin
requireAdmin();

$db = getDB();
$errors = [];
$success = '';

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
    $image = null;
    
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
    
    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($_FILES['image']['type'], $allowedTypes)) {
            $errors[] = 'Invalid image type. Please upload JPEG, PNG, GIF, or WebP images only.';
        } elseif ($_FILES['image']['size'] > $maxSize) {
            $errors[] = 'Image size must be less than 5MB.';
        } else {
            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image = uniqid() . '.' . $extension;
            $uploadPath = $uploadDir . $image;
            
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                $errors[] = 'Failed to upload image.';
                $image = null;
            }
        }
    }
    
    // If no errors, insert product
    if (empty($errors)) {
        try {
            $stmt = $db->prepare("
                INSERT INTO products (name, category, price, stock, description, image) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $name,
                $category,
                $price,
                $stock,
                $description ?: null,
                $image
            ]);
            
            $success = 'Product has been successfully created!';
            
            // Clear form data
            $name = $category = $price = $stock = $description = '';
            
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
    <title>Add Product - BuyDByte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .navbar-brand { font-weight: bold; }
        .form-section { background-color: #f8f9fa; }
        .image-preview { max-width: 200px; max-height: 200px; object-fit: cover; }
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
                <h2><i class="fas fa-plus-circle me-2"></i>Add New Product</h2>
                <p class="text-muted">Create a new product entry with details and image</p>
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

        <!-- Add Product Form -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-box me-2"></i>Product Information</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data" novalidate>
                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?php echo htmlspecialchars($name ?? ''); ?>" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="category" name="category" 
                                           value="<?php echo htmlspecialchars($category ?? ''); ?>" 
                                           placeholder="e.g., Electronics, Clothing" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="price" name="price" 
                                               value="<?php echo htmlspecialchars($price ?? ''); ?>" 
                                               step="0.01" min="0" required>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="stock" class="form-label">Stock Quantity <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="stock" name="stock" 
                                           value="<?php echo htmlspecialchars($stock ?? ''); ?>" 
                                           min="0" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="4" 
                                          placeholder="Enter product description..."><?php echo htmlspecialchars($description ?? ''); ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="image" class="form-label">Product Image</label>
                                <input type="file" class="form-control" id="image" name="image" 
                                       accept="image/jpeg,image/png,image/gif,image/webp">
                                <div class="form-text">Upload JPEG, PNG, GIF, or WebP image (max 5MB)</div>
                                <div id="imagePreview" class="mt-2" style="display: none;">
                                    <img id="previewImg" class="image-preview border rounded" alt="Preview">
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="products.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Create Product
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Help Panel -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Product Guidelines</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <h6><i class="fas fa-tag me-2"></i>Naming</h6>
                            <p class="small mb-0">Use clear, descriptive names that customers will understand.</p>
                        </div>
                        <div class="mb-3">
                            <h6><i class="fas fa-layer-group me-2"></i>Categories</h6>
                            <p class="small mb-0">Group similar products together for better organization.</p>
                        </div>
                        <div class="mb-3">
                            <h6><i class="fas fa-dollar-sign me-2"></i>Pricing</h6>
                            <p class="small mb-0">Set competitive prices based on market research.</p>
                        </div>
                        <div class="mb-3">
                            <h6><i class="fas fa-boxes me-2"></i>Stock</h6>
                            <p class="small mb-0">Keep accurate inventory counts to avoid overselling.</p>
                        </div>
                        <div class="mb-3">
                            <h6><i class="fas fa-image me-2"></i>Images</h6>
                            <p class="small mb-0">High-quality images help customers make purchasing decisions.</p>
                        </div>
                    </div>
                </div>

                <!-- Category Suggestions -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-lightbulb me-2"></i>Popular Categories</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-1">
                            <span class="badge bg-light text-dark">Electronics</span>
                            <span class="badge bg-light text-dark">Clothing</span>
                            <span class="badge bg-light text-dark">Books</span>
                            <span class="badge bg-light text-dark">Home & Garden</span>
                            <span class="badge bg-light text-dark">Sports</span>
                            <span class="badge bg-light text-dark">Toys</span>
                            <span class="badge bg-light text-dark">Beauty</span>
                            <span class="badge bg-light text-dark">Automotive</span>
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
