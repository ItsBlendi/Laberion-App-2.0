<?php
/**
 * Laberion WMS - Main Entry Point
 * Handles routing and initialization
 */

// Start session
session_start();

// Define base path
define('BASE_PATH', __DIR__);
define('APP_PATH', BASE_PATH);

// Load configuration
require_once BASE_PATH . '/includes/config.php';
require_once BASE_PATH . '/includes/db.php';
require_once BASE_PATH . '/includes/auth.php';
require_once BASE_PATH . '/includes/functions.php';

// Set error handling
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
}

// Get requested page
$page = isset($_GET['page']) ? clean_input($_GET['page']) : 'dashboard';
$action = isset($_GET['action']) ? clean_input($_GET['action']) : '';

// Check if user is logged in
if (!is_logged_in() && $page !== 'login') {
    header("Location: login.php");
    exit();
}

// Route handling
switch ($page) {
    // Authentication
    case 'login':
        require_once 'login.php';
        break;
    
    case 'logout':
        logout_user();
        break;
    
    // Admin pages
    case 'dashboard':
        require_login();
        require_once 'admin/dashboard.php';
        break;
    
    case 'workers':
        require_login();
        require_once 'admin/workers.php';
        break;
    
    case 'worker-profile':
        require_login();
        require_once 'admin/worker-profile.php';
        break;
    
    case 'add-worker':
        require_login();
        require_once 'admin/add-worker.php';
        break;
    
    case 'edit-worker':
        require_login();
        require_once 'admin/edit-worker.php';
        break;
    
    case 'attendance':
        require_login();
        require_once 'admin/attendance.php';
        break;
    
    case 'attendance-details':
        require_login();
        require_once 'admin/attendance-details.php';
        break;
    
    case 'leaves':
        require_login();
        require_once 'admin/leaves.php';
        break;
    
    case 'leave-approve':
        require_login();
        require_once 'admin/leave-approve.php';
        break;
    
    case 'leave-reject':
        require_login();
        require_once 'admin/leave-reject.php';
        break;
    
    case 'reports':
        require_login();
        require_once 'admin/reports.php';
        break;
    
    case 'settings':
        require_login();
        require_once 'admin/settings.php';
        break;
    
    // Kiosk
    case 'kiosk':
        require_once 'kiosk/index.php';
        break;
    
    // Default
    default:
        if (is_logged_in()) {
            header("Location: ?page=dashboard");
        } else {
            header("Location: login.php");
        }
        exit();
}
?>
