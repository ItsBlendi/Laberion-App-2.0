<?php
/**
 * Save Attendance API
 * Processes check-in and check-out
 */

require_once '../includes/db.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

$worker_id = isset($_POST['worker_id']) ? (int)$_POST['worker_id'] : 0;
$date = isset($_POST['date']) ? clean_input($_POST['date']) : date('Y-m-d');
$time = isset($_POST['time']) ? clean_input($_POST['time']) : date('H:i:s');
$action = isset($_POST['action']) ? clean_input($_POST['action']) : 'check_in';

// Validate input
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

// Check existing attendance
$existing = db_fetch_one("SELECT * FROM attendance WHERE worker_id = $worker_id AND date = '$date'");

if ($action === 'check_in') {
    // Check if already checked in
    if ($existing) {
        echo json_encode([
            'success' => false,
            'message' => 'Already checked in today',
            'check_in_time' => $existing['check_in'],
            'worker_name' => $worker['name'] . ' ' . $worker['lastname']
        ]);
        exit();
    }
    
    // Determine status (late or present)
    $check_in_time = strtotime($time);
    $work_start = strtotime('09:00:00');
    $status = $check_in_time <= $work_start ? 'present' : 'late';
    
    // Insert attendance
    $attendance_id = db_insert('attendance', [
        'worker_id' => $worker_id,
        'date' => $date,
        'check_in' => $time,
        'status' => $status
    ]);
    
    if ($attendance_id) {
        echo json_encode([
            'success' => true,
            'message' => 'Checked in successfully',
            'worker_name' => $worker['name'] . ' ' . $worker['lastname'],
            'check_in_time' => date('h:i A', strtotime($time)),
            'status' => $status,
            'action' => 'check_in',
            'attendance_id' => $attendance_id
        ]);
        
        // Log activity
        log_activity($worker_id, 'CHECK_IN', "Checked in at $time");
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error saving check-in'
        ]);
    }
}
elseif ($action === 'check_out') {
    // Check if checked in
    if (!$existing) {
        echo json_encode([
            'success' => false,
            'message' => 'No check-in record found for today',
            'worker_name' => $worker['name'] . ' ' . $worker['lastname']
        ]);
        exit();
    }
    
    // Check if already checked out
    if ($existing['check_out']) {
        echo json_encode([
            'success' => false,
            'message' => 'Already checked out today',
            'check_out_time' => $existing['check_out'],
            'worker_name' => $worker['name'] . ' ' . $worker['lastname']
        ]);
        exit();
    }
    
    // Calculate hours worked
    $check_in = strtotime($existing['check_in']);
    $check_out = strtotime($time);
    $hours_worked = ($check_out - $check_in) / 3600;
    
    // Update attendance
    $updated = db_update('attendance', [
        'check_out' => $time,
        'hours' => round($hours_worked, 2)
    ], "id = {$existing['id']}");
    
    if ($updated) {
        echo json_encode([
            'success' => true,
            'message' => 'Checked out successfully',
            'worker_name' => $worker['name'] . ' ' . $worker['lastname'],
            'check_in_time' => date('h:i A', strtotime($existing['check_in'])),
            'check_out_time' => date('h:i A', strtotime($time)),
            'hours_worked' => round($hours_worked, 2),
            'action' => 'check_out'
        ]);
        
        // Log activity
        log_activity($worker_id, 'CHECK_OUT', "Checked out at $time, worked " . round($hours_worked, 2) . " hours");
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error saving check-out'
        ]);
    }
}
else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid action'
    ]);
}
?>