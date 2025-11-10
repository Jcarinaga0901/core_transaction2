<?php 
session_start();
$currentPage = basename($_SERVER['PHP_SELF']); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancelled Orders Request - Core Transaction 2</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap" rel="stylesheet">
    <link href="css/styles.css?v=<?php echo filemtime('css/styles.css'); ?>" rel="stylesheet">
</head>
<body>
    <header class="main-header">
        <div class="sidebar-toggle">
            <i class="bi bi-list"></i>
        </div>
        <h1 style="font-family: 'Great Vibes', cursive !important; font-size: 1.5rem; font-weight: 350; letter-spacing: 2px; text-shadow: 2px 2px 4px rgba(0,0,0,0.2);">
            <a href="index.php" class="brand-link">RAEVOR</a></h1>
        
        <!-- User Info and Settings Dropdown -->
        <div class="user-info ms-auto d-flex align-items-center">
            <?php include 'includes/welcome_user.php'; ?>
            <?php include 'includes/notification_dropdown.php'; ?>

            <div class="dropdown">
                <button class="btn btn-link text-secondary p-0 settings-icon-btn" type="button" id="settingsDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 1.5rem; border: none; background: none; box-shadow: none !important; outline: none !important;">
                    <i class="bi bi-gear-fill"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="settingsDropdown">
                    <li><a class="dropdown-item" href="my_shop.php"><i class="bi bi-shop me-2"></i>My Shop</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                </ul>
            </div>
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
            }
            
            .notification-item.unread {
                background-color: #f8f9fa;
                border-left-color: #0d6efd;
            }
            
            .notification-item:hover {
                background-color: #e9ecef;
            }
            
            .notification-dropdown {
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            }
            
            /* Settings Icon Styles */
            .settings-icon-btn {
                transition: transform 0.3s ease;
                cursor: pointer;
            }
            
            .settings-icon-btn:hover {
                transform: scale(1.1);
                color: #495057 !important;
                text-decoration: none !important;
            }
            
            .settings-icon-btn:focus,
            .settings-icon-btn:active {
                box-shadow: none !important;
                outline: none !important;
                border: none !important;
            }
            
            .settings-icon-btn.show {
                animation: rotateGear 0.5s ease;
            }
            
            @keyframes rotateGear {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(180deg); }
            }
            
            .dropdown-menu {
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                border: 1px solid rgba(0,0,0,0.1);
                border-radius: 8px;
                padding: 0.5rem 0;
            }
            
            .dropdown-item {
                padding: 0.5rem 1.25rem;
                transition: background-color 0.2s ease;
            }
            
            .dropdown-item:hover {
                background-color: #f8f9fa;
            }
        </style>
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Settings dropdown animation
                const settingsBtn = document.getElementById('settingsDropdown');
                if (settingsBtn) {
                    settingsBtn.addEventListener('click', function() {
                        this.classList.add('show');
                        setTimeout(() => {
                            this.classList.remove('show');
                        }, 500);
                    });
                }
                
                // Notification bell animation
                const notificationBtn = document.getElementById('notificationDropdown');
                if (notificationBtn) {
                    notificationBtn.addEventListener('click', function() {
                        this.classList.add('show');
                        setTimeout(() => {
                            this.classList.remove('show');
                        }, 500);
                    });
                }
                
                // Mark individual notification as read
                document.querySelectorAll('.notification-item').forEach(item => {
                    item.addEventListener('click', function(e) {
                        e.preventDefault();
                        this.classList.remove('unread');
                        updateNotificationBadge();
                    });
                });
                
                // Mark all as read
                const markAllReadBtn = document.getElementById('markAllRead');
                if (markAllReadBtn) {
                    markAllReadBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        document.querySelectorAll('.notification-item.unread').forEach(item => {
                            item.classList.remove('unread');
                        });
                        updateNotificationBadge();
                    });
                }
                
                // Update notification badge count
                function updateNotificationBadge() {
                    const unreadCount = document.querySelectorAll('.notification-item.unread').length;
                    const badge = document.getElementById('notificationBadge');
                    if (unreadCount > 0) {
                        badge.textContent = unreadCount;
                        badge.style.display = 'inline-block';
                    } else {
                        badge.style.display = 'none';
                    }
                }
            });
        </script>
    </header>

    <!-- Side Navigation -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h3>Core Transaction 2</h3>
        </div>
        <ul class="sidebar-nav">
            <!-- Dashboard -->
            <li class="sidebar-item <?php if($currentPage == 'index.php') echo 'active'; ?>">
                <a href="index.php">
                    <i class="bi bi-speedometer2"></i>
                    <span class="menu-text">Dashboard</span>
                </a>
            </li>

            <!-- Product and Shop Management -->
            <li class="sidebar-item has-submenu <?php if(in_array($currentPage, ['product_catalog.php','my_shop.php','vendor_requests.php'])) echo 'active'; ?>">
                <a href="product_catalog.php">
                    <i class="bi bi-shop"></i>
                    <span class="menu-text">Product & Shop Management</span>
                    <i class="bi bi-chevron-down submenu-icon"></i>
                </a>
                <ul class="submenu">
                    <li><a href="product_catalog.php" class="<?php if($currentPage == 'product_catalog.php') echo 'active'; ?>"><span class="menu-text">Product Catalog</span></a></li>
                    <li><a href="my_shop.php" class="<?php if($currentPage == 'my_shop.php') echo 'active'; ?>"><span class="menu-text">My Shop</span></a></li>

                    <li><a href="vendor_requests.php" class="<?php if($currentPage == 'vendor_requests.php') echo 'active'; ?>"><span class="menu-text">Pending Products</span></a></li>
                </ul>
            </li>

            <!-- Order Management (dropdown) -->
            <li class="sidebar-item has-submenu <?php if(in_array($currentPage, ['order.php','cancelled_orders.php','return_management.php',''])) echo 'active'; ?>">
                <a href="order.php">
                    <i class="bi bi-cart-check"></i>
                    <span class="menu-text">Order Management</span>
                    <i class="bi bi-chevron-down submenu-icon"></i>
                </a>
                <ul class="submenu">
                    <li><a href="order.php" class="<?php if($currentPage == 'order.php') echo 'active'; ?>"><span class="menu-text">Order List</span></a></li>
                    <li><a href="cancelled_orders.php" class="<?php if($currentPage == 'cancelled_orders.php') echo 'active'; ?>"><span class="menu-text">Cancelled Orders</span></a></li>
                    <li><a href="return_management.php" class="<?php if($currentPage == 'return_management.php') echo 'active'; ?>"><span class="menu-text">Return Management</span></a></li>
                </ul>
            </li>

            <!-- Delivery Management -->
            <li class="sidebar-item <?php if($currentPage == 'delivery.php') echo 'active'; ?>">
                <a href="delivery.php">
                    <i class="bi bi-truck"></i>
                    <span class="menu-text">Delivery Management</span>
                </a>
            </li>

            <!-- Seller Vouchers -->
            <li class="sidebar-item <?php if($currentPage == 'seller_vouchers.php') echo 'active'; ?>">
                <a href="seller_vouchers.php">
                    <i class="bi bi-gift"></i>
                    <span class="menu-text">Seller Vouchers</span>
                </a>
            </li>

            <!-- Reports -->
            <li class="sidebar-item has-submenu <?php if(in_array($currentPage, ['sales_reports.php','commission_reports.php'])) echo 'active'; ?>">
                <a href="sales_reports.php">
                    <i class="bi bi-graph-up"></i>
                    <span class="menu-text">Reports</span>
                    <i class="bi bi-chevron-down submenu-icon"></i>
                </a>
                <ul class="submenu">
                    <li><a href="sales_reports.php" class="<?php if($currentPage == 'sales_reports.php') echo 'active'; ?>"><span class="menu-text">Sales Reports</span></a></li>
                    <li><a href="commission_reports.php" class="<?php if($currentPage == 'commission_reports.php') echo 'active'; ?>"><span class="menu-text">Commission Reports</span></a></li>
                </ul>
            </li>

            <!-- Business Documents -->
            <li class="sidebar-item <?php if($currentPage == 'seller_documents.php') echo 'active'; ?>">
                <a href="seller_documents.php">
                    <i class="bi bi-file-earmark-check"></i>
                    <span class="menu-text">Business Documents</span>
                </a>
            </li>

            <!-- Payment Monitoring -->
            <li class="sidebar-item <?php if($currentPage == 'payment_monitoring.php') echo 'active'; ?>">
                <a href="payment_monitoring.php">
                    <i class="bi bi-credit-card"></i>
                    <span class="menu-text">Payment Monitoring</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="main-content">
        <div class="container-fluid">
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="page-title">Cancelled Orders Request</h2>
                        <p class="page-subtitle">Review cancellation requests for orders containing your products</p>
                        <div class="alert alert-info mt-2" style="background: linear-gradient(135deg, #667eea, #764ba2); color: white; border: none;">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Auto-Cancellation:</strong> Orders are automatically cancelled if not approved within 30 minutes of placement.
                        </div>
                    </div>
                    <div>
                        <button class="btn btn-primary me-2" onclick="triggerAutoCancel()">
                            <i class="bi bi-clock-history me-1"></i>Check Auto-Cancel
                        </button>
                        <a href="cancelled_orders.php" class="btn btn-light">Reset</a>
                    </div>
                </div>
            </div>

            <form id="filterForm" class="row g-2 mb-3">
                <div class="col-md-3">
                    <label class="form-label text-white">From</label>
                    <input type="date" class="form-control" name="from" id="from">
                </div>
                <div class="col-md-3">
                    <label class="form-label text-white">To</label>
                    <input type="date" class="form-control" name="to" id="to">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-white">Search</label>
                    <input class="form-control" name="q" id="q" placeholder="Search order #, product, seller, customer">
                </div>
                <div class="col-md-2 d-grid align-end">
                    <label class="form-label" style="visibility:hidden">Action</label>
                    <button class="btn btn-primary" type="submit"><i class="bi bi-search me-1"></i>Filter</button>
                </div>
            </form>

            <!-- Status Tabs -->
            <ul class="nav nav-tabs mb-3" id="cancellationTabs" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tab-all-cancellations" data-bs-toggle="tab" data-bs-target="#pane-all-cancellations" type="button" role="tab">
                  <i class="bi bi-list-ul me-1"></i>All Cancellations
                  <span class="badge bg-secondary ms-2" id="allCancellationBadge">0</span>
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-pending-cancellations" data-bs-toggle="tab" data-bs-target="#pane-pending-cancellations" type="button" role="tab">
                  <i class="bi bi-clock me-1"></i>Pending Approval
                  <span class="badge bg-warning ms-2" id="pendingCancellationBadge">0</span>
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-approved-cancellations" data-bs-toggle="tab" data-bs-target="#pane-approved-cancellations" type="button" role="tab">
                  <i class="bi bi-check-circle me-1"></i>Approved
                  <span class="badge bg-success ms-2" id="approvedCancellationBadge">0</span>
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-rejected-cancellations" data-bs-toggle="tab" data-bs-target="#pane-rejected-cancellations" type="button" role="tab">
                  <i class="bi bi-x-circle me-1"></i>Rejected
                  <span class="badge bg-danger ms-2" id="rejectedCancellationBadge">0</span>
                </button>
              </li>
            </ul>

            <div class="tab-content">
              <!-- All Cancellations Tab -->
              <div class="tab-pane fade show active" id="pane-all-cancellations" role="tabpanel" aria-labelledby="tab-all-cancellations">
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0" id="tblCancelledAll">
                                <thead class="table-light">
                                    <tr>
                                        <th>Order #</th>
                                        <th>Date</th>
                                        <th>Customer Name</th>
                                        <th>Seller Name</th>
                                        <th>Product (type of item)</th>
                                        <th class="text-end">Quantity</th>
                                        <th class="text-end">Amount per item</th>
                                        <th class="text-end">Total order amount</th>
                                        <th>Reason for cancellation</th>
                                        <th>Approval Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
              </div>

              <!-- Pending Cancellations Tab -->
              <div class="tab-pane fade" id="pane-pending-cancellations" role="tabpanel" aria-labelledby="tab-pending-cancellations">
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0" id="tblCancelledPending">
                                <thead class="table-light">
                                    <tr>
                                        <th>Order #</th>
                                        <th>Date</th>
                                        <th>Customer Name</th>
                                        <th>Seller Name</th>
                                        <th>Product (type of item)</th>
                                        <th class="text-end">Quantity</th>
                                        <th class="text-end">Amount per item</th>
                                        <th class="text-end">Total order amount</th>
                                        <th>Reason for cancellation</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
              </div>

              <!-- Approved Cancellations Tab -->
              <div class="tab-pane fade" id="pane-approved-cancellations" role="tabpanel" aria-labelledby="tab-approved-cancellations">
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0" id="tblCancelledApproved">
                                <thead class="table-light">
                                    <tr>
                                        <th>Order #</th>
                                        <th>Date</th>
                                        <th>Customer Name</th>
                                        <th>Seller Name</th>
                                        <th>Product (type of item)</th>
                                        <th class="text-end">Quantity</th>
                                        <th class="text-end">Amount per item</th>
                                        <th class="text-end">Total order amount</th>
                                        <th>Reason for cancellation</th>
                                        <th>Approved By</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
              </div>

              <!-- Rejected Cancellations Tab -->
              <div class="tab-pane fade" id="pane-rejected-cancellations" role="tabpanel" aria-labelledby="tab-rejected-cancellations">
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0" id="tblCancelledRejected">
                                <thead class="table-light">
                                    <tr>
                                        <th>Order #</th>
                                        <th>Date</th>
                                        <th>Customer Name</th>
                                        <th>Seller Name</th>
                                        <th>Product (type of item)</th>
                                        <th class="text-end">Quantity</th>
                                        <th class="text-end">Amount per item</th>
                                        <th class="text-end">Total order amount</th>
                                        <th>Reason for cancellation</th>
                                        <th>Rejected By</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
              </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Include Logout Modal -->
    <?php include 'includes/logout_modal.php'; ?>
    
    <script>
        document.querySelector('.sidebar-toggle').addEventListener('click', function() {
            document.body.classList.toggle('sidebar-collapsed');
        });

        const peso = v => '₱' + Number(v||0).toLocaleString(undefined,{minimumFractionDigits:2, maximumFractionDigits:2});
        let currentCancellationData = [];

        // No local samples; we seed DB when empty

        function getDates() {
            const to = document.getElementById('to');
            const from = document.getElementById('from');
            if (!to.value) to.valueAsDate = new Date();
            if (!from.value) { const d = new Date(); d.setDate(d.getDate()-30); from.valueAsDate = d; }
            return { from: from.value, to: to.value };
        }

        async function loadCancelled() {
            const { from, to } = getDates();
            const q = document.getElementById('q').value;
            const qs = new URLSearchParams({ action:'cancelled_items', from, to, q });
            let rows = [];
            try {
                const res = await fetch('api/orders.php?' + qs.toString());
                const data = await res.json();
                if (data.ok && Array.isArray(data.rows) && data.rows.length) {
                    rows = data.rows;
                }
            } catch (_) {}

            if (rows.length === 0) {
                try {
                    const fd = new FormData();
                    fd.append('action', 'seed_cancelled');
                    await fetch('api/orders.php', { method: 'POST', body: fd });
                } catch (_) {}
                try {
                    const res2 = await fetch('api/orders.php?' + qs.toString());
                    const data2 = await res2.json();
                    if (data2.ok && Array.isArray(data2.rows) && data2.rows.length) {
                        rows = data2.rows;
                    }
                } catch (_) {}
            }

            currentCancellationData = rows;
            
            // Update badges
            const pendingCount = rows.filter(r => !r.cancel_approved && !r.cancel_rejected).length;
            const approvedCount = rows.filter(r => r.cancel_approved).length;
            const rejectedCount = rows.filter(r => r.cancel_rejected).length;
            
            document.getElementById('allCancellationBadge').textContent = rows.length;
            document.getElementById('pendingCancellationBadge').textContent = pendingCount;
            document.getElementById('approvedCancellationBadge').textContent = approvedCount;
            document.getElementById('rejectedCancellationBadge').textContent = rejectedCount;
            
            // Load data for all tabs
            loadTabData('all', rows);
            loadTabData('pending', rows.filter(r => !r.cancel_approved && !r.cancel_rejected));
            loadTabData('approved', rows.filter(r => r.cancel_approved));
            loadTabData('rejected', rows.filter(r => r.cancel_rejected));
        }

        function loadTabData(tabName, rows) {
            const tbody = document.querySelector(`#tblCancelled${tabName.charAt(0).toUpperCase() + tabName.slice(1)} tbody`);
            if (!tbody) return;
            
            tbody.innerHTML = '';
            
            // Show empty state if no cancelled orders
            if (rows.length === 0) {
                const emptyMessage = tabName === 'all' ? 
                    'No cancelled orders for now' : 
                    `No ${tabName} cancellations found`;
                const colSpan = tabName === 'all' ? '11' : tabName === 'pending' ? '10' : '11';
                tbody.innerHTML = `<tr><td colspan="${colSpan}" class="text-center text-muted py-5"><i class="bi bi-inbox" style="font-size: 3rem; display: block; margin-bottom: 1rem;"></i><h5>${emptyMessage}</h5><p>Stay tuned! Cancelled order requests will appear here when customers cancel orders containing your products.</p></td></tr>`;
                return;
            }
            
            for (const r of rows) {
                const tr = renderTableRow(r, tabName);
                tbody.appendChild(tr);
            }
        }

        document.getElementById('filterForm').addEventListener('submit', (e) => { e.preventDefault(); loadCancelled(); });
        
        // Event delegation for all cancellation tables
        document.addEventListener('click', async (e) => {
            const btn = e.target.closest('button[data-action]');
            if (!btn) return;
            const id = btn.getAttribute('data-id');
            const action = btn.getAttribute('data-action');
            if (action === 'approve') {
                if (!confirm('Approve this cancellation request?')) return;
                const fd = new FormData(); fd.append('action','approve_cancellation'); fd.append('id', id);
                const res = await fetch('api/orders.php', { method: 'POST', body: fd });
                const d = await res.json(); 
                if (d.ok) {
                    alert('Cancellation approved successfully!');
                    loadCancelled();
                } else {
                    alert('Error approving cancellation: ' + (d.error || 'Failed to approve'));
                }
            } else if (action === 'reject') {
                if (!confirm('Reject this cancellation request and restore previous status?')) return;
                const fd = new FormData(); fd.append('action','reject_cancellation'); fd.append('id', id);
                const res = await fetch('api/orders.php', { method: 'POST', body: fd });
                const d = await res.json(); 
                if (d.ok) {
                    alert('Cancellation rejected successfully!');
                    loadCancelled();
                } else {
                    alert('Error rejecting cancellation: ' + (d.error || 'Failed to reject'));
                }
            }
        });
        
        // Add viewOrderDetails function
        function viewOrderDetails(orderId) {
            // You can implement this function to show order details
            alert('View order details for order #' + orderId);
        }
        
        // Auto-cancellation trigger function
        async function triggerAutoCancel() {
            const btn = event.target;
            const originalText = btn.innerHTML;
            
            btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Checking...';
            btn.disabled = true;
            
            try {
                const response = await fetch('api/auto_cancel.php');
                const data = await response.json();
                
                if (data.success) {
                    alert(`Auto-cancellation check completed!\n\nCancelled orders: ${data.cancelled_orders}\nNotifications sent: ${data.notifications_sent}`);
                    loadCancelled(); // Refresh the data
                } else {
                    alert('Auto-cancellation check failed: ' + data.message);
                }
            } catch (error) {
                console.error('Auto-cancel error:', error);
                alert('Error checking auto-cancellation: ' + error.message);
            } finally {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        }
        
        // Enhanced table row rendering with auto-cancellation info
        function renderTableRow(r, tabName) {
            const tr = document.createElement('tr');
            
            // Determine approval status with auto-cancellation info
            let approvalStatus = '';
            let approvalStatusClass = 'secondary';
            let statusIcon = '';
            
            if (r.cancel_approved) {
                approvalStatus = 'Approved';
                approvalStatusClass = 'success';
                statusIcon = '<i class="bi bi-check-circle me-1"></i>';
            } else if (r.cancel_rejected) {
                approvalStatus = 'Rejected';
                approvalStatusClass = 'danger';
                statusIcon = '<i class="bi bi-x-circle me-1"></i>';
            } else {
                approvalStatus = 'Pending';
                approvalStatusClass = 'warning';
                statusIcon = '<i class="bi bi-clock me-1"></i>';
            }
            
            // Add auto-cancellation indicator
            if (r.auto_cancel_processed) {
                approvalStatus += ' (Auto)';
                statusIcon = '<i class="bi bi-robot me-1"></i>';
            }
            
            // Build action buttons based on tab
            let actionButtons = '';
            if (tabName === 'pending') {
                actionButtons = `
                    <button class="btn btn-sm btn-outline-success me-1" data-action="approve" data-id="${r.order_id}" ${r.is_sample ? 'disabled' : ''}>
                        <i class="bi bi-check me-1"></i>Approve
                    </button>
                    <button class="btn btn-sm btn-outline-danger" data-action="reject" data-id="${r.order_id}" ${r.is_sample ? 'disabled' : ''}>
                        <i class="bi bi-x me-1"></i>Reject
                    </button>`;
            } else {
                actionButtons = `
                    <button class="btn btn-sm btn-outline-primary me-1" onclick="viewOrderDetails(${r.order_id})" title="View Details">
                        <i class="bi bi-eye"></i>
                    </button>
                    ${r.auto_cancel_processed ? '<span class="badge bg-info ms-1">Auto</span>' : ''}`;
            }
            
            // Build table row based on tab
            if (tabName === 'all') {
                tr.innerHTML = `
                    <td>#${r.order_id}</td>
                    <td>${r.order_date || ''}</td>
                    <td>${r.customer_name || ''}</td>
                    <td>${r.seller_name || 'N/A'}</td>
                    <td>${r.product_name}${r.category_name ? `<div class="text-muted small">${r.category_name}</div>` : ''}</td>
                    <td class="text-end">${r.quantity}</td>
                    <td class="text-end">${peso(r.unit_price)}</td>
                    <td class="text-end">${peso(r.order_total)}</td>
                    <td>${r.cancel_reason || ''}</td>
                    <td><span class="badge bg-${approvalStatusClass}">${statusIcon}${approvalStatus}</span></td>
                    <td class="text-end">${actionButtons}</td>`;
            } else if (tabName === 'pending') {
                tr.innerHTML = `
                    <td>#${r.order_id}</td>
                    <td>${r.order_date || ''}</td>
                    <td>${r.customer_name || ''}</td>
                    <td>${r.seller_name || 'N/A'}</td>
                    <td>${r.product_name}${r.category_name ? `<div class="text-muted small">${r.category_name}</div>` : ''}</td>
                    <td class="text-end">${r.quantity}</td>
                    <td class="text-end">${peso(r.unit_price)}</td>
                    <td class="text-end">${peso(r.order_total)}</td>
                    <td>${r.cancel_reason || ''}</td>
                    <td class="text-end">${actionButtons}</td>`;
            } else if (tabName === 'approved' || tabName === 'rejected') {
                tr.innerHTML = `
                    <td>#${r.order_id}</td>
                    <td>${r.order_date || ''}</td>
                    <td>${r.customer_name || ''}</td>
                    <td>${r.seller_name || 'N/A'}</td>
                    <td>${r.product_name}${r.category_name ? `<div class="text-muted small">${r.category_name}</div>` : ''}</td>
                    <td class="text-end">${r.quantity}</td>
                    <td class="text-end">${peso(r.unit_price)}</td>
                    <td class="text-end">${peso(r.order_total)}</td>
                    <td>${r.cancel_reason || ''}</td>
                    <td>${r.cancel_actor || 'Admin'}</td>
                    <td class="text-end">${actionButtons}</td>`;
            }
            
            return tr;
        }
        
        loadCancelled();
    </script>
</body>
</html>
    