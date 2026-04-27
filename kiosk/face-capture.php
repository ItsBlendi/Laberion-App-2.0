<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');

// Check if this is for registration or check-in
$worker_id = isset($_POST['worker_id']) ? (int)$_POST['worker_id'] : 0;
$action = isset($_POST['action']) ? clean_input($_POST['action']) : 'check_in';

if (!isset($_FILES['face_image'])) {
    echo json_encode(['success' => false, 'message' => 'No image provided']);
    exit();
}

$file = $_FILES['face_image'];

// Validate file
if ($file['error'] != 0) {
    echo json_encode(['success' => false, 'message' => 'File upload error']);
    exit();
}

$allowed = ['jpg', 'jpeg', 'png'];
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

if (!in_array($ext, $allowed)) {
    echo json_encode(['success' => false, 'message' => 'Invalid file type']);
    exit();
}

// Save temporary image
$temp_filename = 'temp_' . time() . '.' . $ext;
$temp_path = '../assets/uploads/faces/' . $temp_filename;

if (!move_uploaded_file($file['tmp_name'], $temp_path)) {
    echo json_encode(['success' => false, 'message' => 'Failed to save image']);
    exit();
}

// Call Python face recognition script
$python_path = '/usr/bin/python3'; // Adjust based on your system
$script_path = '../python/face_recognition.py';
$command = escapeshellcmd("$python_path $script_path $temp_path");

$output = shell_exec($command);
$result = json_decode($output, true);

// Clean up temp file
unlink($temp_path);

if ($result && $result['success']) {
    $recognized_worker_id = $result['worker_id'];
    
    // Get worker details
    $worker = mysqli_fetch_assoc(db_query("SELECT name, lastname FROM workers WHERE id = $recognized_worker_id"));
    
    if ($worker) {
        echo json_encode([
            'success' => true,
            'worker_id' => $recognized_worker_id,
            'worker_name' => $worker['name'] . ' ' . $worker['lastname'],
            'confidence' => $result['confidence'],
            'action' => $action
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Worker not found']);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => $result['message'] ?? 'Face not recognized'
    ]);
}
?>