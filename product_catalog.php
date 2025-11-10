<?php 
session_start();

// Include authentication and database
require_once 'includes/auth.php';
require_once 'config/database.php';

// Require authentication to access product catalog
requireAuth('login.php');

$currentPage = basename($_SERVER['PHP_SELF']);

// Get products and categories
try {
    $pdo = getDBConnection();
    
    // Get seller's vendor_id from session
    $vendorId = $_SESSION['vendor_id'] ?? null;
    
    // Get all categories for the dropdown
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
    $categories = $stmt->fetchAll();
    
    // Get subcategories grouped by category
    $stmt = $pdo->query("SELECT s.*, c.name as category_name FROM subcategories s LEFT JOIN categories c ON s.category_id = c.id ORDER BY c.name, s.name");
    $subcategories = $stmt->fetchAll();
    
    // Group subcategories by category
    $subcategoriesByCategory = [];
    foreach ($subcategories as $subcat) {
        $subcategoriesByCategory[$subcat['category_id']][] = $subcat;
    }
    
    // Get products with category and subcategory names (filtered by vendor if seller has vendor_id)
    // ONLY show approved/active products (status != 'pending')
    if ($vendorId) {
        $stmt = $pdo->prepare("SELECT p.*, c.name as category_name, s.name as subcategory_name 
                              FROM products p 
                              LEFT JOIN categories c ON p.category_id = c.id 
                              LEFT JOIN subcategories s ON p.subcategory_id = s.id 
                              WHERE p.vendor_id = ? AND p.status != 'pending'
                              ORDER BY p.created_at DESC");
        $stmt->execute([$vendorId]);
        $products = $stmt->fetchAll();
        // Fallback to global sample products if vendor has none yet
        if (!$products || count($products) === 0) {
            $stmt = $pdo->query("SELECT p.*, c.name as category_name, s.name as subcategory_name 
                                FROM products p 
                                LEFT JOIN categories c ON p.category_id = c.id 
                                LEFT JOIN subcategories s ON p.subcategory_id = s.id 
                                WHERE p.status != 'pending'
                                ORDER BY p.created_at DESC");
            $products = $stmt->fetchAll();
        }
    } else {
        $stmt = $pdo->query("SELECT p.*, c.name as category_name, s.name as subcategory_name 
                            FROM products p 
                            LEFT JOIN categories c ON p.category_id = c.id 
                            LEFT JOIN subcategories s ON p.subcategory_id = s.id 
                            WHERE p.status != 'pending'
                            ORDER BY p.created_at DESC");
        $products = $stmt->fetchAll();
    }
    
} catch(PDOException $e) {
    $dbError = "Database error: " . $e->getMessage();
    $categories = [];
    $products = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Catalog - Core Transaction 2</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="css/styles.css?v=<?php echo filemtime('css/styles.css'); ?>" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .product-card {
            transition: all 0.3s ease;
            border-radius: 12px;
            overflow: hidden;
            height: 100%;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        }
        
        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background-color: #f8f9fa;
        }
        
        .product-info {
            padding: 15px;
        }
        
        .product-name {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 8px;
            color: #212529;
        }
        
        .product-price {
            font-size: 1.3rem;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 8px;
        }
        
        .product-category {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 8px;
        }
        
        .product-stock {
            font-size: 0.9rem;
            padding: 4px 12px;
            border-radius: 20px;
            display: inline-block;
        }
        
        .stock-in {
            background: rgba(34, 197, 94, 0.1);
            color: #22c55e;
        }
        
        .stock-low {
            background: rgba(251, 146, 60, 0.1);
            color: #fb923c;
        }
        
        .stock-out {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }
        
        .product-actions {
            padding: 10px 15px;
            background-color: #f8f9fa;
            border-top: 1px solid #e9ecef;
            display: flex;
            gap: 8px;
        }
        
        .page-header {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        }
        
        .page-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #212529;
            margin-bottom: 8px;
        }
        
        .filter-section {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        
        .modal-content {
            border-radius: 15px;
            border: none;
        }
        
        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0;
        }
        
        .form-label {
            font-weight: 600;
            color: #495057;
        }
        
        .preview-image {
            max-width: 200px;
            max-height: 200px;
            border-radius: 8px;
            margin-top: 10px;
        }
        
        .product-detail-image-container {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
        }
        
        .product-detail-image-container img {
            max-height: 400px;
            width: 100%;
            object-fit: contain;
        }
        
        #viewProductModal .modal-body {
            padding: 30px;
        }
        
        #viewProductModal h3 {
            font-weight: 700;
            color: #212529;
        }
        
        #viewProductModal h6 {
            font-weight: 600;
            color: #495057;
            margin-bottom: 10px;
        }
        
        #viewProductModal hr {
            margin: 20px 0;
            opacity: 0.1;
        }
        
        #addCustomCategoryDiv, #addCustomSubcategoryDiv,
        #editCustomCategoryDiv, #editCustomSubcategoryDiv {
            animation: slideDown 0.3s ease-out;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        #addCustomCategoryDiv input, #addCustomSubcategoryDiv input,
        #editCustomCategoryDiv input, #editCustomSubcategoryDiv input {
            border-left: 3px solid #667eea;
            background-color: #f8f9fa;
        }
        
        /* Product Specifications Styles */
        .size-options, .color-options {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 15px;
        }
        
        .size-options .form-check,
        .color-options .form-check {
            margin-bottom: 0;
            margin-right: 0;
        }
        
        .size-options .form-check-input {
            margin-right: 5px;
        }
        
        .color-swatch {
            display: inline-block;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            margin-right: 6px;
            vertical-align: middle;
            border: 1px solid #ddd;
        }
        
        .color-options .form-check-label {
            display: flex;
            align-items: center;
            font-size: 0.9rem;
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 6px;
            transition: background-color 0.2s ease;
        }
        
        .color-options .form-check-label:hover {
            background-color: #f8f9fa;
        }
        
        .size-options .form-check-label {
            font-size: 0.9rem;
            cursor: pointer;
            padding: 6px 12px;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            transition: all 0.2s ease;
            background-color: #fff;
        }
        
        .size-options .form-check-input:checked + .form-check-label {
            background-color: #667eea;
            color: white;
            border-color: #667eea;
        }
        
        .color-options .form-check-input:checked + .form-check-label {
            background-color: #667eea;
            color: white;
        }
        
        .variant-stock-item {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 8px;
        }
        
        .variant-stock-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #495057;
            margin-bottom: 5px;
        }
        
        .variant-stock-input {
            width: 80px;
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

            <li class="sidebar-item has-submenu <?php if(in_array($currentPage, ['order.php','return_management.php'])) echo 'active'; ?>">
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
            <!-- Page Header -->
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="page-title">Product Catalog</h2>
                        <p class="page-subtitle">Manage your product inventory and catalog</p>
                    </div>
                    <div>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                            <i class="bi bi-plus-circle me-2"></i>Add New Product
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Info Alert -->
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Product Approval Process:</strong> 
                <br>
                • New products are submitted for admin approval (CT3)
                <br>
                • Check <a href="vendor_requests.php" class="alert-link"><i class="bi bi-clock-history me-1"></i>Pending Products</a> to see products awaiting approval
                <br>
                • Approved products will appear in this catalog automatically
                <br>
                • You can edit or cancel pending products before approval
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>

            <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>Product added successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <?php if(isset($_GET['updated'])): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>Product updated successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <?php if(isset($_GET['deleted'])): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="bi bi-trash me-2"></i>Product deleted successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <?php if(isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i><?php echo htmlspecialchars(urldecode($_GET['error'])); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <?php if(isset($_GET['upload_error'])): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i>Image upload failed: <?php echo htmlspecialchars(urldecode($_GET['upload_error'])); ?>. Product saved with default image.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <?php if(isset($dbError)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Database Error!</strong> <?php echo $dbError; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <!-- Filter Section -->
            <div class="filter-section">
                <div class="row g-3">
                    <div class="col-md-3">
                        <input type="text" class="form-control" id="searchProduct" placeholder="Search products...">
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" id="filterCategory">
                            <option value="">All Categories</option>
                            <?php foreach($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" id="filterSubcategory">
                            <option value="">All Subcategories</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" id="filterStock">
                            <option value="">All Stock Levels</option>
                            <option value="in">In Stock</option>
                            <option value="low">Low Stock</option>
                            <option value="out">Out of Stock</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" id="filterStatus">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="pending">Pending</option>
                            <option value="out_of_stock">Out of Stock</option>
                            <option value="phase_out">Phase Out</option>
                            <option value="unavailable">Unavailable</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-secondary w-100" onclick="resetFilters()" title="Reset Filters">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="row" id="productsGrid">
                <?php if(empty($products)): ?>
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="bi bi-clock-history" style="font-size: 4rem; color: #cbd5e1;"></i>
                        <h4 class="mt-3 text-muted">No Approved Products Yet</h4>
                        <p class="text-muted">Products you add will appear here after admin approval.</p>
                        <p class="text-muted small">Check <a href="vendor_requests.php" class="text-decoration-none"><i class="bi bi-clock-history me-1"></i>Pending Products</a> to see products awaiting approval.</p>
                        <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addProductModal">
                            <i class="bi bi-plus-circle me-2"></i>Add New Product
                        </button>
                    </div>
                </div>
                <?php else: ?>
                    <?php foreach($products as $product): 
                        $stock = (int)($product['stock_quantity'] ?? 0);
                        $stockClass = $stock > 10 ? 'stock-in' : ($stock > 0 ? 'stock-low' : 'stock-out');
                        $stockText = $stock > 10 ? 'In Stock' : ($stock > 0 ? 'Low Stock' : 'Out of Stock');
                    ?>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 product-item" 
                         data-name="<?php echo strtolower($product['name']); ?>"
                         data-category="<?php echo $product['category_id']; ?>"
                         data-subcategory="<?php echo $product['subcategory_id']; ?>"
                         data-stock="<?php echo $stock > 10 ? 'in' : ($stock > 0 ? 'low' : 'out'); ?>"
                         data-status="<?php echo $product['status'] ?? 'active'; ?>">
                        <div class="card product-card">
                            <?php 
                                $rawImageUrl = $product['image_url'] ?? '';
                                $resolvedImageUrl = 'uploads/products/default.svg';
                                if ($rawImageUrl) {
                                    // If absolute URL, use as-is; else verify file exists relative to app root
                                    if (preg_match('/^https?:\/\//i', $rawImageUrl)) {
                                        $resolvedImageUrl = $rawImageUrl;
                                    } else if (file_exists($rawImageUrl)) {
                                        $resolvedImageUrl = $rawImageUrl;
                                    }
                                }
                            ?>
                            <img src="<?php echo htmlspecialchars($resolvedImageUrl); ?>" 
                                 class="product-image" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                                 onerror="this.src='uploads/products/default.svg'"
                                 loading="lazy">
                            <div class="product-info">
                                <h5 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h5>
                                <div class="product-price">₱<?php echo number_format($product['price'], 2); ?></div>
                                <div class="product-category">
                                    <i class="bi bi-tag me-1"></i><?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?>
                                    <?php if ($product['subcategory_name']): ?>
                                        <br><small class="text-muted"><i class="bi bi-tags me-1"></i><?php echo htmlspecialchars($product['subcategory_name']); ?></small>
                                    <?php endif; ?>
                                </div>
                                <div class="d-flex gap-2 align-items-center mb-2">
                                    <span class="product-stock <?php echo $stockClass; ?>">
                                        <?php echo $stockText; ?> (<?php echo $stock; ?>)
                                    </span>
                                    <?php 
                                    $productStatus = $product['status'] ?? 'active';
                                    $statusBadgeClass = match($productStatus) {
                                        'active' => 'bg-success',
                                        'inactive' => 'bg-secondary',
                                        'pending' => 'bg-warning text-dark',
                                        'out_of_stock' => 'bg-danger',
                                        'phase_out' => 'bg-info',
                                        'unavailable' => 'bg-dark',
                                        default => 'bg-secondary'
                                    };
                                    $statusText = match($productStatus) {
                                        'active' => 'Active',
                                        'inactive' => 'Inactive',
                                        'pending' => 'Pending',
                                        'out_of_stock' => 'Out of Stock',
                                        'phase_out' => 'Phase Out',
                                        'unavailable' => 'Unavailable',
                                        default => ucfirst($productStatus)
                                    };
                                    ?>
                                    <span class="badge <?php echo $statusBadgeClass; ?>">
                                        <?php echo $statusText; ?>
                                    </span>
                                </div>
                            </div>
                            <div class="product-actions">
                                <button class="btn btn-sm btn-outline-info" onclick="viewProduct(<?php echo htmlspecialchars(json_encode($product)); ?>)" title="View Details">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-primary flex-fill" onclick="editProduct(<?php echo htmlspecialchars(json_encode($product)); ?>)">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Add New Product</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="addProductForm" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="create">
                    <input type="hidden" name="custom_category_name" id="addCustomCategoryName">
                    <input type="hidden" name="custom_subcategory_name" id="addCustomSubcategoryName">
                    <div class="modal-body">
                        <!-- Line 1: Product Name (Full Width) -->
                        <div class="mb-3">
                            <label class="form-label">Product Name *</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        
                        <!-- Line 2: Category and Subcategory -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Category *</label>
                                <div class="input-group">
                                    <select class="form-select" name="category_id" id="addCategory" required>
                                        <option value="">Select Category</option>
                                        <?php foreach($categories as $cat): ?>
                                            <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                        <?php endforeach; ?>
                                        <option value="custom">+ Add New Category</option>
                                    </select>
                                    <button class="btn btn-outline-secondary" type="button" data-bs-toggle="tooltip" title="Category not in list? Click to add new">
                                        <i class="bi bi-question-circle"></i>
                                    </button>
                                </div>
                                <!-- Custom Category Input (Hidden by default) -->
                                <div id="addCustomCategoryDiv" style="display: none;" class="mt-2">
                                    <input type="text" class="form-control" id="addCustomCategoryInput" placeholder="Enter new category name">
                                    <small class="text-muted">This will create a new category</small>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Subcategory</label>
                                <div class="input-group">
                                    <select class="form-select" name="subcategory_id" id="addSubcategory">
                                        <option value="">Select Subcategory</option>
                                        <option value="custom">+ Add New Subcategory</option>
                                    </select>
                                </div>
                                <!-- Custom Subcategory Input (Hidden by default) -->
                                <div id="addCustomSubcategoryDiv" style="display: none;" class="mt-2">
                                    <input type="text" class="form-control" id="addCustomSubcategoryInput" placeholder="Enter new subcategory name">
                                    <small class="text-muted">This will create a new subcategory</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Line 3: Price and Stock Quantity -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Price (₱) *</label>
                                <input type="number" class="form-control" name="price" step="0.01" min="0" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Stock Quantity *</label>
                                <input type="number" class="form-control" name="stock_quantity" min="0" value="0" required>
                            </div>
                        </div>
                        
                        <!-- Line 4: Description (Full Width) -->
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                        
                        <!-- Line 5: Product Specifications (Full Width) -->
                        <div class="mb-3">
                            <label class="form-label">Product Specifications</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label small">Available Sizes</label>
                                    <div class="size-options">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input size-checkbox" type="checkbox" id="size_xs" value="XS">
                                            <label class="form-check-label" for="size_xs">XS</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input size-checkbox" type="checkbox" id="size_s" value="S">
                                            <label class="form-check-label" for="size_s">S</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input size-checkbox" type="checkbox" id="size_m" value="M">
                                            <label class="form-check-label" for="size_m">M</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input size-checkbox" type="checkbox" id="size_l" value="L">
                                            <label class="form-check-label" for="size_l">L</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input size-checkbox" type="checkbox" id="size_xl" value="XL">
                                            <label class="form-check-label" for="size_xl">XL</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input size-checkbox" type="checkbox" id="size_xxl" value="XXL">
                                            <label class="form-check-label" for="size_xxl">XXL</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small">Available Colors</label>
                                    <div class="color-options">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input color-checkbox" type="checkbox" id="color_black" value="Black">
                                            <label class="form-check-label" for="color_black">
                                                <span class="color-swatch" style="background-color: #000000;"></span> Black
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input color-checkbox" type="checkbox" id="color_white" value="White">
                                            <label class="form-check-label" for="color_white">
                                                <span class="color-swatch" style="background-color: #ffffff; border: 1px solid #ccc;"></span> White
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input color-checkbox" type="checkbox" id="color_red" value="Red">
                                            <label class="form-check-label" for="color_red">
                                                <span class="color-swatch" style="background-color: #dc3545;"></span> Red
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input color-checkbox" type="checkbox" id="color_blue" value="Blue">
                                            <label class="form-check-label" for="color_blue">
                                                <span class="color-swatch" style="background-color: #0d6efd;"></span> Blue
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input color-checkbox" type="checkbox" id="color_green" value="Green">
                                            <label class="form-check-label" for="color_green">
                                                <span class="color-swatch" style="background-color: #198754;"></span> Green
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input color-checkbox" type="checkbox" id="color_yellow" value="Yellow">
                                            <label class="form-check-label" for="color_yellow">
                                                <span class="color-swatch" style="background-color: #ffc107;"></span> Yellow
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input color-checkbox" type="checkbox" id="color_pink" value="Pink">
                                            <label class="form-check-label" for="color_pink">
                                                <span class="color-swatch" style="background-color: #e91e63;"></span> Pink
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input color-checkbox" type="checkbox" id="color_purple" value="Purple">
                                            <label class="form-check-label" for="color_purple">
                                                <span class="color-swatch" style="background-color: #6f42c1;"></span> Purple
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input color-checkbox" type="checkbox" id="color_gray" value="Gray">
                                            <label class="form-check-label" for="color_gray">
                                                <span class="color-swatch" style="background-color: #6c757d;"></span> Gray
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input color-checkbox" type="checkbox" id="color_brown" value="Brown">
                                            <label class="form-check-label" for="color_brown">
                                                <span class="color-swatch" style="background-color: #8b4513;"></span> Brown
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Line 6: Variant Stock Management (Full Width) -->
                        <div class="mb-3" id="variantStockSection" style="display: none;">
                            <label class="form-label">Stock by Size & Color</label>
                            <div class="alert alert-info">
                                <small><i class="bi bi-info-circle me-1"></i>Set stock quantity for each selected size and color combination. Leave empty or 0 if not available.</small>
                            </div>
                            <div id="variantStockGrid" class="row g-2">
                                <!-- Dynamic variant stock inputs will be generated here -->
                            </div>
                        </div>
                        
                        <!-- Line 7: Product Image (Full Width) -->
                        <div class="mb-3">
                            <label class="form-label">Product Image</label>
                            <input type="file" class="form-control" name="image_file" accept="image/*" onchange="previewImage(this, 'addPreview')">
                            <small class="text-muted">Or enter image URL below</small>
                            <input type="url" class="form-control mt-2" name="image_url" placeholder="https://example.com/image.jpg">
                            <img id="addPreview" class="preview-image" style="display:none;">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i>Add Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Product</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="api/products.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" id="editProductId">
                    <input type="hidden" name="custom_category_name" id="editCustomCategoryName">
                    <input type="hidden" name="custom_subcategory_name" id="editCustomSubcategoryName">
                    <div class="modal-body">
                        <!-- Line 1: Product Name (Full Width) -->
                        <div class="mb-3">
                            <label class="form-label">Product Name *</label>
                            <input type="text" class="form-control" name="name" id="editName" required>
                        </div>
                        
                        <!-- Line 2: Category and Subcategory -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Category *</label>
                                <div class="input-group">
                                    <select class="form-select" name="category_id" id="editCategory" required>
                                        <option value="">Select Category</option>
                                        <?php foreach($categories as $cat): ?>
                                            <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                        <?php endforeach; ?>
                                        <option value="custom">+ Add New Category</option>
                                    </select>
                                    <button class="btn btn-outline-secondary" type="button" data-bs-toggle="tooltip" title="Category not in list? Click to add new">
                                        <i class="bi bi-question-circle"></i>
                                    </button>
                                </div>
                                <!-- Custom Category Input (Hidden by default) -->
                                <div id="editCustomCategoryDiv" style="display: none;" class="mt-2">
                                    <input type="text" class="form-control" id="editCustomCategoryInput" placeholder="Enter new category name">
                                    <small class="text-muted">This will create a new category</small>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Subcategory</label>
                                <div class="input-group">
                                    <select class="form-select" name="subcategory_id" id="editSubcategory">
                                        <option value="">Select Subcategory</option>
                                        <option value="custom">+ Add New Subcategory</option>
                                    </select>
                                </div>
                                <!-- Custom Subcategory Input (Hidden by default) -->
                                <div id="editCustomSubcategoryDiv" style="display: none;" class="mt-2">
                                    <input type="text" class="form-control" id="editCustomSubcategoryInput" placeholder="Enter new subcategory name">
                                    <small class="text-muted">This will create a new subcategory</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Line 3: Price, Stock Quantity, and Status -->
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Price (₱) *</label>
                                <input type="number" class="form-control" name="price" id="editPrice" step="0.01" min="0" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Stock Quantity *</label>
                                <input type="number" class="form-control" name="stock_quantity" id="editStock" min="0" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Status *</label>
                                <select class="form-select" name="status" id="editStatus" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="pending">Pending</option>
                                    <option value="out_of_stock">Out of Stock</option>
                                    <option value="phase_out">Phase Out</option>
                                    <option value="unavailable">Unavailable</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Line 4: Description (Full Width) -->
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" id="editDescription" rows="3"></textarea>
                        </div>
                        
                        <!-- Line 5: Product Image (Full Width) -->
                        <div class="mb-3">
                            <label class="form-label">Product Image</label>
                            <input type="file" class="form-control" name="image_file" accept="image/*" onchange="previewImage(this, 'editPreview')">
                            <small class="text-muted">Or enter image URL below (leave blank to keep current image)</small>
                            <input type="url" class="form-control mt-2" name="image_url" id="editImageUrl" placeholder="https://example.com/image.jpg">
                            <img id="editPreview" class="preview-image">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>Update Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i>Confirm Delete</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="api/products.php" method="POST">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" id="deleteProductId">
                    <div class="modal-body">
                        <p>Are you sure you want to delete <strong id="deleteProductName"></strong>?</p>
                        <p class="text-danger mb-0"><i class="bi bi-exclamation-circle me-1"></i>This action cannot be undone.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash me-1"></i>Delete Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Product Modal -->
    <div class="modal fade" id="viewProductModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="bi bi-eye me-2"></i>Product Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Left Column - Product Image -->
                        <div class="col-md-5">
                            <div class="product-detail-image-container">
                                <img id="viewProductImage" src="" alt="Product Image" class="img-fluid rounded shadow-sm">
                            </div>
                            <div class="mt-3">
                                <span class="badge" id="viewProductStatus" style="font-size: 0.9rem; padding: 8px 15px;"></span>
                            </div>
                        </div>
                        
                        <!-- Right Column - Product Details -->
                        <div class="col-md-7">
                            <h3 id="viewProductName" class="mb-3"></h3>
                            
                            <div class="mb-4">
                                <h2 class="text-primary mb-0" id="viewProductPrice"></h2>
                            </div>
                            
                            <hr>
                            
                            <!-- Product Information Grid -->
                            <div class="row mb-3">
                                <div class="col-6">
                                    <p class="mb-2"><strong><i class="bi bi-tag me-2"></i>Category:</strong></p>
                                    <p class="text-muted" id="viewProductCategory"></p>
                                </div>
                                <div class="col-6">
                                    <p class="mb-2"><strong><i class="bi bi-tags me-2"></i>Subcategory:</strong></p>
                                    <p class="text-muted" id="viewProductSubcategory"></p>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-6">
                                    <p class="mb-2"><strong><i class="bi bi-shop me-2"></i>Vendor:</strong></p>
                                    <p class="text-muted" id="viewProductVendor"></p>
                                </div>
                                <div class="col-6">
                                    <p class="mb-2"><strong><i class="bi bi-box me-2"></i>Product ID:</strong></p>
                                    <p class="text-muted" id="viewProductId"></p>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <!-- Stock Information -->
                            <div class="alert alert-light border" role="alert">
                                <h6 class="alert-heading"><i class="bi bi-boxes me-2"></i>Inventory Status</h6>
                                <div class="row">
                                    <div class="col-6">
                                        <p class="mb-1"><strong>Current Stock:</strong> <span id="viewProductStock"></span></p>
                                        <p class="mb-0"><strong>Stock Status:</strong> <span id="viewProductStockStatus"></span></p>
                                    </div>
                                    <div class="col-6">
                                        <p class="mb-1"><strong>Min. Stock Level:</strong> <span id="viewProductMinStock"></span></p>
                                        <p class="mb-0"><strong>Created:</strong> <span id="viewProductCreated"></span></p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Description -->
                            <div class="mt-3">
                                <h6><i class="bi bi-card-text me-2"></i>Description</h6>
                                <p class="text-muted" id="viewProductDescription"></p>
                            </div>
                            
                            <!-- Quick Actions -->
                            <div class="mt-4 d-flex gap-2">
                                <button class="btn btn-primary" onclick="editProductFromView()">
                                    <i class="bi bi-pencil me-1"></i>Edit Product
                                </button>
                                <button class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                    <i class="bi bi-x-circle me-1"></i>Close
                                </button>
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
        // Sidebar toggle
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

        // Subcategories data
        const subcategories = <?php echo json_encode($subcategoriesByCategory); ?>;
        
        // Function to populate subcategories based on selected category
        function populateSubcategories(categoryId, targetSelectId) {
            const targetSelect = document.getElementById(targetSelectId);
            targetSelect.innerHTML = '<option value="">Select Subcategory</option>';
            
            if (categoryId && subcategories[categoryId]) {
                subcategories[categoryId].forEach(subcat => {
                    const option = document.createElement('option');
                    option.value = subcat.id;
                    option.textContent = subcat.name;
                    targetSelect.appendChild(option);
                });
            }
        }
        
        // Product filter functions
        function filterProducts() {
            const searchTerm = document.getElementById('searchProduct').value.toLowerCase();
            const categoryFilter = document.getElementById('filterCategory').value;
            const subcategoryFilter = document.getElementById('filterSubcategory').value;
            const stockFilter = document.getElementById('filterStock').value;
            const statusFilter = document.getElementById('filterStatus').value;
            
            const products = document.querySelectorAll('.product-item');
            
            products.forEach(product => {
                const name = product.getAttribute('data-name');
                const category = product.getAttribute('data-category');
                const subcategory = product.getAttribute('data-subcategory');
                const stock = product.getAttribute('data-stock');
                const status = product.getAttribute('data-status');
                
                let show = true;
                
                if (searchTerm && !name.includes(searchTerm)) {
                    show = false;
                }
                
                if (categoryFilter && category !== categoryFilter) {
                    show = false;
                }
                
                if (subcategoryFilter && subcategory !== subcategoryFilter) {
                    show = false;
                }
                
                if (stockFilter && stock !== stockFilter) {
                    show = false;
                }
                
                if (statusFilter && status !== statusFilter) {
                    show = false;
                }
                
                product.style.display = show ? 'block' : 'none';
            });
        }

        function resetFilters() {
            document.getElementById('searchProduct').value = '';
            document.getElementById('filterCategory').value = '';
            document.getElementById('filterSubcategory').value = '';
            document.getElementById('filterStock').value = '';
            document.getElementById('filterStatus').value = '';
            filterProducts();
        }

        document.getElementById('searchProduct').addEventListener('input', filterProducts);
        document.getElementById('filterCategory').addEventListener('change', function() {
            // Update subcategory filter when category changes
            const categoryId = this.value;
            populateSubcategories(categoryId, 'filterSubcategory');
            filterProducts();
        });
        document.getElementById('filterSubcategory').addEventListener('change', filterProducts);
        document.getElementById('filterStock').addEventListener('change', filterProducts);
        document.getElementById('filterStatus').addEventListener('change', filterProducts);
        
        // Add event listeners for category dropdowns in modals
        document.getElementById('addCategory').addEventListener('change', function() {
            const customDiv = document.getElementById('addCustomCategoryDiv');
            const customInput = document.getElementById('addCustomCategoryInput');
            
            if (this.value === 'custom') {
                customDiv.style.display = 'block';
                customInput.required = true;
                document.getElementById('addSubcategory').disabled = true;
            } else {
                customDiv.style.display = 'none';
                customInput.required = false;
                document.getElementById('addSubcategory').disabled = false;
                populateSubcategories(this.value, 'addSubcategory');
            }
        });
        
        document.getElementById('editCategory').addEventListener('change', function() {
            const customDiv = document.getElementById('editCustomCategoryDiv');
            const customInput = document.getElementById('editCustomCategoryInput');
            
            if (this.value === 'custom') {
                customDiv.style.display = 'block';
                customInput.required = true;
                document.getElementById('editSubcategory').disabled = true;
            } else {
                customDiv.style.display = 'none';
                customInput.required = false;
                document.getElementById('editSubcategory').disabled = false;
                populateSubcategories(this.value, 'editSubcategory');
            }
        });
        
        // Subcategory custom option handlers
        document.getElementById('addSubcategory').addEventListener('change', function() {
            const customDiv = document.getElementById('addCustomSubcategoryDiv');
            const customInput = document.getElementById('addCustomSubcategoryInput');
            
            if (this.value === 'custom') {
                customDiv.style.display = 'block';
                customInput.required = true;
            } else {
                customDiv.style.display = 'none';
                customInput.required = false;
            }
        });
        
        document.getElementById('editSubcategory').addEventListener('change', function() {
            const customDiv = document.getElementById('editCustomSubcategoryDiv');
            const customInput = document.getElementById('editCustomSubcategoryInput');
            
            if (this.value === 'custom') {
                customDiv.style.display = 'block';
                customInput.required = true;
            } else {
                customDiv.style.display = 'none';
                customInput.required = false;
            }
        });
        
        // Form submission handlers to capture custom values and handle API submission
        document.getElementById('addProductForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const categorySelect = document.getElementById('addCategory');
            const subcategorySelect = document.getElementById('addSubcategory');
            
            // Handle custom category
            if (categorySelect.value === 'custom') {
                const customCategoryName = document.getElementById('addCustomCategoryInput').value.trim();
                if (!customCategoryName) {
                    alert('Please enter a category name');
                    return false;
                }
                document.getElementById('addCustomCategoryName').value = customCategoryName;
            }
            
            // Handle custom subcategory
            if (subcategorySelect.value === 'custom') {
                const customSubcategoryName = document.getElementById('addCustomSubcategoryInput').value.trim();
                if (!customSubcategoryName) {
                    alert('Please enter a subcategory name');
                    return false;
                }
                document.getElementById('addCustomSubcategoryName').value = customSubcategoryName;
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Submitting...';
            submitBtn.disabled = true;
            
            // Submit form data to API
            const formData = new FormData(this);
            
            fetch('api/products.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addProductModal'));
                    modal.hide();
                    
                    // Show success message
                    alert('Product submitted successfully! It will appear in Pending Products for admin approval.');
                    
                    // Redirect to pending products page with success message
                    window.location.href = 'vendor_requests.php?submitted=1';
                } else {
                    alert('Error: ' + (data.message || 'Failed to submit product'));
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error submitting product. Please try again.');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
        
        document.querySelector('#editProductModal form').addEventListener('submit', function(e) {
            const categorySelect = document.getElementById('editCategory');
            const subcategorySelect = document.getElementById('editSubcategory');
            
            if (categorySelect.value === 'custom') {
                const customCategoryName = document.getElementById('editCustomCategoryInput').value.trim();
                if (!customCategoryName) {
                    e.preventDefault();
                    alert('Please enter a category name');
                    return false;
                }
                document.getElementById('editCustomCategoryName').value = customCategoryName;
            }
            
            if (subcategorySelect.value === 'custom') {
                const customSubcategoryName = document.getElementById('editCustomSubcategoryInput').value.trim();
                if (!customSubcategoryName) {
                    e.preventDefault();
                    alert('Please enter a subcategory name');
                    return false;
                }
                document.getElementById('editCustomSubcategoryName').value = customSubcategoryName;
            }
        });

        // Edit product function
        function editProduct(product) {
            document.getElementById('editProductId').value = product.id;
            document.getElementById('editName').value = product.name;
            document.getElementById('editCategory').value = product.category_id;
            document.getElementById('editPrice').value = product.price;
            document.getElementById('editStock').value = product.stock_quantity || 0;
            document.getElementById('editDescription').value = product.description || '';
            document.getElementById('editImageUrl').value = '';
            document.getElementById('editStatus').value = product.status || 'active';
            
            // Populate subcategories for the selected category
            populateSubcategories(product.category_id, 'editSubcategory');
            document.getElementById('editSubcategory').value = product.subcategory_id || '';
            
            const preview = document.getElementById('editPreview');
            if (product.image_url) {
                preview.src = product.image_url;
                preview.style.display = 'block';
            } else {
                preview.style.display = 'none';
            }
            
            const modal = new bootstrap.Modal(document.getElementById('editProductModal'));
            modal.show();
        }

        // View product function
        let currentViewProduct = null;
        
        function viewProduct(product) {
            currentViewProduct = product;
            
            // Populate modal fields
            document.getElementById('viewProductImage').src = product.image_url || 'uploads/products/default.svg';
            document.getElementById('viewProductName').textContent = product.name;
            document.getElementById('viewProductPrice').textContent = '₱' + parseFloat(product.price).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            document.getElementById('viewProductCategory').textContent = product.category_name || 'Uncategorized';
            document.getElementById('viewProductSubcategory').textContent = product.subcategory_name || 'None';
            document.getElementById('viewProductVendor').textContent = product.vendor_name || 'N/A';
            document.getElementById('viewProductId').textContent = '#' + product.id;
            document.getElementById('viewProductStock').textContent = product.stock_quantity || 0;
            document.getElementById('viewProductMinStock').textContent = product.min_stock_level || 10;
            document.getElementById('viewProductDescription').textContent = product.description || 'No description available.';
            
            // Format created date
            const createdDate = new Date(product.created_at);
            document.getElementById('viewProductCreated').textContent = createdDate.toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric' 
            });
            
            // Stock status badge
            const stock = parseInt(product.stock_quantity || 0);
            const statusBadge = document.getElementById('viewProductStockStatus');
            const productStatus = document.getElementById('viewProductStatus');
            
            if (stock > 10) {
                statusBadge.innerHTML = '<span class="badge bg-success">In Stock</span>';
                productStatus.className = 'badge bg-success';
                productStatus.textContent = 'Available';
            } else if (stock > 0) {
                statusBadge.innerHTML = '<span class="badge bg-warning">Low Stock</span>';
                productStatus.className = 'badge bg-warning';
                productStatus.textContent = 'Low Stock';
            } else {
                statusBadge.innerHTML = '<span class="badge bg-danger">Out of Stock</span>';
                productStatus.className = 'badge bg-danger';
                productStatus.textContent = 'Out of Stock';
            }
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('viewProductModal'));
            modal.show();
        }
        
        // Edit from view modal
        function editProductFromView() {
            // Close view modal
            const viewModal = bootstrap.Modal.getInstance(document.getElementById('viewProductModal'));
            if (viewModal) {
                viewModal.hide();
            }
            
            // Open edit modal with product data
            setTimeout(() => {
                editProduct(currentViewProduct);
            }, 300);
        }

        // Delete product function
        function deleteProduct(id, name) {
            document.getElementById('deleteProductId').value = id;
            document.getElementById('deleteProductName').textContent = name;
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }

        // Image preview function
        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        // Product Specifications Functions
        function generateVariantStockInputs() {
            const selectedSizes = Array.from(document.querySelectorAll('.size-checkbox:checked')).map(cb => cb.value);
            const selectedColors = Array.from(document.querySelectorAll('.color-checkbox:checked')).map(cb => cb.value);
            const variantSection = document.getElementById('variantStockSection');
            const variantGrid = document.getElementById('variantStockGrid');
            
            // Show/hide variant stock section based on selections
            if (selectedSizes.length > 0 && selectedColors.length > 0) {
                variantSection.style.display = 'block';
                
                // Clear existing inputs
                variantGrid.innerHTML = '';
                
                // Generate inputs for each size-color combination
                selectedSizes.forEach(size => {
                    selectedColors.forEach(color => {
                        const variantId = `variant_${size}_${color}`.replace(/\s+/g, '_').toLowerCase();
                        const variantLabel = `${size} - ${color}`;
                        
                        const variantItem = document.createElement('div');
                        variantItem.className = 'col-md-4 col-sm-6';
                        variantItem.innerHTML = `
                            <div class="variant-stock-item">
                                <div class="variant-stock-label">${variantLabel}</div>
                                <input type="number" 
                                       class="form-control variant-stock-input" 
                                       name="variant_stock[${size}][${color}]" 
                                       id="${variantId}"
                                       min="0" 
                                       value="0"
                                       placeholder="0">
                            </div>
                        `;
                        
                        variantGrid.appendChild(variantItem);
                    });
                });
            } else {
                variantSection.style.display = 'none';
                variantGrid.innerHTML = '';
            }
        }
        
        // Add event listeners for size and color checkboxes
        document.addEventListener('DOMContentLoaded', function() {
            // Size checkbox listeners
            document.querySelectorAll('.size-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', generateVariantStockInputs);
            });
            
            // Color checkbox listeners
            document.querySelectorAll('.color-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', generateVariantStockInputs);
            });
            
            // Reset variant inputs when modal is hidden
            const addProductModal = document.getElementById('addProductModal');
            addProductModal.addEventListener('hidden.bs.modal', function() {
                // Uncheck all size and color checkboxes
                document.querySelectorAll('.size-checkbox, .color-checkbox').forEach(checkbox => {
                    checkbox.checked = false;
                });
                
                // Hide variant stock section
                document.getElementById('variantStockSection').style.display = 'none';
                document.getElementById('variantStockGrid').innerHTML = '';
            });
        });
    </script>
</body>
</html>

