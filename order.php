<?php 
session_start();

// Include authentication
require_once 'includes/auth.php';

// Require authentication to access this page
requireAuth('login.php');

$currentPage = basename($_SERVER['PHP_SELF']); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management - Core Transaction 2</title>
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
        
        <style>
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
            
            /* Order Management specific styles */
            .page-title {
                color: #212529 !important;
                font-weight: 700;
                font-size: 2rem;
            }
            
            .page-subtitle {
                color: #6c757d !important;
                font-size: 1rem;
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
            <li class="sidebar-item has-submenu <?php if(in_array($currentPage, ['order.php'])) echo 'active'; ?>">
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
                        <h2 class="page-title">Order Management</h2>
                        <p class="page-subtitle">View and approve orders containing your products</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="order.php" class="btn btn-light">Reset</a>
                    <button id="openHistoryTop" type="button" class="btn btn-light">History</button>
                    </div>
                </div>
            </div>

            <!-- Order Management Tabs -->
            <ul class="nav nav-tabs mb-3" id="orderTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tab-confirmation" data-bs-toggle="tab" data-bs-target="#pane-confirmation" type="button" role="tab" style="color: #212529 !important;">
                        <i class="bi bi-clock-history me-1"></i>Order Confirmation
                        <span class="badge bg-warning ms-2" id="confirmationBadge">0</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-orders" data-bs-toggle="tab" data-bs-target="#pane-orders" type="button" role="tab" style="color: #212529 !important;">
                        <i class="bi bi-list-ul me-1"></i>Order List
                    </button>
                </li>
            </ul>

            <?php $status = $_GET['status'] ?? ''; $category = $_GET['category'] ?? ''; ?>

            <div class="tab-content">
                <!-- Order Confirmation Tab -->
                <div class="tab-pane fade show active" id="pane-confirmation" role="tabpanel" aria-labelledby="tab-confirmation">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h4 class="mb-1" style="color: #212529 !important; font-weight: 600;">New Orders Awaiting Confirmation</h4>
                            <p class="text-muted mb-0" style="color: #6c757d !important;">Review and confirm customer orders containing your products</p>
                        </div>
                        <div class="d-flex gap-2">
                            <div class="bulk-actions" id="confirmationBulkActions" style="display: none;">
                                <button class="btn btn-success btn-sm" id="bulkApproveBtn">
                                    <i class="bi bi-check-circle me-1"></i>Approve Selected
                                </button>
                                <button class="btn btn-danger btn-sm" id="bulkRejectBtn">
                                    <i class="bi bi-x-circle me-1"></i>Reject Selected
                                </button>
                        </div>
                        <button class="btn btn-light" id="refreshConfirmationBtn">
                            <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                        </button>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover mb-0" id="confirmationTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>
                                                <input type="checkbox" id="selectAllConfirmation" class="form-check-input">
                                            </th>
                                            <th>Order #</th>
                                            <th>Date</th>
                                            <th>Customer</th>
                                            <th>Category</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order List Tab -->
                <div class="tab-pane fade" id="pane-orders" role="tabpanel" aria-labelledby="tab-orders">
                    <form method="GET" class="row g-2 mb-3">
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">All Statuses</option>
                                <?php foreach (['Pending','Processing','Completed','Cancelled'] as $s): ?>
                                    <option value="<?php echo $s; ?>" <?php echo $status===$s?'selected':''; ?>><?php echo $s; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="category" class="form-select">
                                <option value="">All Categories</option>
                                <?php
                                // Optional: pull real categories if DB available
                                try {
                                    require_once 'config/database.php';
                                    $pdo = getDBConnection();
                                    $cats = $pdo->query("SELECT id, name FROM categories WHERE is_active=1 ORDER BY name")->fetchAll();
                                } catch (Throwable $e) { $cats = []; }
                                $fallbackCats = ['Jackets','Tees','Denim','Shorts','Tops','Dress','Jeans','Sweater','Skirts','Pants'];
                                $sourceCats = !empty($cats) ? array_map(fn($c)=>$c['name'],$cats) : $fallbackCats;
                                foreach ($sourceCats as $c): ?>
                                    <option value="<?php echo htmlspecialchars($c); ?>" <?php echo $category===$c?'selected':''; ?>><?php echo htmlspecialchars($c); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input name="q" class="form-control" placeholder="Search by Order #, customer, item" value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>">
                        </div>
                        <div class="col-md-2 d-grid">
                            <button class="btn btn-primary" type="submit"><i class="bi bi-search me-1"></i>Filter</button>
                        </div>
                    </form>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="bulk-actions" id="ordersBulkActions" style="display: none;">
                            <button class="btn btn-warning btn-sm" id="bulkSendToDeliveryBtn">
                                <i class="bi bi-truck me-1"></i>Send to Delivery
                            </button>
                            <button class="btn btn-info btn-sm" id="bulkCheckPaymentBtn">
                                <i class="bi bi-credit-card me-1"></i>Check Payment
                            </button>
                        </div>
                        <div class="text-muted">
                            <span id="selectedOrdersCount">0</span> orders selected
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover mb-0" id="ordersTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>
                                                <input type="checkbox" id="selectAllOrders" class="form-check-input">
                                            </th>
                                            <th>Order #</th>
                                            <th>Date</th>
                                            <th>Customer</th>
                                            <th>Category</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th>Payment Status</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cancelled Orders Tab -->
                <div class="tab-pane fade" id="pane-cancelled" role="tabpanel" aria-labelledby="tab-cancelled">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h4 class="mb-1" style="color: #212529 !important; font-weight: 600;">Cancelled Orders</h4>
                            <p class="text-muted mb-0" style="color: #6c757d !important;">View all cancelled orders and their reasons</p>
            </div>
                        <div class="d-flex gap-2">
                            <div class="bulk-actions" id="cancelledBulkActions" style="display: none;">
                                <button class="btn btn-info btn-sm" id="bulkViewCancelledBtn">
                                    <i class="bi bi-eye me-1"></i>View Selected
                                </button>
                            </div>
                            <button class="btn btn-light" id="refreshCancelledBtn">
                                <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                            </button>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover mb-0" id="cancelledTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>
                                                <input type="checkbox" id="selectAllCancelled" class="form-check-input">
                                            </th>
                                            <th>Order #</th>
                                            <th>Date</th>
                                            <th>Customer</th>
                                            <th>Category</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th>Reason</th>
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

    <!-- View Modal -->
    <div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Receipt</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
            <div id="orderDetails"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-dark" id="downloadReceiptBtn">Download</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
                        </div>
                        </div>
                    </div>

    <!-- History Modal -->
    <div class="modal fade" id="historyModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Order History</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <div class="text-muted small">Recent changes across orders</div>
              <div>
                <select id="historyLimit" class="form-select form-select-sm" style="width:auto;display:inline-block">
                  <option value="50">Last 50</option>
                  <option value="100" selected>Last 100</option>
                  <option value="150">Last 150</option>
                  <option value="200">Last 200</option>
                </select>
              </div>
            </div>
            <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                  <tr><th>When</th><th>Order #</th><th>Action</th><th>Actor</th><th>Details</th></tr>
                                </thead>
                <tbody id="historyRows"></tbody>
                            </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
            <h5 class="modal-title">Update Order</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
          <form id="editForm">
                <div class="modal-body">
              <input type="hidden" name="id" id="editId">
                        <div class="mb-3">
                <label class="form-label">Order Status</label>
                <select class="form-select" name="order_status" id="editOrderStatus" required>
                  <option>Pending</option>
                  <option>Processing</option>
                  <option>Completed</option>
                  <option>Cancelled</option>
                            </select>
                        </div>
                        <div class="mb-3">
                <label class="form-label">Payment Status</label>
                <select class="form-select" name="payment_status" id="editPaymentStatus" required>
                  <option>Unpaid</option>
                  <option>Paid</option>
                  <option>Refunded</option>
                </select>
              </div>
                        </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
        </div>
      </div>
    </div>

    <!-- Cancel Modal -->
    <div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Cancel Order</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form id="cancelForm">
            <div class="modal-body">
              <input type="hidden" name="id" id="cancelId">
              <div class="mb-3">
                <label class="form-label">Reason (optional)</label>
                <textarea class="form-control" name="reason" rows="3" placeholder="e.g., Customer request"></textarea>
              </div>
              <div class="alert alert-warning small">This will set the order status to Cancelled.</div>
                </div>
                <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-warning">Confirm Cancel</button>
                </div>
          </form>
            </div>
        </div>
    </div>

    <!-- Order Confirmation Modal -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Order Confirmation</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="alert alert-info">
              <i class="bi bi-info-circle me-2"></i>
              <strong>Order Confirmation Required</strong><br>
              Please review this order and choose to either confirm or reject it.
            </div>
            <div class="mb-3">
              <label class="form-label">Reason (optional)</label>
              <textarea class="form-control" id="confirmationReason" rows="3" placeholder="Add any notes about this decision..."></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-danger" id="rejectOrderBtn">
              <i class="bi bi-x-circle me-1"></i>Reject Order
            </button>
            <button type="button" class="btn btn-success" id="confirmOrderBtn">
              <i class="bi bi-check-circle me-1"></i>Confirm Order
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Payment Check Modal -->
    <div class="modal fade" id="paymentCheckModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Payment Verification</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6">
                <h6>Payment Details</h6>
                <div class="card">
                  <div class="card-body">
                    <div class="row mb-2">
                      <div class="col-4"><strong>Order ID:</strong></div>
                      <div class="col-8" id="paymentOrderId">#12345</div>
                    </div>
                    <div class="row mb-2">
                      <div class="col-4"><strong>Amount:</strong></div>
                      <div class="col-8" id="paymentAmount">₱1,500.00</div>
                    </div>
                    <div class="row mb-2">
                      <div class="col-4"><strong>Method:</strong></div>
                      <div class="col-8" id="paymentMethod">Bank Transfer</div>
                    </div>
                    <div class="row mb-2">
                      <div class="col-4"><strong>Status:</strong></div>
                      <div class="col-8">
                        <span class="badge bg-warning" id="paymentStatus">Pending Verification</span>
                      </div>
                    </div>
                    <div class="row mb-2">
                      <div class="col-4"><strong>Reference:</strong></div>
                      <div class="col-8" id="paymentReference">TXN123456789</div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <h6>Verification</h6>
                <div class="card">
                  <div class="card-body">
                    <div class="mb-3">
                      <label class="form-label">Transfer Reference Number</label>
                      <input type="text" class="form-control" id="transferReference" placeholder="Enter reference number">
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Amount Received</label>
                      <input type="number" class="form-control" id="amountReceived" placeholder="Enter amount">
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Bank Account</label>
                      <select class="form-select" id="bankAccount">
                        <option value="">Select bank account...</option>
                        <option value="BPI">BPI - 1234567890</option>
                        <option value="BDO">BDO - 0987654321</option>
                        <option value="Metrobank">Metrobank - 1122334455</option>
                      </select>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Notes (optional)</label>
                      <textarea class="form-control" id="paymentNotes" rows="2" placeholder="Additional notes..."></textarea>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-danger" id="rejectPaymentBtn">
              <i class="bi bi-x-circle me-1"></i>Reject Payment
            </button>
            <button type="button" class="btn btn-success" id="verifyPaymentBtn">
              <i class="bi bi-check-circle me-1"></i>Verify Payment
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Courier Selection Modal -->
    <div class="modal fade" id="courierSelectionModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Select Courier for Delivery</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="alert alert-info">
              <i class="bi bi-truck me-2"></i>
              <strong>Choose Delivery Method</strong><br>
              Select the courier service for this order delivery.
            </div>
            <div class="mb-3">
              <label class="form-label">Courier Service</label>
              <select class="form-select" id="selectedCourier" required>
                <option value="">Select courier...</option>
                <option value="J&T Express">J&T Express - ₱50.00</option>
                <option value="LBC Express">LBC Express - ₱60.00</option>
                <option value="Flash Express">Flash Express - ₱45.00</option>
                <option value="Grab Express">Grab Express - ₱80.00</option>
                <option value="Lalamove">Lalamove - ₱70.00</option>
                <option value="In-house">In-house Delivery - ₱30.00</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Delivery Notes (optional)</label>
              <textarea class="form-control" id="courierNotes" rows="3" placeholder="Add any special delivery instructions..."></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label">Expected Delivery Date</label>
              <input type="date" class="form-control" id="expectedDelivery" min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" id="confirmCourierBtn">
              <i class="bi bi-truck me-1"></i>Proceed to Delivery Management
            </button>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.1/build/qrcode.min.js"></script>
    
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
        
        // Handle direct navigation links (Dashboard, etc.)
        directLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                // Allow normal navigation for direct links
                // No preventDefault needed - let the link work normally
                
                // Add tooltip data for collapsed state
                if (!this.hasAttribute('data-title')) {
                    const menuText = this.querySelector('.menu-text').textContent;
                    this.setAttribute('data-title', menuText);
                }
            });
        });
        
        // Handle submenu items
        menuItems.forEach(item => {
            const mainLink = item.querySelector('a');
            const submenuLinks = item.querySelectorAll('.submenu a');
                
            // Handle main menu item click (for submenu toggle)
            mainLink.addEventListener('click', function(e) {
                // Add tooltip data for collapsed state
                if (!this.hasAttribute('data-title')) {
                    const menuText = this.querySelector('.menu-text').textContent;
                    this.setAttribute('data-title', menuText);
                }
                
                // If sidebar is collapsed, don't prevent navigation
                if (document.body.classList.contains('sidebar-collapsed')) {
                    // In collapsed state, allow the link to work normally
                    // This enables direct navigation to the main module page
                    return;
                }
                
                // In expanded state, toggle submenu
                e.preventDefault();
                    item.classList.toggle('active');
                    
                    // Close other open menus
                    menuItems.forEach(otherItem => {
                        if (otherItem !== item && otherItem.classList.contains('active')) {
                            otherItem.classList.remove('active');
                        }
                    });
            });
            
            // Handle submenu item clicks
            submenuLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    // Allow normal navigation for submenu links
                    // No preventDefault needed - let the link work normally
                    
                    // Add tooltip data for collapsed state
                    if (!this.hasAttribute('data-title')) {
                        const menuText = this.querySelector('.menu-text').textContent;
                        this.setAttribute('data-title', menuText);
                    }
                });
            });
        });
        
        // Enhanced tooltip functionality for collapsed state
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
        
        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth <= 768) {
                // Auto-collapse on mobile
                document.body.classList.add('sidebar-collapsed');
            }
        });

        // Orders table: fetch and render from API
        const tbody = document.querySelector('#ordersTable tbody');
        const confirmationTbody = document.querySelector('#confirmationTable tbody');
        const cancelledTbody = document.querySelector('#cancelledTable tbody');
        const confirmationBadge = document.getElementById('confirmationBadge');
        const cancelledBadge = document.getElementById('cancelledBadge');
        const params = new URLSearchParams(window.location.search);
        let currentConfirmationOrderId = null;
        let currentDeliveryOrderId = null;

        async function loadOrders() {
            const qs = new URLSearchParams({
                action: 'list',
                status: params.get('status') || '',
                category: params.get('category') || '',
                q: params.get('q') || '',
                exclude_cancelled: '1' // Exclude cancelled orders from main list
            });
        const res = await fetch('api/orders.php?' + qs.toString());
        const data = await res.json();
        tbody.innerHTML = '';
        if (!data.success) return;
        
        // Filter out cancelled orders and only show approved orders (Processing, Shipped, Delivered)
        const approvedOrders = data.orders.filter(order => 
            order.status !== 'Cancelled' && 
            order.status !== 'Just Placed' &&
            (order.status === 'Processing' || order.status === 'Shipped' || order.status === 'Delivered')
        );
        
        // Show empty state if no orders
        if (!approvedOrders || approvedOrders.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-5"><i class="bi bi-inbox" style="font-size: 3rem; display: block; margin-bottom: 1rem;"></i><h5>No approved orders</h5><p>Approved orders will appear here once you confirm them from the Order Confirmation tab.</p></td></tr>';
            return;
        }
        
		for (const o of approvedOrders) {
			const badgeClass = o.status === 'Delivered' ? 'success' : (o.status === 'Processing' ? 'warning' : (o.status === 'Cancelled' ? 'danger' : 'secondary'));
			const tr = document.createElement('tr');
			
			// No approve buttons in main order list (only in confirmation tab)
			const showApprove = false;
			// Show Send to Delivery button only for Processing orders
			const showDelivery = o.status === 'Processing';
			// Show Check button for Processing orders (to proceed to delivery management)
			const showCheck = o.status === 'Processing';
			
			// Get payment status
			const paymentStatus = o.payment_status || 'Pending';
			const paymentBadgeClass = paymentStatus === 'Captured' ? 'success' : (paymentStatus === 'Failed' ? 'danger' : 'warning');
			const paymentStatusText = paymentStatus === 'Captured' ? 'Verified' : (paymentStatus === 'Failed' ? 'Rejected' : paymentStatus);
			
			tr.innerHTML = `
				<td><input type="checkbox" class="form-check-input order-checkbox" data-order-id="${o.id}"></td>
				<td>#${o.id}</td>
				<td>${new Date(o.created_at).toLocaleDateString()}</td>
				<td>${o.customer_name || ''}</td>
				<td>General</td>
				<td>₱${Number(o.total_amount||0).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2})}</td>
				<td><span class="badge bg-${badgeClass}">${o.status}</span></td>
				<td><span class="badge bg-${paymentBadgeClass}">${paymentStatusText}</span></td>
				<td class="text-end">
					<button class="btn btn-sm btn-outline-primary me-1" data-action="view" data-id="${o.id}" title="View Order"><i class="bi bi-eye"></i></button>
					<button class="btn btn-sm btn-outline-info me-1" data-action="view-payment" data-id="${o.id}" title="View Payment"><i class="bi bi-credit-card"></i></button>
					${showApprove ? '<button class="btn btn-sm btn-outline-success me-1" data-action="approve" data-id="'+o.id+'" title="Approve Order"><i class="bi bi-check-circle"></i> Approve</button>' : ''}
					${showApprove ? '<button class="btn btn-sm btn-outline-danger me-1" data-action="reject" data-id="'+o.id+'" title="Reject Order"><i class="bi bi-x-circle"></i> Reject</button>' : ''}
					${showCheck ? '<button class="btn btn-sm btn-outline-warning me-1" data-action="check-delivery" data-id="'+o.id+'" title="Check for Delivery"><i class="bi bi-check-square"></i></button>' : ''}
					${showDelivery ? '<button class="btn btn-sm btn-outline-info" data-action="send-to-delivery" data-id="'+o.id+'" title="Proceed to Delivery Management"><i class="bi bi-truck"></i> Delivery</button>' : ''}
				</td>`;
			tbody.appendChild(tr);
		}
        }

        // Load confirmation orders (new orders awaiting confirmation)
        async function loadConfirmationOrders() {
            const qs = new URLSearchParams({
                action: 'list',
                status: 'Just Placed', // Only show newly placed orders
                category: '',
                q: ''
            });
            const res = await fetch('api/orders.php?' + qs.toString());
            const data = await res.json();
            confirmationTbody.innerHTML = '';
            
            if (!data.success) {
                confirmationBadge.textContent = '0';
                return;
            }
            
            // Update badge count
            const confirmationCount = data.orders ? data.orders.length : 0;
            confirmationBadge.textContent = confirmationCount;
            
            // Show empty state if no orders
            if (!data.orders || data.orders.length === 0) {
                confirmationTbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-5"><i class="bi bi-check-circle" style="font-size: 3rem; display: block; margin-bottom: 1rem; color: #28a745;"></i><h5>All caught up!</h5><p>No new orders awaiting confirmation.</p></td></tr>';
                return;
            }
            
            for (const o of data.orders) {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><input type="checkbox" class="form-check-input confirmation-checkbox" data-order-id="${o.id}"></td>
                    <td>#${o.id}</td>
                    <td>${o.order_date || ''}</td>
                    <td>${o.customer_name || ''}</td>
                    <td>${o.category_label || ''}</td>
                    <td>₱${Number(o.total_amount||0).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2})}</td>
                    <td><span class="badge bg-warning">Just Placed</span></td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-outline-primary me-1" data-action="view" data-id="${o.id}" title="View Order"><i class="bi bi-eye"></i></button>
                        <button class="btn btn-sm btn-outline-success me-1" data-action="approve" data-id="${o.id}" title="Approve Order"><i class="bi bi-check-circle"></i> Approve</button>
                        <button class="btn btn-sm btn-outline-danger me-1" data-action="reject" data-id="${o.id}" title="Reject Order"><i class="bi bi-x-circle"></i> Reject</button>
                    </td>`;
                confirmationTbody.appendChild(tr);
            }
        }

        // Load cancelled orders
        async function loadCancelledOrders() {
            const qs = new URLSearchParams({
                action: 'list',
                status: 'Cancelled',
                category: '',
                q: ''
            });
            const res = await fetch('api/orders.php?' + qs.toString());
            const data = await res.json();
            cancelledTbody.innerHTML = '';
            
            if (!data.success) {
                cancelledBadge.textContent = '0';
                return;
            }
            
            // Update badge count
            const cancelledCount = data.orders ? data.orders.length : 0;
            cancelledBadge.textContent = cancelledCount;
            
            // Show empty state if no orders
            if (!data.orders || data.orders.length === 0) {
                cancelledTbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-5"><i class="bi bi-x-circle" style="font-size: 3rem; display: block; margin-bottom: 1rem; color: #dc3545;"></i><h5>No cancelled orders</h5><p>All orders are active and processing normally.</p></td></tr>';
                return;
            }
            
            for (const o of data.orders) {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><input type="checkbox" class="form-check-input cancelled-checkbox" data-order-id="${o.id}"></td>
                    <td>#${o.id}</td>
                    <td>${o.order_date || ''}</td>
                    <td>${o.customer_name || ''}</td>
                    <td>${o.category_label || ''}</td>
                    <td>₱${Number(o.total_amount||0).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2})}</td>
                    <td><span class="badge bg-danger">Cancelled</span></td>
                    <td>${o.cancel_reason || 'No reason provided'}</td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-outline-primary me-1" data-action="view" data-id="${o.id}" title="View Order"><i class="bi bi-eye"></i></button>
                    </td>`;
                cancelledTbody.appendChild(tr);
            }
        }

        // Load initial data
        loadOrders();
        loadConfirmationOrders();
        loadCancelledOrders();

        // View
        const viewModal = new bootstrap.Modal(document.getElementById('viewModal'));
        async function openView(id) {
            const res = await fetch('api/orders.php?action=view&id=' + id);
            const data = await res.json();
            if (!data.ok) return;
            const d = data.order;
            const toMoney = (n) => '₱' + Number(n||0).toLocaleString(undefined,{minimumFractionDigits:2, maximumFractionDigits:2});
            const dateStr = (d.order_date || '').toString().slice(0,19).replace('T',' ');
            let itemsHtml = '';
            for (const it of data.items) {
                itemsHtml += `
                    <div style="display:flex;justify-content:space-between;gap:10px;margin:10px 0;font-size:16px">
                        <div style="flex:1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${it.quantity}x ${it.product_name}</div>
                        <div style="min-width:110px;text-align:right">${toMoney(it.line_total)}</div>
                    </div>`;
            }

            const total = Number(data.total||0);

            const receiptHtml = `
                <div id="receiptContainer" style="width:360px;margin:0 auto;background:#ffffff;color:#000;padding:20px 18px;font-family:system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;">
                    <div style="text-align:center;margin-bottom:6px">
                        <div style="font-family:'Great Vibes', cursive; font-size:28px; font-weight:400; letter-spacing:2px;">RAEVOR</div>
                    </div>
                    <div style="border-top:3px dashed #555;border-bottom:3px dashed #555;height:0;margin:4px 0 16px 0"></div>
                    <div style="text-align:center;font-weight:800;font-size:36px;letter-spacing:2px;margin:6px 0 10px">RECEIPT</div>
                    <div style="border-top:3px dashed #555;border-bottom:3px dashed #555;height:0;margin:6px 0 16px 0"></div>

                    <div style="font-size:13px;color:#333;text-align:center;margin-bottom:10px">Order #${d.id} • ${dateStr}<br/>${d.customer_name}</div>

                    ${itemsHtml}

                    <div style="border-top:2px dashed #bbb;margin:10px 0 14px"></div>
                    <div style="display:flex;justify-content:space-between;font-weight:800;font-size:16px;margin:6px 0">
                        <div>TOTAL AMOUNT</div>
                        <div>${toMoney(total)}</div>
                    </div>
                    <div style="border-top:2px dashed #bbb;margin:10px 0 14px"></div>

                    <div style="display:flex;justify-content:space-between;margin:6px 0;font-size:16px"><div>CASH</div><div>${toMoney(total)}</div></div>
                    <div style="display:flex;justify-content:space-between;margin:6px 0;font-size:16px"><div>CHANGE</div><div>${toMoney(0)}</div></div>

                    <div style="border-top:2px dashed #bbb;margin:14px 0 18px"></div>
                    <div style="text-align:center;font-weight:800;font-size:28px;margin:8px 0 10px">THANK YOU</div>
                    <div style="border-top:2px dashed #bbb;margin:8px 0 14px"></div>
                    <div style="display:flex;align-items:center;justify-content:space-between;gap:12px">
                        <canvas id="rcBarcode" style="flex:1;height:90px"></canvas>
                        <canvas id="rcQR" width="96" height="96" style="width:96px;height:96px"></canvas>
                    </div>
                </div>`;

            document.getElementById('orderDetails').innerHTML = receiptHtml;

            // Generate barcode and QR
            try { JsBarcode('#rcBarcode', 'ORD-' + d.id, { format: 'CODE128', background: '#ffffff', lineColor: '#000000', width: 2, height: 80, displayValue: false, margin: 0 }); } catch (_) {}
            try { QRCode.toCanvas(document.getElementById('rcQR'), 'ORD-' + d.id + ' | ' + dateStr, { width: 96, margin: 1, color: { dark: '#000000', light: '#ffffff' } }, function(){}); } catch (_) {}

            // Bind download handler
            const dlBtn = document.getElementById('downloadReceiptBtn');
            if (dlBtn) {
                dlBtn.onclick = async () => {
                    const node = document.getElementById('receiptContainer');
                    if (!node) return;
                    const canvas = await html2canvas(node, { scale: 2, backgroundColor: '#ffffff' });
                    const imgData = canvas.toDataURL('image/png');
                    const { jsPDF } = window.jspdf;
                    const pdf = new jsPDF({ orientation: 'portrait', unit: 'pt', format: [canvas.width, canvas.height] });
                    pdf.addImage(imgData, 'PNG', 0, 0, canvas.width, canvas.height);
                    pdf.save(`receipt_${d.id}.pdf`);
                };
            }

            viewModal.show();
        }

        // Comprehensive Invoice View
        async function openInvoiceView(id) {
            const res = await fetch('api/orders.php?action=view&id=' + id);
            const data = await res.json();
            if (!data.ok) return;
            
            const d = data.order;
            const toMoney = (n) => '₱' + Number(n||0).toLocaleString(undefined,{minimumFractionDigits:2, maximumFractionDigits:2});
            const dateStr = (d.order_date || '').toString().slice(0,19).replace('T',' ');
            
            // Calculate VAT (12% of subtotal)
            const subtotal = Number(data.total || 0);
            const vatRate = 0.12;
            const vatAmount = subtotal * vatRate;
            const totalWithVat = subtotal + vatAmount;
            
            // Sample voucher and courier data
            const voucherCode = d.voucher_code || 'N/A';
            const voucherDiscount = d.voucher_discount || 0;
            const courier = d.courier || 'J&T Express';
            const shippingFee = d.shipping_fee || 50;
            
            let itemsHtml = '';
            for (const it of data.items) {
                const itemSubtotal = Number(it.line_total || 0);
                const itemVat = itemSubtotal * vatRate;
                const itemTotal = itemSubtotal + itemVat;
                
                itemsHtml += `
                    <tr>
                        <td>${it.product_name}</td>
                        <td class="text-center">${it.quantity}</td>
                        <td class="text-end">${toMoney(it.unit_price || 0)}</td>
                        <td class="text-end">${toMoney(itemSubtotal)}</td>
                        <td class="text-end">${toMoney(itemVat)}</td>
                        <td class="text-end"><strong>${toMoney(itemTotal)}</strong></td>
                    </tr>`;
            }

            const invoiceHtml = `
                <div id="invoiceContainer" style="max-width:800px;margin:0 auto;background:#ffffff;color:#000;padding:30px;font-family:system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;box-shadow:0 0 20px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <div style="text-align:center;margin-bottom:30px;border-bottom:3px solid #007bff;padding-bottom:20px;">
                        <div style="font-family:'Great Vibes', cursive; font-size:36px; font-weight:400; letter-spacing:3px;color:#007bff;">RAEVOR</div>
                        <div style="font-size:14px;color:#666;margin-top:5px;">E-Commerce Platform</div>
                        <div style="font-size:24px;font-weight:bold;margin-top:15px;color:#333;">PURCHASE INVOICE</div>
                    </div>

                    <!-- Invoice Details -->
                    <div style="display:flex;justify-content:space-between;margin-bottom:30px;">
                        <div style="flex:1;">
                            <h4 style="color:#333;margin-bottom:15px;">Bill To:</h4>
                            <div style="background:#f8f9fa;padding:15px;border-radius:5px;">
                                <div style="font-weight:bold;font-size:16px;">${d.customer_name || 'Customer Name'}</div>
                                <div style="color:#666;margin-top:5px;">${d.customer_email || 'customer@email.com'}</div>
                                <div style="color:#666;">${d.customer_phone || '+63 912 345 6789'}</div>
                                <div style="color:#666;margin-top:5px;">${d.shipping_address || '123 Main Street, Manila, Philippines'}</div>
                            </div>
                        </div>
                        <div style="flex:1;text-align:right;">
                            <h4 style="color:#333;margin-bottom:15px;">Invoice Details:</h4>
                            <div style="background:#f8f9fa;padding:15px;border-radius:5px;">
                                <div><strong>Invoice #:</strong> INV-${d.id}</div>
                                <div><strong>Order #:</strong> ORD-${d.id}</div>
                                <div><strong>Date:</strong> ${dateStr}</div>
                                <div><strong>Status:</strong> <span style="color:#28a745;font-weight:bold;">${d.status || 'Processing'}</span></div>
                            </div>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div style="margin-bottom:30px;">
                        <h4 style="color:#333;margin-bottom:15px;">Items Purchased:</h4>
                        <table style="width:100%;border-collapse:collapse;border:1px solid #ddd;">
                            <thead style="background:#007bff;color:white;">
                                <tr>
                                    <th style="padding:12px;text-align:left;">Product</th>
                                    <th style="padding:12px;text-align:center;">Qty</th>
                                    <th style="padding:12px;text-align:right;">Unit Price</th>
                                    <th style="padding:12px;text-align:right;">Subtotal</th>
                                    <th style="padding:12px;text-align:right;">VAT (12%)</th>
                                    <th style="padding:12px;text-align:right;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${itemsHtml}
                            </tbody>
                        </table>
                    </div>

                    <!-- Summary -->
                    <div style="display:flex;justify-content:space-between;margin-bottom:30px;">
                        <div style="flex:1;">
                            <h4 style="color:#333;margin-bottom:15px;">Delivery Information:</h4>
                            <div style="background:#f8f9fa;padding:15px;border-radius:5px;">
                                <div><strong>Courier:</strong> ${courier}</div>
                                <div><strong>Shipping Fee:</strong> ${toMoney(shippingFee)}</div>
                                <div><strong>Delivery Address:</strong> ${d.shipping_address || '123 Main Street, Manila, Philippines'}</div>
                                <div><strong>Expected Delivery:</strong> ${new Date(Date.now() + 3*24*60*60*1000).toLocaleDateString()}</div>
                            </div>
                        </div>
                        <div style="flex:1;text-align:right;">
                            <h4 style="color:#333;margin-bottom:15px;">Payment Summary:</h4>
                            <div style="background:#f8f9fa;padding:15px;border-radius:5px;">
                                <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
                                    <span>Subtotal:</span>
                                    <span>${toMoney(subtotal)}</span>
                                </div>
                                <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
                                    <span>VAT (12%):</span>
                                    <span>${toMoney(vatAmount)}</span>
                                </div>
                                <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
                                    <span>Shipping Fee:</span>
                                    <span>${toMoney(shippingFee)}</span>
                                </div>
                                ${voucherCode !== 'N/A' ? `
                                <div style="display:flex;justify-content:space-between;margin-bottom:8px;color:#28a745;">
                                    <span>Voucher (${voucherCode}):</span>
                                    <span>-${toMoney(voucherDiscount)}</span>
                                </div>` : ''}
                                <div style="border-top:2px solid #007bff;padding-top:8px;margin-top:8px;">
                                    <div style="display:flex;justify-content:space-between;font-weight:bold;font-size:18px;color:#007bff;">
                                        <span>TOTAL:</span>
                                        <span>${toMoney(totalWithVat + shippingFee - voucherDiscount)}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div style="text-align:center;margin-top:30px;padding-top:20px;border-top:2px solid #007bff;">
                        <div style="font-size:14px;color:#666;">
                            Thank you for your business!<br>
                            For inquiries, contact us at support@raevor.com
                        </div>
                        <div style="margin-top:15px;">
                            <canvas id="invoiceBarcode" style="height:60px;margin-right:20px;"></canvas>
                            <canvas id="invoiceQR" width="80" height="80" style="width:80px;height:80px;"></canvas>
                        </div>
                    </div>
                </div>`;

            document.getElementById('orderDetails').innerHTML = invoiceHtml;

            // Generate barcode and QR for invoice
            try { 
                JsBarcode('#invoiceBarcode', 'INV-' + d.id, { 
                    format: 'CODE128', 
                    background: '#ffffff', 
                    lineColor: '#000000', 
                    width: 2, 
                    height: 60, 
                    displayValue: true,
                    margin: 5
                }); 
            } catch (_) {}
            
            try { 
                QRCode.toCanvas(document.getElementById('invoiceQR'), 'INV-' + d.id + ' | ' + dateStr, { 
                    width: 80, 
                    margin: 1, 
                    color: { dark: '#000000', light: '#ffffff' } 
                }, function(){}); 
            } catch (_) {}

            // Bind download handler
            const dlBtn = document.getElementById('downloadReceiptBtn');
            if (dlBtn) {
                dlBtn.onclick = async () => {
                    const node = document.getElementById('invoiceContainer');
                    if (!node) return;
                    const canvas = await html2canvas(node, { scale: 2, backgroundColor: '#ffffff' });
                    const imgData = canvas.toDataURL('image/png');
                    const { jsPDF } = window.jspdf;
                    const pdf = new jsPDF({ orientation: 'portrait', unit: 'pt', format: [canvas.width, canvas.height] });
                    pdf.addImage(imgData, 'PNG', 0, 0, canvas.width, canvas.height);
                    pdf.save(`invoice_${d.id}.pdf`);
                };
            }

            viewModal.show();
        }

        // Edit
        const editModal = new bootstrap.Modal(document.getElementById('editModal'));
        const historyModal = new bootstrap.Modal(document.getElementById('historyModal'));
        const cancelModal = new bootstrap.Modal(document.getElementById('cancelModal'));
        function openEdit(id, currentStatus, currentPayment) {
            document.getElementById('editId').value = id;
            document.getElementById('editOrderStatus').value = currentStatus;
            document.getElementById('editPaymentStatus').value = currentPayment || 'Unpaid';
            editModal.show();
        }
        document.getElementById('editForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const fd = new FormData(e.target);
            fd.append('action','update');
            const res = await fetch('api/orders.php', { method: 'POST', body: fd });
            const data = await res.json();
            if (data.ok) { editModal.hide(); loadOrders(); }
        });

        // History
        async function openHistory(id) {
            const tbodyH = document.getElementById('historyRows');
            if (tbodyH) {
                tbodyH.innerHTML = '<tr><td colspan="5" class="text-muted text-center">Loading...</td></tr>';
            }
            const limitEl = document.getElementById('historyLimit');
            const limitVal = limitEl && limitEl.value ? limitEl.value : 100;
            const url = id ? ('api/orders.php?action=history&id=' + encodeURIComponent(id))
                           : ('api/orders.php?action=history&limit=' + encodeURIComponent(limitVal));
            try {
                const res = await fetch(url);
                const data = await res.json();
                if (!data.ok) throw new Error(data.error || 'Failed to load');
                if (tbodyH) tbodyH.innerHTML = '';
                if (!data.history || data.history.length === 0) {
                    if (tbodyH) tbodyH.innerHTML = '<tr><td colspan="5" class="text-muted text-center">No history yet.</td></tr>';
                } else {
                    for (const h of data.history) {
                        let details = '';
                        try {
                            const oldV = h.old_value ? JSON.parse(h.old_value) : null;
                            const newV = h.new_value ? JSON.parse(h.new_value) : null;
                            if (oldV && newV && oldV.status !== undefined && newV.status !== undefined) {
                                details = `Status: ${oldV.status} → ${newV.status}`;
                            } else {
                                details = h.note || '';
                            }
                        } catch (_) { details = h.note || ''; }
                        const tr = document.createElement('tr');
                        tr.innerHTML = `<td>${h.created_at}</td><td>#${h.order_id || ''}</td><td>${h.action}</td><td>${h.actor||''}</td><td>${details}</td>`;
                        if (tbodyH) tbodyH.appendChild(tr);
                    }
                }
            } catch (e) {
                if (tbodyH) tbodyH.innerHTML = '<tr><td colspan="5" class="text-danger text-center">Error loading history</td></tr>';
            }
            historyModal.show();
        }

        // Delete
        async function doDelete(id) {
            if (!confirm('Delete this order? This cannot be undone.')) return;
            const fd = new FormData();
            fd.append('action','delete');
            fd.append('id', id);
            const res = await fetch('api/orders.php', { method: 'POST', body: fd });
            const data = await res.json();
            if (data.ok) { loadOrders(); }
        }

	// Actions delegation for main orders table
	tbody.addEventListener('click', async (e) => {
		const btn = e.target.closest('button[data-action]');
		if (!btn) return;
		const id = btn.getAttribute('data-id');
		const action = btn.getAttribute('data-action');
		if (action === 'view') {
			openInvoiceView(id);
		} else if (action === 'view-payment') {
			openPaymentCheck(id);
		} else if (action === 'approve') {
			if (!confirm('Approve this order and move it to Processing?')) return;
			const fd = new FormData();
			fd.append('action', 'approve_order');
			fd.append('id', id);
			const res = await fetch('api/orders.php', { method: 'POST', body: fd });
			const data = await res.json();
			if (data.success) {
				alert('Order approved successfully!');
				loadOrders();
			} else {
				alert('Error: ' + (data.message || 'Failed to approve order'));
			}
		} else if (action === 'reject') {
			const reason = prompt('Enter rejection reason:');
			if (!reason) return;
			const fd = new FormData();
			fd.append('action', 'reject_order');
			fd.append('id', id);
			fd.append('reason', reason);
			const res = await fetch('api/orders.php', { method: 'POST', body: fd });
			const data = await res.json();
			if (data.success) {
				alert('Order rejected successfully!');
				loadOrders();
			} else {
				alert('Error: ' + (data.message || 'Failed to reject order'));
			}
		} else if (action === 'edit') {
			// fetch current to prefill
			const res = await fetch('api/orders.php?action=view&id=' + id);
			const data = await res.json();
			if (data.ok) openEdit(id, data.order.order_status, data.order.payment_status);
		} else if (action === 'cancel') {
			document.getElementById('cancelId').value = id;
			cancelModal.show();
		} else if (action === 'delete') {
			doDelete(id);
		} else if (action === 'send-to-delivery') {
			openCourierSelection(id);
		} else if (action === 'check-delivery') {
			// Navigate to delivery management page for this order
			window.location.href = `delivery.php?order_id=${id}&status=Pending`;
		}
	});

	// Actions delegation for confirmation table
	confirmationTbody.addEventListener('click', async (e) => {
		const btn = e.target.closest('button[data-action]');
		if (!btn) return;
		const id = btn.getAttribute('data-id');
		const action = btn.getAttribute('data-action');
		
		if (action === 'view') {
			openInvoiceView(id);
		} else if (action === 'approve') {
			if (!confirm('Approve this order and move it to Order List?')) return;
			const fd = new FormData();
			fd.append('action', 'approve_order');
			fd.append('id', id);
			const res = await fetch('api/orders.php', { method: 'POST', body: fd });
			const data = await res.json();
			if (data.success) {
				alert('Order approved successfully! It will now appear in the Order List.');
				loadConfirmationOrders(); // Refresh confirmation tab
				loadOrders(); // Refresh main orders tab
			} else {
				alert('Error: ' + (data.message || 'Failed to approve order'));
			}
		} else if (action === 'reject') {
			const reason = prompt('Enter rejection reason:');
			if (!reason) return;
			const fd = new FormData();
			fd.append('action', 'reject_order');
			fd.append('id', id);
			fd.append('reason', reason);
			const res = await fetch('api/orders.php', { method: 'POST', body: fd });
			const data = await res.json();
			if (data.success) {
				alert('Order rejected successfully! It will now appear in Cancelled Orders.');
				loadConfirmationOrders(); // Refresh confirmation tab
				loadCancelledOrders(); // Refresh cancelled orders tab
			} else {
				alert('Error: ' + (data.message || 'Failed to reject order'));
			}
		}
	});

	// Actions delegation for cancelled orders table
	cancelledTbody.addEventListener('click', async (e) => {
		const btn = e.target.closest('button[data-action]');
		if (!btn) return;
		const id = btn.getAttribute('data-id');
		const action = btn.getAttribute('data-action');
		
		if (action === 'view') {
			openInvoiceView(id);
		}
	});

        // Top history button
        const openHistBtn = document.getElementById('openHistoryTop');
        if (openHistBtn) openHistBtn.addEventListener('click', () => openHistory(null));
        const limitSelect = document.getElementById('historyLimit');
        if (limitSelect) limitSelect.addEventListener('change', () => openHistory(null));

        // Cancel submit
        document.getElementById('cancelForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const fd = new FormData(e.target);
            fd.append('action','cancel');
            const res = await fetch('api/orders.php', { method: 'POST', body: fd });
            const data = await res.json();
            if (data.ok) { cancelModal.hide(); loadOrders(); }
        });

        // Confirmation modal handlers
        document.getElementById('confirmOrderBtn').addEventListener('click', async () => {
            if (!currentConfirmationOrderId) return;
            
            const reason = document.getElementById('confirmationReason').value;
            const fd = new FormData();
            fd.append('action', 'confirm_order');
            fd.append('id', currentConfirmationOrderId);
            fd.append('reason', reason);
            
            try {
                const res = await fetch('api/orders.php', { method: 'POST', body: fd });
                const data = await res.json();
                
                if (data.ok) {
                    alert('Order confirmed successfully! It will now appear in the Order List.');
                    bootstrap.Modal.getInstance(document.getElementById('confirmationModal')).hide();
                    loadConfirmationOrders(); // Refresh confirmation tab
                    loadOrders(); // Refresh main orders tab
                    document.getElementById('confirmationReason').value = ''; // Clear reason
                } else {
                    alert('Error: ' + (data.error || 'Failed to confirm order'));
                }
            } catch (error) {
                alert('Error confirming order: ' + error.message);
            }
        });

        document.getElementById('rejectOrderBtn').addEventListener('click', async () => {
            if (!currentConfirmationOrderId) return;
            
            const reason = document.getElementById('confirmationReason').value;
            if (!reason.trim()) {
                alert('Please provide a reason for rejecting this order.');
                return;
            }
            
            const fd = new FormData();
            fd.append('action', 'reject_order');
            fd.append('id', currentConfirmationOrderId);
            fd.append('reason', reason);
            
            try {
                const res = await fetch('api/orders.php', { method: 'POST', body: fd });
                const data = await res.json();
                
                if (data.ok) {
                    alert('Order rejected successfully!');
                    bootstrap.Modal.getInstance(document.getElementById('confirmationModal')).hide();
                    loadConfirmationOrders(); // Refresh confirmation tab
                    document.getElementById('confirmationReason').value = ''; // Clear reason
                } else {
                    alert('Error: ' + (data.error || 'Failed to reject order'));
                }
            } catch (error) {
                alert('Error rejecting order: ' + error.message);
            }
        });

        // Refresh confirmation button
        document.getElementById('refreshConfirmationBtn').addEventListener('click', () => {
            loadConfirmationOrders();
        });

        // Tab switching - refresh data when switching to confirmation tab
        document.getElementById('tab-confirmation').addEventListener('shown.bs.tab', () => {
            loadConfirmationOrders();
        });

        // Payment Check Functions
        let currentPaymentOrderId = null;
        
        async function openPaymentCheck(id) {
            currentPaymentOrderId = id;
            
            // Fetch order details for payment verification
            const res = await fetch('api/orders.php?action=view&id=' + id);
            const data = await res.json();
            
            if (data.ok) {
                const order = data.order;
                document.getElementById('paymentOrderId').textContent = '#' + order.id;
                document.getElementById('paymentAmount').textContent = '₱' + Number(order.total_amount || 0).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
                document.getElementById('paymentMethod').textContent = order.payment_method || 'Bank Transfer';
                document.getElementById('paymentReference').textContent = order.payment_reference || 'TXN' + order.id + '789';
                
                // Set default expected delivery date
                document.getElementById('expectedDelivery').value = new Date(Date.now() + 3*24*60*60*1000).toISOString().split('T')[0];
            }
            
            const paymentModal = new bootstrap.Modal(document.getElementById('paymentCheckModal'));
            paymentModal.show();
        }
        
        // Payment verification handlers
        document.getElementById('verifyPaymentBtn').addEventListener('click', async () => {
            if (!currentPaymentOrderId) return;
            
            const transferRef = document.getElementById('transferReference').value;
            const amountReceived = document.getElementById('amountReceived').value;
            const bankAccount = document.getElementById('bankAccount').value;
            const notes = document.getElementById('paymentNotes').value;
            
            if (!transferRef || !amountReceived || !bankAccount) {
                alert('Please fill in all required fields.');
                return;
            }
            
            const fd = new FormData();
            fd.append('action', 'verify_payment');
            fd.append('id', currentPaymentOrderId);
            fd.append('transfer_reference', transferRef);
            fd.append('amount_received', amountReceived);
            fd.append('bank_account', bankAccount);
            fd.append('notes', notes);
            
            try {
                const res = await fetch('api/orders.php', { method: 'POST', body: fd });
                const data = await res.json();
                
                if (data.success) {
                    alert('Payment verified successfully!');
                    bootstrap.Modal.getInstance(document.getElementById('paymentCheckModal')).hide();
                    loadOrders(); // Refresh orders list
                    // Clear form
                    document.getElementById('transferReference').value = '';
                    document.getElementById('amountReceived').value = '';
                    document.getElementById('bankAccount').value = '';
                    document.getElementById('paymentNotes').value = '';
                } else {
                    alert('Error: ' + (data.message || 'Failed to verify payment'));
                }
            } catch (error) {
                alert('Error verifying payment: ' + error.message);
            }
        });
        
        document.getElementById('rejectPaymentBtn').addEventListener('click', async () => {
            if (!currentPaymentOrderId) return;
            
            const reason = prompt('Enter reason for rejecting payment:');
            if (!reason) return;
            
            const fd = new FormData();
            fd.append('action', 'reject_payment');
            fd.append('id', currentPaymentOrderId);
            fd.append('reason', reason);
            
            try {
                const res = await fetch('api/orders.php', { method: 'POST', body: fd });
                const data = await res.json();
                
                if (data.success) {
                    alert('Payment rejected successfully!');
                    bootstrap.Modal.getInstance(document.getElementById('paymentCheckModal')).hide();
                    loadOrders(); // Refresh orders list
                } else {
                    alert('Error: ' + (data.message || 'Failed to reject payment'));
                }
            } catch (error) {
                alert('Error rejecting payment: ' + error.message);
            }
        });
        
        // Courier Selection Functions
        let currentCourierOrderId = null;
        
        function openCourierSelection(id) {
            currentCourierOrderId = id;
            
            // Set default expected delivery date
            document.getElementById('expectedDelivery').value = new Date(Date.now() + 3*24*60*60*1000).toISOString().split('T')[0];
            
            const courierModal = new bootstrap.Modal(document.getElementById('courierSelectionModal'));
            courierModal.show();
        }
        
        document.getElementById('confirmCourierBtn').addEventListener('click', async () => {
            if (!currentCourierOrderId) return;
            
            const courier = document.getElementById('selectedCourier').value;
            const notes = document.getElementById('courierNotes').value;
            const expectedDelivery = document.getElementById('expectedDelivery').value;
            
            if (!courier) {
                alert('Please select a courier service.');
                return;
            }
            
            if (!expectedDelivery) {
                alert('Please select an expected delivery date.');
                return;
            }
            
            const fd = new FormData();
            fd.append('action', 'send_to_delivery');
            fd.append('id', currentCourierOrderId);
            fd.append('courier', courier);
            fd.append('notes', notes);
            fd.append('expected_delivery', expectedDelivery);
            
            try {
                const res = await fetch('api/orders.php', { method: 'POST', body: fd });
                const data = await res.json();
                
                if (data.success) {
                    alert('Order sent to Delivery Management successfully! It will appear in the Pending Pickup section.');
                    bootstrap.Modal.getInstance(document.getElementById('courierSelectionModal')).hide();
                    loadOrders(); // Refresh orders list
                    // Clear form
                    document.getElementById('selectedCourier').value = '';
                    document.getElementById('courierNotes').value = '';
                    document.getElementById('expectedDelivery').value = '';
                } else {
                    alert('Error: ' + (data.message || 'Failed to send order to delivery'));
                }
            } catch (error) {
                alert('Error sending order to delivery: ' + error.message);
            }
        });

        // Bulk Selection Functionality
        function initializeBulkSelection() {
            // Select All functionality for each table
            const selectAllConfirmation = document.getElementById('selectAllConfirmation');
            const selectAllOrders = document.getElementById('selectAllOrders');
            const selectAllCancelled = document.getElementById('selectAllCancelled');

            // Confirmation table bulk selection
            if (selectAllConfirmation) {
                selectAllConfirmation.addEventListener('change', function() {
                    const checkboxes = document.querySelectorAll('.confirmation-checkbox');
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    updateBulkActions('confirmation');
                });
            }

            // Orders table bulk selection
            if (selectAllOrders) {
                selectAllOrders.addEventListener('change', function() {
                    const checkboxes = document.querySelectorAll('.order-checkbox');
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    updateBulkActions('orders');
                });
            }

            // Cancelled table bulk selection
            if (selectAllCancelled) {
                selectAllCancelled.addEventListener('change', function() {
                    const checkboxes = document.querySelectorAll('.cancelled-checkbox');
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    updateBulkActions('cancelled');
                });
            }

            // Individual checkbox change handlers
            document.addEventListener('change', function(e) {
                if (e.target.classList.contains('confirmation-checkbox')) {
                    updateBulkActions('confirmation');
                } else if (e.target.classList.contains('order-checkbox')) {
                    updateBulkActions('orders');
                } else if (e.target.classList.contains('cancelled-checkbox')) {
                    updateBulkActions('cancelled');
                }
            });
        }

        function updateBulkActions(tableType) {
            let checkboxes, selectAll, bulkActions, countElement;
            
            switch(tableType) {
                case 'confirmation':
                    checkboxes = document.querySelectorAll('.confirmation-checkbox');
                    selectAll = document.getElementById('selectAllConfirmation');
                    bulkActions = document.getElementById('confirmationBulkActions');
                    break;
                case 'orders':
                    checkboxes = document.querySelectorAll('.order-checkbox');
                    selectAll = document.getElementById('selectAllOrders');
                    bulkActions = document.getElementById('ordersBulkActions');
                    countElement = document.getElementById('selectedOrdersCount');
                    break;
                case 'cancelled':
                    checkboxes = document.querySelectorAll('.cancelled-checkbox');
                    selectAll = document.getElementById('selectAllCancelled');
                    bulkActions = document.getElementById('cancelledBulkActions');
                    break;
            }

            const checkedBoxes = Array.from(checkboxes).filter(cb => cb.checked);
            
            if (bulkActions) {
                if (checkedBoxes.length > 0) {
                    bulkActions.style.display = 'block';
                } else {
                    bulkActions.style.display = 'none';
                }
            }

            if (countElement) {
                countElement.textContent = checkedBoxes.length;
            }

            // Update select all checkbox state
            if (selectAll && checkboxes.length > 0) {
                if (checkedBoxes.length === checkboxes.length) {
                    selectAll.checked = true;
                    selectAll.indeterminate = false;
                } else if (checkedBoxes.length > 0) {
                    selectAll.checked = false;
                    selectAll.indeterminate = true;
                } else {
                    selectAll.checked = false;
                    selectAll.indeterminate = false;
                }
            }
        }

        // Bulk action handlers
        document.getElementById('bulkApproveBtn')?.addEventListener('click', async function() {
            const selectedIds = getSelectedOrderIds('confirmation');
            if (selectedIds.length === 0) return;

            if (!confirm(`Approve ${selectedIds.length} selected orders?`)) return;

            let successCount = 0;
            let errorCount = 0;
            const errors = [];

            for (const id of selectedIds) {
                const fd = new FormData();
                fd.append('action', 'approve_order');
                fd.append('id', id);
                const res = await fetch('api/orders.php', { method: 'POST', body: fd });
                const data = await res.json();
                if (data.success) {
                    successCount++;
                } else {
                    errorCount++;
                    errors.push(`Order #${id}: ${data.message}`);
                }
            }

            if (successCount > 0) {
                alert(`Successfully approved ${successCount} orders!`);
                loadConfirmationOrders();
                loadOrders();
            }
            
            if (errorCount > 0) {
                alert(`Failed to approve ${errorCount} orders:\n${errors.join('\n')}`);
            }
        });

        document.getElementById('bulkRejectBtn')?.addEventListener('click', async function() {
            const selectedIds = getSelectedOrderIds('confirmation');
            if (selectedIds.length === 0) return;

            const reason = prompt('Enter rejection reason for all selected orders:');
            if (!reason) return;

            let successCount = 0;
            let errorCount = 0;
            const errors = [];

            for (const id of selectedIds) {
                const fd = new FormData();
                fd.append('action', 'reject_order');
                fd.append('id', id);
                fd.append('reason', reason);
                const res = await fetch('api/orders.php', { method: 'POST', body: fd });
                const data = await res.json();
                if (data.success) {
                    successCount++;
                } else {
                    errorCount++;
                    errors.push(`Order #${id}: ${data.message}`);
                }
            }

            if (successCount > 0) {
                alert(`Successfully rejected ${successCount} orders!`);
                loadConfirmationOrders();
                loadCancelledOrders();
            }
            
            if (errorCount > 0) {
                alert(`Failed to reject ${errorCount} orders:\n${errors.join('\n')}`);
            }
        });

        document.getElementById('bulkSendToDeliveryBtn')?.addEventListener('click', async function() {
            const selectedIds = getSelectedOrderIds('orders');
            if (selectedIds.length === 0) return;

            if (!confirm(`Send ${selectedIds.length} selected orders to delivery?`)) return;

            let successCount = 0;
            let errorCount = 0;
            const errors = [];

            for (const id of selectedIds) {
                const fd = new FormData();
                fd.append('action', 'send_to_delivery');
                fd.append('id', id);
                fd.append('courier', 'J&T Express');
                fd.append('notes', 'Bulk delivery');
                fd.append('expected_delivery', new Date(Date.now() + 3*24*60*60*1000).toISOString().split('T')[0]);
                const res = await fetch('api/orders.php', { method: 'POST', body: fd });
                const data = await res.json();
                if (data.success) {
                    successCount++;
                } else {
                    errorCount++;
                    errors.push(`Order #${id}: ${data.message}`);
                }
            }

            if (successCount > 0) {
                alert(`Successfully sent ${successCount} orders to delivery!`);
                loadOrders();
            }
            
            if (errorCount > 0) {
                alert(`Failed to send ${errorCount} orders:\n${errors.join('\n')}`);
            }
        });

        document.getElementById('bulkCheckPaymentBtn')?.addEventListener('click', function() {
            const selectedIds = getSelectedOrderIds('orders');
            if (selectedIds.length === 0) return;

            alert(`Payment check for ${selectedIds.length} orders would be implemented here.`);
        });

        document.getElementById('bulkViewCancelledBtn')?.addEventListener('click', function() {
            const selectedIds = getSelectedOrderIds('cancelled');
            if (selectedIds.length === 0) return;

            alert(`View details for ${selectedIds.length} cancelled orders would be implemented here.`);
        });

        function getSelectedOrderIds(tableType) {
            let checkboxes;
            switch(tableType) {
                case 'confirmation':
                    checkboxes = document.querySelectorAll('.confirmation-checkbox:checked');
                    break;
                case 'orders':
                    checkboxes = document.querySelectorAll('.order-checkbox:checked');
                    break;
                case 'cancelled':
                    checkboxes = document.querySelectorAll('.cancelled-checkbox:checked');
                    break;
            }
            return Array.from(checkboxes).map(cb => cb.dataset.orderId);
        }

        // Initialize bulk selection when page loads
        document.addEventListener('DOMContentLoaded', function() {
            initializeBulkSelection();
        });
    </script>
</body>
</html> 


