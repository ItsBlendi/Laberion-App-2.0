<?php
/**
 * Reject Leave Request API
 */

require_once '../includes/db.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');

// Check authentication
if (!is_logged_in()) {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized'
    ]);
    exit();
}

$leave_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$reason = isset($_POST['reason']) ? clean_input($_POST['reason']) : '';

if ($leave_id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid leave ID'
    ]);
    exit();
}

// Get leave
$leave = db_fetch_one("SELECT * FROM leaves WHERE id = $leave_id");

if (!$leave) {
    echo json_encode([
        'success' => false,
        'message' => 'Leave request not found'
    ]);
    exit();
}

// Update leave status
$updated = db_update('leaves', [
    'status' => 'rejected',
    'reason' => $reason
], "id = $leave_id");

if ($updated) {
    echo json_encode([
        'success' => true,
        'message' => 'Leave request rejected'
    ]);
    
    // Log activity
    log_activity($_SESSION['admin_id'], 'LEAVE_REJECT', "Rejected leave request #$leave_id");
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Error rejecting leave request'
    ]);
}
?>