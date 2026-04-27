<?php
$page_title = "Reports - Laberion WMS";
include '../includes/header.php';

// Date range
$month = isset($_GET['month']) ? clean_input($_GET['month']) : date('Y-m');
$year = isset($_GET['year']) ? clean_input($_GET['year']) : date('Y');
$department_filter = isset($_GET['department']) ? clean_input($_GET['department']) : '';

// Get departments
$departments = db_query("SELECT DISTINCT department FROM workers WHERE department IS NOT NULL ORDER BY department");

// Build query
$where = "WHERE DATE_FORMAT(a.date, '%Y-%m') = '$month'";
if ($department_filter) {
    $where .= " AND w.department = '$department_filter'";
}

// Get report data
$report_data = db_query("
    SELECT 
        w.id,
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

// Summary statistics
$summary = mysqli_fetch_assoc(db_query("
    SELECT 
        COUNT(DISTINCT w.id) as total_workers,
        COUNT(DISTINCT CASE WHEN a.status != 'absent' THEN a.worker_id END) as workers_present,
        SUM(CASE WHEN a.status = 'late' THEN 1 ELSE 0 END) as total_late,
        SUM(a.hours) as total_hours
    FROM workers w
    LEFT JOIN attendance a ON w.id = a.worker_id $where
    WHERE w.status = 'active'
"));
?>

<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <h1><i class="fas fa-chart-bar"></i> Reports</h1>
                <a href="export-excel.php?type=report&month=<?php echo $month; ?>&department=<?php echo $department_filter; ?>" 
                   class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Export to Excel
                </a>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="table-container mb-4">
            <form method="GET" action="" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Month</label>
                    <input type="month" name="month" class="form-control" 
                           value="<?php echo $month; ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Department</label>
                    <select name="department" class="form-select">
                        <option value="">All Departments</option>
                        <?php while ($dept = mysqli_fetch_assoc($departments)): ?>
                            <option value="<?php echo $dept['department']; ?>" 
                                    <?php echo $department_filter == $dept['department'] ? 'selected' : ''; ?>>
                                <?php echo $dept['department']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i> Generate Report
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Summary Statistics -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="stat-card primary">
                    <div class="icon"><i class="fas fa-users"></i></div>
                    <h3><?php echo $summary['total_workers']; ?></h3>
                    <p>Total Workers</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card success">
                    <div class="icon"><i class="fas fa-check"></i></div>
                    <h3><?php echo $summary['workers_present']; ?></h3>
                    <p>Workers Present</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card warning">
                    <div class="icon"><i class="fas fa-clock"></i></div>
                    <h3><?php echo $summary['total_late']; ?></h3>
                    <p>Late Arrivals</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card info">
                    <div class="icon"><i class="fas fa-hourglass-half"></i></div>
                    <h3><?php echo round($summary['total_hours'], 1); ?></h3>
                    <p>Total Hours</p>
                </div>
            </div>
        </div>
        
        <!-- Report Table -->
        <div class="table-container">
            <h5 class="mb-3">
                <i class="fas fa-list"></i> Attendance Report - <?php echo date('F Y', strtotime($month . '-01')); ?>
            </h5>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Worker</th>
                            <th>Position</th>
                            <th>Department</th>
                            <th>Present Days</th>
                            <th>Late Days</th>
                            <th>Absent Days</th>
                            <th>Total Hours</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($report_data) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($report_data)): ?>
                            <tr>
                                <td><strong><?php echo $row['name'] . ' ' . $row['lastname']; ?></strong></td>
                                <td><?php echo $row['position']; ?></td>
                                <td><?php echo $row['department']; ?></td>
                                <td>
                                    <span class="badge bg-success">
                                        <?php echo $row['present_days']; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-warning">
                                        <?php echo $row['late_days']; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-danger">
                                        <?php echo $row['absent_days']; ?>
                                    </span>
                                </td>
                                <td><?php echo round($row['total_hours'], 1); ?>h</td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    No data available for this period
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>