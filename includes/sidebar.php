<?php
/**
 * Sidebar Navigation Template
 * Included on every admin page
 */

$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar">
    <!-- Logo Section -->
    <div class="sidebar-logo">
        <div class="logo-container">
            <i class="fas fa-building"></i>
            <h3>LABERION</h3>
        </div>
        <p class="logo-subtitle">WMS</p>
    </div>
    
    <!-- User Profile Section -->
    <div class="sidebar-user">
        <div class="user-avatar">
            <i class="fas fa-user-circle"></i>
        </div>
        <div class="user-info">
            <p class="user-name"><?php echo htmlspecialchars($_SESSION['admin_username']); ?></p>
            <p class="user-role">Administrator</p>
        </div>
    </div>
    
    <!-- Navigation Menu -->
    <nav class="sidebar-nav">
        <div class="nav-section">
            <p class="nav-section-title">Main</p>
            
            <a href="dashboard.php" class="nav-item <?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
                <?php if ($current_page == 'dashboard.php'): ?>
                    <span class="nav-indicator"></span>
                <?php endif; ?>
            </a>
        </div>
        
        <div class="nav-section">
            <p class="nav-section-title">Management</p>
            
            <a href="workers.php" class="nav-item <?php echo $current_page == 'workers.php' || $current_page == 'worker-profile.php' || $current_page == 'add-worker.php' || $current_page == 'edit-worker.php' ? 'active' : ''; ?>">
                <i class="fas fa-users"></i>
                <span>Workers</span>
                <?php if (in_array($current_page, ['workers.php', 'worker-profile.php', 'add-worker.php', 'edit-worker.php'])): ?>
                    <span class="nav-indicator"></span>
                <?php endif; ?>
            </a>
            
            <a href="attendance.php" class="nav-item <?php echo $current_page == 'attendance.php' || $current_page == 'attendance-details.php' ? 'active' : ''; ?>">
                <i class="fas fa-clock"></i>
                <span>Attendance</span>
                <?php if (in_array($current_page, ['attendance.php', 'attendance-details.php'])): ?>
                    <span class="nav-indicator"></span>
                <?php endif; ?>
            </a>
            
            <a href="leaves.php" class="nav-item <?php echo $current_page == 'leaves.php' || $current_page == 'leave-approve.php' || $current_page == 'leave-reject.php' ? 'active' : ''; ?>">
                <i class="fas fa-umbrella-beach"></i>
                <span>Leaves</span>
                <?php if (in_array($current_page, ['leaves.php', 'leave-approve.php', 'leave-reject.php'])): ?>
                    <span class="nav-indicator"></span>
                <?php endif; ?>
            </a>
        </div>
        
        <div class="nav-section">
            <p class="nav-section-title">Reports</p>
            
            <a href="reports.php" class="nav-item <?php echo $current_page == 'reports.php' ? 'active' : ''; ?>">
                <i class="fas fa-chart-bar"></i>
                <span>Reports</span>
                <?php if ($current_page == 'reports.php'): ?>
                    <span class="nav-indicator"></span>
                <?php endif; ?>
            </a>
        </div>
        
        <div class="nav-section">
            <p class="nav-section-title">System</p>
            
            <a href="settings.php" class="nav-item <?php echo $current_page == 'settings.php' ? 'active' : ''; ?>">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
                <?php if ($current_page == 'settings.php'): ?>
                    <span class="nav-indicator"></span>
                <?php endif; ?>
            </a>
        </div>
    </nav>
    
    <!-- Logout Button -->
    <div class="sidebar-footer">
        <a href="logout.php" class="nav-item logout" onclick="return confirm('Are you sure you want to logout?');">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </div>
</div>

<!-- Sidebar Styles -->
<style>
    .sidebar {
        position: fixed;
        left: 0;
        top: 0;
        width: 260px;
        height: 100vh;
        background: linear-gradient(180deg, #1e3a8a 0%, #1e40af 100%);
        color: white;
        padding: 0;
        z-index: 1000;
        overflow-y: auto;
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
    }
    
    .sidebar-logo {
        padding: 25px 20px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        text-align: center;
    }
    
    .logo-container {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        margin-bottom: 5px;
    }
    
    .logo-container i {
        font-size: 28px;
    }
    
    .logo-container h3 {
        margin: 0;
        font-size: 22px;
        font-weight: 700;
    }
    
    .logo-subtitle {
        margin: 0;
        font-size: 12px;
        opacity: 0.8;
    }
    
    .sidebar-user {
        padding: 15px 20px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .user-avatar {
        font-size: 32px;
        opacity: 0.9;
    }
    
    .user-info {
        flex: 1;
    }
    
    .user-name {
        margin: 0;
        font-size: 14px;
        font-weight: 600;
    }
    
    .user-role {
        margin: 0;
        font-size: 12px;
        opacity: 0.8;
    }
    
    .sidebar-nav {
        padding: 20px 0;
    }
    
    .nav-section {
        margin-bottom: 20px;
    }
    
    .nav-section-title {
        padding: 0 20px;
        margin: 0 0 10px 0;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        opacity: 0.6;
        letter-spacing: 1px;
    }
    
    .nav-item {
        display: flex;
        align-items: center;
        padding: 12px 20px;
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        transition: all 0.3s;
        position: relative;
        gap: 12px;
    }
    
    .nav-item:hover {
        background: rgba(255, 255, 255, 0.1);
        color: white;
    }
    
    .nav-item.active {
        background: rgba(255, 255, 255, 0.15);
        color: white;
        font-weight: 600;
    }
    
    .nav-item i {
        width: 20px;
        text-align: center;
        font-size: 16px;
    }
    
    .nav-item span:not(.nav-indicator) {
        flex: 1;
    }
    
    .nav-indicator {
        width: 4px;
        height: 20px;
        background: #10b981;
        border-radius: 2px;
    }
    
    .nav-item.logout {
        color: #fca5a5;
    }
    
    .nav-item.logout:hover {
        background: rgba(239, 68, 68, 0.2);
        color: #fecaca;
    }
    
    .sidebar-footer {
        position: absolute;
        bottom: 0;
        width: 100%;
        padding: 15px 0;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    /* Scrollbar */
    .sidebar::-webkit-scrollbar {
        width: 6px;
    }
    
    .sidebar::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.05);
    }
    
    .sidebar::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 3px;
    }
    
    .sidebar::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.3);
    }
</style>