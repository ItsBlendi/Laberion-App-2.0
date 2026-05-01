/**
 * Workers Management JavaScript
 */

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeWorkers();
});

/**
 * Initialize workers page
 */
function initializeWorkers() {
    setupWorkerFilters();
    setupWorkerActions();
}

/**
 * Setup worker filters
 */
function setupWorkerFilters() {
    const filterForm = document.querySelector('form');
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            // Form will submit normally
        });
    }
}

/**
 * Setup worker actions
 */
function setupWorkerActions() {
    // Delete worker
    document.querySelectorAll('a[href*="delete-worker"]').forEach(link => {
        link.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this worker?')) {
                e.preventDefault();
            }
        });
    });
}

/**
 * Delete worker via AJAX
 */
function deleteWorker(workerId) {
    if (!confirm('Are you sure you want to delete this worker?')) {
        return;
    }
    
    axios.post('../api/delete-worker.php', {
        id: workerId
    })
    .then(response => {
        if (response.data.success) {
            showSuccess(response.data.message);
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showError(response.data.message);
        }
    })
    .catch(error => {
        showError('Error deleting worker');
        console.error(error);
    });
}

/**
 * Export workers to CSV
 */
function exportWorkers() {
    exportTableToCSV('workersTable', 'workers_' + new Date().toISOString().split('T')[0] + '.csv');
}