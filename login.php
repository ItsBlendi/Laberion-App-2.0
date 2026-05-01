<?php
/**
 * Login Page
 * User authentication for Laberion WMS
 */

require_once 'includes/db.php';
require_once 'includes/auth.php';

// If already logged in, redirect to dashboard
if (is_logged_in()) {
    header("Location: admin/dashboard.php");
    exit();
}

$error = '';
$success = '';

// Check for logout message
if (isset($_GET['logout'])) {
    $success = 'You have been logged out successfully.';
}

// Check for session expired message
if (isset($_GET['expired'])) {
    $error = 'Your session has expired. Please login again.';
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = isset($_POST['username']) ? clean_input($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $remember = isset($_POST['remember']) ? true : false;
    
    // Validate input
    if (empty($username)) {
        $error = 'Username is required';
    } elseif (empty($password)) {
        $error = 'Password is required';
    } else {
        // Attempt login
        if (login_user($username, $password)) {
            // Set remember me cookie
            if ($remember) {
                setcookie('remember_username', $username, time() + (30 * 24 * 60 * 60), '/');
            } else {
                setcookie('remember_username', '', time() - 3600, '/');
            }
            
            // Redirect to dashboard
            header("Location: admin/dashboard.php");
            exit();
        } else {
            $error = 'Invalid username or password';
            
            // Log failed login attempt
            $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
            error_log("Failed login attempt for username: $username from IP: $ip_address");
        }
    }
}

// Get remembered username
$remembered_username = isset($_COOKIE['remember_username']) ? $_COOKIE['remember_username'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Laberion Workforce Management System - Login">
    <meta name="author" content="Laberion">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <title>Login - Laberion WMS</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #1e3a8a;
            --primary-dark: #1e40af;
            --secondary-color: #3b82f6;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --light-color: #f3f4f6;
            --white: #ffffff;
            --dark-color: #1f2937;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html, body {
            height: 100%;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .login-container {
            width: 100%;
            max-width: 450px;
        }
        
        .login-box {
            background: var(--white);
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            animation: slideUp 0.5s ease-out;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: var(--white);
            padding: 40px 30px;
            text-align: center;
        }
        
        .login-header .logo {
            font-size: 48px;
            margin-bottom: 15px;
            display: block;
        }
        
        .login-header h1 {
            font-size: 28px;
            font-weight: 700;
            margin: 0 0 5px 0;
        }
        
        .login-header p {
            font-size: 14px;
            opacity: 0.9;
            margin: 0;
        }
        
        .login-body {
            padding: 40px 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 8px;
            font-size: 14px;
            display: block;
        }
        
        .form-control {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 14px;
            transition: all 0.3s;
            background: #f9fafb;
        }
        
        .form-control:focus {
            background: var(--white);
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            outline: none;
        }
        
        .form-control::placeholder {
            color: #9ca3af;
        }
        
        .input-group-text {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            color: #6b7280;
        }
        
        .form-check {
            margin-bottom: 20px;
        }
        
        .form-check-input {
            width: 18px;
            height: 18px;
            margin-top: 3px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .form-check-input:checked {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .form-check-label {
            margin-left: 8px;
            cursor: pointer;
            font-size: 14px;
            color: var(--dark-color);
        }
        
        .btn-login {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: var(--white);
            border: none;
            border-radius: 8px;
            padding: 12px 20px;
            font-weight: 600;
            font-size: 16px;
            width: 100%;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(30, 58, 138, 0.3);
            color: var(--white);
            text-decoration: none;
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .btn-login:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        .alert {
            border-radius: 8px;
            border: none;
            padding: 15px 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideDown 0.3s ease-out;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            color: #059669;
            border-left: 4px solid #10b981;
        }
        
        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
            border-left: 4px solid #ef4444;
        }
        
        .alert i {
            font-size: 18px;
        }
        
        .login-footer {
            text-align: center;
            padding: 20px 30px;
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
            font-size: 13px;
            color: #6b7280;
        }
        
        .login-footer a {
            color: var(--secondary-color);
            text-decoration: none;
            font-weight: 600;
        }
        
        .login-footer a:hover {
            color: var(--primary-color);
            text-decoration: underline;
        }
        
        .demo-credentials {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            font-size: 13px;
            color: #1e40af;
        }
        
        .demo-credentials strong {
            display: block;
            margin-bottom: 8px;
            color: #1e3a8a;
        }
        
        .demo-credentials code {
            background: rgba(30, 58, 138, 0.1);
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
        
        .password-toggle {
            cursor: pointer;
            color: #6b7280;
            transition: color 0.3s;
        }
        
        .password-toggle:hover {
            color: var(--secondary-color);
        }
        
        .loading-spinner {
            display: none;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: var(--white);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .loading-spinner.show {
            display: inline-block;
        }
        
        /* Responsive */
        @media (max-width: 480px) {
            .login-box {
                border-radius: 10px;
            }
            
            .login-header {
                padding: 30px 20px;
            }
            
            .login-header .logo {
                font-size: 40px;
                margin-bottom: 10px;
            }
            
            .login-header h1 {
                font-size: 24px;
            }
            
            .login-body {
                padding: 30px 20px;
            }
            
            .login-footer {
                padding: 15px 20px;
            }
            
            .form-label {
                font-size: 13px;
            }
            
            .form-control {
                padding: 10px 12px;
                font-size: 13px;
            }
            
            .btn-login {
                padding: 10px 16px;
                font-size: 14px;
            }
        }
        
        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            body {
                background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
            }
            
            .login-box {
                background: #1f2937;
                color: #f3f4f6;
            }
            
            .form-label {
                color: #e5e7eb;
            }
            
            .form-control {
                background: #374151;
                border-color: #4b5563;
                color: #f3f4f6;
            }
            
            .form-control:focus {
                background: #1f2937;
                border-color: #60a5fa;
            }
            
            .form-control::placeholder {
                color: #9ca3af;
            }
            
            .input-group-text {
                background: #374151;
                border-color: #4b5563;
                color: #d1d5db;
            }
            
            .form-check-label {
                color: #e5e7eb;
            }
            
            .login-footer {
                background: #111827;
                border-top-color: #374151;
                color: #9ca3af;
            }
            
            .demo-credentials {
                background: rgba(30, 58, 138, 0.2);
                border-color: rgba(59, 130, 246, 0.3);
                color: #93c5fd;
            }
            
            .demo-credentials strong {
                color: #bfdbfe;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <!-- Header -->
            <div class="login-header">
                <i class="fas fa-building logo"></i>
                <h1>LABERION</h1>
                <p>Workforce Management System</p>
            </div>
            
            <!-- Body -->
            <div class="login-body">
                <!-- Success Message -->
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <span><?php echo htmlspecialchars($success); ?></span>
                    </div>
                <?php endif; ?>
                
                <!-- Error Message -->
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <span><?php echo htmlspecialchars($error); ?></span>
                    </div>
                <?php endif; ?>
                
                <!-- Demo Credentials (Development Only) -->
                <div class="demo-credentials">
                    <strong><i class="fas fa-info-circle"></i> Demo Credentials</strong>
                    Username: <code>admin</code><br>
                    Password: <code>password</code>
                </div>
                
                <!-- Login Form -->
                <form method="POST" action="" id="loginForm">
                    <!-- Username -->
                    <div class="form-group">
                        <label class="form-label" for="username">
                            <i class="fas fa-user"></i> Username
                        </label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="username" 
                            name="username" 
                            placeholder="Enter your username"
                            value="<?php echo htmlspecialchars($remembered_username); ?>"
                            required
                            autofocus
                        >
                    </div>
                    
                    <!-- Password -->
                    <div class="form-group">
                        <label class="form-label" for="password">
                            <i class="fas fa-lock"></i> Password
                        </label>
                        <div class="input-group">
                            <input 
                                type="password" 
                                class="form-control" 
                                id="password" 
                                name="password" 
                                placeholder="Enter your password"
                                required
                            >
                            <span class="input-group-text">
                                <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                            </span>
                        </div>
                    </div>
                    
                    <!-- Remember Me -->
                    <div class="form-check">
                        <input 
                            class="form-check-input" 
                            type="checkbox" 
                            id="remember" 
                            name="remember"
                            <?php echo !empty($remembered_username) ? 'checked' : ''; ?>
                        >
                        <label class="form-check-label" for="remember">
                            Remember me
                        </label>
                    </div>
                    
                    <!-- Submit Button -->
                    <button type="submit" class="btn-login" id="loginBtn">
                        <span class="loading-spinner" id="spinner"></span>
                        <i class="fas fa-sign-in-alt"></i>
                        <span id="btnText">Sign In</span>
                    </button>
                </form>
            </div>
            
            <!-- Footer -->
            <div class="login-footer">
                <p style="margin: 0;">
                    <i class="fas fa-shield-alt"></i> 
                    Secure Login • 
                    <a href="#">Privacy Policy</a> • 
                    <a href="#">Support</a>
                </p>
                <p style="margin: 10px 0 0 0; font-size: 12px;">
                    &copy; 2024 Laberion WMS. All rights reserved.
                </p>
            </div>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        /**
         * Login Page JavaScript
         */
        
        // Toggle password visibility
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        
        if (togglePassword) {
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        }
        
        // Form submission
        const loginForm = document.getElementById('loginForm');
        const loginBtn = document.getElementById('loginBtn');
        const spinner = document.getElementById('spinner');
        const btnText = document.getElementById('btnText');
        
        if (loginForm) {
            loginForm.addEventListener('submit', function(e) {
                // Show loading state
                loginBtn.disabled = true;
                spinner.classList.add('show');
                btnText.textContent = 'Signing in...';
            });
        }
        
        // Focus on username input
        const usernameInput = document.getElementById('username');
        if (usernameInput && !usernameInput.value) {
            usernameInput.focus();
        }
        
        // Enter key to submit
        document.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && document.activeElement === passwordInput) {
                loginForm.submit();
            }
        });
        
        // Auto-hide alerts after 5 seconds
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.opacity = '0';
                alert.style.transition = 'opacity 0.3s ease';
                setTimeout(() => {
                    alert.remove();
                }, 300);
            }, 5000);
        });
        
        // Prevent form resubmission on page refresh
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body>
</html>