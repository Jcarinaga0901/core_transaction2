<?php 
session_start();
require_once 'includes/auth.php';
require_once 'config/database.php';
requireAuth('login.php');

$currentPage = basename($_SERVER['PHP_SELF']);

// Initialize database connection with error handling
$pdo = null;
try {
    $pdo = getDBConnection();
    if (!$pdo) {
        throw new Exception("Database connection failed");
    }
} catch (Exception $e) {
    error_log("Database connection error: " . $e->getMessage());
    // Set default values to prevent errors
    $totalProducts = 0;
    $totalOrders = 0;
    $totalRevenue = 0;
    $avgRating = 0;
    $salesData = [];
    $categoryData = [];
    $topProducts = [];
    $recentOrders = [];
    $shopDetails = null;
}

// Get seller's vendor_id - ensure we have it from database if not in session
$vendorId = $_SESSION['vendor_id'] ?? null;
$userId = $_SESSION['user_id'] ?? null;

// If no vendor_id in session, get it from database
if (!$vendorId && $userId && $pdo) {
    try {
        $stmt = $pdo->prepare("SELECT vendor_id FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        if ($user && $user['vendor_id']) {
            $vendorId = $user['vendor_id'];
            $_SESSION['vendor_id'] = $vendorId;
        }
    } catch (Exception $e) {
        error_log("Error getting vendor_id from database: " . $e->getMessage());
    }
}

// Fetch vendor/shop details
$shopDetails = null;
if ($vendorId && $pdo) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM vendors WHERE id = ?");
        if ($stmt) {
            $stmt->execute([$vendorId]);
            $shopDetails = $stmt->fetch();
        }
        
        // Check for temporary logo in session (fallback for missing database column)
        if (empty($shopDetails['logo_url']) && isset($_SESSION['temp_logo_url']) && $_SESSION['temp_logo_vendor_id'] == $vendorId) {
            $shopDetails['logo_url'] = $_SESSION['temp_logo_url'];
        }
    } catch (Exception $e) {
        error_log("Error fetching shop details: " . $e->getMessage());
    }
}

// Get shop statistics
$totalProducts = 0;
$totalOrders = 0;
$totalRevenue = 0;
$avgRating = 0;
$salesData = [];
$categoryData = [];
$topProducts = [];
$recentOrders = [];

if ($vendorId && $pdo) {
    try {
        // Total products
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE vendor_id = ?");
        if ($stmt) {
            $stmt->execute([$vendorId]);
            $totalProducts = $stmt->fetchColumn();
        }
        
        // Total orders
        $stmt = $pdo->prepare("SELECT COUNT(DISTINCT o.id) FROM orders o 
                              INNER JOIN order_items oi ON o.id = oi.order_id 
                              INNER JOIN products p ON oi.product_id = p.id 
                              WHERE p.vendor_id = ?");
        if ($stmt) {
            $stmt->execute([$vendorId]);
            $totalOrders = $stmt->fetchColumn();
        }
        
        // Total revenue
        $stmt = $pdo->prepare("SELECT COALESCE(SUM(oi.quantity * oi.price), 0) FROM order_items oi
                              INNER JOIN products p ON oi.product_id = p.id 
                              WHERE p.vendor_id = ?");
        if ($stmt) {
            $stmt->execute([$vendorId]);
            $totalRevenue = $stmt->fetchColumn();
        }
    } catch (Exception $e) {
        error_log("Error fetching shop statistics: " . $e->getMessage());
    }
    
    try {
        // Sales data for last 7 days
        $stmt = $pdo->prepare("SELECT DATE(o.created_at) as date, 
                              COALESCE(SUM(oi.quantity * oi.price), 0) as revenue
                              FROM orders o
                              INNER JOIN order_items oi ON o.id = oi.order_id
                              INNER JOIN products p ON oi.product_id = p.id
                              WHERE p.vendor_id = ? AND o.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                              GROUP BY DATE(o.created_at)
                              ORDER BY date ASC");
        if ($stmt) {
            $stmt->execute([$vendorId]);
            $salesData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        // Category distribution
        $stmt = $pdo->prepare("SELECT c.name, COUNT(p.id) as count
                              FROM products p
                              LEFT JOIN categories c ON p.category_id = c.id
                              WHERE p.vendor_id = ?
                              GROUP BY c.id, c.name
                              ORDER BY count DESC
                              LIMIT 5");
        if ($stmt) {
            $stmt->execute([$vendorId]);
            $categoryData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        // Top selling products
        $stmt = $pdo->prepare("SELECT p.name, SUM(oi.quantity) as total_sold, 
                              SUM(oi.quantity * oi.price) as revenue
                              FROM order_items oi
                              INNER JOIN products p ON oi.product_id = p.id
                              WHERE p.vendor_id = ?
                              GROUP BY p.id, p.name
                              ORDER BY total_sold DESC
                              LIMIT 5");
        if ($stmt) {
            $stmt->execute([$vendorId]);
            $topProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        // Recent orders
        $stmt = $pdo->prepare("SELECT DISTINCT o.id, o.total_amount, o.status, o.created_at
                              FROM orders o
                              INNER JOIN order_items oi ON o.id = oi.order_id
                              INNER JOIN products p ON oi.product_id = p.id
                              WHERE p.vendor_id = ?
                              ORDER BY o.created_at DESC
                              LIMIT 5");
        if ($stmt) {
            $stmt->execute([$vendorId]);
            $recentOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (Exception $e) {
        error_log("Error fetching analytics data: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Shop - Core Transaction 2</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap" rel="stylesheet">
    <link href="css/styles.css?v=<?php echo filemtime('css/styles.css'); ?>" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        .shop-header-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            position: relative;
        }
        
        .shop-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            font-weight: bold;
        }
        
        .shop-stats-item {
            text-align: center;
            padding: 15px;
        }
        
        .shop-stats-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: #667eea;
        }
        
        .shop-stats-label {
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        .shop-info-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        }
        
        .info-row {
            display: flex;
            padding: 15px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            flex: 0 0 200px;
            font-weight: 600;
            color: #495057;
        }
        
        .info-value {
            flex: 1;
            color: #6c757d;
        }
        
        .status-badge-large {
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .edit-shop-btn {
            position: absolute;
            top: 20px;
            right: 20px;
        }
        
        .quick-action-btn {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            display: block;
        }
        
        .quick-action-btn:hover {
            border-color: #667eea;
            transform: translateY(-3px);
            box-shadow: 0 8px 16px rgba(102, 126, 234, 0.2);
        }
        
        .action-icon-large {
            width: 60px;
            height: 60px;
            margin: 0 auto 15px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .chart-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            height: 100%;
            border: 1px solid rgba(255,255,255,0.1);
        }
        
        .chart-container {
            position: relative;
            height: 300px;
            margin-top: 20px;
        }
        
        .table-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            border: 1px solid rgba(255,255,255,0.1);
        }
        
        .product-rank {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.9rem;
        }
        
        .order-status-badge {
            padding: 5px 12px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
        }
    </style>
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
            <li class="sidebar-item <?php if($currentPage == 'index.php') echo 'active'; ?>">
                <a href="index.php">
                    <i class="bi bi-speedometer2"></i>
                    <span class="menu-text">Dashboard</span>
                </a>
            </li>

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

            <li class="sidebar-item has-submenu <?php if(in_array($currentPage, ['order.php','return_management.php',''])) echo 'active'; ?>">
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
            <!-- Success/Error Messages -->
            <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; font-weight: 500;">
                <i class="bi bi-check-circle me-2"></i><?php echo htmlspecialchars(urldecode($_GET['success'])); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <?php if(isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i><?php echo htmlspecialchars(urldecode($_GET['error'])); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <!-- Shop Header -->
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="page-title">My Shop</h2>
                        <p class="page-subtitle">Manage your shop details and business information</p>
                    </div>
                    <div>
                        <button class="btn btn-primary edit-shop-btn" data-bs-toggle="modal" data-bs-target="#editShopModal">
                            <i class="bi bi-pencil me-1"></i>Edit Shop
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="shop-header-card">
                
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="shop-avatar">
                            <?php if ($shopDetails['logo_url'] ?? false): ?>
                                <img src="<?php echo htmlspecialchars($shopDetails['logo_url']); ?>" 
                                     alt="Shop Logo" 
                                     style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                            <?php else: ?>
                                <?php echo strtoupper(substr($shopDetails['name'] ?? 'S', 0, 1)); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col">
                        <h2 class="mb-1"><?php echo htmlspecialchars($shopDetails['name'] ?? 'My Shop'); ?></h2>
                        <p class="text-muted mb-2">Active 2 minutes ago</p>
                        <div class="d-flex gap-2">
                            <span class="badge bg-success status-badge-large">
                                <i class="bi bi-check-circle me-1"></i>Active
                            </span>
                            <span class="text-muted">
                                <i class="bi bi-calendar me-1"></i>Joined <?php echo date('F Y', strtotime($shopDetails['created_at'] ?? 'now')); ?>
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Shop Statistics -->
                <div class="row mt-4 pt-4 border-top">
                    <div class="col-md-3">
                        <div class="shop-stats-item">
                            <div class="shop-stats-number"><?php echo number_format($totalProducts); ?></div>
                            <div class="shop-stats-label">Products</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="shop-stats-item">
                            <div class="shop-stats-number"><?php echo number_format($totalOrders); ?></div>
                            <div class="shop-stats-label">Total Orders</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="shop-stats-item">
                            <div class="shop-stats-number">₱<?php echo number_format($totalRevenue, 2); ?></div>
                            <div class="shop-stats-label">Revenue</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="shop-stats-item">
                            <div class="shop-stats-number"><?php echo number_format($avgRating, 1); ?> <i class="bi bi-star-fill text-warning"></i></div>
                            <div class="shop-stats-label">Rating</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shop Information -->
            <div class="row">
                <div class="col-lg-6">
                    <div class="shop-info-card">
                        <h5 class="mb-4"><i class="bi bi-info-circle me-2"></i>Shop Information</h5>
                        
                        <div class="info-row">
                            <div class="info-label"><i class="bi bi-image me-2"></i>Shop Logo</div>
                            <div class="info-value">
                                <?php if ($shopDetails['logo_url'] ?? false): ?>
                                    <img src="<?php echo htmlspecialchars($shopDetails['logo_url']); ?>" 
                                         alt="Shop Logo" 
                                         style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; border: 2px solid #e9ecef;">
                                <?php else: ?>
                                    <div style="width: 60px; height: 60px; background: #f8f9fa; border: 2px dashed #dee2e6; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #6c757d;">
                                        <i class="bi bi-image" style="font-size: 1.5rem;"></i>
                                    </div>
                                <?php endif; ?>
                                <button class="btn btn-sm btn-outline-primary ms-2" data-bs-toggle="modal" data-bs-target="#logoUploadModal">
                                    <i class="bi bi-upload me-1"></i>Upload Logo
                                </button>
                            </div>
                        </div>
                        
                        <div class="info-row">
                            <div class="info-label"><i class="bi bi-shop me-2"></i>Shop Name</div>
                            <div class="info-value"><?php echo htmlspecialchars($shopDetails['name'] ?? 'N/A'); ?></div>
                        </div>
                        
                        <div class="info-row">
                            <div class="info-label"><i class="bi bi-envelope me-2"></i>Email</div>
                            <div class="info-value"><?php echo htmlspecialchars($shopDetails['email'] ?? 'Not set'); ?></div>
                        </div>
                        
                        <div class="info-row">
                            <div class="info-label"><i class="bi bi-telephone me-2"></i>Phone</div>
                            <div class="info-value"><?php echo htmlspecialchars($shopDetails['phone'] ?? 'Not set'); ?></div>
                        </div>
                        
                        <div class="info-row">
                            <div class="info-label"><i class="bi bi-geo-alt me-2"></i>Address</div>
                            <div class="info-value"><?php echo htmlspecialchars($shopDetails['address'] ?? 'Not set'); ?></div>
                        </div>
                        
                        <div class="info-row">
                            <div class="info-label"><i class="bi bi-card-text me-2"></i>Description</div>
                            <div class="info-value"><?php echo htmlspecialchars($shopDetails['description'] ?? 'No description'); ?></div>
                        </div>
                        
                        <div class="info-row">
                            <div class="info-label"><i class="bi bi-link-45deg me-2"></i>Website</div>
                            <div class="info-value">
                                <?php if ($shopDetails['website'] ?? false): ?>
                                    <a href="<?php echo htmlspecialchars($shopDetails['website']); ?>" target="_blank"><?php echo htmlspecialchars($shopDetails['website']); ?></a>
                                <?php else: ?>
                                    Not set
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="shop-info-card">
                        <h5 class="mb-4"><i class="bi bi-lightning-charge me-2"></i>Quick Actions</h5>
                        
                        <div class="row g-3">
                            <div class="col-6">
                                <a href="product_catalog.php" class="quick-action-btn">
                                    <div class="action-icon-large">
                                        <i class="bi bi-plus-circle"></i>
                                    </div>
                                    <h6 class="fw-bold mb-1">Add Product</h6>
                                    <small class="text-muted">Create new product</small>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="order.php" class="quick-action-btn">
                                    <div class="action-icon-large" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                        <i class="bi bi-cart-check"></i>
                                    </div>
                                    <h6 class="fw-bold mb-1">View Orders</h6>
                                    <small class="text-muted">Manage orders</small>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="vendor_requests.php" class="quick-action-btn">
                                    <div class="action-icon-large" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                                        <i class="bi bi-clock-history"></i>
                                    </div>
                                    <h6 class="fw-bold mb-1">Pending Products</h6>
                                    <small class="text-muted">Awaiting approval</small>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="sales_reports.php" class="quick-action-btn">
                                    <div class="action-icon-large" style="background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);">
                                        <i class="bi bi-graph-up"></i>
                                    </div>
                                    <h6 class="fw-bold mb-1">Reports</h6>
                                    <small class="text-muted">Sales analytics</small>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Business Documents Section -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="shop-info-card">
                        <h5 class="mb-4"><i class="bi bi-file-earmark-check me-2"></i>Business Documents</h5>
                        
                        <?php
                        // Get document status
                        $documentStatus = null;
                        if ($pdo) {
                            try {
                                $stmt = $pdo->prepare("SELECT * FROM seller_documents WHERE vendor_id = ?");
                                if ($stmt) {
                                    $stmt->execute([$vendorId]);
                                    $documentStatus = $stmt->fetch();
                                }
                            } catch (Exception $e) {
                                // Handle error silently
                            }
                        }
                        ?>
                        
                        <!-- Document Status -->
                        <?php if($documentStatus): ?>
                            <div class="alert alert-info mb-4">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h6 class="mb-2"><i class="bi bi-info-circle me-2"></i>Document Status</h6>
                                        <p class="mb-2">
                                            <strong>Status:</strong> 
                                            <span class="badge <?php 
                                                echo $documentStatus['status'] === 'approved' ? 'bg-success' : 
                                                    ($documentStatus['status'] === 'rejected' ? 'bg-danger' : 'bg-warning'); 
                                            ?>">
                                                <?php echo ucfirst($documentStatus['status']); ?>
                                            </span>
                                        </p>
                                        <?php if($documentStatus['admin_notes']): ?>
                                            <p class="mb-0"><strong>Admin Notes:</strong> <?php echo htmlspecialchars($documentStatus['admin_notes']); ?></p>
                                        <?php endif; ?>
                                        <small class="text-muted">
                                            Last updated: <?php echo date('M j, Y g:i A', strtotime($documentStatus['updated_at'])); ?>
                                        </small>
                                    </div>
                                    <div class="text-end">
                                        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#viewDocumentsModal">
                                            <i class="bi bi-eye me-1"></i>View Documents
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Document Upload Cards -->
                        <div class="row g-3">
                            <!-- Business Permit -->
                            <div class="col-md-4">
                                <div class="card h-100 border-2 <?php echo ($documentStatus && $documentStatus['business_permit']) ? 'border-success' : 'border-light'; ?>">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="bi bi-building text-primary" style="font-size: 2.5rem;"></i>
                                        </div>
                                        <h6 class="card-title">Business Permit</h6>
                                        <p class="card-text text-muted small">Upload your business permit document</p>
                                        
                                        <?php if($documentStatus && $documentStatus['business_permit']): ?>
                                            <div class="mb-3">
                                                <img src="<?php echo $documentStatus['business_permit']; ?>" 
                                                     class="img-thumbnail" 
                                                     style="max-width: 100px; max-height: 100px; object-fit: cover;"
                                                     alt="Business Permit">
                                            </div>
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle me-1"></i>Uploaded
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">
                                                <i class="bi bi-clock me-1"></i>Not Uploaded
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- BIR -->
                            <div class="col-md-4">
                                <div class="card h-100 border-2 <?php echo ($documentStatus && $documentStatus['bir']) ? 'border-success' : 'border-light'; ?>">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="bi bi-receipt text-info" style="font-size: 2.5rem;"></i>
                                        </div>
                                        <h6 class="card-title">BIR Registration</h6>
                                        <p class="card-text text-muted small">Upload your BIR registration document</p>
                                        
                                        <?php if($documentStatus && $documentStatus['bir']): ?>
                                            <div class="mb-3">
                                                <img src="<?php echo $documentStatus['bir']; ?>" 
                                                     class="img-thumbnail" 
                                                     style="max-width: 100px; max-height: 100px; object-fit: cover;"
                                                     alt="BIR Registration">
                                            </div>
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle me-1"></i>Uploaded
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">
                                                <i class="bi bi-clock me-1"></i>Not Uploaded
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- DTI -->
                            <div class="col-md-4">
                                <div class="card h-100 border-2 <?php echo ($documentStatus && $documentStatus['dti']) ? 'border-success' : 'border-light'; ?>">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="bi bi-award text-warning" style="font-size: 2.5rem;"></i>
                                        </div>
                                        <h6 class="card-title">DTI Registration</h6>
                                        <p class="card-text text-muted small">Upload your DTI registration document</p>
                                        
                                        <?php if($documentStatus && $documentStatus['dti']): ?>
                                            <div class="mb-3">
                                                <img src="<?php echo $documentStatus['dti']; ?>" 
                                                     class="img-thumbnail" 
                                                     style="max-width: 100px; max-height: 100px; object-fit: cover;"
                                                     alt="DTI Registration">
                                            </div>
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle me-1"></i>Uploaded
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">
                                                <i class="bi bi-clock me-1"></i>Not Uploaded
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="text-center mt-4">
                            <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#uploadDocumentsModal">
                                <i class="bi bi-cloud-upload me-1"></i>Upload Documents
                            </button>
                            <?php if($documentStatus): ?>
                                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#viewDocumentsModal">
                                    <i class="bi bi-eye me-1"></i>View All Documents
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Analytics Section -->
            <div class="row mt-5 mb-4">
                <div class="col-12 mb-4">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-graph-up text-white" style="font-size: 1.5rem; margin-right: 10px;"></i>
                        <div>
                            <h4 class="text-white mb-1">Shop Analytics</h4>
                            <p class="text-white-50 mb-0 small">Visualize your shop performance and trends</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Charts Row -->
            <div class="row g-4 mb-4">
                <!-- Sales Trend Chart -->
                <div class="col-lg-8">
                    <div class="chart-card">
                        <div class="d-flex align-items-center mb-4">
                            <div class="me-3" style="width: 40px; height: 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-graph-up text-white"></i>
                            </div>
                            <div>
                                <h5 class="mb-0">Sales Trend (Last 7 Days)</h5>
                            </div>
                        </div>
                        <div class="chart-container">
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Category Distribution Chart -->
                <div class="col-lg-4">
                    <div class="chart-card">
                        <div class="d-flex align-items-center mb-4">
                            <div class="me-3" style="width: 40px; height: 40px; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-pie-chart text-white"></i>
                            </div>
                            <div>
                                <h5 class="mb-0">Product Categories</h5>
                            </div>
                        </div>
                        <div class="chart-container">
                            <canvas id="categoryChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Top Products & Recent Orders -->
            <div class="row g-4">
                <!-- Top Selling Products -->
                <div class="col-lg-6">
                    <div class="table-card">
                        <div class="d-flex align-items-center mb-4">
                            <div class="me-3" style="width: 40px; height: 40px; background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-trophy text-white"></i>
                            </div>
                            <div>
                                <h5 class="mb-0">Top Selling Products</h5>
                            </div>
                        </div>
                        <?php if (empty($topProducts)): ?>
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.3;"></i>
                                <p class="mt-2">No sales data yet</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="50">Rank</th>
                                            <th>Product</th>
                                            <th class="text-center">Sold</th>
                                            <th class="text-end">Revenue</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($topProducts as $index => $product): ?>
                                        <tr>
                                            <td>
                                                <div class="product-rank"><?php echo $index + 1; ?></div>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($product['name']); ?></strong>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-primary"><?php echo number_format($product['total_sold']); ?> units</span>
                                            </td>
                                            <td class="text-end">
                                                <strong class="text-success">₱<?php echo number_format($product['revenue'], 2); ?></strong>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Recent Orders -->
                <div class="col-lg-6">
                    <div class="table-card">
                        <div class="d-flex align-items-center mb-4">
                            <div class="me-3" style="width: 40px; height: 40px; background: linear-gradient(135deg, #30cfd0 0%, #330867 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-clock-history text-white"></i>
                            </div>
                            <div>
                                <h5 class="mb-0">Recent Orders</h5>
                            </div>
                        </div>
                        <?php if (empty($recentOrders)): ?>
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-cart-x" style="font-size: 3rem; opacity: 0.3;"></i>
                                <p class="mt-2">No orders yet</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentOrders as $order): ?>
                                        <tr>
                                            <td><strong>#<?php echo $order['id']; ?></strong></td>
                                            <td><strong>₱<?php echo number_format($order['total_amount'], 2); ?></strong></td>
                                            <td>
                                                <?php
                                                $statusClass = match($order['status']) {
                                                    'Pending' => 'bg-warning text-dark',
                                                    'Processing' => 'bg-info',
                                                    'Completed' => 'bg-success',
                                                    'Cancelled' => 'bg-danger',
                                                    default => 'bg-secondary'
                                                };
                                                ?>
                                                <span class="order-status-badge <?php echo $statusClass; ?>">
                                                    <?php echo htmlspecialchars($order['status']); ?>
                                                </span>
                                            </td>
                                            <td class="text-muted">
                                                <?php echo date('M d, Y', strtotime($order['created_at'])); ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Shop Modal -->
    <div class="modal fade" id="editShopModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Shop Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="api/vendors.php">
                    <input type="hidden" name="action" value="update_shop">
                    <input type="hidden" name="vendor_id" value="<?php echo $vendorId; ?>">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Shop Name *</label>
                                <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($shopDetails['name'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($shopDetails['email'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($shopDetails['phone'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Website</label>
                                <input type="url" class="form-control" name="website" value="<?php echo htmlspecialchars($shopDetails['website'] ?? ''); ?>" placeholder="https://...">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" name="address" rows="2"><?php echo htmlspecialchars($shopDetails['address'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="4" placeholder="Tell customers about your shop..."><?php echo htmlspecialchars($shopDetails['description'] ?? ''); ?></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Include Logout Modal -->
    <?php include 'includes/logout_modal.php'; ?>
    
    <script>
        // Toggle sidebar
        document.querySelector('.sidebar-toggle').addEventListener('click', function() {
            document.body.classList.toggle('sidebar-collapsed');
        });
        
        // Handle sidebar navigation
        const menuItems = document.querySelectorAll('.sidebar-item.has-submenu');
        menuItems.forEach(item => {
            const mainLink = item.querySelector('a');
            mainLink.addEventListener('click', function(e) {
                if (!document.body.classList.contains('sidebar-collapsed')) {
                    e.preventDefault();
                    item.classList.toggle('active');
                    menuItems.forEach(otherItem => {
                        if (otherItem !== item) {
                            otherItem.classList.remove('active');
                        }
                    });
                }
            });
        });
        
        // Prepare chart data from PHP
        const salesData = <?php echo json_encode($salesData); ?>;
        const categoryData = <?php echo json_encode($categoryData); ?>;
        
        // Sales Trend Chart (Line Chart)
        const salesCtx = document.getElementById('salesChart');
        if (salesCtx) {
            // Fill in missing dates for last 7 days
            const last7Days = [];
            const revenueMap = {};
            
            // Create map from sales data
            salesData.forEach(item => {
                revenueMap[item.date] = parseFloat(item.revenue);
            });
            
            // Generate last 7 days
            for (let i = 6; i >= 0; i--) {
                const date = new Date();
                date.setDate(date.getDate() - i);
                const dateStr = date.toISOString().split('T')[0];
                last7Days.push({
                    date: dateStr,
                    revenue: revenueMap[dateStr] || 0
                });
            }
            
            new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: last7Days.map(d => {
                        const date = new Date(d.date);
                        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                    }),
                    datasets: [{
                        label: 'Revenue (₱)',
                        data: last7Days.map(d => d.revenue),
                        borderColor: 'rgb(102, 126, 234)',
                        backgroundColor: 'rgba(102, 126, 234, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        pointBackgroundColor: 'rgb(102, 126, 234)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Revenue: ₱' + context.parsed.y.toLocaleString('en-PH', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    });
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '₱' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        }
        
        // Category Distribution Chart (Doughnut Chart)
        const categoryCtx = document.getElementById('categoryChart');
        if (categoryCtx && categoryData.length > 0) {
            new Chart(categoryCtx, {
                type: 'doughnut',
                data: {
                    labels: categoryData.map(c => c.name || 'Uncategorized'),
                    datasets: [{
                        data: categoryData.map(c => c.count),
                        backgroundColor: [
                            'rgba(102, 126, 234, 0.8)',
                            'rgba(240, 147, 251, 0.8)',
                            'rgba(79, 172, 254, 0.8)',
                            'rgba(250, 112, 154, 0.8)',
                            'rgba(48, 207, 208, 0.8)'
                        ],
                        borderColor: [
                            'rgb(102, 126, 234)',
                            'rgb(240, 147, 251)',
                            'rgb(79, 172, 254)',
                            'rgb(250, 112, 154)',
                            'rgb(48, 207, 208)'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                font: {
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return label + ': ' + value + ' products (' + percentage + '%)';
                                }
                            }
                        }
                    }
                }
            });
        } else if (categoryCtx) {
            // Show message if no data
            categoryCtx.parentElement.innerHTML = '<div class="text-center text-muted py-5"><i class="bi bi-pie-chart" style="font-size: 3rem; opacity: 0.3;"></i><p class="mt-3">No category data available</p></div>';
        }
    </script>

    <!-- Logo Upload Modal -->
    <div class="modal fade" id="logoUploadModal" tabindex="-1" aria-labelledby="logoUploadModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoUploadModalLabel">
                        <i class="bi bi-upload me-2"></i>Upload Shop Logo
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="api/vendors.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="upload_logo">
                        <input type="hidden" name="vendor_id" value="<?php echo $vendorId ?: '0'; ?>">
                        
                        <div class="mb-3">
                            <label for="logo" class="form-label">Select Logo Image</label>
                            <input type="file" class="form-control" id="logo" name="logo" accept="image/*" required>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>
                                Supported formats: JPEG, PNG, GIF. Maximum size: 5MB
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div id="imagePreview" style="display: none;">
                                <label class="form-label">Preview:</label>
                                <div class="text-center">
                                    <img id="previewImg" src="" alt="Preview" style="max-width: 200px; max-height: 200px; border-radius: 8px; border: 2px solid #e9ecef;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-upload me-1"></i>Upload Logo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Image preview functionality
        document.getElementById('logo').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
            }
        });

        // Handle logo upload form submission
        document.querySelector('#logoUploadModal form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Uploading...';
            submitBtn.disabled = true;
            
            fetch('api/vendors.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Close modal and refresh page
                    const modal = bootstrap.Modal.getInstance(document.getElementById('logoUploadModal'));
                    modal.hide();
                    
                    // Show success message
                    alert('Logo uploaded successfully!');
                    
                    // Refresh the page to show new logo
                    window.location.reload();
                } else {
                    // Show detailed error message
                    console.error('Upload error:', data);
                    alert('Upload failed: ' + (data.message || 'Unknown error'));
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Upload error:', error);
                alert('Upload failed. Please try again.');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    </script>

    <!-- Upload Documents Modal -->
    <div class="modal fade" id="uploadDocumentsModal" tabindex="-1" aria-labelledby="uploadDocumentsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="uploadDocumentsModalLabel">
                        <i class="bi bi-cloud-upload me-2"></i>Upload Business Documents
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" enctype="multipart/form-data" action="seller_documents.php">
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Instructions:</strong> Upload clear, readable images of your business documents. Supported formats: JPG, PNG, GIF, WebP (Max 5MB each).
                        </div>
                        
                        <div class="row g-3">
                            <!-- Business Permit -->
                            <div class="col-md-4">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="bi bi-building text-primary" style="font-size: 2rem;"></i>
                                        </div>
                                        <h6 class="card-title">Business Permit</h6>
                                        <input type="file" class="form-control" name="business_permit" accept="image/*" onchange="previewImage(this, 'bp_preview')">
                                        <div id="bp_preview" class="mt-2"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- BIR -->
                            <div class="col-md-4">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="bi bi-receipt text-info" style="font-size: 2rem;"></i>
                                        </div>
                                        <h6 class="card-title">BIR Registration</h6>
                                        <input type="file" class="form-control" name="bir" accept="image/*" onchange="previewImage(this, 'bir_preview')">
                                        <div id="bir_preview" class="mt-2"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- DTI -->
                            <div class="col-md-4">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="bi bi-award text-warning" style="font-size: 2rem;"></i>
                                        </div>
                                        <h6 class="card-title">DTI Registration</h6>
                                        <input type="file" class="form-control" name="dti" accept="image/*" onchange="previewImage(this, 'dti_preview')">
                                        <div id="dti_preview" class="mt-2"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="upload_documents" class="btn btn-primary">
                            <i class="bi bi-cloud-upload me-1"></i>Upload Documents
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Documents Modal -->
    <div class="modal fade" id="viewDocumentsModal" tabindex="-1" aria-labelledby="viewDocumentsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="viewDocumentsModalLabel">
                        <i class="bi bi-eye me-2"></i>View Business Documents
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php if($documentStatus): ?>
                        <!-- Document Status -->
                        <div class="alert alert-<?php echo $documentStatus['status'] === 'approved' ? 'success' : ($documentStatus['status'] === 'rejected' ? 'danger' : 'warning'); ?>">
                            <h6 class="mb-2"><i class="bi bi-info-circle me-2"></i>Document Status</h6>
                            <p class="mb-2">
                                <strong>Status:</strong> 
                                <span class="badge bg-<?php echo $documentStatus['status'] === 'approved' ? 'success' : ($documentStatus['status'] === 'rejected' ? 'danger' : 'warning'); ?>">
                                    <?php echo ucfirst($documentStatus['status']); ?>
                                </span>
                            </p>
                            <?php if($documentStatus['admin_notes']): ?>
                                <p class="mb-0"><strong>Admin Notes:</strong> <?php echo htmlspecialchars($documentStatus['admin_notes']); ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Documents Grid -->
                        <div class="row g-3">
                            <!-- Business Permit -->
                            <?php if($documentStatus['business_permit']): ?>
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-header text-center bg-primary text-white">
                                            <h6 class="mb-0"><i class="bi bi-building me-1"></i>Business Permit</h6>
                                        </div>
                                        <div class="card-body text-center p-2">
                                            <img src="<?php echo $documentStatus['business_permit']; ?>" 
                                                 class="img-fluid rounded" 
                                                 style="max-height: 200px; object-fit: contain;"
                                                 alt="Business Permit">
                                        </div>
                                        <div class="card-footer text-center">
                                            <a href="<?php echo $documentStatus['business_permit']; ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye me-1"></i>View Full Size
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <!-- BIR -->
                            <?php if($documentStatus['bir']): ?>
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-header text-center bg-info text-white">
                                            <h6 class="mb-0"><i class="bi bi-receipt me-1"></i>BIR Registration</h6>
                                        </div>
                                        <div class="card-body text-center p-2">
                                            <img src="<?php echo $documentStatus['bir']; ?>" 
                                                 class="img-fluid rounded" 
                                                 style="max-height: 200px; object-fit: contain;"
                                                 alt="BIR Registration">
                                        </div>
                                        <div class="card-footer text-center">
                                            <a href="<?php echo $documentStatus['bir']; ?>" target="_blank" class="btn btn-sm btn-outline-info">
                                                <i class="bi bi-eye me-1"></i>View Full Size
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <!-- DTI -->
                            <?php if($documentStatus['dti']): ?>
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-header text-center bg-warning text-dark">
                                            <h6 class="mb-0"><i class="bi bi-award me-1"></i>DTI Registration</h6>
                                        </div>
                                        <div class="card-body text-center p-2">
                                            <img src="<?php echo $documentStatus['dti']; ?>" 
                                                 class="img-fluid rounded" 
                                                 style="max-height: 200px; object-fit: contain;"
                                                 alt="DTI Registration">
                                        </div>
                                        <div class="card-footer text-center">
                                            <a href="<?php echo $documentStatus['dti']; ?>" target="_blank" class="btn btn-sm btn-outline-warning">
                                                <i class="bi bi-eye me-1"></i>View Full Size
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Upload Date -->
                        <div class="text-center mt-3">
                            <small class="text-muted">
                                <i class="bi bi-calendar me-1"></i>
                                Last updated: <?php echo date('M j, Y g:i A', strtotime($documentStatus['updated_at'])); ?>
                            </small>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-file-earmark-x text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3">No documents uploaded yet</p>
                            <button class="btn btn-primary" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#uploadDocumentsModal">
                                <i class="bi bi-cloud-upload me-1"></i>Upload Documents
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <?php if($documentStatus): ?>
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#uploadDocumentsModal">
                            <i class="bi bi-pencil me-1"></i>Update Documents
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Image preview functionality for upload modal
        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            const file = input.files[0];
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `
                        <img src="${e.target.result}" class="img-thumbnail" style="max-width: 100px; max-height: 100px; object-fit: cover;" alt="Preview">
                        <div class="small text-success mt-1">
                            <i class="bi bi-check-circle me-1"></i>Ready to upload
                        </div>
                    `;
                };
                reader.readAsDataURL(file);
            } else {
                preview.innerHTML = '';
            }
        }
    </script>
</body>
</html>

