<?php
$page_title = "Add Worker - Laberion WMS";
include '../includes/header.php';

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
    
    // Handle photo upload
    $photo = '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $filename = $_FILES['photo']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $new_filename = 'worker_' . time() . '.' . $ext;
            $upload_path = '../assets/uploads/workers/' . $new_filename;
            
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_path)) {
                $photo = $new_filename;
            }
        }
    }
    
    // Insert worker
    $query = "INSERT INTO workers (name, lastname, position, department, phone, salary, start_date, status, photo, created_at) 
              VALUES ('$name', '$lastname', '$position', '$department', '$phone', '$salary', '$start_date', '$status', '$photo', NOW())";
    
    if (db_query($query)) {
        $worker_id = mysqli_insert_id($GLOBALS['conn']);
        $success = "Worker added successfully! ID: #$worker_id";
        
        // Redirect to face capture if needed
        if (isset($_POST['capture_face'])) {
            header("Location: ../kiosk/face-capture.php?worker_id=$worker_id&return=admin");
            exit();
        }
    } else {
        $error = "Error adding worker. Please try again.";
    }
}
?>

<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <h1><i class="fas fa-user-plus"></i> Add New Worker</h1>
                <a href="workers.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Workers
                </a>
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
                            <!-- Personal Information -->
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-user"></i> Personal Information
                                </h5>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="lastname" class="form-control" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Position <span class="text-danger">*</span></label>
                                <input type="text" name="position" class="form-control" 
                                       placeholder="e.g. Software Engineer" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Department <span class="text-danger">*</span></label>
                                <select name="department" class="form-select" required>
                                    <option value="">Select Department</option>
                                    <option value="IT">IT</option>
                                    <option value="HR">HR</option>
                                    <option value="Finance">Finance</option>
                                    <option value="Marketing">Marketing</option>
                                    <option value="Sales">Sales</option>
                                    <option value="Operations">Operations</option>
                                    <option value="Customer Service">Customer Service</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Phone Number</label>
                                <input type="tel" name="phone" class="form-control" 
                                       placeholder="+1 234 567 8900">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Salary (Optional)</label>
                                <input type="number" name="salary" class="form-control" 
                                       step="0.01" placeholder="0.00">
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
                                       value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            
                            <!-- Photo Upload -->
                            <div class="col-12 mt-4">
                                <h5 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-camera"></i> Photo & Face ID
                                </h5>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Profile Photo</label>
                                <input type="file" name="photo" class="form-control" 
                                       accept="image/jpeg,image/png,image/jpg">
                                <small class="text-muted">Accepted: JPG, JPEG, PNG (Max 5MB)</small>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Face ID Registration</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" 
                                           name="capture_face" id="capture_face" value="1">
                                    <label class="form-check-label" for="capture_face">
                                        Capture Face ID after saving
                                    </label>
                                </div>
                                <small class="text-muted">
                                    Check this to register face for kiosk check-in
                                </small>
                            </div>
                            
                            <!-- Submit Buttons -->
                            <div class="col-12 mt-4">
                                <hr>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save Worker
                                    </button>
                                    <a href="workers.php" class="btn btn-secondary">
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