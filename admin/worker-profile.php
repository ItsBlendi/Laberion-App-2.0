<?php
$page_title = "Worker Profile - Laberion WMS";
include '../includes/header.php';

// Get worker ID
$worker_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get worker details
$worker_query = db_query("SELECT * FROM workers WHERE id = $worker_id");
if (mysqli_num_rows($worker_query) == 0) {
    header("Location: workers.php");
    exit();
}
$worker = mysqli_fetch_assoc($worker_query);

// Get attendance statistics
$total_days = mysqli_fetch_assoc(db_query("SELECT COUNT(*) as count FROM attendance WHERE worker_id = $worker_id"))['count'];
$present_days = mysqli_fetch_assoc(db_query("SELECT COUNT(*) as count FROM attendance WHERE worker_id = $worker_id AND status != 'absent'"))['count'];
$late_days = mysqli_fetch_assoc(db_query("SELECT COUNT(*) as count FROM attendance WHERE worker_id = $worker_id AND status = 'late'"))['count'];
$total_hours = mysqli_fetch_assoc(db_query("SELECT SUM(hours) as total FROM attendance WHERE worker_id = $worker_id"))['total'] ?: 0;

// Get recent attendance (last 30 days)
$recent_attendance = db_query("
    SELECT * FROM attendance 
    WHERE worker_id = $worker_id 
    ORDER BY date DESC 
    LIMIT 30
");

// Get leave history
$leave_history = db_query("
    SELECT * FROM leaves 
    WHERE worker_id = $worker_id 
    ORDER BY created_at DESC 
    LIMIT 10
");
?>

<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <h1><i class="fas fa-user"></i> Worker Profile</h1>
                <div>
                    <a href="edit-worker.php?id=<?php echo $worker_id; ?>" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit Profile
                    </a>
                    <a href="workers.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Workers
                    </a>
                </div>
            </div>
        </div>
        
        <div class="row g-4">
            <!-- Worker Info Card -->
            <div class="col-lg-4">
                <div class="table-container text-center">
                    <img src="../assets/uploads/workers/<?php echo $worker['photo'] ?: 'default-avatar.png'; ?>" 
                         alt="<?php echo $worker['name']; ?>" 
                         class="rounded-circle mb-3" 
                         width="150" height="150">
                    
                    <h3><?php echo $worker['name'] . ' ' . $worker['lastname']; ?></h3>
                    <p class="text-muted"><?php echo $worker['position']; ?></p>
                    
                    <?php
                    $badge_class = 'success';
                    if ($worker['status'] == 'inactive') $badge_class = 'secondary';
                    if ($worker['status'] == 'vacation') $badge_class = 'info';
                    if ($worker['status'] == 'sick') $badge_class = 'warning';
                    ?>
                    <span class="badge bg-<?php echo $badge_class; ?> fs-6 mb-3">
                        <?php echo ucfirst($worker['status']); ?>
                    </span>
                    
                    <hr>
                    
                    <div class="text-start">
                        <p><strong><i class="fas fa-building"></i> Department:</strong><br><?php echo $worker['department']; ?></p>
                        <p><strong><i class="fas fa-phone"></i> Phone:</strong><br><?php echo $worker['phone']; ?></p>
                        <p><strong><i class="fas fa-calendar"></i> Start Date:</strong><br><?php echo date('F d, Y', strtotime($worker['start_date'])); ?></p>
                        <?php if ($worker['salary']): ?>
                        <p><strong><i class="fas fa-dollar-sign"></i> Salary:</strong><br>$<?php echo number_format($worker['salary'], 2); ?></p>
                        <?php endif; ?>
                        <p><strong><i class="fas fa-fingerprint"></i> Face ID:</strong><br>
                            <?php echo $worker['face_data'] ? '<span class="text-success">Registered</span>' : '<span class="text-danger">Not Registered</span>'; ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Statistics and Details -->
            <div class="col-lg-8">
                <!-- Statistics Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="stat-card primary">
                            <div class="icon"><i class="fas fa-calendar-check"></i></div>
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
                
                <!-- Recent Attendance -->
                <div class="table-container mb-4">
                    <h5 class="mb-3"><i class="fas fa-history"></i> Recent Attendance (Last 30 Days)</h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Check In</th>
                                    <th>Check Out</th>
                                    <th>Hours</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($recent_attendance) > 0): ?>
                                    <?php while ($att = mysqli_fetch_assoc($recent_attendance)): ?>
                                    <tr>
                                        <td><?php echo date('M d, Y', strtotime($att['date'])); ?></td>
                                        <td><?php echo $att['check_in'] ? date('h:i A', strtotime($att['check_in'])) : '-'; ?></td>
                                        <td><?php echo $att['check_out'] ? date('h:i A', strtotime($att['check_out'])) : '-'; ?></td>
                                        <td><?php echo $att['hours'] ? $att['hours'] . 'h' : '-'; ?></td>
                                        <td>
                                            <?php
                                            $badge = 'success';
                                            if ($att['status'] == 'late') $badge = 'warning';
                                            if ($att['status'] == 'absent') $badge = 'danger';
                                            ?>
                                            <span class="badge bg-<?php echo $badge; ?>">
                                                <?php echo ucfirst($att['status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No attendance records</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Leave History -->
                <div class="table-container">
                    <h5 class="mb-3"><i class="fas fa-umbrella-beach"></i> Leave History</h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Days</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($leave_history) > 0): ?>
                                    <?php while ($leave = mysqli_fetch_assoc($leave_history)): ?>
                                    <?php
                                    $start = new DateTime($leave['start_date']);
                                    $end = new DateTime($leave['end_date']);
                                    $days = $start->diff($end)->days + 1;
                                    ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-info">
                                                <?php echo ucfirst($leave['type']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($leave['start_date'])); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($leave['end_date'])); ?></td>
                                        <td><?php echo $days; ?> day(s)</td>
                                        <td>
                                            <?php
                                            $badge = 'warning';
                                            if ($leave['status'] == 'approved') $badge = 'success';
                                            if ($leave['status'] == 'rejected') $badge = 'danger';
                                            ?>
                                            <span class="badge bg-<?php echo $badge; ?>">
                                                <?php echo ucfirst($leave['status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No leave records</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>