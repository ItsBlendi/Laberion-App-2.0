<?php
include '../includes/db.php';
include '../includes/auth.php';
require_login();

$worker_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($worker_id > 0) {
    // Get worker photo to delete
    $worker = mysqli_fetch_assoc(db_query("SELECT photo FROM workers WHERE id = $worker_id"));
    
    // Delete worker (this will cascade delete attendance and leaves due to foreign key)
    $delete_query = "DELETE FROM workers WHERE id = $worker_id";
    
    if (db_query($delete_query)) {
        // Delete photo file if exists
        if ($worker['photo'] && file_exists('../assets/uploads/workers/' . $worker['photo'])) {
            unlink('../assets/uploads/workers/' . $worker['photo']);
        }
        
        $_SESSION['success_message'] = "Worker deleted successfully!";
    } else {
        $_SESSION['error_message'] = "Error deleting worker.";
    }
}

header("Location: workers.php");
exit();
?>