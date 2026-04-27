<?php
$page_title = "Attendance Details - Laberion WMS";
include '../includes/header.php';

$worker_id = isset($_GET['worker_id']) ? (int)$_GET['worker_id'] : 0;
$month = isset($_GET['month']) ? clean_input($_GET['month']) : date('Y-m');

// Get worker details
$worker_query = db_query("SELECT * FROM workers WHERE id = $worker_id");
if (mysqli_num_rows($worker_query) == 0) {
    header("Location: attendance.php");
    exit();
}
$worker = mysqli_fetch_assoc($worker_query);

// Get attendance for the month
$attendance = db_query("
    SELECT * FROM attendance 
    WHERE worker_id = $worker_id 
    AND DATE_FORMAT(date, '%Y-%m') = '$month'
    ORDER BY date DESC
");

// Calculate statistics
$total_days = mysqli_num_rows($attendance);
$present_days = mysqli_fetch_assoc(db_query("
    SELECT COUNT(*) as count FROM attendance 
    WHERE worker_id = $worker_id 
    AND DATE_FORMAT(date, '%Y-%m') = '$month'
    AND status != 'absent'
"))['count'];
$late_days = mysqli_fetch_assoc(db_query("
    SELECT COUNT(*) as count FROM attendance 
    WHERE worker_id = $worker_id 
    AND DATE_FORMAT(date, '%Y-%m') = '$month'
    AND status = 'late'
"))['count'];
$total_hours = mysqli_fetch_assoc(db_query("
    SELECT SUM(hours) as total FROM attendance 
    WHERE worker_id = $worker_id 
    AND DATE_FORMAT(date, '%Y-%m') = '$month'
"))['total'] ?: 0;
?>

<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <h1><i class="fas fa-calendar-check"></i> Attendance Details</h1>
                <a href="attendance.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Attendance
                </a>
            </div>
        </div>
        
        <!-- Worker Info -->
        <div class="row g-4 mb-4">
            <div class="col-lg-3">
                <div class="table-container text-center">
                    <img src="../assets/uploads/workers/<?php echo $worker['photo'] ?: 'default-avatar.png'; ?>" 
                         alt="<?php echo $worker['name']; ?>" 
                         class="rounded-circle mb-3" 
                         width="120" height="120">
                    <h4><?php echo $worker['name'] . ' ' . $worker['lastname']; ?></h4>
                    <p class="text-muted"><?php echo $worker['position']; ?></p>
                </div>
            </div>
            
            <div class="col-lg-9">
                <!-- Statistics -->
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="stat-card primary">
                            <div class="icon"><i class="fas fa-calendar"></i></div>
                            <h3><?php echo $total_days; ?></h3>
                            <p>Total Days</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card success">
                            <div class="icon"><i class="fas fa-check"></i></div>
                            <h3><?php echo $present_days; ?></h3>
                            <p>Present</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card warning">
                            <div class="icon"><i class="fas fa-clock"></i></div>
                            <h3><?php echo $late_days; ?></h3>
                            <p>Late</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card info">
                            <div class="icon"><i class="fas fa-hourglass-half"></i></div>
                            <h3><?php echo round($total_hours, 1); ?></h3>
                            <p>Total Hours</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Month Filter -->
        <div class="table-container mb-4">
            <form method="GET" action="" class="row g-3">
                <input type="hidden" name="worker_id" value="<?php echo $worker_id; ?>">
                <div class="col-md-4">
                    <label class="form-label">Select Month</label>
                    <input type="month" name="month" class="form-control" 
                           value="<?php echo $month; ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Attendance Table -->
        <div class="table-container">
            <h5 class="mb-3">
                <i class="fas fa-list"></i> Attendance for <?php echo date('F Y', strtotime($month . '-01')); ?>
            </h5>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Day</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Hours</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($attendance) > 0): ?>
                            <?php while ($att = mysqli_fetch_assoc($attendance)): ?>
                            <tr>
                                <td><?php echo date('M d, Y', strtotime($att['date'])); ?></td>
                                <td><?php echo date('l', strtotime($att['date'])); ?></td>
                                <td>
                                    <?php if ($att['check_in']): ?>
                                        <i class="fas fa-sign-in-alt text-success"></i>
                                        <?php echo date('h:i A', strtotime($att['check_in'])); ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($att['check_out']): ?>
                                        <i class="fas fa-sign-out-alt text-danger"></i>
                                        <?php echo date('h:i A', strtotime($att['check_out'])); ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $att['hours'] ? $att['hours'] . 'h' : '-'; ?></td>
                                <td>
                                    <?php
                                    $badge = 'success';
                                    if ($att['status'] == 'late') $badge = 'warning';
                                    if ($att['status'] == 'absent') $badge = 'danger';
                                    if ($att['status'] == 'half-day') $badge = 'info';
                                    ?>
                                    <span class="badge bg-<?php echo $badge; ?>">
                                        <?php echo ucfirst($att['status']); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    No attendance records for this month
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