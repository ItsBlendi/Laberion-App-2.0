<?php
/**
 * Application Configuration
 * Global constants and settings
 */

// Application Settings
define('APP_NAME', 'Laberion WMS');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/laberion');
define('APP_ENV', 'production'); // development or production

// Timezone
date_default_timezone_set('Europe/Zurich');

// File Upload Settings
define('UPLOAD_DIR', '../assets/uploads');
define('UPLOAD_WORKERS_DIR', UPLOAD_DIR . '/workers');
define('UPLOAD_FACES_DIR', UPLOAD_DIR . '/faces');
define('MAX_UPLOAD_SIZE', 5242880); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'pdf']);

// Working Hours
define('WORK_START_TIME', '09:00:00');
define('WORK_END_TIME', '17:00:00');
define('LATE_THRESHOLD', 15); // minutes

// Date and Time Formats
define('DATE_FORMAT', 'Y-m-d');
define('TIME_FORMAT', 'H:i:s');
define('DATETIME_FORMAT', 'Y-m-d H:i:s');
define('DISPLAY_DATE_FORMAT', 'M d, Y');
define('DISPLAY_TIME_FORMAT', 'h:i A');
define('DISPLAY_DATETIME_FORMAT', 'M d, Y h:i A');

// Pagination
define('ITEMS_PER_PAGE', 15);
define('ITEMS_PER_PAGE_SMALL', 10);

// Session Settings
define('SESSION_LIFETIME', 3600); // 1 hour
define('SESSION_TIMEOUT', 1800); // 30 minutes of inactivity

// Email Settings
define('MAIL_FROM', 'noreply@laberion.com');
define('MAIL_FROM_NAME', 'Laberion WMS');
define('MAIL_HOST', 'localhost');
define('MAIL_PORT', 25);

// Face Recognition Settings
define('FACE_RECOGNITION_THRESHOLD', 0.6);
define('FACE_RECOGNITION_TIMEOUT', 30); // seconds
define('PYTHON_PATH', '/usr/bin/python3');

// Logging
define('LOG_DIR', '../logs');
define('LOG_ERRORS', true);
define('LOG_ACTIVITIES', true);
define('LOG_ATTENDANCE', true);

// Security
define('ENABLE_CSRF_PROTECTION', true);
define('ENABLE_RATE_LIMITING', true);
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_ATTEMPT_TIMEOUT', 900); // 15 minutes

// API Settings
define('API_RATE_LIMIT', 100); // requests per minute
define('API_TIMEOUT', 30); // seconds

// Error Handling
if (APP_ENV === 'production') {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// Create necessary directories
$directories = [
    UPLOAD_DIR,
    UPLOAD_WORKERS_DIR,
    UPLOAD_FACES_DIR,
    LOG_DIR,
];

foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Load environment variables if .env file exists
if (file_exists(__DIR__ . '/../.env')) {
    $env_file = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($env_file as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            putenv("$key=$value");
        }
    }
}

// Custom error handler
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    if (LOG_ERRORS) {
        $error_message = "[$errno] $errstr in $errfile on line $errline";
        error_log($error_message, 3, LOG_DIR . '/error.log');
    }
    
    if (APP_ENV === 'development') {
        echo "<pre>Error: $errstr in $errfile on line $errline</pre>";
    }
});

// Custom exception handler
set_exception_handler(function($exception) {
    if (LOG_ERRORS) {
        error_log($exception->getMessage(), 3, LOG_DIR . '/error.log');
    }
    
    if (APP_ENV === 'development') {
        echo "<pre>" . $exception . "</pre>";
    } else {
        echo "An error occurred. Please try again later.";
    }
});

// Shutdown handler
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== null && LOG_ERRORS) {
        error_log(print_r($error, true), 3, LOG_DIR . '/error.log');
    }
});
?>