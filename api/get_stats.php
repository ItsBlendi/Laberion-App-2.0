<?php
/**
 * Get Dashboard Statistics API
 * Returns real-time dashboard data
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

$today = date('Y-m-d');

// Get statistics
$total_workers = db_count('workers', "status = 'active'");
$present_today = db_count('attendance', "date = '$today'");
$absent_today = $total_workers - $present_today;
$late_today = db_count('attendance', "date = '$today' AND status = 'late'");
$on_vacation = db_count('leaves', "'$today' BETWEEN start_date AND end_date AND status = 'approved' AND type = 'vacation'");
$on_sick = db_count('leaves', "'$today' BETWEEN start_date AND end_date AND status = 'approved' AND type = 'sick'");
$pending_leaves = db_count('leaves', "status = 'pending'");

// Get recent check-ins
$recent_checkins = db_fetch_all("
    SELECT w.id, w.name, w.lastname, w.photo, w.position, a.check_in, a.status
    FROM attendance a
    JOIN workers w ON a.worker_id = w.id
    WHERE a.date = '$today'
    ORDER BY a.check_in DESC
    LIMIT 10
");

// Get pending leave requests
$pending_leave_requests = db_fetch_all("
    SELECT l.id, l.type, l.start_date, l.end_date, w.name, w.lastname, w.photo
    FROM leaves l
    JOIN workers w ON l.worker_id = w.id
    WHERE l.status = 'pending'
    ORDER BY l.created_at DESC
    LIMIT 5
");

// Calculate attendance percentage
$attendance_percentage = $total_workers > 0 ? round(($present_today / $total_workers) * 100, 1) : 0;

echo json_encode([
    'success' => true,
    'statistics' => [
        'total_workers' => $total_workers,
        'present_today' => $present_today,
        'absent_today' => $absent_today,
        'late_today' => $late_today,
        'on_vacation' => $on_vacation,
        'on_sick' => $on_sick,
        'pending_leaves' => $pending_leaves,
        'attendance_percentage' => $attendance_percentage
    ],
    'recent_checkins' => $recent_checkins,
    'pending_leave_requests' => $pending_leave_requests,
    'timestamp' => date('Y-m-d H:i:s')
]);
?>