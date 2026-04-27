<?php
$page_title = "Settings - Laberion WMS";
include '../includes/header.php';

$success = '';
$error = '';

// Get current admin
$admin_id = $_SESSION['admin_id'];
$admin = mysqli_fetch_assoc(db_query("SELECT * FROM admin_users WHERE id = $admin_id"));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = isset($_POST['action']) ? clean_input($_POST['action']) : '';
    
    if ($action == 'update_profile') {
        $email = clean_input($_POST['email']);
        
        $update_query = "UPDATE admin_users SET email = '$email' WHERE id = $admin_id";
        if (db_query($update_query)) {
            $success = "Profile updated successfully!";
            $admin['email'] = $email;
        } else {
            $error = "Error updating profile.";
        }
    }
    
    elseif ($action == 'change_password') {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Verify current password
        if (!password_verify($current_password, $admin['password'])) {
            $error = "Current password is incorrect.";
        } elseif ($new_password !== $confirm_password) {
            $error = "New passwords do not match.";
        } elseif (strlen($new_password) < 6) {
            $error = "Password must be at least 6 characters.";
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
            $update_query = "UPDATE admin_users SET password = '$hashed_password' WHERE id = $admin_id";
            if (db_query($update_query)) {
                $success = "Password changed successfully!";
            } else {
                $error = "Error changing password.";
            }
        }
    }
}
?>

<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <h1><i class="fas fa-cog"></i> Settings</h1>
        </div>
        
        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Profile Settings -->
                <div class="table-container mb-4">
                    <h5 class="mb-4 border-bottom pb-3">
                        <i class="fas fa-user"></i> Profile Settings
                    </h5>
                    
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="update_profile">
                        
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" 
                                   value="<?php echo $admin['username']; ?>" disabled>
                            <small class="text-muted">Username cannot be changed</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" 
                                   value="<?php echo $admin['email']; ?>" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </form>
                </div>
                
                <!-- Change Password -->
                <div class="table-container mb-4">
                    <h5 class="mb-4 border-bottom pb-3">
                        <i class="fas fa-lock"></i> Change Password
                    </h5>
                    
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="change_password">
                        
                        <div class="mb-3">
                            <label class="form-label">Current Password</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" name="new_password" class="form-control" required>
                            <small class="text-muted">Minimum 6 characters</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>
                        
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-key"></i> Change Password
                        </button>
                    </form>
                </div>
                
                <!-- System Settings -->
                <div class="table-container">
                    <h5 class="mb-4 border-bottom pb-3">
                        <i class="fas fa-sliders-h"></i> System Settings
                    </h5>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Working Hours Start</label>
                            <input type="time" class="form-control" value="09:00" disabled>
                            <small class="text-muted">Default: 09:00 AM</small>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Working Hours End</label>
                            <input type="time" class="form-control" value="17:00" disabled>
                            <small class="text-muted">Default: 05:00 PM</small>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Late Threshold (Minutes)</label>
                            <input type="number" class="form-control" value="15" disabled>
                            <small class="text-muted">Default: 15 minutes</small>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Timezone</label>
                            <input type="text" class="form-control" value="Europe/Zurich" disabled>
                            <small class="text-muted">Current timezone</small>
                        </div>
                    </div>
                    
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle"></i>
                        System settings are currently read-only. Contact administrator to modify.
                    </div>
                </div>
            </div>
            
            <!-- Sidebar Info -->
            <div class="col-lg-4">
                <div class="table-container">
                    <h5 class="mb-3 border-bottom pb-3">
                        <i class="fas fa-info-circle"></i> System Information
                    </h5>
                    
                    <div class="mb-3">
                        <strong>Application:</strong>
                        <p class="text-muted">Laberion WMS v1.0</p>
                    </div>
                    
                    <div class="mb-3">
                        <strong>PHP Version:</strong>
                        <p class="text-muted"><?php echo phpversion(); ?></p>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Database:</strong>
                        <p class="text-muted">MySQL</p>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Last Login:</strong>
                        <p class="text-muted"><?php echo date('M d, Y h:i A'); ?></p>
                    </div>
                    
                    <hr>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Important:</strong> Keep your password secure and never share it with anyone.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>