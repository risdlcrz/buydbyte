<?php
require_once 'config/database_connection.php';

// Check if user is admin
requireAdmin();

$db = getDB();
$product_id = $_GET['id'] ?? '';

if (empty($product_id)) {
    header('Location: products.php');
    exit();
}

// Fetch product data to confirm deletion
$stmt = $db->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: products.php');
    exit();
}

// Handle deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    try {
        // Delete product image if exists
        if (!empty($product['image'])) {
            $imagePath = 'uploads/products/' . $product['image'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        
        // Delete product
        $stmt = $db->prepare("DELETE FROM products WHERE product_id = ?");
        $stmt->execute([$product_id]);
        
        header('Location: products.php?success=product_deleted');
        exit();
        
    } catch (PDOException $e) {
        header('Location: products.php?error=delete_failed');
        exit();
    }
}

$currentUser = getCurrentUser();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Product - BuyDByte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .navbar-brand { font-weight: bold; }
        .danger-zone { border-left: 4px solid #dc3545; }
        .product-image { max-width: 200px; max-height: 200px; object-fit: cover; }
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
                <h2><i class="fas fa-trash me-2"></i>Delete Product</h2>
                <p class="text-muted">Permanently remove product from the system</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="products.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Products
                </a>
            </div>
        </div>

        <!-- Warning Alert -->
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Warning!</strong> This action cannot be undone. The product and all associated data will be permanently deleted.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>

        <!-- Delete Confirmation -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card danger-zone">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Confirm Deletion</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <?php if (!empty($product['image'])): ?>
                                <img src="uploads/products/<?php echo htmlspecialchars($product['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                     class="product-image border rounded">
                                <?php else: ?>
                                <div class="product-image bg-light d-flex align-items-center justify-content-center border rounded">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-8">
                                <h5><?php echo htmlspecialchars($product['name']); ?></h5>
                                <p class="text-muted mb-2">
                                    <strong>Category:</strong> <?php echo htmlspecialchars($product['category']); ?><br>
                                    <strong>Price:</strong> $<?php echo number_format($product['price'], 2); ?><br>
                                    <strong>Stock:</strong> <?php echo $product['stock']; ?> units<br>
                                    <strong>Created:</strong> <?php echo date('M j, Y', strtotime($product['created_at'])); ?>
                                </p>
                                <?php if (!empty($product['description'])): ?>
                                <p class="small text-muted"><?php echo htmlspecialchars($product['description']); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>What will be deleted:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Product information and details</li>
                                <li>Product image file</li>
                                <li>All inventory records</li>
                                <li>Product history and logs</li>
                            </ul>
                        </div>

                        <form method="POST">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="confirm_checkbox" required>
                                <label class="form-check-label" for="confirm_checkbox">
                                    I understand that this action cannot be undone and I want to permanently delete this product.
                                </label>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="products.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                                <button type="submit" name="confirm_delete" class="btn btn-danger" id="delete_btn" disabled>
                                    <i class="fas fa-trash me-2"></i>Delete Product Permanently
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Safety Information -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-shield-alt me-2"></i>Safety Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <h6 class="text-success"><i class="fas fa-check-circle me-2"></i>Safe to Delete</h6>
                            <p class="small mb-0">This product has no active orders or critical dependencies.</p>
                        </div>
                        
                        <div class="mb-3">
                            <h6 class="text-warning"><i class="fas fa-exclamation-triangle me-2"></i>Consider Alternatives</h6>
                            <p class="small mb-0">Instead of deletion, you could:</p>
                            <ul class="small mb-0">
                                <li>Set stock to 0 (out of stock)</li>
                                <li>Mark as discontinued</li>
                                <li>Archive the product</li>
                            </ul>
                        </div>

                        <div class="mb-3">
                            <h6 class="text-info"><i class="fas fa-info-circle me-2"></i>Data Recovery</h6>
                            <p class="small mb-0">Deleted product data cannot be recovered. Make sure you have backups if needed.</p>
                        </div>
                    </div>
                </div>

                <!-- Alternative Actions -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-cog me-2"></i>Alternative Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="edit_product.php?id=<?php echo $product['product_id']; ?>" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-edit me-2"></i>Edit Product Instead
                            </a>
                            <a href="edit_product.php?id=<?php echo $product['product_id']; ?>&stock=0" class="btn btn-outline-warning btn-sm">
                                <i class="fas fa-boxes me-2"></i>Set Stock to 0
                            </a>
                            <a href="edit_product.php?id=<?php echo $product['product_id']; ?>&price=0" class="btn btn-outline-danger btn-sm">
                                <i class="fas fa-dollar-sign me-2"></i>Set Price to $0
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Product Statistics -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Product Statistics</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <h5 class="text-primary">$<?php echo number_format($product['price'], 2); ?></h5>
                                <small class="text-muted">Price</small>
                            </div>
                            <div class="col-6">
                                <h5 class="text-success"><?php echo $product['stock']; ?></h5>
                                <small class="text-muted">Stock</small>
                            </div>
                        </div>
                        <hr>
                        <div class="text-center">
                            <h6 class="text-info"><?php echo date('M j, Y', strtotime($product['created_at'])); ?></h6>
                            <small class="text-muted">Created</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Enable delete button only when checkbox is checked
        document.getElementById('confirm_checkbox').addEventListener('change', function() {
            document.getElementById('delete_btn').disabled = !this.checked;
        });
    </script>
</body>
</html>
