<?php
/**
 * Face Recognition API Endpoint
 * Receives face image and returns worker identification
 */

require_once '../includes/db.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

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
        'message' => 'Invalid file type. Only JPG and PNG allowed.'
    ]);
    exit();
}

// Validate file size (max 5MB)
if ($file['size'] > 5242880) {
    echo json_encode([
        'success' => false,
        'message' => 'File too large. Maximum 5MB allowed.'
    ]);
    exit();
}

// Save temporary image
$temp_filename = 'temp_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
$temp_path = '../assets/uploads/faces/' . $temp_filename;

if (!move_uploaded_file($file['tmp_name'], $temp_path)) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to save image'
    ]);
    exit();
}

// Call Python face recognition script
$python_path = getenv('PYTHON_PATH') ?: '/usr/bin/python3';
$script_path = realpath('../python/face_recognition.py');
$temp_path_escaped = escapeshellarg($temp_path);

$command = "$python_path $script_path $temp_path_escaped";
$output = shell_exec($command . ' 2>&1');

// Parse Python output
$result = json_decode($output, true);

// Clean up temp file
if (file_exists($temp_path)) {
    unlink($temp_path);
}

// Check if face was recognized
if ($result && isset($result['success']) && $result['success']) {
    $worker_id = $result['worker_id'];
    $confidence = $result['confidence'] ?? 0;
    
    // Get worker details
    $worker = db_fetch_one("SELECT id, name, lastname FROM workers WHERE id = $worker_id AND status = 'active'");
    
    if ($worker) {
        // Determine action (check-in or check-out)
        $current_hour = date('H');
        $action = $current_hour >= 16 ? 'check_out' : 'check_in';
        
        // Check if already checked in today
        $today = date('Y-m-d');
        $existing = db_fetch_one("SELECT * FROM attendance WHERE worker_id = $worker_id AND date = '$today'");
        
        if ($existing && !$existing['check_out']) {
            $action = 'check_out';
        } elseif ($existing && $existing['check_out']) {
            echo json_encode([
                'success' => false,
                'message' => 'Already checked in and out today',
                'worker_name' => $worker['name'] . ' ' . $worker['lastname']
            ]);
            exit();
        }
        
        echo json_encode([
            'success' => true,
            'worker_id' => $worker_id,
            'worker_name' => $worker['name'] . ' ' . $worker['lastname'],
            'confidence' => round($confidence, 2),
            'action' => $action,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Worker not found or inactive'
        ]);
    }
} else {
    $error_message = $result['message'] ?? 'Face not recognized';
    
    echo json_encode([
        'success' => false,
        'message' => $error_message,
        'confidence' => 0
    ]);
}
?>