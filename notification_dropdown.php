<!-- Notification Icon -->
<div class="dropdown me-3">
    <button class="btn btn-link text-secondary position-relative notification-icon-btn" type="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 1.5rem; border: none; background: none; box-shadow: none !important; outline: none !important; padding: 8px 12px;">
        <i class="bi bi-bell-fill"></i>
        <span class="position-absolute badge rounded-pill" id="notificationBadge" style="top: 0px; right: 0px; background-color: #dc3545 !important; color: white !important; font-size: 0.85rem; min-width: 18px; height: 18px; display: none; align-items: center; justify-content: center; padding: 0 5px; font-weight: 700; border: 2px solid white;">
            0
        </span>
    </button>
    <ul class="dropdown-menu dropdown-menu-end notification-dropdown" aria-labelledby="notificationDropdown" style="width: 350px; max-height: 400px; overflow-y: auto;">
        <li class="dropdown-header d-flex justify-content-between align-items-center">
            <span><strong>Notifications</strong></span>
            <a href="#" class="text-primary small" id="markAllRead">Mark all as read</a>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li id="notificationList">
            <div class="text-center text-muted py-3">
                <div class="spinner-border spinner-border-sm" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item text-center text-primary" href="#" id="viewAllNotifications">View all notifications</a></li>
    </ul>
</div>

<style>
    /* Notification Icon Styles */
    .notification-icon-btn {
        transition: transform 0.3s ease;
        cursor: pointer;
    }
    
    .notification-icon-btn:hover {
        transform: scale(1.1);
        color: #495057 !important;
        text-decoration: none !important;
    }
    
    .notification-icon-btn:focus,
    .notification-icon-btn:active {
        box-shadow: none !important;
        outline: none !important;
        border: none !important;
    }
    
    .notification-icon-btn.show {
        animation: bellShake 0.5s ease;
    }
    
    @keyframes bellShake {
        0%, 100% { transform: rotate(0deg); }
        25% { transform: rotate(15deg); }
        50% { transform: rotate(-15deg); }
        75% { transform: rotate(10deg); }
    }
    
    #notificationBadge {
        box-shadow: 0 2px 6px rgba(0,0,0,0.5) !important;
        animation: pulse 2s infinite;
        z-index: 1000 !important;
        line-height: 1 !important;
    }
    
    @keyframes pulse {
        0%, 100% { 
            opacity: 1;
            transform: scale(1);
        }
        50% { 
            opacity: 1;
            transform: scale(1.1);
        }
    }
    
    .notification-item {
        padding: 0.75rem 1rem;
        border-left: 3px solid transparent;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    
    .notification-item.unread {
        background-color: #f8f9fa;
        border-left-color: #0d6efd;
    }
    
    .notification-item.unread.success-notification {
        background-color: #d4edda;
        border-left-color: #28a745;
        border: 1px solid #c3e6cb;
    }
    
    .notification-item:hover {
        background-color: #e9ecef;
    }
    
    .notification-item.success-notification:hover {
        background-color: #c3e6cb;
    }
    
    .notification-dropdown {
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load notifications on page load
    loadNotifications();
    
    // Reload notifications every 30 seconds
    setInterval(loadNotifications, 30000);
    
    // Notification bell animation
    const notificationBtn = document.getElementById('notificationDropdown');
    if (notificationBtn) {
        notificationBtn.addEventListener('click', function() {
            this.classList.add('show');
            setTimeout(() => {
                this.classList.remove('show');
            }, 500);
            // Reload when dropdown is opened
            loadNotifications();
        });
    }
    
    // Mark all as read
    const markAllReadBtn = document.getElementById('markAllRead');
    if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', function(e) {
            e.preventDefault();
            markAllNotificationsRead();
        });
    }
    
    async function loadNotifications() {
        try {
            const response = await fetch('api/notifications.php?action=list');
            const data = await response.json();
            
            if (data.ok) {
                updateNotificationBadge(data.unread_count);
                renderNotifications(data.notifications);
            }
        } catch (error) {
            console.error('Error loading notifications:', error);
        }
    }
    
    function updateNotificationBadge(count) {
        const badge = document.getElementById('notificationBadge');
        if (count > 0) {
            badge.textContent = count > 99 ? '99+' : count;
            badge.style.display = 'flex';
        } else {
            badge.style.display = 'none';
        }
    }
    
    function renderNotifications(notifications) {
        const listContainer = document.getElementById('notificationList');
        
        if (!notifications || notifications.length === 0) {
            listContainer.innerHTML = '<div class="text-center text-muted py-3"><i class="bi bi-bell-slash" style="font-size: 2rem; display: block;"></i><small>No notifications</small></div>';
            return;
        }
        
        listContainer.innerHTML = notifications.map(notif => {
            const iconMap = {
                'product_created': 'bi-box-seam text-primary',
                'product_approved': 'bi-check-circle text-success',
                'product_rejected': 'bi-x-circle text-danger',
                'voucher_created': 'bi-ticket-perforated text-primary',
                'voucher_approved': 'bi-check-circle text-success',
                'voucher_rejected': 'bi-x-circle text-danger',
                'order_approved': 'bi-check-circle text-success',
                'new_order': 'bi-cart-check text-info',
                'new_review': 'bi-star text-warning',
                'low_stock': 'bi-exclamation-triangle text-warning',
                'documents_uploaded': 'bi-file-earmark-check text-success',
                'general': 'bi-info-circle text-primary'
            };
            
            const icon = iconMap[notif.type] || 'bi-bell text-secondary';
            const unreadClass = notif.is_read == 0 ? 'unread' : '';
            const successClass = notif.type === 'documents_uploaded' ? 'success-notification' : '';
            
            return `
                <a class="dropdown-item notification-item ${unreadClass} ${successClass}" href="#" data-id="${notif.id}">
                    <div class="d-flex">
                        <i class="bi ${icon} me-2" style="font-size: 1.2rem;"></i>
                        <div class="flex-grow-1">
                            <div class="fw-bold">${notif.title}</div>
                            <small class="text-muted">${notif.message}</small>
                            <div class="text-muted" style="font-size: 0.75rem;">${notif.time_ago}</div>
                        </div>
                    </div>
                </a>
            `;
        }).join('');
        
        // Add click handlers to mark as read
        document.querySelectorAll('.notification-item').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const notifId = this.getAttribute('data-id');
                markNotificationRead(notifId);
                this.classList.remove('unread');
                
                // Update badge count
                const badge = document.getElementById('notificationBadge');
                const currentCount = parseInt(badge.textContent) || 0;
                if (currentCount > 0) {
                    updateNotificationBadge(currentCount - 1);
                }
            });
        });
    }
    
    async function markNotificationRead(notifId) {
        try {
            const formData = new FormData();
            formData.append('action', 'mark_read');
            formData.append('id', notifId);
            
            await fetch('api/notifications.php', {
                method: 'POST',
                body: formData
            });
        } catch (error) {
            console.error('Error marking notification as read:', error);
        }
    }
    
    async function markAllNotificationsRead() {
        try {
            const formData = new FormData();
            formData.append('action', 'mark_read');
            
            const response = await fetch('api/notifications.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            if (data.ok) {
                // Reload notifications
                loadNotifications();
            }
        } catch (error) {
            console.error('Error marking all notifications as read:', error);
        }
    }
});
</script>

