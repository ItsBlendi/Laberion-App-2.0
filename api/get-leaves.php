<?php
/**
 * Get Leave Requests API
 * Returns leave data with filters
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

// Get parameters
$status = isset($_GET['status']) ? clean_input($_GET['status']) : '';
$type = isset($_GET['type']) ? clean_input($_GET['type']) : '';
$worker_id = isset($_GET['worker_id']) ? (int)$_GET['worker_id'] : 0;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 15;

// Build query
$where = "WHERE 1=1";

if ($status) {
    $where .= " AND l.status = '$status'";
}

if ($type) {
    $where .= " AND l.type = '$type'";
}

if ($worker_id > 0) {
    $where .= " AND l.worker_id = $worker_id";
}

// Get total count
$total = db_count('leaves', substr($where, 6));

// Get records
$offset = ($page - 1) * $per_page;
$records = db_fetch_all("
    SELECT l.*, w.name, w.lastname, w.position, w.photo
    FROM leaves l
    JOIN workers w ON l.worker_id = w.id
    $where
    ORDER BY l.created_at DESC
    LIMIT $per_page OFFSET $offset
");

// Calculate statistics
$pending = db_count('leaves', "status = 'pending'");
$approved = db_count('leaves', "status = 'approved'");
$rejected = db_count('leaves', "status = 'rejected'");

echo json_encode([
    'success' => true,
    'data' => $records,
    'pagination' => [
        'total' => $total,
        'page' => $page,
        'per_page' => $per_page,
        'total_pages' => ceil($total / $per_page)
    ],
    'statistics' => [
        'pending' => $pending,
        'approved' => $approved,
        'rejected' => $rejected
    ]
]);
?>