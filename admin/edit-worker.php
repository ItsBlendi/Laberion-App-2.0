<?php
$page_title = "Edit Worker - Laberion WMS";
include '../includes/header.php';

$worker_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get worker details
$worker_query = db_query("SELECT * FROM workers WHERE id = $worker_id");
if (mysqli_num_rows($worker_query) == 0) {
    header("Location: workers.php");
    exit();
}
$worker = mysqli_fetch_assoc($worker_query);

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = clean_input($_POST['name']);
    $lastname = clean_input($_POST['lastname']);
    $position = clean_input($_POST['position']);
    $department = clean_input($_POST['department']);
    $phone = clean_input($_POST['phone']);
    $salary = clean_input($_POST['salary']);
    $start_date = clean_input($_POST['start_date']);
    $status = clean_input($_POST['status']);
    
    $photo = $worker['photo'];
    
    // Handle new photo upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $filename = $_FILES['photo']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $new_filename = 'worker_' . time() . '.' . $ext;
            $upload_path = '../assets/uploads/workers/' . $new_filename;
            
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_path)) {
                // Delete old photo
                if ($worker['photo'] && file_exists('../assets/uploads/workers/' . $worker['photo'])) {
                    unlink('../assets/uploads/workers/' . $worker['photo']);
                }
                $photo = $new_filename;
            }
        }
    }
    
    // Update worker
    $query = "UPDATE workers SET 
              name = '$name',
              lastname = '$lastname',
              position = '$position',
              department = '$department',
              phone = '$phone',
              salary = '$salary',
              start_date = '$start_date',
              status = '$status',
              photo = '$photo'
              WHERE id = $worker_id";
    
    if (db_query($query)) {
        $success = "Worker updated successfully!";
        // Refresh worker data
        $worker_query = db_query("SELECT * FROM workers WHERE id = $worker_id");
        $worker = mysqli_fetch_assoc($worker_query);
    } else {
        $error = "Error updating worker. Please try again.";
    }
}
?>

<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <h1><i class="fas fa-user-edit"></i> Edit Worker</h1>
                <div>
                    <a href="worker-profile.php?id=<?php echo $worker_id; ?>" class="btn btn-info">
                        <i class="fas fa-eye"></i> View Profile
                    </a>
                    <a href="workers.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Workers
                    </a>
                </div>
            </div>
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
            <div class="col-lg-8 mx-auto">
                <div class="table-container">
                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="row g-3">
                            <!-- Current Photo -->
                            <div class="col-12 text-center mb-3">
                                <img src="../assets/uploads/workers/<?php echo $worker['photo'] ?: 'default-avatar.png'; ?>" 
                                     alt="<?php echo $worker['name']; ?>" 
                                     class="rounded-circle" 
                                     width="120" height="120">
                            </div>
                            
                            <!-- Personal Information -->
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-user"></i> Personal Information
                                </h5>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" 
                                       value="<?php echo $worker['name']; ?>" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="lastname" class="form-control" 
                                       value="<?php echo $worker['lastname']; ?>" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Position <span class="text-danger">*</span></label>
                                <input type="text" name="position" class="form-control" 
                                       value="<?php echo $worker['position']; ?>" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Department <span class="text-danger">*</span></label>
                                <select name="department" class="form-select" required>
                                    <option value="">Select Department</option>
                                    <option value="IT" <?php echo $worker['department'] == 'IT' ? 'selected' : ''; ?>>IT</option>
                                    <option value="HR" <?php echo $worker['department'] == 'HR' ? 'selected' : ''; ?>>HR</option>
                                    <option value="Finance" <?php echo $worker['department'] == 'Finance' ? 'selected' : ''; ?>>Finance</option>
                                    <option value="Marketing" <?php echo $worker['department'] == 'Marketing' ? 'selected' : ''; ?>>Marketing</option>
                                    <option value="Sales" <?php echo $worker['department'] == 'Sales' ? 'selected' : ''; ?>>Sales</option>
                                    <option value="Operations" <?php echo $worker['department'] == 'Operations' ? 'selected' : ''; ?>>Operations</option>
                                    <option value="Customer Service" <?php echo $worker['department'] == 'Customer Service' ? 'selected' : ''; ?>>Customer Service</option>
                                    <option value="Other" <?php echo $worker['department'] == 'Other' ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Phone Number</label>
                                <input type="tel" name="phone" class="form-control" 
                                       value="<?php echo $worker['phone']; ?>">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Salary (Optional)</label>
                                <input type="number" name="salary" class="form-control" 
                                       step="0.01" value="<?php echo $worker['salary']; ?>">
                            </div>
                            
                            <!-- Employment Details -->
                            <div class="col-12 mt-4">
                                <h5 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-briefcase"></i> Employment Details
                                </h5>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" class="form-control" 
                                       value="<?php echo $worker['start_date']; ?>" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="active" <?php echo $worker['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                                    <option value="inactive" <?php echo $worker['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                    <option value="vacation" <?php echo $worker['status'] == 'vacation' ? 'selected' : ''; ?>>On Vacation</option>
                                    <option value="sick" <?php echo $worker['status'] == 'sick' ? 'selected' : ''; ?>>Sick Leave</option>
                                </select>
                            </div>
                            
                            <!-- Photo Upload -->
                            <div class="col-12 mt-4">
                                <h5 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-camera"></i> Update Photo
                                </h5>
                            </div>
                            
                            <div class="col-md-12">
                                <label class="form-label">New Profile Photo (Optional)</label>
                                <input type="file" name="photo" class="form-control" 
                                       accept="image/jpeg,image/png,image/jpg">
                                <small class="text-muted">Leave empty to keep current photo</small>
                            </div>
                            
                            <!-- Submit Buttons -->
                            <div class="col-12 mt-4">
                                <hr>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update Worker
                                    </button>
                                    <a href="worker-profile.php?id=<?php echo $worker_id; ?>" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>