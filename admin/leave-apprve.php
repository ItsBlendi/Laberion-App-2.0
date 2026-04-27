<?php
include '../includes/db.php';
include '../includes/auth.php';
require_login();

$leave_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($leave_id > 0) {
    $update_query = "UPDATE leaves SET status = 'approved' WHERE id = $leave_id";
    
    if (db_query($update_query)) {
        $_SESSION['success_message'] = "Leave request approved successfully!";
    } else {
        $_SESSION['error_message'] = "Error approving leave request.";
    }
}

header("Location: leaves.php");
exit();
?>