<?php
$page_title = "Reject Leave - Laberion WMS";
include '../includes/header.php';

$leave_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get leave details
$leave_query = db_query("SELECT l.*, w.name, w.lastname FROM leaves l JOIN workers w ON l.worker_id = w.id WHERE l.id = $leave_id");
if (mysqli_num_rows($leave_query) == 0) {
    header("Location: leaves.php");
    exit();
}
$leave = mysqli_fetch_assoc($leave_query);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reason = clean_input($_POST['reason']);
    
    $update_query = "UPDATE leaves SET status = 'rejected' WHERE id = $leave_id";
    
    if (db_query($update_query)) {
        $success = "Leave request rejected successfully!";
        header("Location: leaves.php?success=1");
        exit();
    } else {
        $error = "Error rejecting leave request.";
    }
}
?>

<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <h1><i class="fas fa-times-circle"></i> Reject Leave Request</h1>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-lg-6 mx-auto">
                <div class="table-container">
                    <div class="alert alert-warning mb-4">
                        <i class="fas fa-info-circle"></i>
                        <strong>Leave Request Details:</strong>
                        <br>
                        Worker: <?php echo $leave['name'] . ' ' . $leave['lastname']; ?>
                        <br>
                        Type: <?php echo ucfirst($leave['type']); ?>
                        <br>
                        Period: <?php echo date('M d, Y', strtotime($leave['start_date'])); ?> - <?php echo date('M d, Y', strtotime($leave['end_date'])); ?>
                    </div>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Rejection Reason (Optional)</label>
                            <textarea name="reason" class="form-control" rows="4" 
                                      placeholder="Enter reason for rejection..."></textarea>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-times"></i> Reject Request
                            </button>
                            <a href="leaves.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>