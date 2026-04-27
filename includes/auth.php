<?php
/**
 * Authentication and Session Management
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Session configuration
define('SESSION_LIFETIME', 3600); // 1 hour
define('SESSION_TIMEOUT', 1800); // 30 minutes of inactivity

/**
 * Check if user is logged in
 * @return bool - True if logged in, false otherwise
 */
function is_logged_in() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

/**
 * Check session timeout
 * @return bool - True if session is valid, false if expired
 */
function check_session_timeout() {
    if (!isset($_SESSION['last_activity'])) {
        $_SESSION['last_activity'] = time();
        return true;
    }
    
    $elapsed = time() - $_SESSION['last_activity'];
    
    if ($elapsed > SESSION_TIMEOUT) {
        session_destroy();
        return false;
    }
    
    $_SESSION['last_activity'] = time();
    return true;
}

/**
 * Require user to be logged in
 * Redirects to login page if not authenticated
 */
function require_login() {
    if (!is_logged_in()) {
        header("Location: ../login.php");
        exit();
    }
    
    if (!check_session_timeout()) {
        header("Location: ../login.php?expired=1");
        exit();
    }
}

/**
 * Login user
 * @param string $username - Username
 * @param string $password - Password
 * @return bool - True if login successful, false otherwise
 */
function login_user($username, $password) {
    $username = clean_input($username);
    
    $query = "SELECT * FROM admin_users WHERE username = '$username' LIMIT 1";
    $result = db_query($query);
    
    if ($result && mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            $_SESSION['admin_email'] = $user['email'];
            $_SESSION['login_time'] = time();
            $_SESSION['last_activity'] = time();
            
            // Log login
            log_activity($user['id'], 'LOGIN', 'User logged in');
            
            return true;
        }
    }
    
    return false;
}

/**
 * Logout user
 */
function logout_user() {
    if (isset($_SESSION['admin_id'])) {
        log_activity($_SESSION['admin_id'], 'LOGOUT', 'User logged out');
    }
    
    session_destroy();
    header("Location: ../login.php?logout=1");
    exit();
}

/**
 * Get current logged-in user
 * @return array - User data or null
 */
function get_current_user() {
    if (!is_logged_in()) {
        return null;
    }
    
    $admin_id = $_SESSION['admin_id'];
    return db_fetch_one("SELECT * FROM admin_users WHERE id = $admin_id");
}

/**
 * Change user password
 * @param int $user_id - User ID
 * @param string $old_password - Current password
 * @param string $new_password - New password
 * @return array - ['success' => bool, 'message' => string]
 */
function change_password($user_id, $old_password, $new_password) {
    $user = db_fetch_one("SELECT * FROM admin_users WHERE id = $user_id");
    
    if (!$user) {
        return ['success' => false, 'message' => 'User not found'];
    }
    
    // Verify old password
    if (!password_verify($old_password, $user['password'])) {
        return ['success' => false, 'message' => 'Current password is incorrect'];
    }
    
    // Validate new password
    if (strlen($new_password) < 6) {
        return ['success' => false, 'message' => 'Password must be at least 6 characters'];
    }
    
    // Hash new password
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
    
    // Update password
    if (db_update('admin_users', ['password' => $hashed_password], "id = $user_id")) {
        log_activity($user_id, 'PASSWORD_CHANGE', 'User changed password');
        return ['success' => true, 'message' => 'Password changed successfully'];
    }
    
    return ['success' => false, 'message' => 'Error updating password'];
}

/**
 * Log user activity
 * @param int $user_id - User ID
 * @param string $action - Action type
 * @param string $description - Action description
 */
function log_activity($user_id, $action, $description = '') {
    $timestamp = date('Y-m-d H:i:s');
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    
    $log_entry = "[$timestamp] User ID: $user_id | Action: $action | IP: $ip_address | Description: $description\n";
    
    $log_file = '../logs/activity.log';
    
    if (!file_exists(dirname($log_file))) {
        mkdir(dirname($log_file), 0755, true);
    }
    
    file_put_contents($log_file, $log_entry, FILE_APPEND);
}

/**
 * Verify CSRF token
 * @param string $token - Token to verify
 * @return bool - True if valid, false otherwise
 */
function verify_csrf_token($token) {
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }
    
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Generate CSRF token
 * @return string - CSRF token
 */
function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * Get CSRF token input field
 * @return string - HTML input field
 */
function csrf_token_field() {
    $token = generate_csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}
?>