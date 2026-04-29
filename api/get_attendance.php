<?php
/**
 * Get Attendance Records API
 * Returns attendance data with filters
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
$date_from = isset($_GET['from']) ? clean_input($_GET['from']) : date('Y-m-01');
$date_to = isset($_GET['to']) ? clean_input($_GET['to']) : date('Y-m-d');
$worker_id = isset($_GET['worker_id']) ? (int)$_GET['worker_id'] : 0;
$status = isset($_GET['status']) ? clean_input($_GET['status']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 15;

// Build query
$where = "WHERE a.date BETWEEN '$date_from' AND '$date_to'";

if ($worker_id > 0) {
    $where .= " AND a.worker_id = $worker_id";
}

if ($status) {
    $where .= " AND a.status = '$status'";
}

// Get total count
$total = db_count('attendance', substr($where, 6));

// Get records
$offset = ($page - 1) * $per_page;
$records = db_fetch_all("
    SELECT a.*, w.name, w.lastname, w.position, w.photo
    FROM attendance a
    JOIN workers w ON a.worker_id = w.id
    $where
    ORDER BY a.date DESC, a.check_in DESC
    LIMIT $per_page OFFSET $offset
");

// Calculate statistics
$present = db_count('attendance', substr($where, 6) . " AND status != 'absent'");
$late = db_count('attendance', substr($where, 6) . " AND status = 'late'");
$absent = db_count('attendance', substr($where, 6) . " AND status = 'absent'");

$total_hours = db_fetch_one("SELECT SUM(hours) as total FROM attendance a $where");

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
        'present' => $present,
        'late' => $late,
        'absent' => $absent,
        'total_hours' => round($total_hours['total'] ?? 0, 2)
    ]
]);
?>