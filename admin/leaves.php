<?php
$page_title = "Leaves Management - Laberion WMS";
include '../includes/header.php';

// Filters
$status_filter = isset($_GET['status']) ? clean_input($_GET['status']) : '';
$type_filter = isset($_GET['type']) ? clean_input($_GET['type']) : '';
$worker_filter = isset($_GET['worker']) ? (int)$_GET['worker'] : 0;

// Build query
$where = "WHERE 1=1";
if ($status_filter) {
    $where .= " AND l.status = '$status_filter'";
}
if ($type_filter) {
    $where .= " AND l.type = '$type_filter'";
}
if ($worker_filter > 0) {
    $where .= " AND l.worker_id = $worker_filter";
}

// Get leaves
$leaves = db_query("
    SELECT l.*, w.name, w.lastname, w.photo, w.position, w.department 
    FROM leaves l 
    JOIN workers w ON l.worker_id = w.id 
    $where 
    ORDER BY l.created_at DESC
");

// Get workers for filter
$workers = db_query("SELECT id, name, lastname FROM workers WHERE status='active' ORDER BY name");

// Statistics
$pending_count = mysqli_fetch_assoc(db_query("SELECT COUNT(*) as count FROM leaves WHERE status='pending'"))['count'];
$approved_count = mysqli_fetch_assoc(db_query("SELECT COUNT(*) as count FROM leaves WHERE status='approved'"))['count'];
$rejected_count = mysqli_fetch_assoc(db_query("SELECT COUNT(*) as count FROM leaves WHERE status='rejected'"))['count'];
?>

<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <h1><i class="fas fa-umbrella-beach"></i> Leave Management</h1>
        </div>
        
        <!-- Statistics -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="stat-card warning">
                    <div class="icon"><i class="fas fa-hourglass-half"></i></div>
                    <h3><?php echo $pending_count; ?></h3>
                    <p>Pending Requests</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card success">
                    <div class="icon"><i class="fas fa-check-circle"></i></div>
                    <h3><?php echo $approved_count; ?></h3>
                    <p>Approved</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card danger">
                    <div class="icon"><i class="fas fa-times-circle"></i></div>
                    <h3><?php echo $rejected_count; ?></h3>
                    <p>Rejected</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card info">
                    <div class="icon"><i class="fas fa-list"></i></div>
                    <h3><?php echo $pending_count + $approved_count + $rejected_count; ?></h3>
                    <p>Total Requests</p>
                </div>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="table-container mb-4">
            <form method="GET" action="" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="pending" <?php echo $status_filter == 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="approved" <?php echo $status_filter == 'approved' ? 'selected' : ''; ?>>Approved</option>
                        <option value="rejected" <?php echo $status_filter == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select">
                        <option value="">All Types</option>
                        <option value="vacation" <?php echo $type_filter == 'vacation' ? 'selected' : ''; ?>>Vacation</option>
                        <option value="sick" <?php echo $type_filter == 'sick' ? 'selected' : ''; ?>>Sick Leave</option>
                        <option value="personal" <?php echo $type_filter == 'personal' ? 'selected' : ''; ?>>Personal</option>
                    </select>
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
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Leaves Table -->
        <div class="table-container">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Worker</th>
                            <th>Type</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Days</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($leaves) > 0): ?>
                            <?php while ($leave = mysqli_fetch_assoc($leaves)): ?>
                            <?php
                            $start = new DateTime($leave['start_date']);
                            $end = new DateTime($leave['end_date']);
                            $days = $start->diff($end)->days + 1;
                            ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="../assets/uploads/workers/<?php echo $leave['photo'] ?: 'default-avatar.png'; ?>" 
                                             alt="<?php echo $leave['name']; ?>" 
                                             class="rounded-circle me-2" 
                                             width="40" height="40">
                                        <div>
                                            <strong><?php echo $leave['name'] . ' ' . $leave['lastname']; ?></strong>
                                            <br>
                                            <small class="text-muted"><?php echo $leave['position']; ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        <?php echo ucfirst($leave['type']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($leave['start_date'])); ?></td>
                                <td><?php echo date('M d, Y', strtotime($leave['end_date'])); ?></td>
                                <td><strong><?php echo $days; ?></strong> day(s)</td>
                                <td>
                                    <small><?php echo substr($leave['reason'], 0, 30) . (strlen($leave['reason']) > 30 ? '...' : ''); ?></small>
                                </td>
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
                                <td>
                                    <?php if ($leave['status'] == 'pending'): ?>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="leave-approve.php?id=<?php echo $leave['id']; ?>" 
                                               class="btn btn-success" title="Approve">
                                                <i class="fas fa-check"></i>
                                            </a>
                                            <a href="leave-reject.php?id=<?php echo $leave['id']; ?>" 
                                               class="btn btn-danger" title="Reject">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                    No leave requests found
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