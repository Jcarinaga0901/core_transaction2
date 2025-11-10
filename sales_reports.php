<?php 
session_start();

$currentPage = basename($_SERVER['PHP_SELF']); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Core Transaction 2</title>
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
            
            /* Main content styling to match delivery.php */
            .main-content {
                margin-left: 250px;
                padding: 2rem;
                min-height: calc(100vh - 60px);
                background: #f8f9fa;
            }
            
            .sidebar-collapsed .main-content {
                margin-left: 60px;
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
            <p style="margin: 5px 0 0 0; font-size: 0.8rem; opacity: 0.8;">Seller Dashboard</p>
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
                    <li><a href="order.php" class="<?php if($currentPage == 'order.php' && !in_array($_GET['status'] ?? '', ['Cancelled'])) echo 'active'; ?>"><span class="menu-text">Order List</span></a></li>
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

    <div class="main-content" style="margin-left: 250px; padding: 2rem; min-height: calc(100vh - 60px); background: #f8f9fa;">
        <div class="container-fluid">
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="page-title">Sales Reports</h2>
                        <p class="page-subtitle">View your sales, orders, delivery and inventory insights</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button id="exportCsv" class="btn btn-light"><i class="bi bi-download me-1"></i>Export CSV</button>
                    </div>
                </div>
            </div>

            <form id="reportFilters" class="row g-2 mb-3">
                <div class="col-md-3">
                    <label class="form-label text-white">From</label>
                    <input type="date" class="form-control" name="from" id="from">
                </div>
                <div class="col-md-3">
                    <label class="form-label text-white">To</label>
                    <input type="date" class="form-control" name="to" id="to">
                </div>
                <div class="col-md-3 d-grid align-end">
                    <label class="form-label" style="visibility:hidden">Action</label>
                    <button class="btn btn-primary" type="submit"><i class="bi bi-arrow-repeat me-1"></i>Refresh</button>
                </div>
            </form>

            <div class="row" id="kpiRow">
                <div class="col-md-3 mb-3">
                    <div class="card"><div class="card-body">
                        <div class="text-muted small">Gross Sales</div>
                        <div class="fs-4 fw-semibold" id="kpiGross">₱0.00</div>
                    </div></div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card"><div class="card-body">
                        <div class="text-muted small">Net Sales</div>
                        <div class="fs-4 fw-semibold" id="kpiNet">₱0.00</div>
                    </div></div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card"><div class="card-body">
                        <div class="text-muted small">Orders</div>
                        <div class="fs-4 fw-semibold" id="kpiOrders">0</div>
                    </div></div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card"><div class="card-body">
                        <div class="text-muted small">AOV</div>
                        <div class="fs-4 fw-semibold" id="kpiAov">₱0.00</div>
                    </div></div>
                </div>
            </div>

            <!-- Payment & Delivery KPIs -->
            <div class="row" id="paymentDeliveryKpiRow">
                <div class="col-md-3 mb-3">
                    <div class="card"><div class="card-body">
                        <div class="text-muted small">Verified Payments</div>
                        <div class="fs-4 fw-semibold" id="kpiVerifiedPayments">0</div>
                    </div></div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card"><div class="card-body">
                        <div class="text-muted small">Pending Payments</div>
                        <div class="fs-4 fw-semibold" id="kpiPendingPayments">0</div>
                    </div></div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card"><div class="card-body">
                        <div class="text-muted small">Delivered Orders</div>
                        <div class="fs-4 fw-semibold" id="kpiDeliveredOrders">0</div>
                    </div></div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card"><div class="card-body">
                        <div class="text-muted small">In Transit</div>
                        <div class="fs-4 fw-semibold" id="kpiInTransit">0</div>
                    </div></div>
                </div>
            </div>

            <ul class="nav nav-tabs" id="reportTabs">
                <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-sales">Sales</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-orders">Orders</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-delivery">Delivery</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-connected">Order Flow</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-inventory">Inventory</button></li>
            </ul>
            <div class="tab-content border border-top-0 p-3 bg-white rounded-bottom">
                <div class="tab-pane fade show active" id="tab-sales">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="tblSales">
                            <thead class="table-light"><tr><th>Period</th><th>Orders</th><th>Units</th><th>Gross</th><th>Net</th><th>AOV</th></tr></thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="tab-orders">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="tblOrderStatus">
                            <thead class="table-light"><tr><th>Status</th><th>Count</th></tr></thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="tab-delivery">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="tblDelivery">
                            <thead class="table-light"><tr><th>Pending</th><th>Dispatched</th><th>In Transit</th><th>Delivered</th><th>Failed</th><th>Cancelled</th><th>On-time %</th><th>Avg Fulfillment (days)</th></tr></thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="tab-connected">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="tblConnectedData">
                            <thead class="table-light">
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Order Status</th>
                                    <th>Payment Status</th>
                                    <th>Delivery Status</th>
                                    <th>Delivery Fee</th>
                                    <th>Order Date</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="tab-inventory">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="tblLowStock">
                            <thead class="table-light"><tr><th>Product</th><th>Category</th><th>Stock</th><th>Min Level</th></tr></thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Include Logout Modal -->
    <?php include 'includes/logout_modal.php'; ?>
    
    <script>
        // Toggle sidebar collapse
        document.querySelector('.sidebar-toggle').addEventListener('click', function() {
            document.body.classList.toggle('sidebar-collapsed');
        });
        
        // Handle sidebar navigation
        const menuItems = document.querySelectorAll('.sidebar-item.has-submenu');
        const directLinks = document.querySelectorAll('.sidebar-item:not(.has-submenu) a');
        
        directLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                if (!this.hasAttribute('data-title')) {
                    const menuText = this.querySelector('.menu-text').textContent;
                    this.setAttribute('data-title', menuText);
                }
            });
        });
        
        menuItems.forEach(item => {
            const mainLink = item.querySelector('a');
            const submenuLinks = item.querySelectorAll('.submenu a');
            
            mainLink.addEventListener('click', function(e) {
                if (!this.hasAttribute('data-title')) {
                    const menuText = this.querySelector('.menu-text').textContent;
                    this.setAttribute('data-title', menuText);
                }
                if (document.body.classList.contains('sidebar-collapsed')) {
                    return;
                }
                e.preventDefault();
                item.classList.toggle('active');
                menuItems.forEach(otherItem => {
                    if (otherItem !== item && otherItem.classList.contains('active')) {
                        otherItem.classList.remove('active');
                    }
                });
            });
            
            submenuLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    if (!this.hasAttribute('data-title')) {
                        const menuText = this.querySelector('.menu-text').textContent;
                        this.setAttribute('data-title', menuText);
                    }
                });
            });
        });
        
        if (window.innerWidth > 768) {
            const allSidebarLinks = document.querySelectorAll('.sidebar-item a');
            allSidebarLinks.forEach(link => {
                link.addEventListener('mouseenter', function() {
                    if (document.body.classList.contains('sidebar-collapsed')) {
                        const menuText = this.querySelector('.menu-text').textContent;
                        this.setAttribute('data-title', menuText);
                    }
                });
            });
        }
        
        window.addEventListener('resize', function() {
            if (window.innerWidth <= 768) {
                document.body.classList.add('sidebar-collapsed');
            }
        });

        // Reports wiring
        const fmtPeso = v => '₱' + Number(v || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        function getDates() {
            const today = new Date();
            const to = document.getElementById('to');
            const from = document.getElementById('from');
            if (!to.value) to.valueAsDate = today;
            if (!from.value) {
                const d = new Date(); d.setDate(d.getDate() - 30); from.valueAsDate = d;
            }
            return { from: from.value, to: to.value };
        }

        async function loadSales() {
            const { from, to } = getDates();
            const res = await fetch(`api/reports.php?action=sales_summary&from=${encodeURIComponent(from)}&to=${encodeURIComponent(to)}`);
            const data = await res.json();
            const tbody = document.querySelector('#tblSales tbody');
            tbody.innerHTML = '';
            if (!data.success) return;
            
            // Update main KPIs
            document.getElementById('kpiGross').textContent = fmtPeso(data.summary.total_sales);
            document.getElementById('kpiNet').textContent = fmtPeso(data.summary.completed_sales);
            document.getElementById('kpiOrders').textContent = data.summary.total_orders;
            document.getElementById('kpiAov').textContent = fmtPeso(data.summary.average_order_value);
            
            // Update payment and delivery KPIs
            if (data.payment_summary) {
                document.getElementById('kpiVerifiedPayments').textContent = data.payment_summary.verified_payments;
                document.getElementById('kpiPendingPayments').textContent = data.payment_summary.pending_payments;
            }
            
            if (data.delivery_summary) {
                document.getElementById('kpiDeliveredOrders').textContent = data.delivery_summary.successful_deliveries;
                document.getElementById('kpiInTransit').textContent = data.delivery_summary.in_transit_deliveries;
            }
            
            // Empty state
            if (!data.rows || data.rows.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4"><i class="bi bi-graph-up" style="font-size: 2.5rem; display: block; margin-bottom: 0.5rem;"></i>No sales data for the selected period</td></tr>';
                document.getElementById('kpiGross').textContent = '₱0.00';
                document.getElementById('kpiNet').textContent = '₱0.00';
                document.getElementById('kpiOrders').textContent = '0';
                document.getElementById('kpiAov').textContent = '₱0.00';
                return;
            }
            
            let totalOrders = 0, totalGross = 0, totalNet = 0;
            data.rows.forEach(r => {
                totalOrders += Number(r.orders);
                totalGross += Number(r.gross);
                totalNet   += Number(r.net);
                const tr = document.createElement('tr');
                tr.innerHTML = `<td>${r.period}</td><td>${r.orders}</td><td>${r.units}</td><td>${fmtPeso(r.gross)}</td><td>${fmtPeso(r.net)}</td><td>${fmtPeso(r.aov)}</td>`;
                tbody.appendChild(tr);
            });
            // KPIs
            document.getElementById('kpiGross').textContent = fmtPeso(totalGross);
            document.getElementById('kpiNet').textContent = fmtPeso(totalNet);
            document.getElementById('kpiOrders').textContent = totalOrders.toString();
            document.getElementById('kpiAov').textContent = totalOrders ? fmtPeso(totalNet / totalOrders) : '₱0.00';
        }

        async function loadOrderStatus() {
            const { from, to } = getDates();
            const res = await fetch(`api/reports.php?action=order_status_breakdown&from=${encodeURIComponent(from)}&to=${encodeURIComponent(to)}`);
            const data = await res.json();
            const tbody = document.querySelector('#tblOrderStatus tbody');
            tbody.innerHTML = '';
            if (!data.ok) return;
            
            // Empty state
            if (!data.rows || data.rows.length === 0) {
                tbody.innerHTML = '<tr><td colspan="2" class="text-center text-muted py-4"><i class="bi bi-cart-check" style="font-size: 2.5rem; display: block; margin-bottom: 0.5rem;"></i>No orders for the selected period</td></tr>';
                return;
            }
            
            data.rows.forEach(r => {
                const tr = document.createElement('tr');
                tr.innerHTML = `<td>${r.status}</td><td>${r.count}</td>`;
                tbody.appendChild(tr);
            });
        }

        async function loadDelivery() {
            const { from, to } = getDates();
            const res = await fetch(`api/reports.php?action=delivery_kpis&from=${encodeURIComponent(from)}&to=${encodeURIComponent(to)}`);
            const data = await res.json();
            const tbody = document.querySelector('#tblDelivery tbody');
            tbody.innerHTML = '';
            if (!data.ok) return;
            const k = data.kpis || {};
            
            // Check if all values are zero (no delivery data)
            const hasData = (k.pending||0) + (k.dispatched||0) + (k.in_transit||0) + (k.delivered||0) + (k.failed||0) + (k.cancelled||0) > 0;
            
            if (!hasData) {
                tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4"><i class="bi bi-truck" style="font-size: 2.5rem; display: block; margin-bottom: 0.5rem;"></i>No delivery data for the selected period</td></tr>';
                return;
            }
            
            const tr = document.createElement('tr');
            tr.innerHTML = `<td>${k.pending||0}</td><td>${k.dispatched||0}</td><td>${k.in_transit||0}</td><td>${k.delivered||0}</td><td>${k.failed||0}</td><td>${k.cancelled||0}</td><td>${(k.on_time_rate||0)}%</td><td>${(Number(k.avg_fulfillment_days)||0).toFixed(1)}</td>`;
            tbody.appendChild(tr);
        }

        async function loadLowStock() {
            const res = await fetch('api/reports.php?action=inventory_low_stock');
            const data = await res.json();
            const tbody = document.querySelector('#tblLowStock tbody');
            tbody.innerHTML = '';
            if (!data.ok) return;
            
            // Empty state
            if (!data.rows || data.rows.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-4"><i class="bi bi-box-seam" style="font-size: 2.5rem; display: block; margin-bottom: 0.5rem;"></i>All products are well-stocked!</td></tr>';
                return;
            }
            
            data.rows.forEach(r => {
                const tr = document.createElement('tr');
                tr.innerHTML = `<td>${r.name}</td><td>${r.category_name||''}</td><td>${r.stock_quantity}</td><td>${r.min_stock_level}</td>`;
                tbody.appendChild(tr);
            });
        }

        async function loadConnectedData() {
            const res = await fetch('api/reports.php?action=connected_order_data');
            const data = await res.json();
            const tbody = document.querySelector('#tblConnectedData tbody');
            tbody.innerHTML = '';
            if (!data.success) return;
            
            // Empty state
            if (!data.connected_data || data.connected_data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4"><i class="bi bi-link-45deg" style="font-size: 2.5rem; display: block; margin-bottom: 0.5rem;"></i>No connected order data available</td></tr>';
                return;
            }
            
            data.connected_data.forEach(order => {
                const tr = document.createElement('tr');
                const orderDate = new Date(order.order_date).toLocaleDateString();
                const statusBadge = getStatusBadge(order.order_status);
                const paymentBadge = getPaymentBadge(order.payment_status);
                const deliveryBadge = getDeliveryBadge(order.delivery_status);
                
                tr.innerHTML = `
                    <td>#${order.id}</td>
                    <td>${order.customer_name || 'N/A'}</td>
                    <td>${fmtPeso(order.total_amount)}</td>
                    <td>${statusBadge}</td>
                    <td>${paymentBadge}</td>
                    <td>${deliveryBadge}</td>
                    <td>${order.delivery_fee ? '₱' + order.delivery_fee : 'N/A'}</td>
                    <td>${orderDate}</td>
                `;
                tbody.appendChild(tr);
            });
        }

        function getStatusBadge(status) {
            const badges = {
                'Processing': '<span class="badge bg-warning">Processing</span>',
                'Shipped': '<span class="badge bg-info">Shipped</span>',
                'Delivered': '<span class="badge bg-success">Delivered</span>',
                'Cancelled': '<span class="badge bg-danger">Cancelled</span>'
            };
            return badges[status] || `<span class="badge bg-secondary">${status}</span>`;
        }

        function getPaymentBadge(status) {
            const badges = {
                'verified': '<span class="badge bg-success">Verified</span>',
                'pending': '<span class="badge bg-warning">Pending</span>',
                'rejected': '<span class="badge bg-danger">Rejected</span>',
                'proof_submitted': '<span class="badge bg-info">Proof Submitted</span>'
            };
            return badges[status] || `<span class="badge bg-secondary">${status || 'N/A'}</span>`;
        }

        function getDeliveryBadge(status) {
            const badges = {
                'delivered': '<span class="badge bg-success">Delivered</span>',
                'in_transit': '<span class="badge bg-info">In Transit</span>',
                'pending_pickup': '<span class="badge bg-warning">Pending Pickup</span>',
                'failed': '<span class="badge bg-danger">Failed</span>'
            };
            return badges[status] || `<span class="badge bg-secondary">${status || 'N/A'}</span>`;
        }


        document.getElementById('reportFilters').addEventListener('submit', (e) => {
            e.preventDefault();
            loadSales(); loadOrderStatus(); loadDelivery(); loadConnectedData(); loadLowStock();
        });

        // CSV export of active tab's table
        document.getElementById('exportCsv').addEventListener('click', () => {
            const active = document.querySelector('.tab-pane.active table');
            if (!active) return;
            let csv = '';
            const rows = active.querySelectorAll('tr');
            rows.forEach(row => {
                const cols = Array.from(row.querySelectorAll('th,td')).map(td => '"' + (td.textContent || '').replace(/"/g,'""') + '"');
                csv += cols.join(',') + '\n';
            });
            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url; a.download = 'report.csv'; a.click(); URL.revokeObjectURL(url);
        });

        // Initial load
        loadSales(); loadOrderStatus(); loadDelivery(); loadConnectedData(); loadLowStock();
    </script>
</body>
</html>


