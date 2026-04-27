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

// Check if already checked in today
$existing = mysqli_fetch_assoc(db_query("
    SELECT * FROM attendance 
    WHERE worker_id = $worker_id 
    AND date = '$date'
    LIMIT 1
"));

if ($existing) {
    echo json_encode([
        'success' => false,
        'message' => 'Already checked in today',
        'check_in_time' => $existing['check_in']
    ]);
    exit();
}

// Determine if late (assuming work starts at 09:00)
$check_in_time = strtotime($time);
$work_start = strtotime('09:00:00');
$status = $check_in_time <= $work_start ? 'present' : 'late';

// Insert attendance record
$query = "INSERT INTO attendance (worker_id, date, check_in, status) 
          VALUES ($worker_id, '$date', '$time', '$status')";

if (db_query($query)) {
    // Get worker details
    $worker = mysqli_fetch_assoc(db_query("SELECT name, lastname FROM workers WHERE id = $worker_id"));
    
    echo json_encode([
        'success' => true,
        'message' => 'Checked in successfully',
        'worker_name' => $worker['name'] . ' ' . $worker['lastname'],
        'check_in_time' => $time,
        'status' => $status,
        'action' => 'check_in'
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error saving attendance']);
}
?>