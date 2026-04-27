<?php
$page_title = "Dashboard - Laberion WMS";
include '../includes/header.php';

// Get current date
$today = date('Y-m-d');

// Statistics Queries
$total_workers = mysqli_fetch_assoc(db_query("SELECT COUNT(*) as count FROM workers WHERE status='active'"))['count'];
$present_today = mysqli_fetch_assoc(db_query("SELECT COUNT(DISTINCT worker_id) as count FROM attendance WHERE date = '$today'"))['count'];
$absent_today = $total_workers - $present_today;
$on_vacation = mysqli_fetch_assoc(db_query("SELECT COUNT(DISTINCT worker_id) as count FROM leaves WHERE '$today' BETWEEN start_date AND end_date AND status='approved' AND type='vacation'"))['count'];
$on_sick = mysqli_fetch_assoc(db_query("SELECT COUNT(DISTINCT worker_id) as count FROM leaves WHERE '$today' BETWEEN start_date AND end_date AND status='approved' AND type='sick'"))['count'];
$late_today = mysqli_fetch_assoc(db_query("SELECT COUNT(*) as count FROM attendance WHERE date = '$today' AND status='late'"))['count'];

// Recent check-ins (last 10)
$recent_checkins = db_query("
    SELECT w.id, w.name, w.lastname, w.photo, w.position, a.check_in, a.check_out, a.status, a.date 
    FROM attendance a 
    JOIN workers w ON a.worker_id = w.id 
    WHERE a.date = '$today'
    ORDER BY a.check_in DESC 
    LIMIT 10
");

// Pending leave requests
$pending_leaves = db_query("
    SELECT l.*, w.name, w.lastname, w.photo 
    FROM leaves l 
    JOIN workers w ON l.worker_id = w.id 
    WHERE l.status = 'pending' 
    ORDER BY l.created_at DESC 
    LIMIT 5
");
?>

<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1><i class="fas fa-home"></i> Dashboard</h1>
                    <p class="text-muted mb-0">Welcome back, <?php echo $_SESSION['admin_username']; ?>!</p>
                </div>
                <div>
                    <span class="badge bg-primary fs-6">
                        <i class="fas fa-calendar"></i> <?php echo date('l, F d, Y'); ?>
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Statistics Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="stat-card primary">
                    <div class="icon"><i class="fas fa-users"></i></div>
                    <h3><?php echo $total_workers; ?></h3>
                    <p>Total Workers</p>
                    <a href="workers.php" class="card-link">View All <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card success">
                    <div class="icon"><i class="fas fa-check-circle"></i></div>
                    <h3><?php echo $present_today; ?></h3>
                    <p>Present Today</p>
                    <small class="text-muted"><?php echo round(($present_today/$total_workers)*100, 1); ?>% attendance</small>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card danger">
                    <div class="icon"><i class="fas fa-times-circle"></i></div>
                    <h3><?php echo $absent_today; ?></h3>
                    <p>Absent Today</p>
                    <small class="text-muted"><?php echo $on_sick; ?> sick, <?php echo $on_vacation; ?> vacation</small>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card warning">
                    <div class="icon"><i class="fas fa-clock"></i></div>
                    <h3><?php echo $late_today; ?></h3>
                    <p>Late Arrivals</p>
                    <a href="attendance.php?filter=late" class="card-link">View Details <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
        
        <div class="row g-4">
            <!-- Recent Check-ins -->
            <div class="col-lg-8">
                <div class="table-container">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-0"><i class="fas fa-clock"></i> Recent Check-ins</h4>
                        <a href="attendance.php" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Worker</th>
                                    <th>Position</th>
                                    <th>Check In</th>
                                    <th>Check Out</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($recent_checkins) > 0): ?>
                                    <?php while ($row = mysqli_fetch_assoc($recent_checkins)): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="../assets/uploads/workers/<?php echo $row['photo'] ?: 'default-avatar.png'; ?>" 
                                                     alt="<?php echo $row['name']; ?>" 
                                                     class="rounded-circle me-2" 
                                                     width="40" height="40">
                                                <div>
                                                    <strong><?php echo $row['name'] . ' ' . $row['lastname']; ?></strong>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo $row['position']; ?></td>
                                        <td>
                                            <i class="fas fa-sign-in-alt text-success"></i>
                                            <?php echo date('h:i A', strtotime($row['check_in'])); ?>
                                        </td>
                                        <td>
                                            <?php if ($row['check_out']): ?>
                                                <i class="fas fa-sign-out-alt text-danger"></i>
                                                <?php echo date('h:i A', strtotime($row['check_out'])); ?>
                                            <?php else: ?>
                                                <span class="text-muted">Not yet</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            $badge_class = 'success';
                                            if ($row['status'] == 'late') $badge_class = 'warning';
                                            if ($row['status'] == 'absent') $badge_class = 'danger';
                                            ?>
                                            <span class="badge bg-<?php echo $badge_class; ?>">
                                                <?php echo ucfirst($row['status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No check-ins today</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Pending Leave Requests -->
            <div class="col-lg-4">
                <div class="table-container">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-0"><i class="fas fa-umbrella-beach"></i> Pending Leaves</h4>
                        <a href="leaves.php" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    
                    <?php if (mysqli_num_rows($pending_leaves) > 0): ?>
                        <div class="list-group">
                            <?php while ($leave = mysqli_fetch_assoc($pending_leaves)): ?>
                            <div class="list-group-item">
                                <div class="d-flex align-items-center mb-2">
                                    <img src="../assets/uploads/workers/<?php echo $leave['photo'] ?: 'default-avatar.png'; ?>" 
                                         alt="<?php echo $leave['name']; ?>" 
                                         class="rounded-circle me-2" 
                                         width="35" height="35">
                                    <div class="flex-grow-1">
                                        <strong><?php echo $leave['name'] . ' ' . $leave['lastname']; ?></strong>
                                        <br>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar"></i>
                                            <?php echo date('M d', strtotime($leave['start_date'])); ?> - 
                                            <?php echo date('M d', strtotime($leave['end_date'])); ?>
                                        </small>
                                    </div>
                                    <span class="badge bg-info"><?php echo ucfirst($leave['type']); ?></span>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="leave-approve.php?id=<?php echo $leave['id']; ?>" 
                                       class="btn btn-sm btn-success flex-grow-1">
                                        <i class="fas fa-check"></i> Approve
                                    </a>
                                    <a href="leave-reject.php?id=<?php echo $leave['id']; ?>" 
                                       class="btn btn-sm btn-danger flex-grow-1">
                                        <i class="fas fa-times"></i> Reject
                                    </a>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No pending leave requests
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="row g-4 mt-2">
            <div class="col-md-12">
                <div class="table-container">
                    <h4 class="mb-3"><i class="fas fa-bolt"></i> Quick Actions</h4>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="add-worker.php" class="btn btn-primary w-100 py-3">
                                <i class="fas fa-user-plus fa-2x d-block mb-2"></i>
                                Add New Worker
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="attendance.php" class="btn btn-success w-100 py-3">
                                <i class="fas fa-clipboard-check fa-2x d-block mb-2"></i>
                                View Attendance
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="reports.php" class="btn btn-info w-100 py-3">
                                <i class="fas fa-chart-bar fa-2x d-block mb-2"></i>
                                Generate Report
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="leaves.php" class="btn btn-warning w-100 py-3">
                                <i class="fas fa-calendar-alt fa-2x d-block mb-2"></i>
                                Manage Leaves
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>