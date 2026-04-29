<?php
/**
 * Get Worker Information API
 * Returns worker details by ID
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

$worker_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($worker_id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid worker ID'
    ]);
    exit();
}

// Get worker details
$worker = db_fetch_one("SELECT * FROM workers WHERE id = $worker_id");

if (!$worker) {
    echo json_encode([
        'success' => false,
        'message' => 'Worker not found'
    ]);
    exit();
}

// Get today's attendance
$today = date('Y-m-d');
$attendance = db_fetch_one("SELECT * FROM attendance WHERE worker_id = $worker_id AND date = '$today'");

// Get statistics
$total_days = db_count('attendance', "worker_id = $worker_id");
$present_days = db_count('attendance', "worker_id = $worker_id AND status != 'absent'");
$late_days = db_count('attendance', "worker_id = $worker_id AND status = 'late'");
$total_hours = db_fetch_one("SELECT SUM(hours) as total FROM attendance WHERE worker_id = $worker_id");

// Get pending leaves
$pending_leaves = db_count('leaves', "worker_id = $worker_id AND status = 'pending'");

echo json_encode([
    'success' => true,
    'worker' => [
        'id' => $worker['id'],
        'name' => $worker['name'],
        'lastname' => $worker['lastname'],
        'position' => $worker['position'],
        'department' => $worker['department'],
        'phone' => $worker['phone'],
        'salary' => $worker['salary'],
        'status' => $worker['status'],
        'photo' => $worker['photo'],
        'start_date' => $worker['start_date'],
        'face_registered' => !empty($worker['face_data'])
    ],
    'today_attendance' => $attendance ? [
        'check_in' => $attendance['check_in'],
        'check_out' => $attendance['check_out'],
        'status' => $attendance['status'],
        'hours' => $attendance['hours']
    ] : null,
    'statistics' => [
        'total_days' => $total_days,
        'present_days' => $present_days,
        'late_days' => $late_days,
        'total_hours' => round($total_hours['total'] ?? 0, 2),
        'pending_leaves' => $pending_leaves
    ]
]);
?>