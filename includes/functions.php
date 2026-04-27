<?php
/**
 * Helper Functions
 * Common utility functions used throughout the application
 */

/**
 * Format date to readable format
 * @param string $date - Date string (Y-m-d)
 * @param string $format - Output format
 * @return string - Formatted date
 */
function format_date($date, $format = 'M d, Y') {
    if (!$date) return '-';
    return date($format, strtotime($date));
}

/**
 * Format time to readable format
 * @param string $time - Time string (H:i:s)
 * @param string $format - Output format
 * @return string - Formatted time
 */
function format_time($time, $format = 'h:i A') {
    if (!$time) return '-';
    return date($format, strtotime($time));
}

/**
 * Format datetime
 * @param string $datetime - DateTime string
 * @param string $format - Output format
 * @return string - Formatted datetime
 */
function format_datetime($datetime, $format = 'M d, Y h:i A') {
    if (!$datetime) return '-';
    return date($format, strtotime($datetime));
}

/**
 * Get status badge HTML
 * @param string $status - Status value
 * @param string $type - Badge type (attendance, leave, worker)
 * @return string - HTML badge
 */
function get_status_badge($status, $type = 'attendance') {
    $badges = [
        'attendance' => [
            'present' => ['class' => 'success', 'icon' => 'fa-check', 'label' => 'Present'],
            'late' => ['class' => 'warning', 'icon' => 'fa-clock', 'label' => 'Late'],
            'absent' => ['class' => 'danger', 'icon' => 'fa-times', 'label' => 'Absent'],
            'half-day' => ['class' => 'info', 'icon' => 'fa-hourglass-half', 'label' => 'Half Day'],
        ],
        'leave' => [
            'pending' => ['class' => 'warning', 'icon' => 'fa-hourglass-half', 'label' => 'Pending'],
            'approved' => ['class' => 'success', 'icon' => 'fa-check', 'label' => 'Approved'],
            'rejected' => ['class' => 'danger', 'icon' => 'fa-times', 'label' => 'Rejected'],
        ],
        'worker' => [
            'active' => ['class' => 'success', 'icon' => 'fa-check-circle', 'label' => 'Active'],
            'inactive' => ['class' => 'secondary', 'icon' => 'fa-times-circle', 'label' => 'Inactive'],
            'vacation' => ['class' => 'info', 'icon' => 'fa-umbrella-beach', 'label' => 'On Vacation'],
            'sick' => ['class' => 'warning', 'icon' => 'fa-heartbeat', 'label' => 'Sick Leave'],
        ],
    ];
    
    if (!isset($badges[$type][$status])) {
        return '<span class="badge bg-secondary">Unknown</span>';
    }
    
    $badge = $badges[$type][$status];
    return '<span class="badge bg-' . $badge['class'] . '">
                <i class="fas ' . $badge['icon'] . '"></i> ' . $badge['label'] . '
            </span>';
}

/**
 * Calculate hours between two times
 * @param string $check_in
continue

 * @param string $check_out
 * @return float - Hours worked
 */
function calculate_hours($check_in, $check_out) {
    if (!$check_in || !$check_out) return 0;
    
    $in_time = strtotime($check_in);
    $out_time = strtotime($check_out);
    
    $seconds = $out_time - $in_time;
    return round($seconds / 3600, 2);
}

/**
 * Check if time is late
 * @param string $check_in - Check-in time
 * @param string $work_start - Work start time (default: 09:00)
 * @return bool - True if late
 */
function is_late($check_in, $work_start = '09:00:00') {
    if (!$check_in) return false;
    
    $check_in_time = strtotime($check_in);
    $start_time = strtotime($work_start);
    
    return $check_in_time > $start_time;
}

/**
 * Get attendance status
 * @param string $check_in - Check-in time
 * @param string $check_out - Check-out time
 * @return string - Status (present, late, absent, half-day)
 */
function get_attendance_status($check_in, $check_out = null) {
    if (!$check_in) {
        return 'absent';
    }
    
    if (is_late($check_in)) {
        return 'late';
    }
    
    if (!$check_out) {
        return 'half-day';
    }
    
    return 'present';
}

/**
 * Calculate leave days
 * @param string $start_date - Start date
 * @param string $end_date - End date
 * @return int - Number of days
 */
function calculate_leave_days($start_date, $end_date) {
    $start = new DateTime($start_date);
    $end = new DateTime($end_date);
    $interval = $start->diff($end);
    return $interval->days + 1;
}

/**
 * Format currency
 * @param float $amount - Amount to format
 * @param string $currency - Currency symbol
 * @return string - Formatted currency
 */
function format_currency($amount, $currency = '$') {
    return $currency . number_format($amount, 2);
}

/**
 * Format percentage
 * @param float $value - Value
 * @param int $decimals - Decimal places
 * @return string - Formatted percentage
 */
function format_percentage($value, $decimals = 1) {
    return number_format($value, $decimals) . '%';
}

/**
 * Get initials from name
 * @param string $first_name - First name
 * @param string $last_name - Last name
 * @return string - Initials
 */
function get_initials($first_name, $last_name = '') {
    $initials = strtoupper(substr($first_name, 0, 1));
    if ($last_name) {
        $initials .= strtoupper(substr($last_name, 0, 1));
    }
    return $initials;
}

/**
 * Get full name
 * @param string $first_name - First name
 * @param string $last_name - Last name
 * @return string - Full name
 */
function get_full_name($first_name, $last_name = '') {
    return trim($first_name . ' ' . $last_name);
}

/**
 * Truncate text
 * @param string $text - Text to truncate
 * @param int $length - Max length
 * @param string $suffix - Suffix (default: ...)
 * @return string - Truncated text
 */
function truncate_text($text, $length = 50, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . $suffix;
}

/**
 * Get file size in human readable format
 * @param int $bytes - File size in bytes
 * @return string - Human readable size
 */
function format_file_size($bytes) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    
    return round($bytes, 2) . ' ' . $units[$pow];
}

/**
 * Generate random string
 * @param int $length - String length
 * @return string - Random string
 */
function generate_random_string($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $string = '';
    
    for ($i = 0; $i < $length; $i++) {
        $string .= $characters[rand(0, strlen($characters) - 1)];
    }
    
    return $string;
}

/**
 * Send email
 * @param string $to - Recipient email
 * @param string $subject - Email subject
 * @param string $message - Email message
 * @param array $headers - Additional headers
 * @return bool - Success or failure
 */
function send_email($to, $subject, $message, $headers = []) {
    $default_headers = [
        'From' => 'noreply@laberion.com',
        'Content-Type' => 'text/html; charset=UTF-8',
    ];
    
    $headers = array_merge($default_headers, $headers);
    $header_string = '';
    
    foreach ($headers as $key => $value) {
        $header_string .= $key . ': ' . $value . "\r\n";
    }
    
    return mail($to, $subject, $message, $header_string);
}

/**
 * Validate email
 * @param string $email - Email to validate
 * @return bool - True if valid
 */
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate phone number
 * @param string $phone - Phone number to validate
 * @return bool - True if valid
 */
function validate_phone($phone) {
    return preg_match('/^[0-9\s\-\+\(\)]+$/', $phone) && strlen(preg_replace('/\D/', '', $phone)) >= 10;
}

/**
 * Get age from date of birth
 * @param string $dob - Date of birth (Y-m-d)
 * @return int - Age
 */
function get_age($dob) {
    $today = new DateTime();
    $birthdate = new DateTime($dob);
    $age = $today->diff($birthdate)->y;
    return $age;
}

/**
 * Get day name
 * @param string $date - Date (Y-m-d)
 * @return string - Day name
 */
function get_day_name($date) {
    return date('l', strtotime($date));
}

/**
 * Get month name
 * @param int $month - Month number (1-12)
 * @return string - Month name
 */
function get_month_name($month) {
    $months = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ];
    return $months[$month - 1] ?? '';
}

/**
 * Check if date is weekend
 * @param string $date - Date (Y-m-d)
 * @return bool - True if weekend
 */
function is_weekend($date) {
    $day = date('w', strtotime($date));
    return $day == 0 || $day == 6;
}

/**
 * Get working days between two dates
 * @param string $start_date - Start date
 * @param string $end_date - End date
 * @return int - Number of working days
 */
function get_working_days($start_date, $end_date) {
    $start = new DateTime($start_date);
    $end = new DateTime($end_date);
    $working_days = 0;
    
    while ($start <= $end) {
        if (!is_weekend($start->format('Y-m-d'))) {
            $working_days++;
        }
        $start->modify('+1 day');
    }
    
    return $working_days;
}

/**
 * Get attendance percentage
 * @param int $present_days - Days present
 * @param int $total_days - Total working days
 * @return float - Attendance percentage
 */
function get_attendance_percentage($present_days, $total_days) {
    if ($total_days == 0) return 0;
    return round(($present_days / $total_days) * 100, 2);
}

/**
 * Get color based on percentage
 * @param float $percentage - Percentage value
 * @return string - Color class
 */
function get_percentage_color($percentage) {
    if ($percentage >= 90) return 'success';
    if ($percentage >= 75) return 'warning';
    return 'danger';
}

/**
 * Log error
 * @param string $message - Error message
 * @param string $file - File name
 * @param int $line - Line number
 */
function log_error($message, $file = '', $line = 0) {
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[$timestamp] Error: $message";
    
    if ($file) {
        $log_entry .= " | File: $file";
    }
    if ($line) {
        $log_entry .= " | Line: $line";
    }
    
    $log_entry .= "\n";
    
    $log_file = '../logs/error.log';
    
    if (!file_exists(dirname($log_file))) {
        mkdir(dirname($log_file), 0755, true);
    }
    
    file_put_contents($log_file, $log_entry, FILE_APPEND);
}

/**
 * Redirect with message
 * @param string $url - Redirect URL
 * @param string $message - Message to display
 * @param string $type - Message type (success, error, warning, info)
 */
function redirect_with_message($url, $message, $type = 'info') {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
    header("Location: $url");
    exit();
}

/**
 * Display message
 * @return string - HTML message
 */
function display_message() {
    if (!isset($_SESSION['message'])) {
        return '';
    }
    
    $message = $_SESSION['message'];
    $type = $_SESSION['message_type'] ?? 'info';
    
    $html = '<div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">';
    $html .= $message;
    $html .= '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
    $html .= '</div>';
    
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
    
    return $html;
}

/**
 * Get system settings
 * @param string $key - Setting key
 * @param mixed $default - Default value
 * @return mixed - Setting value
 */
function get_setting($key, $default = null) {
    $settings = [
        'app_name' => 'Laberion WMS',
        'app_version' => '1.0.0',
        'work_start_time' => '09:00:00',
        'work_end_time' => '17:00:00',
        'late_threshold' => 15, // minutes
        'timezone' => 'Europe/Zurich',
        'date_format' => 'Y-m-d',
        'time_format' => 'H:i:s',
    ];
    
    return $settings[$key] ?? $default;
}

/**
 * Get system info
 * @return array - System information
 */
function get_system_info() {
    return [
        'php_version' => phpversion(),
        'mysql_version' => mysqli_get_server_info($GLOBALS['conn']),
        'server_os' => php_uname(),
        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
        'memory_limit' => ini_get('memory_limit'),
        'max_upload_size' => ini_get('upload_max_filesize'),
    ];
}
?>