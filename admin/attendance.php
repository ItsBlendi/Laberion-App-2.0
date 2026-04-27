<?php
$page_title = "Attendance - Laberion WMS";
include '../includes/header.php';

// Date filter
$date_from = isset($_GET['date_from']) ? clean_input($_GET['date_from']) : date('Y-m-01');
$date_to = isset($_GET['date_to']) ? clean_input($_GET['date_to']) : date('Y-m-d');
$worker_filter = isset($_GET['worker']) ? (int)$_GET['worker'] : 0;
$status_filter = isset($_GET['status']) ? clean_input($_GET['status']) : '';

// Build query
$where = "WHERE a.date BETWEEN '$date_from' AND '$date_to'";
if ($worker_filter > 0) {
    $where .= " AND a.worker_id = $worker_filter";
}
if ($status_filter) {
    $where .= " AND a.status = '$status_filter'";
}

// Get attendance records
$attendance = db_query("
    SELECT a.*, w.name, w.lastname, w.photo, w.position 
    FROM attendance a 
    JOIN workers w ON a.worker_id = w.id 
    $where 
    ORDER BY a.date DESC, a.check_in DESC
");

// Get all workers for filter
$workers = db_query("SELECT id, name, lastname FROM workers WHERE status='active' ORDER BY name");

// Statistics
$total_records = mysqli_num_rows($attendance);
$present_count = mysqli_fetch_assoc(db_query("SELECT COUNT(*) as count FROM attendance a $where AND a.status != 'absent'"))['count'];
$late_count = mysqli_fetch_assoc(db_query("SELECT COUNT(*) as count FROM attendance a $where AND a.status = 'late'"))['count'];
$total_hours = mysqli_fetch_assoc(db_query("SELECT SUM(hours) as total FROM attendance a $where"))['total'] ?: 0;
?>

<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <h1><i class="fas fa-clock"></i> Attendance Records</h1>
                <a href="export-excel.php?type=attendance&from=<?php echo $date_from; ?>&to=<?php echo $date_to; ?>" 
                   class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Export to Excel
                </a>
            </div>
        </div>
        
        <!-- Statistics -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="stat-card primary">
                    <div class="icon"><i class="fas fa-list"></i></div>
                    <h3><?php echo $total_records; ?></h3>
                    <p>Total Records</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card success">
                    <div class="icon"><i class="fas fa-check"></i></div>
                    <h3><?php echo $present_count; ?></h3>
                    <p>Present</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card warning">
                    <div class="icon"><i class="fas fa-clock"></i></div>
                    <h3><?php echo $late_count; ?></h3>
                    <p>Late Arrivals</p>
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
        
        <!-- Filters -->
        <div class="table-container mb-4">
            <form method="GET" action="" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">From Date</label>
                    <input type="date" name="date_from" class="form-control" 
                           value="<?php echo $date_from; ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">To Date</label>
                    <input type="date" name="date_to" class="form-control" 
                           value="<?php echo $date_to; ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Worker</label>
                    <select name="worker" class="form-select">
                        <option value="0">All Workers</option>
                        <?php while ($w = mysqli_fetch_assoc($workers)): ?>
                            <option value="<?php echo $w['id']; ?>" 
                                    <?php echo $worker_filter == $w['id'] ? 'selected' : ''; ?>>
                                <?php echo $w['name'] . ' ' . $w['lastname']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="present" <?php echo $status_filter == 'present' ? 'selected' : ''; ?>>Present</option>
                        <option value="late" <?php echo $status_filter == 'late' ? 'selected' : ''; ?>>Late</option>
                        <option value="absent" <?php echo $status_filter == 'absent' ? 'selected' : ''; ?>>Absent</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i>
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Attendance Table -->
        <div class="table-container">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Worker</th>
                            <th>Position</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Hours</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($attendance) > 0): ?>

                            <?php while ($att = mysqli_fetch_assoc($attendance)): ?>
                            <tr>
                                <td><?php echo date('M d, Y', strtotime($att['date'])); ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="../assets/uploads/workers/<?php echo $att['photo'] ?: 'default-avatar.png'; ?>" 
                                             alt="<?php echo $att['name']; ?>" 
                                             class="rounded-circle me-2" 
                                             width="40" height="40">
                                        <div>
                                            <strong><?php echo $att['name'] . ' ' . $att['lastname']; ?></strong>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo $att['position']; ?></td>
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
                                <td>
                                    <a href="attendance-details.php?worker_id=<?php echo $att['worker_id']; ?>" 
                                       class="btn btn-sm btn-info" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                    No attendance records found
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