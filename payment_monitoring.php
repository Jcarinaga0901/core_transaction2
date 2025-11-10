<?php
session_start();
require_once 'config/database.php';
require_once 'includes/auth.php';

// Check if user is logged in
if (!isAuthenticated()) {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'];
$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Monitoring - RAEVOR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
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
    </style>
</head>
<body>
    <!-- Main Header -->
    <header class="main-header">
        <div class="sidebar-toggle">
            <i class="bi bi-list"></i>
        </div>
        <h1 style="font-family: 'Great Vibes', cursive !important; font-size: 1.5rem; font-weight: 350; letter-spacing: 2px; text-shadow: 2px 2px 4px rgba(0,0,0,0.2);">
            <a href="index.php" class="brand-link">RAEVOR</a>
        </h1>
        
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
    </header>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h3>Core Transaction 2</h3>
            <p style="margin: 5px 0 0 0; font-size: 0.8rem; opacity: 0.8;">Seller Dashboard</p>
        </div>
        
        <ul class="sidebar-nav">
            <!-- Dashboard -->
            <li class="sidebar-item">
                <a href="index.php">
                    <i class="bi bi-speedometer2"></i>
                    <span class="menu-text">Dashboard</span>
                </a>
            </li>

            <!-- Product and Shop Management -->
            <li class="sidebar-item has-submenu">
                <a href="product_catalog.php">
                    <i class="bi bi-shop"></i>
                    <span class="menu-text">Product & Shop Management</span>
                    <i class="bi bi-chevron-down submenu-icon"></i>
                </a>
                <ul class="submenu">
                    <li><a href="product_catalog.php"><span class="menu-text">Product Catalog</span></a></li>
                    <li><a href="my_shop.php"><span class="menu-text">My Shop</span></a></li>
                    <li><a href="vendor_requests.php"><span class="menu-text">Pending Products</span></a></li>
                </ul>
            </li>

            <!-- Order Management (dropdown) -->
            <li class="sidebar-item has-submenu">
                <a href="order.php">
                    <i class="bi bi-cart-check"></i>
                    <span class="menu-text">Order Management</span>
                    <i class="bi bi-chevron-down submenu-icon"></i>
                </a>
                <ul class="submenu">
                    <li><a href="order.php"><span class="menu-text">Order List</span></a></li>
                    <li><a href="cancelled_orders.php"><span class="menu-text">Cancelled Orders</span></a></li>
                    <li><a href="return_management.php"><span class="menu-text">Return Management</span></a></li>
                </ul>
            </li>

            <!-- Delivery Management -->
            <li class="sidebar-item">
                <a href="delivery.php">
                    <i class="bi bi-truck"></i>
                    <span class="menu-text">Delivery Management</span>
                </a>
            </li>

            <!-- Seller Vouchers -->
            <li class="sidebar-item">
                <a href="seller_vouchers.php">
                    <i class="bi bi-gift"></i>
                    <span class="menu-text">Seller Vouchers</span>
                </a>
            </li>

            <!-- Reports -->
            <li class="sidebar-item has-submenu">
                <a href="sales_reports.php">
                    <i class="bi bi-graph-up"></i>
                    <span class="menu-text">Reports</span>
                    <i class="bi bi-chevron-down submenu-icon"></i>
                </a>
                <ul class="submenu">
                    <li><a href="sales_reports.php"><span class="menu-text">Sales Reports</span></a></li>
                    <li><a href="commission_reports.php"><span class="menu-text">Commission Reports</span></a></li>
                </ul>
            </li>

            <!-- Business Documents -->
            <li class="sidebar-item">
                <a href="seller_documents.php">
                    <i class="bi bi-file-earmark-check"></i>
                    <span class="menu-text">Business Documents</span>
                </a>
            </li>

            <!-- Payment Monitoring -->
            <li class="sidebar-item has-submenu active">
                <a href="payment_monitoring.php">
                    <i class="bi bi-credit-card"></i>
                    <span class="menu-text">Payment Monitoring</span>
                    <i class="bi bi-chevron-down submenu-icon"></i>
                </a>
                <ul class="submenu">
                    <li><a href="payment_monitoring.php" class="active"><span class="menu-text">Payment Monitoring</span></a></li>
                    <li><a href="payment_settings.php"><span class="menu-text">Payment Settings</span></a></li>
                </ul>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content" style="margin-left: 250px; padding: 2rem; min-height: calc(100vh - 60px); background: #f8f9fa; color: #212529;">
        <div class="container-fluid">
            <!-- Page Header -->
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="page-title">Payment Monitoring</h1>
                        <p class="page-subtitle">Monitor and verify customer payments for your orders</p>
                    </div>
                    <div>
                        <a href="payment_settings.php" class="btn btn-outline-primary me-2">
                            <i class="bi bi-gear me-1"></i>Payment Settings
                        </a>
                    </div>
                </div>
            </div>

            <!-- Payment Statistics -->
            <div class="row mb-4">
                <div class="col-md-3 mb-4">
                    <div class="card" style="background: #ffffff; border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon" style="width: 50px; height: 50px; background: linear-gradient(135deg, #2ecc71, #27ae60); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                        <i class="bi bi-credit-card" style="color: white; font-size: 1.5rem;"></i>
                                    </div>
                                    <div>
                                        <h3 class="stat-number mb-1" id="totalPayments" style="font-size: 2rem; font-weight: 700; color: #212529; margin: 0; font-family: 'Inter', sans-serif;">0</h3>
                                        <p class="stat-label mb-0" style="color: #495057; font-size: 0.9rem; font-weight: 600; font-family: 'Inter', sans-serif;">TOTAL PAYMENTS</p>
                                        <small style="color: #6c757d; font-size: 0.8rem; font-family: 'Inter', sans-serif; font-weight: 500;">All payment records</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card" style="background: #ffffff; border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon" style="width: 50px; height: 50px; background: linear-gradient(135deg, #27ae60, #2ecc71); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                        <i class="bi bi-check-circle" style="color: white; font-size: 1.5rem;"></i>
                                    </div>
                                    <div>
                                        <h3 class="stat-number mb-1" id="verifiedPayments" style="font-size: 2rem; font-weight: 700; color: #212529; margin: 0; font-family: 'Inter', sans-serif;">0</h3>
                                        <p class="stat-label mb-0" style="color: #495057; font-size: 0.9rem; font-weight: 600; font-family: 'Inter', sans-serif;">VERIFIED</p>
                                        <small style="color: #6c757d; font-size: 0.8rem; font-family: 'Inter', sans-serif; font-weight: 500;">Approved payments</small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="badge" style="background: #27ae60; color: white; font-size: 0.7rem; padding: 4px 8px; border-radius: 6px;">
                                        <i class="bi bi-check me-1"></i>Success
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card" style="background: #ffffff; border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon" style="width: 50px; height: 50px; background: linear-gradient(135deg, #f39c12, #e67e22); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                        <i class="bi bi-clock" style="color: white; font-size: 1.5rem;"></i>
                                    </div>
                                    <div>
                                        <h3 class="stat-number mb-1" id="pendingPayments" style="font-size: 2rem; font-weight: 700; color: #212529; margin: 0; font-family: 'Inter', sans-serif;">0</h3>
                                        <p class="stat-label mb-0" style="color: #495057; font-size: 0.9rem; font-weight: 600; font-family: 'Inter', sans-serif;">PENDING</p>
                                        <small style="color: #6c757d; font-size: 0.8rem; font-family: 'Inter', sans-serif; font-weight: 500;">Awaiting verification</small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="badge" style="background: #f39c12; color: white; font-size: 0.7rem; padding: 4px 8px; border-radius: 6px;">
                                        <i class="bi bi-hourglass me-1"></i>Pending
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card" style="background: #ffffff; border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon" style="width: 50px; height: 50px; background: linear-gradient(135deg, #e74c3c, #c0392b); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                        <i class="bi bi-x-circle" style="color: white; font-size: 1.5rem;"></i>
                                    </div>
                                    <div>
                                        <h3 class="stat-number mb-1" id="rejectedPayments" style="font-size: 2rem; font-weight: 700; color: #212529; margin: 0; font-family: 'Inter', sans-serif;">0</h3>
                                        <p class="stat-label mb-0" style="color: #495057; font-size: 0.9rem; font-weight: 600; font-family: 'Inter', sans-serif;">REJECTED</p>
                                        <small style="color: #6c757d; font-size: 0.8rem; font-family: 'Inter', sans-serif; font-weight: 500;">Failed payments</small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="badge" style="background: #e74c3c; color: white; font-size: 0.7rem; padding: 4px 8px; border-radius: 6px;">
                                        <i class="bi bi-x me-1"></i>Failed
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters and Search -->
            <div class="card mb-4" style="background: #ffffff; border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
                <div class="card-header" style="background: linear-gradient(135deg, #f8f9fa, #e9ecef); border: none; border-radius: 12px 12px 0 0; padding: 20px;">
                    <h5 class="card-title mb-0" style="color: #212529; font-weight: 600; font-size: 1.1rem; font-family: 'Inter', sans-serif;">
                        <i class="bi bi-funnel me-2"></i>Filters & Search
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label" style="color: #212529; font-weight: 600; font-family: 'Inter', sans-serif;">Status Filter</label>
                            <select class="form-select" id="statusFilter" style="border: 1px solid #dee2e6; border-radius: 8px; padding: 10px 15px; font-family: 'Inter', sans-serif;">
                                <option value="">All Status</option>
                                <option value="pending">Pending</option>
                                <option value="verified">Verified</option>
                                <option value="rejected">Rejected</option>
                                <option value="refunded">Refunded</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" style="color: #212529; font-weight: 600; font-family: 'Inter', sans-serif;">Payment Method</label>
                            <select class="form-select" id="methodFilter" style="border: 1px solid #dee2e6; border-radius: 8px; padding: 10px 15px; font-family: 'Inter', sans-serif;">
                                <option value="">All Methods</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="gcash">GCash</option>
                                <option value="paymaya">PayMaya</option>
                                <option value="credit_card">Credit Card</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" style="color: #212529; font-weight: 600; font-family: 'Inter', sans-serif;">Date Range</label>
                            <input type="date" class="form-control" id="dateFilter" style="border: 1px solid #dee2e6; border-radius: 8px; padding: 10px 15px; font-family: 'Inter', sans-serif;">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" style="color: #212529; font-weight: 600; font-family: 'Inter', sans-serif;">Search</label>
                            <input type="text" class="form-control" id="searchInput" placeholder="Order ID, Customer..." style="border: 1px solid #dee2e6; border-radius: 8px; padding: 10px 15px; font-family: 'Inter', sans-serif;">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment List -->
            <div class="card" style="background: #ffffff; border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
                <div class="card-header" style="background: linear-gradient(135deg, #f8f9fa, #e9ecef); border: none; border-radius: 12px 12px 0 0; padding: 20px;">
                    <h5 class="card-title mb-0" style="color: #212529; font-weight: 600; font-size: 1.1rem; font-family: 'Inter', sans-serif;">
                        <i class="bi bi-credit-card me-2"></i>Payment Transactions
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" style="background: #ffffff;">
                            <thead class="table-light">
                                <tr>
                                    <th style="padding: 15px; font-weight: 600; color: #495057; font-family: 'Inter', sans-serif;">Payment ID</th>
                                    <th style="padding: 15px; font-weight: 600; color: #495057; font-family: 'Inter', sans-serif;">Order ID</th>
                                    <th style="padding: 15px; font-weight: 600; color: #495057; font-family: 'Inter', sans-serif;">Customer</th>
                                    <th style="padding: 15px; font-weight: 600; color: #495057; font-family: 'Inter', sans-serif;">Amount</th>
                                    <th style="padding: 15px; font-weight: 600; color: #495057; font-family: 'Inter', sans-serif;">Method</th>
                                    <th style="padding: 15px; font-weight: 600; color: #495057; font-family: 'Inter', sans-serif;">Status</th>
                                    <th style="padding: 15px; font-weight: 600; color: #495057; font-family: 'Inter', sans-serif;">Date</th>
                                    <th style="padding: 15px; font-weight: 600; color: #495057; font-family: 'Inter', sans-serif;" class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="paymentsTableBody">
                                <!-- Payment data will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Details Modal -->
    <div class="modal fade" id="paymentDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Payment Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="paymentDetailsContent">
                    <!-- Payment details will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="verifyPaymentBtn">Verify Payment</button>
                    <button type="button" class="btn btn-danger" id="rejectPaymentBtn">Reject Payment</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Logout Modal -->
    <?php include 'includes/logout_modal.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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

        // Load payment data
        function loadPayments() {
            fetch('api/payments.php?action=list')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayPayments(data.payments);
                        updateStats(data.stats);
                    }
                });
        }

        function displayPayments(payments) {
            const tbody = document.getElementById('paymentsTableBody');
            tbody.innerHTML = '';

            payments.forEach(payment => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td style="padding: 15px; font-family: 'Inter', sans-serif;">${payment.payment_id}</td>
                    <td style="padding: 15px; font-family: 'Inter', sans-serif;">${payment.order_id}</td>
                    <td style="padding: 15px; font-family: 'Inter', sans-serif;">${payment.customer_name}</td>
                    <td style="padding: 15px; font-family: 'Inter', sans-serif; font-weight: 600;">₱${payment.amount.toLocaleString()}</td>
                    <td style="padding: 15px; font-family: 'Inter', sans-serif;">${payment.method_name}</td>
                    <td style="padding: 15px;"><span class="badge bg-${getStatusColor(payment.status)}" style="font-size: 0.75rem; padding: 4px 8px; border-radius: 6px;">${payment.status}</span></td>
                    <td style="padding: 15px; font-family: 'Inter', sans-serif;">${new Date(payment.created_at).toLocaleDateString()}</td>
                    <td style="padding: 15px;" class="text-end">
                        <button class="btn btn-sm btn-outline-primary" onclick="viewPayment(${payment.payment_id})" style="border-radius: 6px; padding: 6px 12px; font-family: 'Inter', sans-serif; font-weight: 500;">
                            <i class="bi bi-eye me-1"></i>View
                        </button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }

        function getStatusColor(status) {
            switch(status) {
                case 'verified': return 'success';
                case 'pending': return 'warning';
                case 'rejected': return 'danger';
                case 'refunded': return 'info';
                default: return 'secondary';
            }
        }

        function updateStats(stats) {
            document.getElementById('totalPayments').textContent = stats.total || 0;
            document.getElementById('verifiedPayments').textContent = stats.verified || 0;
            document.getElementById('pendingPayments').textContent = stats.pending || 0;
            document.getElementById('rejectedPayments').textContent = stats.rejected || 0;
        }

        function viewPayment(paymentId) {
            fetch(`api/payments.php?action=view&payment_id=${paymentId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayPaymentDetails(data.payment);
                        const modal = new bootstrap.Modal(document.getElementById('paymentDetailsModal'));
                        modal.show();
                    }
                });
        }

        function displayPaymentDetails(payment) {
            const content = document.getElementById('paymentDetailsContent');
            content.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <h6 style="color: #212529; font-weight: 600; font-family: 'Inter', sans-serif;">Payment Information</h6>
                        <p style="font-family: 'Inter', sans-serif;"><strong>Payment ID:</strong> ${payment.payment_id}</p>
                        <p style="font-family: 'Inter', sans-serif;"><strong>Order ID:</strong> ${payment.order_id}</p>
                        <p style="font-family: 'Inter', sans-serif;"><strong>Amount:</strong> ₱${payment.amount.toLocaleString()}</p>
                        <p style="font-family: 'Inter', sans-serif;"><strong>Method:</strong> ${payment.method_name}</p>
                        <p style="font-family: 'Inter', sans-serif;"><strong>Status:</strong> <span class="badge bg-${getStatusColor(payment.status)}">${payment.status}</span></p>
                    </div>
                    <div class="col-md-6">
                        <h6 style="color: #212529; font-weight: 600; font-family: 'Inter', sans-serif;">Customer Information</h6>
                        <p style="font-family: 'Inter', sans-serif;"><strong>Name:</strong> ${payment.customer_name}</p>
                        <p style="font-family: 'Inter', sans-serif;"><strong>Email:</strong> ${payment.customer_email}</p>
                        <p style="font-family: 'Inter', sans-serif;"><strong>Phone:</strong> ${payment.customer_phone}</p>
                    </div>
                </div>
                ${payment.payment_proof ? `
                <div class="mt-3">
                    <h6 style="color: #212529; font-weight: 600; font-family: 'Inter', sans-serif;">Payment Proof</h6>
                    <img src="${payment.payment_proof}" class="img-fluid" style="max-width: 300px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                </div>
                ` : ''}
            `;
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            loadPayments();
        });
    </script>
</body>
</html>
