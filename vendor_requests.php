<?php 
session_start();
require_once 'includes/auth.php';
require_once 'config/database.php';
requireAuth('login.php');

$currentPage = basename($_SERVER['PHP_SELF']); 
$pdo = getDBConnection();

// Get seller's vendor_id
$vendorId = $_SESSION['vendor_id'] ?? null;

// Fetch pending products for this seller
$pendingProducts = [];
if ($vendorId) {
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name 
                          FROM products p 
                          LEFT JOIN categories c ON p.category_id = c.id 
                          WHERE p.vendor_id = ? AND p.status = 'pending'
                          ORDER BY p.created_at DESC");
    $stmt->execute([$vendorId]);
    $pendingProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Products - Core Transaction 2</title>
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
            <li class="sidebar-item has-submenu <?php if(in_array($currentPage, ['order.php','return_management.php',''])) echo 'active'; ?>">
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

            <!-- Settings -->
            <li class="sidebar-item has-submenu <?php if(in_array($currentPage, ['system_settings.php','user_management.php','database_config.php','api_settings.php'])) echo 'active'; ?>">
                <a href="system_settings.php">
                    <i class="bi bi-gear"></i>
                    <span class="menu-text">Settings</span>
                    <i class="bi bi-chevron-down submenu-icon"></i>
                </a>
                <ul class="submenu">
                    <li><a href="system_settings.php" class="<?php if($currentPage == 'system_settings.php') echo 'active'; ?>"><span class="menu-text">System Settings</span></a></li>
                    <li><a href="user_management.php" class="<?php if($currentPage == 'user_management.php') echo 'active'; ?>"><span class="menu-text">User Management</span></a></li>
                    <li><a href="database_config.php" class="<?php if($currentPage == 'database_config.php') echo 'active'; ?>"><span class="menu-text">Database Configuration</span></a></li>
                    <li><a href="api_settings.php" class="<?php if($currentPage == 'api_settings.php') echo 'active'; ?>"><span class="menu-text">API Settings</span></a></li>
                </ul>
            </li>
        </ul>
    </div>

    <div class="main-content" style="margin-left: 250px; padding: 2rem; min-height: calc(100vh - 60px); background: #f8f9fa;">
        <div class="container-fluid">
            <!-- Success/Error Messages -->
            <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i><?php echo htmlspecialchars(urldecode($_GET['success'])); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <?php if(isset($_GET['submitted'])): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="bi bi-info-circle me-2"></i>Product submitted successfully! It's now waiting for admin approval (CT3).
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <?php if(isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i><?php echo htmlspecialchars(urldecode($_GET['error'])); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <style>
                .page-header { background:#fff; border:1px solid #e9ecef; border-radius:12px; padding:16px 20px; 
                    box-shadow:0 4px 12px rgba(0,0,0,0.06); margin-top:20px; margin-bottom:16px; }
                .page-title { color:#212529; font-weight:700; font-size:1.75rem; margin:0 0 4px 0; }
                .page-subtitle { color:#6c757d; font-size:0.95rem; margin:0; }
            </style>
            <div class="page-header d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="page-title">Pending Products</h2>
                    <p class="page-subtitle">Your products waiting for admin approval (CT3) before appearing in the catalog.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="product_catalog.php" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i>Add New Product</a>
                    <button class="btn btn-light" onclick="location.reload()"><i class="bi bi-arrow-clockwise me-1"></i>Refresh</button>
                </div>
            </div>

            <form class="row g-2 mb-3" id="reqSearchForm">
                <div class="col-md-6"><input id="rq" class="form-control" placeholder="Search by product name or category..."></div>
                <div class="col-md-2 d-grid"><button class="btn btn-primary" type="submit"><i class="bi bi-search me-1"></i>Search</button></div>
            </form>

            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0" id="requestsTable">
                            <thead class="table-light">
                                <tr>
                                    <th width="60">ID</th>
                                    <th width="80">Image</th>
                                    <th>Product Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Status</th>
                                    <th>Submitted Date</th>
                                    <th class="text-end" width="120">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($pendingProducts)): ?>
                                    <tr>
                                        <td colspan="9" class="text-center py-5">
                                            <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                                            <p class="mt-3 mb-0 fw-semibold">No pending products</p>
                                            <p class="text-muted small">All your products are approved or you haven't added any products yet.</p>
                                            <a href="product_catalog.php" class="btn btn-primary mt-3">
                                                <i class="bi bi-plus-circle me-1"></i>Add New Product
                                            </a>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($pendingProducts as $product): ?>
                                        <tr>
                                            <td class="fw-semibold">#<?php echo $product['id']; ?></td>
                                            <td>
                                                <img src="<?php echo htmlspecialchars($product['image_url'] ?? 'uploads/products/default.svg'); ?>" 
                                                     style="width:48px;height:48px;object-fit:cover" 
                                                     class="rounded" 
                                                     onerror="this.src='uploads/products/default.svg'"/>
                                            </td>
                                            <td><strong><?php echo htmlspecialchars($product['name']); ?></strong></td>
                                            <td class="text-muted"><?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?></td>
                                            <td class="fw-semibold">₱<?php echo number_format($product['price'], 2); ?></td>
                                            <td><?php echo $product['stock_quantity']; ?> units</td>
                                            <td><span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i>Pending</span></td>
                                            <td class="text-muted"><?php echo date('Y-m-d', strtotime($product['created_at'])); ?></td>
                                            <td class="text-end">
                                                <div class="d-flex flex-column gap-1 align-items-end">
                                                    <button class="btn btn-sm btn-outline-info" 
                                                            onclick="viewProduct(<?php echo htmlspecialchars(json_encode($product)); ?>)" 
                                                            title="View Details"
                                                            style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger" 
                                                            onclick="cancelProduct(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?>')" 
                                                            title="Cancel Request"
                                                            style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;">
                                                        <i class="bi bi-x-circle"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
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

        // View product details (modal - same look as catalog)
        function viewProduct(product) {
            // Populate modal fields
            const img = document.getElementById('vr_viewProductImage');
            img.src = product.image_url || 'uploads/products/default.svg';
            img.onerror = function(){ this.src='uploads/products/default.svg'; };

            document.getElementById('vr_viewProductName').textContent = product.name || '';
            document.getElementById('vr_viewProductPrice').textContent = '₱' + parseFloat(product.price || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            document.getElementById('vr_viewProductCategory').textContent = product.category_name || 'Uncategorized';
            document.getElementById('vr_viewProductSubcategory').textContent = product.subcategory_name || '—';
            document.getElementById('vr_viewProductVendor').textContent = product.vendor_name || 'N/A';
            document.getElementById('vr_viewProductId').textContent = '#' + (product.id || '');
            document.getElementById('vr_viewProductStock').textContent = product.stock_quantity ?? 0;
            document.getElementById('vr_viewProductMinStock').textContent = product.min_stock_level ?? 10;
            document.getElementById('vr_viewProductDescription').textContent = product.description || 'No description provided.';

            // Dates / status
            const created = product.created_at ? new Date(product.created_at) : new Date();
            document.getElementById('vr_viewProductCreated').textContent = created.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });

            // Badges
            const stock = parseInt(product.stock_quantity || 0);
            const stockBadgeEl = document.getElementById('vr_viewProductStockStatus');
            const productStatusEl = document.getElementById('vr_viewProductStatus');
            if (stock > 10) {
                stockBadgeEl.innerHTML = '<span class="badge bg-success">In Stock</span>';
            } else if (stock > 0) {
                stockBadgeEl.innerHTML = '<span class="badge bg-warning text-dark">Low Stock</span>';
            } else {
                stockBadgeEl.innerHTML = '<span class="badge bg-danger">Out of Stock</span>';
            }
            productStatusEl.className = 'badge bg-warning text-dark';
            productStatusEl.textContent = 'Pending';

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('vr_viewProductModal'));
            modal.show();
        }

        // Cancel product submission
        function cancelProduct(id, name) {
            if (confirm(`Are you sure you want to cancel the submission for "${name}"?\n\nThis will remove the product from pending approval.`)) {
                // TODO: Implement API call to cancel/delete pending product
                // For now, redirect to delete endpoint
                if (confirm('This will permanently delete the product. Continue?')) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = 'api/products.php';
                    
                    const actionInput = document.createElement('input');
                    actionInput.type = 'hidden';
                    actionInput.name = 'action';
                    actionInput.value = 'delete';
                    
                    const idInput = document.createElement('input');
                    idInput.type = 'hidden';
                    idInput.name = 'id';
                    idInput.value = id;
                    
                    form.appendChild(actionInput);
                    form.appendChild(idInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            }
        }

        // Search functionality
        document.getElementById('reqSearchForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const searchTerm = document.getElementById('rq').value.toLowerCase();
            const rows = document.querySelectorAll('#requestsTable tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>

    <!-- View Product Modal (mirrors catalog) -->
    <div class="modal fade" id="vr_viewProductModal" tabindex="-1">
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
                            <div class="product-detail-image-container" style="background:#f8f9fa;padding:20px;border-radius:12px;text-align:center;">
                                <img id="vr_viewProductImage" src="" alt="Product Image" class="img-fluid rounded shadow-sm" style="max-height:400px;object-fit:contain;width:100%">
                            </div>
                            <div class="mt-3">
                                <span class="badge" id="vr_viewProductStatus" style="font-size:0.9rem;padding:8px 15px;"></span>
                            </div>
                        </div>
                        
                        <!-- Right Column - Product Details -->
                        <div class="col-md-7">
                            <h3 id="vr_viewProductName" class="mb-3"></h3>
                            <div class="mb-4">
                                <h2 class="text-primary mb-0" id="vr_viewProductPrice"></h2>
                            </div>
                            <hr>
                            <!-- Product Information Grid -->
                            <div class="row mb-3">
                                <div class="col-6">
                                    <p class="mb-2"><strong><i class="bi bi-tag me-2"></i>Category:</strong></p>
                                    <p class="text-muted" id="vr_viewProductCategory"></p>
                                </div>
                                <div class="col-6">
                                    <p class="mb-2"><strong><i class="bi bi-tags me-2"></i>Subcategory:</strong></p>
                                    <p class="text-muted" id="vr_viewProductSubcategory"></p>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-6">
                                    <p class="mb-2"><strong><i class="bi bi-shop me-2"></i>Vendor:</strong></p>
                                    <p class="text-muted" id="vr_viewProductVendor"></p>
                                </div>
                                <div class="col-6">
                                    <p class="mb-2"><strong><i class="bi bi-box me-2"></i>Product ID:</strong></p>
                                    <p class="text-muted" id="vr_viewProductId"></p>
                                </div>
                            </div>
                            
                            <hr>
                            <!-- Stock Information -->
                            <div class="alert alert-light border" role="alert">
                                <h6 class="alert-heading"><i class="bi bi-boxes me-2"></i>Inventory Status</h6>
                                <div class="row">
                                    <div class="col-6">
                                        <p class="mb-1"><strong>Current Stock:</strong> <span id="vr_viewProductStock"></span></p>
                                        <p class="mb-0"><strong>Stock Status:</strong> <span id="vr_viewProductStockStatus"></span></p>
                                    </div>
                                    <div class="col-6">
                                        <p class="mb-1"><strong>Min. Stock Level:</strong> <span id="vr_viewProductMinStock"></span></p>
                                        <p class="mb-0"><strong>Created:</strong> <span id="vr_viewProductCreated"></span></p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Description -->
                            <div class="mt-3">
                                <h6><i class="bi bi-card-text me-2"></i>Description</h6>
                                <p class="text-muted" id="vr_viewProductDescription"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>




