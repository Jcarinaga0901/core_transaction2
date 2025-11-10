<?php 
// Error reporting for production (disabled for security)
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Enhanced session security - MUST be set before session_start()
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // Set to 1 only if using HTTPS
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Strict');

// Start session after setting ini values
session_start();

try {
    // Include authentication
require_once 'includes/auth.php';

    // Check if user is authenticated
    requireAuth();

    // Include database configuration
require_once 'config/database.php';
} catch (Exception $e) {
    error_log("Index.php initialization error: " . $e->getMessage());
    die("System initialization error. Please contact administrator.");
}

// Get user information
$user_id = $_SESSION['user_id'] ?? null;
$username = $_SESSION['username'] ?? 'User';
$role = $_SESSION['role'] ?? 'seller';
$vendor_id = $_SESSION['vendor_id'] ?? null;

// Get vendor information
$vendor_info = null;
if ($vendor_id) {
try {
    $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT * FROM vendors WHERE id = ?");
        $stmt->execute([$vendor_id]);
        $vendor_info = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error fetching vendor info: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RAEVOR - Seller Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            <li class="sidebar-item active">
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
            <li class="sidebar-item">
                <a href="payment_monitoring.php">
                    <i class="bi bi-credit-card"></i>
                    <span class="menu-text">Payment Monitoring</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content" style="margin-left: 250px; padding: 2rem; min-height: calc(100vh - 60px); background: #f8f9fa; color: #212529;">
        <div class="container-fluid">
            <!-- Dashboard Header -->
            <div class="dashboard-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; color: #ffffff; border-radius: 16px; padding: 30px; margin-bottom: 30px; box-shadow: 0 8px 32px rgba(0,0,0,0.1);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 style="font-size: 2.5rem; font-weight: 700; margin-bottom: 10px; color: #ffffff !important; font-family: 'Inter', sans-serif; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">Seller Dashboard</h1>
                        <p style="font-size: 1.1rem; color: #ffffff !important; margin-bottom: 0; font-family: 'Inter', sans-serif; font-weight: 500; text-shadow: 0 1px 2px rgba(0,0,0,0.3);">Welcome back, <?php echo htmlspecialchars($username); ?>! Here's what's happening with your store today.</p>
            </div>
                    <div class="text-end">
                        <button class="btn btn-light" id="refreshDashboardBtn" style="border-radius: 8px; padding: 10px 20px; font-weight: 600; font-family: 'Inter', sans-serif; background: rgba(255,255,255,0.9); color: #333; border: none;">
                            <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                        </button>
            </div>
                </div>
            </div>
            <!-- Quick Actions -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="mb-3">
                        <h4 style="color: #212529; font-weight: 700; margin-bottom: 5px; font-size: 1.4rem; font-family: 'Inter', sans-serif;">Quick Actions</h4>
                        <p style="color: #495057; margin-bottom: 20px; font-size: 1.1rem; font-family: 'Inter', sans-serif; font-weight: 500;">Fast access to common tasks</p>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="card" style="background: #ffffff; border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); cursor: pointer; transition: transform 0.3s ease;" onclick="window.location.href='product_catalog.php'">
                                <div class="card-body text-center p-4">
                                    <div class="mb-3">
                                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #9b59b6, #8e44ad); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                                            <i class="bi bi-plus-circle" style="color: white; font-size: 1.5rem;"></i>
                                        </div>
                                    </div>
                                    <h6 style="color: #212529; font-weight: 700; margin-bottom: 5px; font-size: 1rem; font-family: 'Inter', sans-serif;">Add Product</h6>
                                    <small style="color: #495057; font-size: 0.9rem; font-family: 'Inter', sans-serif; font-weight: 500;">Create new product</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card" style="background: #ffffff; border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); cursor: pointer; transition: transform 0.3s ease;" onclick="window.location.href='order.php'">
                                <div class="card-body text-center p-4">
                                    <div class="mb-3">
                                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #e74c3c, #c0392b); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                                            <i class="bi bi-cart" style="color: white; font-size: 1.5rem;"></i>
                                        </div>
                                    </div>
                                    <h6 style="color: #212529; font-weight: 700; margin-bottom: 5px; font-size: 1rem; font-family: 'Inter', sans-serif;">View Orders</h6>
                                    <small style="color: #495057; font-size: 0.9rem; font-family: 'Inter', sans-serif; font-weight: 500;">Manage orders</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card" style="background: #ffffff; border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); cursor: pointer; transition: transform 0.3s ease;" onclick="window.location.href='vendor_requests.php'">
                                <div class="card-body text-center p-4">
                                    <div class="mb-3">
                                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #3498db, #2980b9); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                                            <i class="bi bi-clock" style="color: white; font-size: 1.5rem;"></i>
                                        </div>
                                    </div>
                                    <h6 style="color: #212529; font-weight: 700; margin-bottom: 5px; font-size: 1rem; font-family: 'Inter', sans-serif;">Pending Products</h6>
                                    <small style="color: #495057; font-size: 0.9rem; font-family: 'Inter', sans-serif; font-weight: 500;">Awaiting approval</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card" style="background: #ffffff; border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); cursor: pointer; transition: transform 0.3s ease;" onclick="window.location.href='sales_reports.php'">
                                <div class="card-body text-center p-4">
                                    <div class="mb-3">
                                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #1abc9c, #16a085); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                                            <i class="bi bi-graph-up" style="color: white; font-size: 1.5rem;"></i>
                                        </div>
                                    </div>
                                    <h6 style="color: #212529; font-weight: 700; margin-bottom: 5px; font-size: 1rem; font-family: 'Inter', sans-serif;">View Reports</h6>
                                    <small style="color: #495057; font-size: 0.9rem; font-family: 'Inter', sans-serif; font-weight: 500;">Sales analytics</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                
            <!-- Enhanced KPI Cards -->
            <div class="row mb-4">
                <div class="col-md-3 mb-4">
                    <div class="card" style="background: #ffffff; border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); transition: transform 0.3s ease;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon" style="width: 50px; height: 50px; background: linear-gradient(135deg, #ff6b6b, #ee5a52); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                        <i class="bi bi-cart-check" style="color: white; font-size: 1.5rem;"></i>
                                </div>
                                    <div>
                                        <h3 class="stat-number mb-1" id="total-orders" style="font-size: 2rem; font-weight: 700; color: #212529; margin: 0; font-family: 'Inter', sans-serif;">57</h3>
                                        <p class="stat-label mb-0" style="color: #495057; font-size: 0.9rem; font-weight: 600; font-family: 'Inter', sans-serif;">TOTAL ORDERS</p>
                                        <small style="color: #6c757d; font-size: 0.8rem; font-family: 'Inter', sans-serif; font-weight: 500;"><i class="bi bi-arrow-up me-1"></i>+12% from last month</small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="badge" style="background: #ff6b6b; color: white; font-size: 0.7rem; padding: 4px 8px; border-radius: 6px;">
                                        <i class="bi bi-trending-up me-1"></i>Active
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card" style="background: #ffffff; border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); transition: transform 0.3s ease;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon" style="width: 50px; height: 50px; background: linear-gradient(135deg, #4ecdc4, #44a08d); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                        <i class="bi bi-box-seam" style="color: white; font-size: 1.5rem;"></i>
                                </div>
                                    <div>
                                        <h3 class="stat-number mb-1" id="total-products" style="font-size: 2rem; font-weight: 700; color: #212529; margin: 0; font-family: 'Inter', sans-serif;">44</h3>
                                        <p class="stat-label mb-0" style="color: #495057; font-size: 0.9rem; font-weight: 600; font-family: 'Inter', sans-serif;">TOTAL PRODUCTS</p>
                                        <small style="color: #6c757d; font-size: 0.8rem; font-family: 'Inter', sans-serif; font-weight: 500;"><i class="bi bi-check-circle me-1"></i>All active</small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="badge" style="background: #4ecdc4; color: white; font-size: 0.7rem; padding: 4px 8px; border-radius: 6px;">
                                        <i class="bi bi-eye me-1"></i>Live
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card" style="background: #ffffff; border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); transition: transform 0.3s ease;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon" style="width: 50px; height: 50px; background: linear-gradient(135deg, #f39c12, #e67e22); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                        <i class="bi bi-currency-dollar" style="color: white; font-size: 1.5rem;"></i>
                                </div>
                                    <div>
                                        <h3 class="stat-number mb-1" id="total-revenue" style="font-size: 2rem; font-weight: 700; color: #212529; margin: 0; font-family: 'Inter', sans-serif;">₱24,500</h3>
                                        <p class="stat-label mb-0" style="color: #495057; font-size: 0.9rem; font-weight: 600; font-family: 'Inter', sans-serif;">TOTAL REVENUE</p>
                                        <small style="color: #6c757d; font-size: 0.8rem; font-family: 'Inter', sans-serif; font-weight: 500;"><i class="bi bi-arrow-up me-1"></i>+18% this month</small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="badge" style="background: #f39c12; color: white; font-size: 0.7rem; padding: 4px 8px; border-radius: 6px;">
                                        <i class="bi bi-graph-up me-1"></i>Growing
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card" style="background: #ffffff; border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); transition: transform 0.3s ease;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon" style="width: 50px; height: 50px; background: linear-gradient(135deg, #9b59b6, #8e44ad); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                        <i class="bi bi-people" style="color: white; font-size: 1.5rem;"></i>
                                    </div>
                                    <div>
                                        <h3 class="stat-number mb-1" id="total-customers" style="font-size: 2rem; font-weight: 700; color: #212529; margin: 0; font-family: 'Inter', sans-serif;">128</h3>
                                        <p class="stat-label mb-0" style="color: #495057; font-size: 0.9rem; font-weight: 600; font-family: 'Inter', sans-serif;">CUSTOMERS</p>
                                        <small style="color: #6c757d; font-size: 0.8rem; font-family: 'Inter', sans-serif; font-weight: 500;"><i class="bi bi-person-plus me-1"></i>+8 new this week</small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="badge" style="background: #9b59b6; color: white; font-size: 0.7rem; padding: 4px 8px; border-radius: 6px;">
                                        <i class="bi bi-heart me-1"></i>Loyal
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Status Overview -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card" style="background: #ffffff; border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
                        <div class="card-header" style="background: linear-gradient(135deg, #667eea, #764ba2); border: none; border-radius: 12px 12px 0 0; color: white;">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0" style="color: white !important; font-weight: 600; font-size: 1.1rem; text-shadow: 0 1px 2px rgba(0,0,0,0.3);"><i class="bi bi-graph-up me-2"></i>Order Status Overview</h5>
                                <span class="badge" style="background: rgba(255,255,255,0.2); color: white !important; font-size: 0.8rem; padding: 4px 8px; border-radius: 6px; text-shadow: 0 1px 2px rgba(0,0,0,0.3);">Real-time</span>
                </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <div class="d-flex align-items-center p-3" style="background: linear-gradient(135deg, #ff9a9e, #fecfef); border-radius: 10px;">
                                        <div class="me-3">
                                            <i class="bi bi-clock-history" style="font-size: 2rem; color: #ff6b6b;"></i>
                </div>
                                        <div>
                                            <h4 class="mb-1" style="color: #2c3e50; font-weight: 700; font-size: 1.5rem;">8</h4>
                                            <p class="mb-0" style="color: #7f8c8d; font-size: 0.9rem; font-weight: 600;">Pending Confirmation</p>
                        </div>
                </div>
                        </div>
                                <div class="col-md-3 mb-3">
                                    <div class="d-flex align-items-center p-3" style="background: linear-gradient(135deg, #a8edea, #fed6e3); border-radius: 10px;">
                                        <div class="me-3">
                                            <i class="bi bi-gear" style="font-size: 2rem; color: #4ecdc4;"></i>
                </div>
                                        <div>
                                            <h4 class="mb-1" style="color: #2c3e50; font-weight: 700; font-size: 1.5rem;">12</h4>
                                            <p class="mb-0" style="color: #7f8c8d; font-size: 0.9rem; font-weight: 600;">Processing</p>
                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="d-flex align-items-center p-3" style="background: linear-gradient(135deg, #d299c2, #fef9d7); border-radius: 10px;">
                                        <div class="me-3">
                                            <i class="bi bi-truck" style="font-size: 2rem; color: #9b59b6;"></i>
                                        </div>
                                        <div>
                                            <h4 class="mb-1" style="color: #2c3e50; font-weight: 700; font-size: 1.5rem;">15</h4>
                                            <p class="mb-0" style="color: #7f8c8d; font-size: 0.9rem; font-weight: 600;">Shipped</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="d-flex align-items-center p-3" style="background: linear-gradient(135deg, #89f7fe, #66a6ff); border-radius: 10px;">
                                        <div class="me-3">
                                            <i class="bi bi-check-circle" style="font-size: 2rem; color: #3498db;"></i>
                                        </div>
                                        <div>
                                            <h4 class="mb-1" style="color: #2c3e50; font-weight: 700; font-size: 1.5rem;">22</h4>
                                            <p class="mb-0" style="color: #7f8c8d; font-size: 0.9rem; font-weight: 600;">Delivered</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3 mb-4">
                    <div class="card" style="background: #ffffff; border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); transition: transform 0.3s ease;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon" style="width: 50px; height: 50px; background: linear-gradient(135deg, #2ecc71, #27ae60); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                        <i class="bi bi-credit-card" style="color: white; font-size: 1.5rem;"></i>
                                    </div>
                            <div>
                                        <h3 class="stat-number mb-1" id="total-payments" style="font-size: 2rem; font-weight: 700; color: #212529; margin: 0; font-family: 'Inter', sans-serif;">45</h3>
                                        <p class="stat-label mb-0" style="color: #495057; font-size: 0.9rem; font-weight: 600; font-family: 'Inter', sans-serif;">TOTAL PAYMENTS</p>
                                        <small style="color: #6c757d; font-size: 0.8rem; font-family: 'Inter', sans-serif; font-weight: 500;"><i class="bi bi-arrow-up me-1"></i>All payment records</small>
                            </div>
                        </div>
                                <div class="text-end">
                                    <span class="badge" style="background: #2ecc71; color: white; font-size: 0.7rem; padding: 4px 8px; border-radius: 6px;">
                                        <i class="bi bi-credit-card me-1"></i>Active
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card" style="background: #ffffff; border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); transition: transform 0.3s ease;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                    <div class="stat-icon" style="width: 50px; height: 50px; background: linear-gradient(135deg, #27ae60, #2ecc71); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                        <i class="bi bi-check-circle" style="color: white; font-size: 1.5rem;"></i>
                                        </div>
                                    <div>
                                        <h3 class="stat-number mb-1" id="verified-payments" style="font-size: 2rem; font-weight: 700; color: #212529; margin: 0; font-family: 'Inter', sans-serif;">38</h3>
                                        <p class="stat-label mb-0" style="color: #495057; font-size: 0.9rem; font-weight: 600; font-family: 'Inter', sans-serif;">VERIFIED</p>
                                        <small style="color: #6c757d; font-size: 0.8rem; font-family: 'Inter', sans-serif; font-weight: 500;"><i class="bi bi-check me-1"></i>Approved payments</small>
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
                    <div class="card" style="background: #ffffff; border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); transition: transform 0.3s ease;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon" style="width: 50px; height: 50px; background: linear-gradient(135deg, #f39c12, #e67e22); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                        <i class="bi bi-clock" style="color: white; font-size: 1.5rem;"></i>
                                    </div>
                                    <div>
                                        <h3 class="stat-number mb-1" id="pending-payments" style="font-size: 2rem; font-weight: 700; color: #212529; margin: 0; font-family: 'Inter', sans-serif;">5</h3>
                                        <p class="stat-label mb-0" style="color: #495057; font-size: 0.9rem; font-weight: 600; font-family: 'Inter', sans-serif;">PENDING</p>
                                        <small style="color: #6c757d; font-size: 0.8rem; font-family: 'Inter', sans-serif; font-weight: 500;"><i class="bi bi-hourglass me-1"></i>Awaiting verification</small>
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
                    <div class="card" style="background: #ffffff; border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); transition: transform 0.3s ease;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon" style="width: 50px; height: 50px; background: linear-gradient(135deg, #e74c3c, #c0392b); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                        <i class="bi bi-x-circle" style="color: white; font-size: 1.5rem;"></i>
                                    </div>
                                    <div>
                                        <h3 class="stat-number mb-1" id="rejected-payments" style="font-size: 2rem; font-weight: 700; color: #212529; margin: 0; font-family: 'Inter', sans-serif;">2</h3>
                                        <p class="stat-label mb-0" style="color: #495057; font-size: 0.9rem; font-weight: 600; font-family: 'Inter', sans-serif;">REJECTED</p>
                                        <small style="color: #6c757d; font-size: 0.8rem; font-family: 'Inter', sans-serif; font-weight: 500;"><i class="bi bi-x me-1"></i>Failed payments</small>
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
                
            <!-- RAEVOR Announcements & Sales Promo -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; border-radius: 16px; box-shadow: 0 8px 32px rgba(0,0,0,0.2); overflow: hidden;">
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <div class="d-flex align-items-center mb-3">
                                        <div style="width: 50px; height: 50px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                            <i class="bi bi-megaphone" style="color: white; font-size: 1.5rem;"></i>
                </div>
                            <div>
                                            <h4 style="color: white !important; font-weight: 700; margin: 0; font-size: 1.5rem; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">RAEVOR Platform Updates</h4>
                                            <p style="color: rgba(255,255,255,0.9) !important; margin: 5px 0 0 0; font-size: 0.95rem; text-shadow: 0 1px 2px rgba(0,0,0,0.3);">Stay updated with the latest features and announcements</p>
                            </div>
                        </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div style="background: rgba(255,255,255,0.1); padding: 15px; border-radius: 10px; margin-bottom: 10px;">
                                                <h6 style="color: white !important; font-weight: 600; margin-bottom: 5px; font-size: 1rem; text-shadow: 0 1px 2px rgba(0,0,0,0.3);">
                                                    <i class="bi bi-gift me-2"></i>New Seller Bonus Program
                                                </h6>
                                                <p style="color: rgba(255,255,255,0.9) !important; margin: 0; font-size: 0.9rem; text-shadow: 0 1px 2px rgba(0,0,0,0.3);">Earn 5% bonus commission on first 10 sales this month!</p>
                                </div>
                </div>
                                        <div class="col-md-6">
                                            <div style="background: rgba(255,255,255,0.1); padding: 15px; border-radius: 10px; margin-bottom: 10px;">
                                                <h6 style="color: white !important; font-weight: 600; margin-bottom: 5px; font-size: 1rem; text-shadow: 0 1px 2px rgba(0,0,0,0.3);">
                                                    <i class="bi bi-lightning me-2"></i>Flash Sale Promotion
                                            </h6>
                                                <p style="color: rgba(255,255,255,0.9) !important; margin: 0; font-size: 0.9rem; text-shadow: 0 1px 2px rgba(0,0,0,0.3);">50% off on all delivery fees for orders above ₱500</p>
                                        </div>
                </div>
                        </div>
                                </div>
                                <div class="col-md-4 text-center">
                                    <div style="background: rgba(255,255,255,0.15); padding: 20px; border-radius: 12px;">
                                        <h3 style="color: white !important; font-weight: 700; margin-bottom: 5px; font-size: 2rem; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">₱2,450</h3>
                                        <p style="color: rgba(255,255,255,0.9) !important; margin: 0; font-size: 0.9rem; text-shadow: 0 1px 2px rgba(0,0,0,0.3);">This Month's Earnings</p>
                                        <div style="background: rgba(46, 204, 113, 0.3); padding: 8px 12px; border-radius: 20px; margin-top: 10px; display: inline-block;">
                                            <span style="color: white !important; font-weight: 600; font-size: 0.85rem; text-shadow: 0 1px 2px rgba(0,0,0,0.3);">
                                                <i class="bi bi-arrow-up me-1"></i>+15% from last month
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Analytics Charts -->
            <div class="row mb-4">
                <div class="col-md-8 mb-4">
                    <div class="card" style="background: #ffffff; border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
                        <div class="card-header" style="background: linear-gradient(135deg, #667eea, #764ba2); border: none; border-radius: 12px 12px 0 0; color: white;">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0" style="color: white !important; font-weight: 600; font-size: 1.1rem; text-shadow: 0 1px 2px rgba(0,0,0,0.3);">Sales Performance</h5>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-light active" onclick="changeChartPeriod('week')" style="color: white; border-color: rgba(255,255,255,0.5);">Week</button>
                                    <button type="button" class="btn btn-outline-light" onclick="changeChartPeriod('month')" style="color: white; border-color: rgba(255,255,255,0.5);">Month</button>
                                    <button type="button" class="btn btn-outline-light" onclick="changeChartPeriod('year')" style="color: white; border-color: rgba(255,255,255,0.5);">Year</button>
                            </div>
                        </div>
                                </div>
                        <div class="card-body p-4">
                            <canvas id="salesChart" width="400" height="200"></canvas>
                                        </div>
                                    </div>
                                </div>
                <div class="col-md-4 mb-4">
                    <div class="card" style="background: #ffffff; border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
                        <div class="card-header" style="background: linear-gradient(135deg, #f093fb, #f5576c); border: none; border-radius: 12px 12px 0 0; color: white;">
                            <h5 class="card-title mb-0" style="color: white !important; font-weight: 600; font-size: 1.1rem; text-shadow: 0 1px 2px rgba(0,0,0,0.3);">Product Categories</h5>
                        </div>
                        <div class="card-body p-4">
                            <canvas id="categoryChart" width="300" height="200"></canvas>
                        </div>
                        </div>
        </div>
    </div>

            <!-- Enhanced Recent Activity -->
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card" style="background: #ffffff; border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
                        <div class="card-header" style="background: linear-gradient(135deg, #ff6b6b, #ee5a52); border: none; border-radius: 12px 12px 0 0; color: white;">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0" style="color: white !important; font-weight: 600; font-size: 1.1rem; text-shadow: 0 1px 2px rgba(0,0,0,0.3);"><i class="bi bi-box-seam me-2"></i>Top Selling Products</h5>
                                <a href="product_catalog.php" class="btn btn-sm" style="background: rgba(255,255,255,0.2); color: white !important; border: none; border-radius: 6px; font-weight: 500; text-shadow: 0 1px 2px rgba(0,0,0,0.3);">View All</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="recent-products">
                                <div class="d-flex align-items-center p-3 mb-3" style="border: 1px solid #ecf0f1; border-radius: 8px; background: linear-gradient(135deg, #f8f9fa, #ffffff);">
                                    <div class="me-3">
                                        <img src="uploads/products/default.jpg" alt="Product" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 style="color: #2c3e50; font-weight: 600; margin-bottom: 5px; font-size: 1rem;">Sample White Cargo Pants</h6>
                                        <small style="color: #7f8c8d; font-size: 0.9rem;">Dress • 15 sold</small>
                                    </div>
                                    <div class="text-end">
                                        <div style="color: #2c3e50; font-weight: 600; margin-bottom: 5px; font-size: 1rem;">₱1,250</div>
                                        <span class="badge" style="background: #27ae60; color: white; font-size: 0.7rem; padding: 2px 6px; border-radius: 4px;">BESTSELLER</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center p-3 mb-3" style="border: 1px solid #ecf0f1; border-radius: 8px; background: linear-gradient(135deg, #f8f9fa, #ffffff);">
                                    <div class="me-3">
                                        <img src="uploads/products/default.jpg" alt="Product" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 style="color: #2c3e50; font-weight: 600; margin-bottom: 5px; font-size: 1rem;">Vintage Denim Jacket</h6>
                                        <small style="color: #7f8c8d; font-size: 0.9rem;">Clothing • 8 sold</small>
                                    </div>
                                    <div class="text-end">
                                        <div style="color: #2c3e50; font-weight: 600; margin-bottom: 5px; font-size: 1rem;">₱2,800</div>
                                        <span class="badge" style="background: #3498db; color: white; font-size: 0.7rem; padding: 2px 6px; border-radius: 4px;">TRENDING</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center p-3" style="border: 1px solid #ecf0f1; border-radius: 8px; background: linear-gradient(135deg, #f8f9fa, #ffffff);">
                                    <div class="me-3">
                                        <img src="uploads/products/default.jpg" alt="Product" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 style="color: #2c3e50; font-weight: 600; margin-bottom: 5px; font-size: 1rem;">Classic Sneakers</h6>
                                        <small style="color: #7f8c8d; font-size: 0.9rem;">Shoes • 5 sold</small>
                                    </div>
                                    <div class="text-end">
                                        <div style="color: #2c3e50; font-weight: 600; margin-bottom: 5px; font-size: 1rem;">₱3,500</div>
                                        <span class="badge" style="background: #9b59b6; color: white; font-size: 0.7rem; padding: 2px 6px; border-radius: 4px;">NEW</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card" style="background: #ffffff; border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
                        <div class="card-header" style="background: linear-gradient(135deg, #4ecdc4, #44a08d); border: none; border-radius: 12px 12px 0 0; color: white;">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0" style="color: white !important; font-weight: 600; font-size: 1.1rem; text-shadow: 0 1px 2px rgba(0,0,0,0.3);"><i class="bi bi-cart-check me-2"></i>Recent Orders</h5>
                                <a href="order.php" class="btn btn-sm" style="background: rgba(255,255,255,0.2); color: white !important; border: none; border-radius: 6px; font-weight: 500; text-shadow: 0 1px 2px rgba(0,0,0,0.3);">View All</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="recent-orders">
                                <div class="d-flex justify-content-between align-items-center mb-3 p-3" style="border: 1px solid #ecf0f1; border-radius: 8px; background: linear-gradient(135deg, #f8f9fa, #ffffff);">
                                    <div>
                                        <strong style="color: #2c3e50; font-size: 1rem;">Order #51</strong>
                                        <br><small style="color: #7f8c8d; font-size: 0.9rem;">Jane Smith • 2 items</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge" style="background: #f39c12; color: white; font-size: 0.7rem; padding: 4px 8px; border-radius: 4px;">PENDING</span>
                                        <br><small style="color: #7f8c8d; font-size: 0.9rem;">₱2,800</small>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-3 p-3" style="border: 1px solid #ecf0f1; border-radius: 8px; background: linear-gradient(135deg, #f8f9fa, #ffffff);">
                                    <div>
                                        <strong style="color: #2c3e50; font-size: 1rem;">Order #32</strong>
                                        <br><small style="color: #7f8c8d; font-size: 0.9rem;">John Doe • 1 item</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge" style="background: #3498db; color: white; font-size: 0.7rem; padding: 4px 8px; border-radius: 4px;">PROCESSING</span>
                                        <br><small style="color: #7f8c8d; font-size: 0.9rem;">₱800</small>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center p-3" style="border: 1px solid #ecf0f1; border-radius: 8px; background: linear-gradient(135deg, #f8f9fa, #ffffff);">
                                    <div>
                                        <strong style="color: #2c3e50; font-size: 1rem;">Order #22</strong>
                                        <br><small style="color: #7f8c8d; font-size: 0.9rem;">Mike Johnson • 3 items</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge" style="background: #27ae60; color: white; font-size: 0.7rem; padding: 4px 8px; border-radius: 4px;">DELIVERED</span>
                                        <br><small style="color: #7f8c8d; font-size: 0.9rem;">₱3,500</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Insights -->
            <div class="row mb-4">
                <div class="col-md-4 mb-4">
                    <div class="card" style="background: #ffffff; border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
                        <div class="card-header" style="background: linear-gradient(135deg, #667eea, #764ba2); border: none; border-radius: 12px 12px 0 0; color: white;">
                            <h5 class="card-title mb-0" style="color: white !important; font-weight: 600; font-size: 1.1rem; text-shadow: 0 1px 2px rgba(0,0,0,0.3);"><i class="bi bi-trophy me-2"></i>Performance</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="text-center mb-3">
                                <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                                    <span style="color: white; font-size: 1.5rem; font-weight: 700;">4.8</span>
                                </div>
                                <h6 style="color: #2c3e50; font-weight: 600; margin-top: 10px;">Seller Rating</h6>
                                <small style="color: #7f8c8d;">Based on 45 reviews</small>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span style="color: #7f8c8d; font-size: 0.9rem;">Response Rate</span>
                                <span style="color: #2c3e50; font-weight: 600;">98%</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span style="color: #7f8c8d; font-size: 0.9rem;">On-time Delivery</span>
                                <span style="color: #2c3e50; font-weight: 600;">95%</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span style="color: #7f8c8d; font-size: 0.9rem;">Order Fulfillment</span>
                                <span style="color: #2c3e50; font-weight: 600;">100%</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card" style="background: #ffffff; border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
                        <div class="card-header" style="background: linear-gradient(135deg, #f093fb, #f5576c); border: none; border-radius: 12px 12px 0 0; color: white;">
                            <h5 class="card-title mb-0" style="color: white !important; font-weight: 600; font-size: 1.1rem; text-shadow: 0 1px 2px rgba(0,0,0,0.3);"><i class="bi bi-lightning me-2"></i>Quick Stats</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row text-center">
                                <div class="col-6 mb-3">
                                    <div style="background: linear-gradient(135deg, #ff9a9e, #fecfef); padding: 15px; border-radius: 10px;">
                                        <h4 style="color: #2c3e50; font-weight: 700; margin: 0;">₱2,450</h4>
                                        <small style="color: #7f8c8d; font-size: 0.8rem;">This Month</small>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div style="background: linear-gradient(135deg, #a8edea, #fed6e3); padding: 15px; border-radius: 10px;">
                                        <h4 style="color: #2c3e50; font-weight: 700; margin: 0;">+18%</h4>
                                        <small style="color: #7f8c8d; font-size: 0.8rem;">Growth</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div style="background: linear-gradient(135deg, #d299c2, #fef9d7); padding: 15px; border-radius: 10px;">
                                        <h4 style="color: #2c3e50; font-weight: 700; margin: 0;">128</h4>
                                        <small style="color: #7f8c8d; font-size: 0.8rem;">Customers</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div style="background: linear-gradient(135deg, #89f7fe, #66a6ff); padding: 15px; border-radius: 10px;">
                                        <h4 style="color: #2c3e50; font-weight: 700; margin: 0;">4.8★</h4>
                                        <small style="color: #7f8c8d; font-size: 0.8rem;">Rating</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card" style="background: #ffffff; border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
                        <div class="card-header" style="background: linear-gradient(135deg, #4facfe, #00f2fe); border: none; border-radius: 12px 12px 0 0; color: white;">
                            <h5 class="card-title mb-0" style="color: white !important; font-weight: 600; font-size: 1.1rem; text-shadow: 0 1px 2px rgba(0,0,0,0.3);"><i class="bi bi-bell me-2"></i>Notifications</h5>
                        </div>
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center mb-3 p-2" style="background: #fff3cd; border-radius: 8px; border-left: 4px solid #ffc107;">
                                <i class="bi bi-exclamation-triangle me-2" style="color: #856404;"></i>
                                <div>
                                    <small style="color: #856404; font-weight: 600;">Low Stock Alert</small>
                                    <br><small style="color: #856404; font-size: 0.8rem;">3 products running low</small>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-3 p-2" style="background: #d1ecf1; border-radius: 8px; border-left: 4px solid #17a2b8;">
                                <i class="bi bi-info-circle me-2" style="color: #0c5460;"></i>
                                <div>
                                    <small style="color: #0c5460; font-weight: 600;">New Order</small>
                                    <br><small style="color: #0c5460; font-size: 0.8rem;">Order #51 needs confirmation</small>
                                </div>
                            </div>
                            <div class="d-flex align-items-center p-2" style="background: #d4edda; border-radius: 8px; border-left: 4px solid #28a745;">
                                <i class="bi bi-check-circle me-2" style="color: #155724;"></i>
                                <div>
                                    <small style="color: #155724; font-weight: 600;">Payment Received</small>
                                    <br><small style="color: #155724; font-size: 0.8rem;">₱2,800 from Order #32</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Logout Modal -->
    <?php include 'includes/logout_modal.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle sidebar
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
        
        // Load dashboard data
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
            
            loadDashboardData();
            
            // Refresh button functionality
            document.getElementById('refreshDashboardBtn').addEventListener('click', function() {
                const btn = this;
                const originalText = btn.innerHTML;
                
                // Show loading state
                btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Refreshing...';
                btn.disabled = true;
                
                // Reload dashboard data
                loadDashboardData().finally(() => {
                    // Reset button state
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                });
            });
        });
        
        function loadDashboardData() {
            return Promise.all([
                // Load stats
                fetch('api/orders.php?action=dashboard_stats')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('total-products').textContent = data.stats.total_products || '0';
                            document.getElementById('total-orders').textContent = data.stats.total_orders || '0';
                            document.getElementById('total-sales').textContent = '₱' + (data.stats.total_sales || '0');
                            document.getElementById('pending-deliveries').textContent = data.stats.pending_orders || '0';
                        }
                    })
                    .catch(error => {
                        console.error('Error loading dashboard stats:', error);
                    }),

                // Load payment statistics
                fetch('api/payments.php?action=stats')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('total-payments').textContent = data.stats.total || '0';
                            document.getElementById('verified-payments').textContent = data.stats.verified || '0';
                            document.getElementById('pending-payments').textContent = data.stats.pending || '0';
                            document.getElementById('rejected-payments').textContent = data.stats.rejected || '0';
                        }
                    })
                    .catch(error => {
                        console.error('Error loading payment stats:', error);
                    }),

                // Load recent orders
                fetch('api/orders.php?action=recent_orders&limit=5')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.orders) {
                            const container = document.getElementById('recent-orders');
                            container.innerHTML = '';
                            
                            if (data.orders.length === 0) {
                                container.innerHTML = '<p class="text-muted text-center">No recent orders</p>';
                            } else {
                                data.orders.forEach(order => {
                                    const orderElement = document.createElement('div');
                                    orderElement.className = 'd-flex justify-content-between align-items-center mb-2';
                                    orderElement.innerHTML = `
                                        <div>
                                            <strong>Order #${order.id}</strong>
                                            <br><small class="text-muted">${order.customer_name}</small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-${getStatusColor(order.status)}">${order.status}</span>
                                            <br><small class="text-muted">₱${order.total_amount}</small>
                                        </div>
                                    `;
                                    container.appendChild(orderElement);
                                });
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error loading recent orders:', error);
                        document.getElementById('recent-orders').innerHTML = '<p class="text-muted text-center">Error loading orders</p>';
                    }),

                // Load top products
                fetch('api/products.php?action=top_products&limit=5')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.products) {
                            const container = document.getElementById('top-products');
                            container.innerHTML = '';
                            
                            if (data.products.length === 0) {
                                container.innerHTML = '<p class="text-muted text-center">No products found</p>';
                            } else {
                                data.products.forEach(product => {
                                    const productElement = document.createElement('div');
                                    productElement.className = 'd-flex justify-content-between align-items-center mb-2';
                                    productElement.innerHTML = `
                                        <div>
                                            <strong>${product.name}</strong>
                                            <br><small class="text-muted">Stock: ${product.stock}</small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-success">₱${product.price}</span>
                                        </div>
                                    `;
                                    container.appendChild(productElement);
                                });
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error loading top products:', error);
                        document.getElementById('top-products').innerHTML = '<p class="text-muted text-center">Error loading products</p>';
                    })
            ]);
        }

        function getStatusColor(status) {
            switch(status.toLowerCase()) {
                case 'pending': return 'warning';
                case 'processing': return 'info';
                case 'shipped': return 'primary';
                case 'delivered': return 'success';
                case 'cancelled': return 'danger';
                default: return 'secondary';
            }
        }
        
        // Enhanced dashboard interactions
        document.addEventListener('DOMContentLoaded', function() {
            // Add hover effects to all cards
            document.querySelectorAll('.card').forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px)';
                    this.style.boxShadow = '0 8px 30px rgba(0,0,0,0.15)';
                    this.style.transition = 'all 0.3s ease';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = '0 4px 20px rgba(0,0,0,0.1)';
                });
            });

            // Add click effects to quick action cards
            document.querySelectorAll('.card[onclick]').forEach(card => {
                card.addEventListener('click', function() {
                    this.style.transform = 'translateY(-2px) scale(0.98)';
                    setTimeout(() => {
                        this.style.transform = 'translateY(0) scale(1)';
                    }, 150);
                });
            });

            // Add pulse animation to notification badges
            const notificationBadges = document.querySelectorAll('.badge');
            notificationBadges.forEach(badge => {
                if (badge.textContent.includes('PENDING') || badge.textContent.includes('NEW')) {
                    badge.style.animation = 'pulse 2s infinite';
                }
            });

            // Add real-time updates simulation
            setInterval(() => {
                updateRealTimeStats();
            }, 30000); // Update every 30 seconds
            
            // Initialize charts
            initializeCharts();
        });

        // Real-time stats update simulation
        function updateRealTimeStats() {
            // Simulate real-time updates
            const stats = ['total-orders', 'total-products', 'total-revenue', 'total-customers'];
            stats.forEach(statId => {
                const element = document.getElementById(statId);
                if (element) {
                    element.style.transform = 'scale(1.05)';
                    element.style.color = '#28a745';
                    setTimeout(() => {
                        element.style.transform = 'scale(1)';
                        element.style.color = '#212529';
                    }, 500);
                }
            });
        }
        
        // Chart initialization
        let salesChart, categoryChart;
        
        function initializeCharts() {
            // Sales Performance Chart
            const salesCtx = document.getElementById('salesChart').getContext('2d');
            salesChart = new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                    datasets: [{
                        label: 'Sales (₱)',
                        data: [1200, 1900, 3000, 5000, 2000, 3000, 4500],
                        borderColor: '#667eea',
                        backgroundColor: 'rgba(102, 126, 234, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#667eea',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0,0,0,0.1)'
                            },
                            ticks: {
                                color: '#666',
                                font: {
                                    size: 12,
                                    weight: '500'
                                },
                                callback: function(value) {
                                    return '₱' + value.toLocaleString();
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#666',
                                font: {
                                    size: 12,
                                    weight: '500'
                                }
                            }
                        }
                    },
                    elements: {
                        point: {
                            hoverRadius: 8
                        }
                    }
                }
            });
            
            // Product Categories Chart
            const categoryCtx = document.getElementById('categoryChart').getContext('2d');
            categoryChart = new Chart(categoryCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Electronics', 'Clothing', 'Home & Garden', 'Sports', 'Books', 'Others'],
                    datasets: [{
                        data: [30, 25, 20, 15, 5, 5],
                        backgroundColor: [
                            '#667eea',
                            '#f093fb',
                            '#4facfe',
                            '#43e97b',
                            '#fa709a',
                            '#ffecd2'
                        ],
                        borderWidth: 0,
                        cutout: '60%'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                                font: {
                                    size: 11,
                                    weight: '500'
                                },
                                color: '#666'
                            }
                        }
                    }
                }
            });
        }
        
        // Chart period change function
        function changeChartPeriod(period) {
            // Update button states
            document.querySelectorAll('.btn-group button').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
            
            // Update chart data based on period
            let newData, newLabels;
            
            switch(period) {
                case 'week':
                    newLabels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                    newData = [800, 1200, 1500, 1100, 1800, 2200, 1900];
                    break;
                case 'month':
                    newLabels = ['Week 1', 'Week 2', 'Week 3', 'Week 4'];
                    newData = [4500, 6200, 5800, 7200];
                    break;
                case 'year':
                    newLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                    newData = [1200, 1900, 3000, 5000, 2000, 3000, 4500, 3800, 4200, 5100, 4800, 5500];
                    break;
            }
            
            salesChart.data.labels = newLabels;
            salesChart.data.datasets[0].data = newData;
            salesChart.update();
        }
    </script>
</body>
</html>
