<?php
require_once 'config/database_connection.php';

// Check if user is admin
requireAdmin();

$db = getDB();
$errors = [];
$success = '';
$user = null;

// Get user ID from URL
$user_id = $_GET['id'] ?? '';
if (empty($user_id)) {
    header('Location: users.php');
    exit();
}

// Fetch user data
$stmt = $db->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: users.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone_number = trim($_POST['phone_number'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? 'consumer';
    $status = $_POST['status'] ?? 'pending_verification';
    $update_password = !empty($password);
    
    // Validation
    if (empty($first_name)) {
        $errors[] = 'First name is required.';
    }
    
    if (empty($email)) {
        $errors[] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    } else {
        // Check if email already exists (excluding current user)
        $checkEmail = $db->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
        $checkEmail->execute([$email, $user_id]);
        if ($checkEmail->fetch()) {
            $errors[] = 'Email address is already registered.';
        }
    }
    
    if (!empty($phone_number)) {
        // Check if phone number already exists (excluding current user)
        $checkPhone = $db->prepare("SELECT user_id FROM users WHERE phone_number = ? AND user_id != ?");
        $checkPhone->execute([$phone_number, $user_id]);
        if ($checkPhone->fetch()) {
            $errors[] = 'Phone number is already registered.';
        }
    }
    
    if ($update_password) {
        if (strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters long.';
        }
        
        if ($password !== $confirm_password) {
            $errors[] = 'Passwords do not match.';
        }
    }
    
    if (!in_array($role, ['admin', 'consumer', 'warehouse', 'finance'])) {
        $errors[] = 'Invalid role selected.';
    }
    
    if (!in_array($status, ['active', 'inactive', 'banned', 'pending_verification'])) {
        $errors[] = 'Invalid status selected.';
    }
    
    // If no errors, update user
    if (empty($errors)) {
        try {
            if ($update_password) {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("
                    UPDATE users 
                    SET first_name = ?, last_name = ?, email = ?, phone_number = ?, 
                        password = ?, role = ?, status = ?, updated_at = CURRENT_TIMESTAMP
                    WHERE user_id = ?
                ");
                $stmt->execute([
                    $first_name,
                    $last_name ?: null,
                    $email,
                    $phone_number ?: null,
                    $hashedPassword,
                    $role,
                    $status,
                    $user_id
                ]);
            } else {
                $stmt = $db->prepare("
                    UPDATE users 
                    SET first_name = ?, last_name = ?, email = ?, phone_number = ?, 
                        role = ?, status = ?, updated_at = CURRENT_TIMESTAMP
                    WHERE user_id = ?
                ");
                $stmt->execute([
                    $first_name,
                    $last_name ?: null,
                    $email,
                    $phone_number ?: null,
                    $role,
                    $status,
                    $user_id
                ]);
            }
            
            $success = 'User has been successfully updated!';
            
            // Refresh user data
            $stmt = $db->prepare("SELECT * FROM users WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();
            
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
    <title>Edit User - BuyDByte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .navbar-brand { font-weight: bold; }
        .form-section { background-color: #f8f9fa; }
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
                <h2><i class="fas fa-user-edit me-2"></i>Edit User</h2>
                <p class="text-muted">Update user information and permissions</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="users.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Users
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

        <!-- Edit User Form -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-user me-2"></i>User Information
                            <span class="badge bg-secondary ms-2">ID: <?php echo htmlspecialchars($user['user_id']); ?></span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" novalidate>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" 
                                           value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="last_name" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" 
                                           value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="phone_number" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone_number" name="phone_number" 
                                           value="<?php echo htmlspecialchars($user['phone_number'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">New Password</label>
                                    <input type="password" class="form-control" id="password" name="password" 
                                           minlength="6">
                                    <div class="form-text">Leave blank to keep current password</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                           minlength="6">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                                    <select class="form-select" id="role" name="role" required>
                                        <option value="consumer" <?php echo $user['role'] === 'consumer' ? 'selected' : ''; ?>>Consumer</option>
                                        <option value="warehouse" <?php echo $user['role'] === 'warehouse' ? 'selected' : ''; ?>>Warehouse</option>
                                        <option value="finance" <?php echo $user['role'] === 'finance' ? 'selected' : ''; ?>>Finance</option>
                                        <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="pending_verification" <?php echo $user['status'] === 'pending_verification' ? 'selected' : ''; ?>>Pending Verification</option>
                                        <option value="active" <?php echo $user['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                                        <option value="inactive" <?php echo $user['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                        <option value="banned" <?php echo $user['status'] === 'banned' ? 'selected' : ''; ?>>Banned</option>
                                    </select>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="users.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update User
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- User Info Panel -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>User Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>User ID:</strong>
                            <p class="small mb-0"><?php echo htmlspecialchars($user['user_id']); ?></p>
                        </div>
                        <div class="mb-3">
                            <strong>Created:</strong>
                            <p class="small mb-0"><?php echo date('M j, Y g:i A', strtotime($user['created_at'])); ?></p>
                        </div>
                        <div class="mb-3">
                            <strong>Last Updated:</strong>
                            <p class="small mb-0"><?php echo date('M j, Y g:i A', strtotime($user['updated_at'])); ?></p>
                        </div>
                        <div class="mb-3">
                            <strong>Current Role:</strong>
                            <p class="small mb-0">
                                <span class="badge bg-<?php 
                                    echo $user['role'] === 'admin' ? 'danger' : 
                                        ($user['role'] === 'consumer' ? 'primary' : 
                                        ($user['role'] === 'warehouse' ? 'warning' : 'info')); 
                                ?>">
                                    <?php echo ucfirst($user['role']); ?>
                                </span>
                            </p>
                        </div>
                        <div class="mb-3">
                            <strong>Current Status:</strong>
                            <p class="small mb-0">
                                <span class="badge bg-<?php 
                                    echo $user['status'] === 'active' ? 'success' : 
                                        ($user['status'] === 'inactive' ? 'secondary' : 
                                        ($user['status'] === 'banned' ? 'danger' : 'warning')); 
                                ?>">
                                    <?php echo ucfirst($user['status']); ?>
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Role Information -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Role Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong class="text-danger">Admin</strong>
                            <p class="small mb-0">Full system access, can manage all users and products</p>
                        </div>
                        <div class="mb-3">
                            <strong class="text-primary">Consumer</strong>
                            <p class="small mb-0">Regular customer with shopping privileges</p>
                        </div>
                        <div class="mb-3">
                            <strong class="text-warning">Warehouse</strong>
                            <p class="small mb-0">Inventory management and order fulfillment</p>
                        </div>
                        <div class="mb-3">
                            <strong class="text-info">Finance</strong>
                            <p class="small mb-0">Financial reports and payment processing</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password confirmation validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (password !== confirmPassword) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html>
