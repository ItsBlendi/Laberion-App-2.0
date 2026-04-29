<?php
/**
 * Upload Face Image API
 * Registers worker face for recognition
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

$worker_id = isset($_POST['worker_id']) ? (int)$_POST['worker_id'] : 0;

if ($worker_id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid worker ID'
    ]);
    exit();
}

// Check if image is provided
if (!isset($_FILES['face_image'])) {
    echo json_encode([
        'success' => false,
        'message' => 'No image provided'
    ]);
    exit();
}

$file = $_FILES['face_image'];

// Validate file
if ($file['error'] != 0) {
    echo json_encode([
        'success' => false,
        'message' => 'File upload error'
    ]);
    exit();
}

// Validate file type
$allowed = ['jpg', 'jpeg', 'png'];
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

if (!in_array($ext, $allowed)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid file type'
    ]);
    exit();
}

// Validate file size
if ($file['size'] > 5242880) {
    echo json_encode([
        'success' => false,
        'message' => 'File too large'
    ]);
    exit();
}

// Save face image
$face_filename = 'face_' . $worker_id . '_' . time() . '.' . $ext;
$face_path = '../assets/uploads/faces/' . $face_filename;

if (!move_uploaded_file($file['tmp_name'], $face_path)) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to save image'
    ]);
    exit();
}

// Call Python training script
$python_path = getenv('PYTHON_PATH') ?: '/usr/bin/python3';
$script_path = realpath('../python/train_faces.py');
$face_path_escaped = escapeshellarg($face_path);
$worker_id_escaped = escapeshellarg($worker_id);

$command = "$python_path $script_path $worker_id_escaped $face_path_escaped";
$output = shell_exec($command . ' 2>&1');

// Parse Python output
$result = json_decode($output, true);

if ($result && isset($result['success']) && $result['success']) {
    // Update worker with face data
    $face_encoding = $result['encoding'] ?? $face_filename;
    
    db_update('workers', [
        'face_data' => $face_encoding
    ], "id = $worker_id");
    
    echo json_encode([
        'success' => true,
        'message' => 'Face registered successfully',
        'face_id' => $face_filename
    ]);
    
    // Log activity
    log_activity($_SESSION['admin_id'], 'FACE_REGISTRATION', "Registered face for worker $worker_id");
} else {
    // Delete uploaded file if training failed
    if (file_exists($face_path)) {
        unlink($face_path);
    }
    
    $error_message = $result['message'] ?? 'Failed to train face model';
    
    echo json_encode([
        'success' => false,
        'message' => $error_message
    ]);
}
?>