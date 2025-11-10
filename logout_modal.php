<!-- Logout Confirmation Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="logoutModalLabel">
                    <i class="bi bi-box-arrow-right me-2"></i>Confirm Logout
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="mb-3">
                    <i class="bi bi-question-circle text-warning" style="font-size: 3rem;"></i>
                </div>
                <h6 class="mb-3">Are you sure you want to logout?</h6>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>Cancel
                </button>
                <button type="button" class="btn btn-danger" id="confirmLogoutBtn">
                    <i class="bi bi-box-arrow-right me-2"></i>Yes, Logout
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const confirmLogoutBtn = document.getElementById('confirmLogoutBtn');
    
    if (confirmLogoutBtn) {
        confirmLogoutBtn.addEventListener('click', function() {
            // Show loading state
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Logging out...';
            this.disabled = true;
            
            // Send logout request
            fetch('logout.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=logout'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Close modal
                    const logoutModal = bootstrap.Modal.getInstance(document.getElementById('logoutModal'));
                    if (logoutModal) {
                        logoutModal.hide();
                    }
                    
                    // Show success message briefly
                    this.innerHTML = '<i class="bi bi-check-circle me-2"></i>Logged out!';
                    
                    // Redirect to login page after a short delay
                    setTimeout(() => {
                        window.location.href = 'login.php';
                    }, 1000);
                } else {
                    // Handle error
                    this.innerHTML = originalText;
                    this.disabled = false;
                    alert('Error logging out. Please try again.');
                }
            })
            .catch(error => {
                console.error('Logout error:', error);
                this.innerHTML = originalText;
                this.disabled = false;
                alert('Error logging out. Please try again.');
            });
        });
    }
    
    // Handle logout links - show modal instead of direct logout
    document.querySelectorAll('a[href*="logout.php"]').forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const logoutModal = new bootstrap.Modal(document.getElementById('logoutModal'));
            logoutModal.show();
        });
    });
});
</script>
