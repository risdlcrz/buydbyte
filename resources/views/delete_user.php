<?php
require_once 'config/database_connection.php';

// Check if user is admin
requireAdmin();

$db = getDB();
$user_id = $_GET['id'] ?? '';

if (empty($user_id)) {
    header('Location: users.php');
    exit();
}

// Fetch user data to confirm deletion
$stmt = $db->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: users.php');
    exit();
}

// Check if trying to delete self
$currentUser = getCurrentUser();
if ($user_id === $currentUser['user_id']) {
    header('Location: users.php?error=cannot_delete_self');
    exit();
}

// Handle deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    try {
        // Delete user
        $stmt = $db->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->execute([$user_id]);
        
        header('Location: users.php?success=user_deleted');
        exit();
        
    } catch (PDOException $e) {
        header('Location: users.php?error=delete_failed');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete User - BuyDByte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .navbar-brand { font-weight: bold; }
        .danger-zone { border-left: 4px solid #dc3545; }
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
                <h2><i class="fas fa-user-times me-2"></i>Delete User</h2>
                <p class="text-muted">Permanently remove user from the system</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="users.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Users
                </a>
            </div>
        </div>

        <!-- Warning Alert -->
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Warning!</strong> This action cannot be undone. The user and all associated data will be permanently deleted.
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
                            <div class="col-md-6">
                                <h6>User Information</h6>
                                <p><strong>Name:</strong> <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></p>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                                <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone_number'] ?? 'N/A'); ?></p>
                            </div>
                            <div class="col-md-6">
                                <h6>Account Details</h6>
                                <p><strong>Role:</strong> 
                                    <span class="badge bg-<?php 
                                        echo $user['role'] === 'admin' ? 'danger' : 
                                            ($user['role'] === 'consumer' ? 'primary' : 
                                            ($user['role'] === 'warehouse' ? 'warning' : 'info')); 
                                    ?>">
                                        <?php echo ucfirst($user['role']); ?>
                                    </span>
                                </p>
                                <p><strong>Status:</strong> 
                                    <span class="badge bg-<?php 
                                        echo $user['status'] === 'active' ? 'success' : 
                                            ($user['status'] === 'inactive' ? 'secondary' : 
                                            ($user['status'] === 'banned' ? 'danger' : 'warning')); 
                                    ?>">
                                        <?php echo ucfirst($user['status']); ?>
                                    </span>
                                </p>
                                <p><strong>Created:</strong> <?php echo date('M j, Y', strtotime($user['created_at'])); ?></p>
                            </div>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>What will be deleted:</strong>
                            <ul class="mb-0 mt-2">
                                <li>User account and profile information</li>
                                <li>All user sessions and login history</li>
                                <li>Associated addresses and contact information</li>
                                <li>Audit logs and activity history</li>
                            </ul>
                        </div>

                        <form method="POST">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="confirm_checkbox" required>
                                <label class="form-check-label" for="confirm_checkbox">
                                    I understand that this action cannot be undone and I want to permanently delete this user.
                                </label>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="users.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                                <button type="submit" name="confirm_delete" class="btn btn-danger" id="delete_btn" disabled>
                                    <i class="fas fa-trash me-2"></i>Delete User Permanently
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
                            <p class="small mb-0">This user has no critical system dependencies.</p>
                        </div>
                        
                        <div class="mb-3">
                            <h6 class="text-warning"><i class="fas fa-exclamation-triangle me-2"></i>Consider Alternatives</h6>
                            <p class="small mb-0">Instead of deletion, you could:</p>
                            <ul class="small mb-0">
                                <li>Change status to "Inactive"</li>
                                <li>Change status to "Banned"</li>
                                <li>Remove admin privileges</li>
                            </ul>
                        </div>

                        <div class="mb-3">
                            <h6 class="text-info"><i class="fas fa-info-circle me-2"></i>Data Recovery</h6>
                            <p class="small mb-0">Deleted user data cannot be recovered. Make sure you have backups if needed.</p>
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
                            <a href="edit_user.php?id=<?php echo $user['user_id']; ?>" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-edit me-2"></i>Edit User Instead
                            </a>
                            <a href="edit_user.php?id=<?php echo $user['user_id']; ?>&status=inactive" class="btn btn-outline-warning btn-sm">
                                <i class="fas fa-user-slash me-2"></i>Deactivate User
                            </a>
                            <a href="edit_user.php?id=<?php echo $user['user_id']; ?>&status=banned" class="btn btn-outline-danger btn-sm">
                                <i class="fas fa-ban me-2"></i>Ban User
                            </a>
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
