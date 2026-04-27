<?php
$page_title = "Workers - Laberion WMS";
include '../includes/header.php';

// Pagination
$per_page = 15;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $per_page;

// Search and Filter
$search = isset($_GET['search']) ? clean_input($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? clean_input($_GET['status']) : '';
$department_filter = isset($_GET['department']) ? clean_input($_GET['department']) : '';

// Build query
$where = "WHERE 1=1";
if ($search) {
    $where .= " AND (name LIKE '%$search%' OR lastname LIKE '%$search%' OR position LIKE '%$search%')";
}
if ($status_filter) {
    $where .= " AND status = '$status_filter'";
}
if ($department_filter) {
    $where .= " AND department = '$department_filter'";
}

// Get total count
$total_query = db_query("SELECT COUNT(*) as count FROM workers $where");
$total_workers = mysqli_fetch_assoc($total_query)['count'];
$total_pages = ceil($total_workers / $per_page);

// Get workers
$workers = db_query("
    SELECT * FROM workers 
    $where 
    ORDER BY created_at DESC 
    LIMIT $per_page OFFSET $offset
");

// Get departments for filter
$departments = db_query("SELECT DISTINCT department FROM workers WHERE department IS NOT NULL ORDER BY department");
?>

<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <h1><i class="fas fa-users"></i> Workers Management</h1>
                <a href="add-worker.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Worker
                </a>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="table-container mb-4">
            <form method="GET" action="" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" 
                           placeholder="Search by name or position..." 
                           value="<?php echo $search; ?>">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" <?php echo $status_filter == 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo $status_filter == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        <option value="vacation" <?php echo $status_filter == 'vacation' ? 'selected' : ''; ?>>On Vacation</option>
                        <option value="sick" <?php echo $status_filter == 'sick' ? 'selected' : ''; ?>>Sick Leave</option>
                    </select>
                </div>
                <div class="col-md-3">
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
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Filter
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Workers Table -->
        <div class="table-container">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Total Workers: <?php echo $total_workers; ?></h5>
                <div>
                    <a href="export-excel.php?type=workers" class="btn btn-sm btn-success">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>Position</th>
                            <th>Department</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($workers) > 0): ?>
                            <?php while ($worker = mysqli_fetch_assoc($workers)): ?>
                            <tr>
                                <td><?php echo $worker['id']; ?></td>
                                <td>
                                    <img src="../assets/uploads/workers/<?php echo $worker['photo'] ?: 'default-avatar.png'; ?>" 
                                         alt="<?php echo $worker['name']; ?>" 
                                         class="rounded-circle" 
                                         width="50" height="50">
                                </td>
                                <td>
                                    <strong><?php echo $worker['name'] . ' ' . $worker['lastname']; ?></strong>
                                </td>
                                <td><?php echo $worker['position']; ?></td>
                                <td><?php echo $worker['department']; ?></td>
                                <td><?php echo $worker['phone']; ?></td>
                                <td>
                                    <?php
                                    $badge_class = 'success';
                                    if ($worker['status'] == 'inactive') $badge_class = 'secondary';
                                    if ($worker['status'] == 'vacation') $badge_class = 'info';
                                    if ($worker['status'] == 'sick') $badge_class = 'warning';
                                    ?>
                                    <span class="badge bg-<?php echo $badge_class; ?>">
                                        <?php echo ucfirst($worker['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="worker-profile.php?id=<?php echo $worker['id']; ?>" 
                                           class="btn btn-sm btn-info" title="View Profile">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="edit-worker.php?id=<?php echo $worker['id']; ?>" 
                                           class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="delete-worker.php?id=<?php echo $worker['id']; ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Are you sure you want to delete this worker?')" 
                                           title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-users fa-3x mb-3 d-block"></i>
                                    No workers found
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <nav aria-label="Page navigation" class="mt-4">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $page-1; ?>&search=<?php echo $search; ?>&status=<?php echo $status_filter; ?>&department=<?php echo $department_filter; ?>">
                            Previous
                        </a>
                    </li>
                    
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>&status=<?php echo $status_filter; ?>&department=<?php echo $department_filter; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                    
                    <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $page+1; ?>&search=<?php echo $search; ?>&status=<?php echo $status_filter; ?>&department=<?php echo $department_filter; ?>">
                            Next
                        </a>
                    </li>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>