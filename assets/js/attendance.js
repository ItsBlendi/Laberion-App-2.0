/**
 * Attendance Management JavaScript
 */

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeAttendance();
});

/**
 * Initialize attendance page
 */
function initializeAttendance() {
    setupAttendanceFilters();
    setupAttendanceTable();
}

/**
 * Setup attendance filters
 */
function setupAttendanceFilters() {
    const filterForm = document.querySelector('form');
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            // Form will submit normally
        });
    }
}

/**
 * Setup attendance table
 */
function setupAttendanceTable() {
    // Initialize DataTable if available
    if ($.fn.dataTable) {
        $('#attendanceTable').DataTable({
            pageLength: 15,
            order: [[0, 'desc']],
            columnDefs: [
                { orderable: false, targets: -1 }
            ]
        });
    }
}

/**
 * Export attendance to CSV
 */
function exportAttendance() {
    const dateFrom = document.querySelector('input[name="date_from"]').value;
    const dateTo = document.querySelector('input[name="date_to"]').value;
    
    window.location.href = `../api/export-excel.php?type=attendance&from=${dateFrom}&to=${dateTo}`;
}

/**
 * Mark attendance manually
 */
function markAttendance(workerId, date, status) {
    axios.post('../api/save-attendance.php', {
        worker_id: workerId,
        date: date,
        time: new Date().toTimeString().split(' ')[0],
        action: 'check_in',
        status: status
    })
    .then(response => {
        if (response.data.success) {
            showSuccess('Attendance marked successfully');
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showError(response.data.message);
        }
    })
    .catch(error => {
        showError('Error marking attendance');
        console.error(error);
    });
}