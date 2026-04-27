<?php
include '../includes/db.php';
include '../includes/auth.php';
require_login();

// Check if PHPExcel is available, otherwise use simple CSV
$type = isset($_GET['type']) ? clean_input($_GET['type']) : 'workers';

if ($type == 'workers') {
    export_workers();
} elseif ($type == 'attendance') {
    export_attendance();
} elseif ($type == 'report') {
    export_report();
}

function export_workers() {
    global $conn;
    
    $filename = 'workers_' . date('Y-m-d') . '.csv';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename);
    
    $output = fopen('php://output', 'w');
    
    // Headers
    fputcsv($output, ['ID', 'Name', 'Last Name', 'Position', 'Department', 'Phone', 'Salary', 'Status', 'Start Date']);
    
    // Data
    $result = db_query("SELECT * FROM workers ORDER BY name");
    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, [
            $row['id'],
            $row['name'],
            $row['lastname'],
            $row['position'],
            $row['department'],
            $row['phone'],
            $row['salary'],
            $row['status'],
            $row['start_date']
        ]);
    }
    
    fclose($output);
    exit();
}

function export_attendance() {
    $date_from = isset($_GET['from']) ? clean_input($_GET['from']) : date('Y-m-01');
    $date_to = isset($_GET['to']) ? clean_input($_GET['to']) : date('Y-m-d');
    
    $filename = 'attendance_' . $date_from . '_to_' . $date_to . '.csv';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename);
    
    $output = fopen('php://output', 'w');
    
    // Headers
    fputcsv($output, ['Date', 'Worker', 'Position', 'Check In', 'Check Out', 'Hours', 'Status']);
    
    // Data
    $result = db_query("
        SELECT a.*, w.name, w.lastname, w.position 
        FROM attendance a 
        JOIN workers w ON a.worker_id = w.id 
        WHERE a.date BETWEEN '$date_from' AND '$date_to'
        ORDER BY a.date DESC
    ");
    
    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, [
            date('M d, Y', strtotime($row['date'])),
            $row['name'] . ' ' . $row['lastname'],
            $row['position'],
            $row['check_in'] ? date('h:i A', strtotime($row['check_in'])) : '-',
            $row['check_out'] ? date('h:i A', strtotime($row['check_out'])) : '-',
            $row['hours'] ?: '-',
            $row['status']
        ]);
    }
    
    fclose($output);
    exit();
}

function export_report() {
    $month = isset($_GET['month']) ? clean_input($_GET['month']) : date('Y-m');
    $department = isset($_GET['department']) ? clean_input($_GET['department']) : '';
    
    $filename = 'report_' . $month . '.csv';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename);
    
    $output = fopen('php://output', 'w');
    
    // Headers
    fputcsv($output, ['Worker', 'Position', 'Department', 'Present Days', 'Late Days', 'Absent Days', 'Total Hours']);
    
    // Build query
    $where = "WHERE DATE_FORMAT(a.date, '%Y-%m') = '$month'";
    if ($department) {
        $where .= " AND w.department = '$department'";
    }
    
    // Data
    $result = db_query("
        SELECT 
            w.name,
            w.lastname,
            w.position,
            w.department,
            COUNT(CASE WHEN a.status != 'absent' THEN 1 END) as present_days,
            COUNT(CASE WHEN a.status = 'late' THEN 1 END) as late_days,
            COUNT(CASE WHEN a.status = 'absent' THEN 1 END) as absent_days,
            SUM(a.hours) as total_hours
        FROM workers w
        LEFT JOIN attendance a ON w.id = a.worker_id $where
        WHERE w.status = 'active'
        GROUP BY w.id
        ORDER BY w.name
    ");
    
    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, [
            $row['name'] . ' ' . $row['lastname'],
            $row['position'],
            $row['department'],
            $row['present_days'],
            $row['late_days'],
            $row['absent_days'],
            round($row['total_hours'], 2)
        ]);
    }
    
    fclose($output);
    exit();
}
?>