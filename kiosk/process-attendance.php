<?php
require_once '../includes/db.php';

header('Content-Type: application/json');

$worker_id = isset($_POST['worker_id']) ? (int)$_POST['worker_id'] : 0;
$date = isset($_POST['date']) ? clean_input($_POST['date']) : date('Y-m-d');
$time = isset($_POST['time']) ? clean_input($_POST['time']) : date('H:i:s');
$action = isset($_POST['action']) ? clean_input($_POST['action']) : 'check_in';

if ($worker_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid worker ID']);
    exit();
}

// Get worker details
$worker = mysqli_fetch_assoc(db_query("SELECT * FROM workers WHERE id = $worker_id"));

if (!$worker) {
    echo json_encode(['success' => false, 'message' => 'Worker not found']);
    exit();
}

// Check existing attendance for today
$existing = mysqli_fetch_assoc(db_query("
    SELECT * FROM attendance 
    WHERE worker_id = $worker_id 
    AND date = '$date'
    LIMIT 1
"));

if ($action === 'check_in') {
    if ($existing) {
        echo json_encode([
            'success' => false,
            'message' => 'Already checked in today at ' . date('h:i A', strtotime($existing['check_in'])),
            'worker_name' => $worker['name'] . ' ' . $worker['lastname']
        ]);
        exit();
    }
    
    // Determine if late (work starts at 09:00)
    $check_in_time = strtotime($time);
    $work_start = strtotime('09:00:00');
    $status = $check_in_time <= $work_start ? 'present' : 'late';
    
    // Insert check-in
    $query = "INSERT INTO attendance (worker_id, date, check_in, status) 
              VALUES ($worker_id, '$date', '$time', '$status')";
    
    if (db_query($query)) {
        echo json_encode([
            'success' => true,
            'message' => 'Checked in successfully',
            'worker_name' => $worker['name'] . ' ' . $worker['lastname'],
            'time' => date('h:i A', strtotime($time)),
            'status' => $status,
            'action' => 'check_in'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error saving check-in']);
    }
}
elseif ($action === 'check_out') {
    if (!$existing) {
        echo json_encode([
            'success' => false,
            'message' => 'No check-in record found for today',
            'worker_name' => $worker['name'] . ' ' . $worker['lastname']
        ]);
        exit();
    }
    
    if ($existing['check_out']) {
        echo json_encode([
            'success' => false,
            'message' => 'Already checked out today at ' . date('h:i A', strtotime($existing['check_out'])),
            'worker_name' => $worker['name'] . ' ' . $worker['lastname']
        ]);
        exit();
    }
    
    // Calculate hours worked
    $check_in = strtotime($existing['check_in']);
    $check_out = strtotime($time);
    $hours_worked = ($check_out - $check_in) / 3600;
    
    // Update check-out
    $query = "UPDATE attendance 
              SET check_out = '$time', hours = $hours_worked 
              WHERE id = {$existing['id']}";
    
    if (db_query($query)) {
        echo json_encode([
            'success' => true,
            'message' => 'Checked out successfully',
            'worker_name' => $worker['name'] . ' ' . $worker['lastname'],
            'check_in_time' => date('h:i A', strtotime($existing['check_in'])),
            'check_out_time' => date('h:i A', strtotime($time)),
            'hours_worked' => round($hours_worked, 2),
            'action' => 'check_out'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error saving check-out']);
    }
}
else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>