<?php
/**
 * Delete Worker API
 * Soft delete worker record
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

$worker_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($worker_id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid worker ID'
    ]);
    exit();
}

// Get worker
$worker = db_fetch_one("SELECT * FROM workers WHERE id = $worker_id");

if (!$worker) {
    echo json_encode([
        'success' => false,
        'message' => 'Worker not found'
    ]);
    exit();
}

// Soft delete (set status to inactive)
$updated = db_update('workers', [
    'status' => 'inactive'
], "id = $worker_id");

if ($updated) {
    echo json_encode([
        'success' => true,
        'message' => 'Worker deleted successfully'
    ]);
    
    // Log activity
    log_activity($_SESSION['admin_id'], 'WORKER_DELETE', "Deleted worker: " . $worker['name'] . ' ' . $worker['lastname']);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Error deleting worker'
    ]);
}
?>