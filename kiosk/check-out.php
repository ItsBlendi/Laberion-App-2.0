<?php
require_once '../includes/db.php';

header('Content-Type: application/json');

$worker_id = isset($_POST['worker_id']) ? (int)$_POST['worker_id'] : 0;
$date = isset($_POST['date']) ? clean_input($_POST['date']) : date('Y-m-d');
$time = isset($_POST['time']) ? clean_input($_POST['time']) : date('H:i:s');

if ($worker_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid worker ID']);
    exit();
}

// Check if checked in today
$attendance = mysqli_fetch_assoc(db_query("
    SELECT * FROM attendance 
    WHERE worker_id = $worker_id 
    AND date = '$date'
    LIMIT 1
"));

if (!$attendance) {
    echo json_encode([
        'success' => false,
        'message' => 'No check-in record found for today'
    ]);
    exit();
}

if ($attendance['check_out']) {
    echo json_encode([
        'success' => false,
        'message' => 'Already checked out today',
        'check_out_time' => $attendance['check_out']
    ]);
    exit();
}

// Calculate hours worked
$check_in = strtotime($attendance['check_in']);
$check_out = strtotime($time);
$hours_worked = ($check_out - $check_in) / 3600; // Convert seconds to hours

// Update attendance record
$query = "UPDATE attendance 
          SET check_out = '$time', hours = $hours_worked 
          WHERE id = {$attendance['id']}";

if (db_query($query)) {
    // Get worker details
    $worker = mysqli_fetch_assoc(db_query("SELECT name, lastname FROM workers WHERE id = $worker_id"));
    
    echo json_encode([
        'success' => true,
        'message' => 'Checked out successfully',
        'worker_name' => $worker['name'] . ' ' . $worker['lastname'],
        'check_in_time' => $attendance['check_in'],
        'check_out_time' => $time,
        'hours_worked' => round($hours_worked, 2),
        'action' => 'check_out'
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error updating attendance']);
}
?>