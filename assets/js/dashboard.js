/**
 * Dashboard JavaScript
 * Dashboard-specific functionality
 */

// Initialize dashboard on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeDashboard();
});

/**
 * Initialize dashboard
 */
function initializeDashboard() {
    loadDashboardStats();
    initializeCharts();
    setupRefreshInterval();
}

/**
 * Load dashboard statistics
 */
function loadDashboardStats() {
    axios.get('../api/get-stats.php')
        .then(response => {
            if (response.data.success) {
                updateStatistics(response.data.statistics);
                updateRecentCheckins(response.data.recent_checkins);
                updatePendingLeaves(response.data.pending_leave_requests);
            }
        })
        .catch(error => {
            console.error('Error loading stats:', error);
        });
}

/**
 * Update statistics display
 */
function updateStatistics(stats) {
    // Update stat cards
    updateStatCard('total-workers', stats.total_workers);
    updateStatCard('present-today', stats.present_today);
    updateStatCard('absent-today', stats.absent_today);
    updateStatCard('late-today', stats.late_today);
    updateStatCard('on-vacation', stats.on_vacation);
    updateStatCard('on-sick', stats.on_sick);
    updateStatCard('pending-leaves', stats.pending_leaves);
    updateStatCard('attendance-percentage', stats.attendance_percentage + '%');
}

/**
 * Update individual stat card
 */
function updateStatCard(elementId, value) {
    const element = document.getElementById(elementId);
    if (element) {
        element.textContent = value;
    }
}

/**
 * Update recent check-ins
 */
function updateRecentCheckins(checkins) {
    const container = document.getElementById('recent-checkins');
    if (!container) return;
    
    if (checkins.length === 0) {
        container.innerHTML = '<p class="text-muted text-center">No check-ins today</p>';
        return;
    }
    
    let html = '';
    checkins.forEach(checkin => {
        const time = new Date(checkin.check_in).toLocaleTimeString('en-US', {
            hour: '2-digit',
            minute: '2-digit'
        });
        
        const statusBadge = `<span class="badge bg-${checkin.status === 'late' ? 'warning' : 'success'}">
            ${checkin.status.charAt(0).toUpperCase() + checkin.status.slice(1)}
        </span>`;
        
        html += `
            <div class="activity-item">
                <div class="activity-icon check-in">
                    <i class="fas fa-sign-in-alt"></i>
                </div>
                <div class="activity-content">
                    <div class="activity-title">${checkin.name} ${checkin.lastname}</div>
                    <div class="activity-time">${time} - ${statusBadge}</div>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

/**
 * Update pending leaves
 */
function updatePendingLeaves(leaves) {
    const container = document.getElementById('pending-leaves');
    if (!container) return;
    
    if (leaves.length === 0) {
        container.innerHTML = '<p class="text-muted text-center">No pending leave requests</p>';
        return;
    }
    
    let html = '';
    leaves.forEach(leave => {
        const startDate = new Date(leave.start_date).toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric'
        });
        const endDate = new Date(leave.end_date).toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric'
        });
        
        html += `
            <div class="activity-item">
                <div class="activity-icon leave">
                    <i class="fas fa-umbrella-beach"></i>
                </div>
                <div class="activity-content">
                    <div class="activity-title">${leave.name} ${leave.lastname}</div>
                    <div class="activity-time">${startDate} - ${endDate}</div>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

/**
 * Initialize charts
 */
function initializeCharts() {
    initializeAttendanceChart();
    initializeDepartmentChart();
}

/**
 * Initialize attendance chart
 */
function initializeAttendanceChart() {
    const ctx = document.getElementById('attendanceChart');
    if (!ctx) return;
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Present', 'Late', 'Absent'],
            datasets: [{
                data: [65, 15, 20],
                backgroundColor: [
                    '#10b981',
                    '#f59e0b',
                    '#ef4444'
                ],
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

/**
 * Initialize department chart
 */
function initializeDepartmentChart() {
    const ctx = document.getElementById('departmentChart');
    if (!ctx) return;
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['IT', 'HR', 'Finance', 'Marketing', 'Sales'],
            datasets: [{
                label: 'Employees',
                data: [12, 8, 6, 10, 14],
                backgroundColor: '#3b82f6',
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

/**
 * Setup auto-refresh interval
 */
function setupRefreshInterval() {
    // Refresh stats every 30 seconds
    setInterval(loadDashboardStats, 30000);
}

/**
 * Refresh dashboard manually
 */
function refreshDashboard() {
    loadDashboardStats();
    showSuccess('Dashboard refreshed');
}