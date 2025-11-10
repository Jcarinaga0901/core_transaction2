<?php 
session_start();

// Include authentication
require_once 'includes/auth.php';
require_once 'config/database.php';

// Require authentication to access this page
requireAuth('login.php');

$currentPage = basename($_SERVER['PHP_SELF']); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return Management - RAEVOR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap" rel="stylesheet">
    <link href="css/styles.css?v=<?php echo filemtime('css/styles.css'); ?>" rel="stylesheet">
    <style>
        .return-card {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            transition: all 0.3s ease;
            background: white;
        }
        
        .return-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }
        
        .return-status {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }
        
        .status-approved {
            background: #d1fae5;
            color: #065f46;
        }
        
        .status-rejected {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .status-processing {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .return-reason {
            background: #f8fafc;
            border-left: 4px solid #3b82f6;
            padding: 12px;
            border-radius: 0 8px 8px 0;
            margin: 8px 0;
        }
        
        .proof-images {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 8px;
            margin-top: 8px;
        }
        
        .proof-image {
            width: 100%;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #e5e7eb;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .proof-image:hover {
            border-color: #3b82f6;
            transform: scale(1.05);
        }
        
        .return-details {
            background: #f9fafb;
            border-radius: 8px;
            padding: 16px;
            margin: 12px 0;
        }
        
        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        .modal-image {
            max-width: 100%;
            max-height: 400px;
            object-fit: contain;
            border-radius: 8px;
        }
        
        .return-timeline {
            position: relative;
            padding-left: 20px;
        }
        
        .return-timeline::before {
            content: '';
            position: absolute;
            left: 8px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e5e7eb;
        }
        
        .timeline-item {
            position: relative;
            margin-bottom: 16px;
        }
        
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -16px;
            top: 6px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #3b82f6;
            border: 3px solid white;
            box-shadow: 0 0 0 2px #e5e7eb;
        }
        
        .filter-tabs {
            background: #f8fafc;
            border-radius: 8px;
            padding: 4px;
            margin-bottom: 20px;
        }
        
        .filter-tab {
            padding: 8px 16px;
            border-radius: 6px;
            border: none;
            background: transparent;
            color: #6b7280;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        
        .filter-tab.active {
            background: white;
            color: #1f2937;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
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
    </header>

    <!-- Side Navigation -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h3>Core Transaction 2</h3>
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
            <li class="sidebar-item has-submenu <?php if(in_array($currentPage, ['order.php', 'return_management.php'])) echo 'active'; ?>">
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
            <li class="sidebar-item <?php if($currentPage == 'seller_documents.php') echo 'active'; ?>">
                <a href="seller_documents.php">
                    <i class="bi bi-file-earmark-check"></i>
                    <span class="menu-text">Business Documents</span>
                </a>
            </li>
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
                        <h2 class="page-title" style="color: #212529 !important; font-weight: 700; font-size: 1.75rem;">Return Management</h2>
                        <p class="page-subtitle" style="color: #6c757d !important; font-size: 0.95rem;">Manage product returns and refunds with detailed tracking</p>
                    </div>
                    <div class="d-flex gap-2">
                        <div class="input-group" style="width: 250px;">
                            <input type="text" class="form-control" id="searchReturns" placeholder="Search returns...">
                            <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                        <button class="btn btn-light" id="refreshReturnsBtn">
                            <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                        </button>
                    </div>
                </div>
            </div>

            <!-- Filter Tabs -->
            <div class="filter-tabs">
                <button class="filter-tab active" data-status="all">All Returns</button>
                <button class="filter-tab" data-status="pending">Pending</button>
                <button class="filter-tab" data-status="approved">Approved</button>
                <button class="filter-tab" data-status="processing">Processing</button>
                <button class="filter-tab" data-status="rejected">Rejected</button>
            </div>

            <!-- Returns List -->
            <div class="row" id="returnsList">
                <!-- Returns will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Return Details Modal -->
    <div class="modal fade" id="returnDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Return Request Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="returnDetailsContent">
                    <!-- Return details will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Preview Modal -->
    <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Proof Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="previewImage" class="modal-image" alt="Proof Image">
                </div>
            </div>
        </div>
    </div>

    <!-- Action Modal -->
    <div class="modal fade" id="actionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="actionModalTitle">Process Return</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="actionForm">
                        <input type="hidden" id="returnId" name="return_id">
                        <input type="hidden" id="actionType" name="action">
                        
                        <div class="mb-3">
                            <label class="form-label">Response</label>
                            <textarea class="form-control" id="responseText" name="response" rows="4" placeholder="Add your response or notes..."></textarea>
                        </div>
                        
                        <div class="mb-3" id="refundAmountGroup" style="display: none;">
                            <label class="form-label">Refund Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number" class="form-control" id="refundAmount" name="refund_amount" step="0.01" min="0">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="submitAction">Submit</button>
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
        
        // Handle direct navigation links
        directLinks.forEach(link => {
            link.addEventListener('click', function(e) {
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
        
        // Enhanced action buttons with confirmation
        function confirmAction(action, returnId, actionText) {
            const confirmMessage = `Are you sure you want to ${actionText.toLowerCase()} this return request?`;
            if (confirm(confirmMessage)) {
                processReturn(returnId, action);
            }
        }
        
        // Load returns data
        async function loadReturns(status = 'all') {
            try {
                const response = await fetch(`api/returns.php?action=list&status=${status}`);
                const data = await response.json();
                
                if (!data.success) {
                    // If no data, try to create sample data first
                    if (data.message && data.message.includes('not found')) {
                        await createSampleData();
                        // Try loading again
                        const retryResponse = await fetch(`api/returns.php?action=list&status=${status}`);
                        const retryData = await retryResponse.json();
                        if (retryData.success) {
                            displayReturns(retryData.returns || []);
                            return;
                        }
                    }
                    // Fallback to sample data
                    displaySampleReturns(status);
                    return;
                }
                
                displayReturns(data.returns || []);
            } catch (error) {
                console.error('Error loading returns:', error);
                // Show sample data if API fails
                displaySampleReturns(status);
            }
        }
        
        // Create sample data
        async function createSampleData() {
            try {
                await fetch('api/returns.php?action=create_sample', { method: 'POST' });
            } catch (error) {
                console.error('Error creating sample data:', error);
            }
        }
        
        // Display sample returns
        function displaySampleReturns(status) {
            const sampleReturns = [
                {
                    id: 1,
                    order_id: 1,
                    customer_name: 'John Doe',
                    product_name: 'Classic Denim Jacket',
                    amount: 2598.00,
                    reason: 'Product arrived damaged with visible scratches on the surface',
                    proof_images: 'uploads/products/default.jpg,uploads/products/default.svg',
                    status: 'pending',
                    created_at: '2024-01-10T09:30:00'
                },
                {
                    id: 2,
                    order_id: 2,
                    customer_name: 'Jane Smith',
                    product_name: 'Vintage T-Shirt',
                    amount: 1299.00,
                    reason: 'Wrong size received - ordered Large but received Medium',
                    proof_images: 'uploads/products/default.jpg',
                    status: 'approved',
                    created_at: '2024-01-12T14:20:00'
                },
                {
                    id: 3,
                    order_id: 3,
                    customer_name: 'Mike Johnson',
                    product_name: 'Premium Hoodie',
                    amount: 2999.00,
                    reason: 'Product not as described - color is different from website',
                    proof_images: 'uploads/products/default.jpg,uploads/products/default.svg',
                    status: 'processing',
                    created_at: '2024-01-15T11:45:00'
                },
                {
                    id: 4,
                    order_id: 4,
                    customer_name: 'Sarah Wilson',
                    product_name: 'Basic T-Shirt',
                    amount: 599.00,
                    reason: 'Changed mind - no longer need this item',
                    proof_images: '',
                    status: 'rejected',
                    created_at: '2024-01-18T16:30:00'
                },
                {
                    id: 5,
                    order_id: 5,
                    customer_name: 'David Brown',
                    product_name: 'Designer Jeans',
                    amount: 2999.00,
                    reason: 'Defective product - button not working properly',
                    proof_images: 'uploads/products/default.jpg',
                    status: 'completed',
                    created_at: '2024-01-20T13:15:00'
                },
                {
                    id: 6,
                    order_id: 6,
                    customer_name: 'Lisa Davis',
                    product_name: 'Casual Shirt',
                    amount: 799.00,
                    reason: 'Quality issue - fabric feels cheap and rough',
                    proof_images: 'uploads/products/default.jpg',
                    status: 'pending',
                    created_at: '2024-01-22T10:00:00'
                },
                {
                    id: 7,
                    order_id: 7,
                    customer_name: 'Tom Miller',
                    product_name: 'Summer Dress',
                    amount: 899.00,
                    reason: 'Wrong item shipped - received different product',
                    proof_images: 'uploads/products/default.jpg,uploads/products/default.svg',
                    status: 'approved',
                    created_at: '2024-01-25T15:45:00'
                },
                {
                    id: 8,
                    order_id: 8,
                    customer_name: 'Amy Garcia',
                    product_name: 'Sports Shorts',
                    amount: 1199.00,
                    reason: 'Size too small - need to exchange for larger size',
                    proof_images: 'uploads/products/default.jpg',
                    status: 'processing',
                    created_at: '2024-01-28T12:30:00'
                }
            ];
            
            // Filter by status
            let filteredReturns = sampleReturns;
            if (status !== 'all') {
                filteredReturns = sampleReturns.filter(returnItem => returnItem.status === status);
            }
            
            displayReturns(filteredReturns);
        }
        
        // Display returns
        function displayReturns(returns) {
            const container = document.getElementById('returnsList');
            
            if (!returns || returns.length === 0) {
                container.innerHTML = `
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="bi bi-inbox" style="font-size: 3rem; color: #6b7280; margin-bottom: 1rem;"></i>
                            <h5>No returns found</h5>
                            <p class="text-muted">No return requests match your current filter.</p>
                        </div>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = returns.map(returnItem => `
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="return-card p-3">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h6 class="mb-1">Return #${returnItem.id}</h6>
                                <small class="text-muted">Order #${returnItem.order_id}</small>
                            </div>
                            <span class="return-status status-${returnItem.status.toLowerCase()}">${returnItem.status}</span>
                        </div>
                        
                        <div class="return-details">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Customer:</span>
                                <span class="fw-medium">${returnItem.customer_name}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Product:</span>
                                <span class="fw-medium">${returnItem.product_name}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Amount:</span>
                                <span class="fw-medium">₱${parseFloat(returnItem.amount).toLocaleString()}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Date:</span>
                                <span class="fw-medium">${new Date(returnItem.created_at).toLocaleDateString()}</span>
                            </div>
                        </div>
                        
                        <div class="return-reason">
                            <strong>Reason:</strong> ${returnItem.reason}
                        </div>
                        
                        ${returnItem.proof_images ? `
                            <div class="proof-images">
                                ${returnItem.proof_images.split(',').slice(0, 3).map(img => `
                                    <img src="${img.trim()}" class="proof-image" alt="Proof" onclick="previewImage('${img.trim()}')">
                                `).join('')}
                                ${returnItem.proof_images.split(',').length > 3 ? `
                                    <div class="proof-image d-flex align-items-center justify-content-center" style="background: #f3f4f6;">
                                        <small class="text-muted">+${returnItem.proof_images.split(',').length - 3} more</small>
                                    </div>
                                ` : ''}
                            </div>
                        ` : ''}
                        
                        <div class="action-buttons mt-3">
                            <button class="btn btn-sm btn-outline-primary" onclick="viewReturnDetails(${returnItem.id})" title="View detailed information">
                                <i class="bi bi-eye me-1"></i>View Details
                            </button>
                            ${returnItem.status === 'pending' ? `
                                <button class="btn btn-sm btn-success" onclick="confirmAction('approve', ${returnItem.id}, 'Approve')" title="Approve this return request">
                                    <i class="bi bi-check me-1"></i>Approve
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="confirmAction('reject', ${returnItem.id}, 'Reject')" title="Reject this return request">
                                    <i class="bi bi-x me-1"></i>Reject
                                </button>
                            ` : ''}
                            ${returnItem.status === 'approved' ? `
                                <button class="btn btn-sm btn-info" onclick="confirmAction('process', ${returnItem.id}, 'Process')" title="Start processing this return">
                                    <i class="bi bi-truck me-1"></i>Process Return
                                </button>
                            ` : ''}
                            ${returnItem.status === 'processing' ? `
                                <button class="btn btn-sm btn-success" onclick="confirmAction('complete', ${returnItem.id}, 'Complete')" title="Mark this return as completed">
                                    <i class="bi bi-check-circle me-1"></i>Complete Return
                                </button>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `).join('');
        }
        
        // Preview image
        function previewImage(imageSrc) {
            document.getElementById('previewImage').src = imageSrc;
            new bootstrap.Modal(document.getElementById('imagePreviewModal')).show();
        }
        
        // View return details
        async function viewReturnDetails(returnId) {
            try {
                const response = await fetch(`api/returns.php?action=view&id=${returnId}`);
                const data = await response.json();
                
                if (!data.success) {
                    throw new Error(data.message || 'Failed to load return details');
                }
                
                const returnItem = data.return;
                document.getElementById('returnDetailsContent').innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Return Information</h6>
                            <table class="table table-sm">
                                <tr><td>Return ID:</td><td>#${returnItem.id}</td></tr>
                                <tr><td>Order ID:</td><td>#${returnItem.order_id}</td></tr>
                                <tr><td>Customer:</td><td>${returnItem.customer_name}</td></tr>
                                <tr><td>Product:</td><td>${returnItem.product_name}</td></tr>
                                <tr><td>Amount:</td><td>₱${parseFloat(returnItem.amount).toLocaleString()}</td></tr>
                                <tr><td>Status:</td><td><span class="return-status status-${returnItem.status.toLowerCase()}">${returnItem.status}</span></td></tr>
                                <tr><td>Date:</td><td>${new Date(returnItem.created_at).toLocaleString()}</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Return Reason</h6>
                            <div class="return-reason">
                                ${returnItem.reason}
                            </div>
                            
                            ${returnItem.proof_images ? `
                                <h6 class="mt-3">Proof Images</h6>
                                <div class="proof-images">
                                    ${returnItem.proof_images.split(',').map(img => `
                                        <img src="${img.trim()}" class="proof-image" alt="Proof" onclick="previewImage('${img.trim()}')">
                                    `).join('')}
                                </div>
                            ` : ''}
                        </div>
                    </div>
                    
                    ${returnItem.timeline ? `
                        <div class="mt-4">
                            <h6>Return Timeline</h6>
                            <div class="return-timeline">
                                ${returnItem.timeline.map(item => `
                                    <div class="timeline-item">
                                        <div class="d-flex justify-content-between">
                                            <span class="fw-medium">${item.action}</span>
                                            <small class="text-muted">${new Date(item.created_at).toLocaleString()}</small>
                                        </div>
                                        ${item.notes ? `<div class="text-muted small">${item.notes}</div>` : ''}
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    ` : ''}
                `;
                
                new bootstrap.Modal(document.getElementById('returnDetailsModal')).show();
            } catch (error) {
                console.error('Error loading return details:', error);
                alert('Error loading return details: ' + error.message);
            }
        }
        
        // Process return
        function processReturn(returnId, action) {
            document.getElementById('returnId').value = returnId;
            document.getElementById('actionType').value = action;
            document.getElementById('responseText').value = '';
            document.getElementById('refundAmount').value = '';
            
            const modal = new bootstrap.Modal(document.getElementById('actionModal'));
            const title = document.getElementById('actionModalTitle');
            const refundGroup = document.getElementById('refundAmountGroup');
            const responseLabel = document.querySelector('label[for="responseText"]');
            const responseTextarea = document.getElementById('responseText');
            
            // Clear previous validation
            responseTextarea.required = false;
            document.getElementById('refundAmount').required = false;
            
            if (action === 'approve') {
                title.textContent = 'Approve Return Request';
                responseLabel.textContent = 'Approval Notes (Optional)';
                responseTextarea.placeholder = 'Add any notes about the approval...';
                refundGroup.style.display = 'block';
                document.getElementById('refundAmount').required = true;
                document.getElementById('refundAmount').placeholder = 'Enter refund amount';
            } else if (action === 'reject') {
                title.textContent = 'Reject Return Request';
                responseLabel.textContent = 'Rejection Reason (Required)';
                responseTextarea.placeholder = 'Please provide a reason for rejection...';
                responseTextarea.required = true;
                refundGroup.style.display = 'none';
            } else if (action === 'process') {
                title.textContent = 'Process Return';
                responseLabel.textContent = 'Processing Notes (Optional)';
                responseTextarea.placeholder = 'Add any notes about the processing...';
                refundGroup.style.display = 'none';
            } else if (action === 'complete') {
                title.textContent = 'Complete Return';
                responseLabel.textContent = 'Completion Notes (Optional)';
                responseTextarea.placeholder = 'Add any final notes...';
                refundGroup.style.display = 'none';
            }
            
            modal.show();
        }
        
        // Submit action
        document.getElementById('submitAction').addEventListener('click', async function() {
            const form = document.getElementById('actionForm');
            const formData = new FormData(form);
            const actionType = document.getElementById('actionType').value;
            const responseText = document.getElementById('responseText').value.trim();
            const refundAmount = document.getElementById('refundAmount').value;
            
            // Validation
            if (actionType === 'reject' && !responseText) {
                alert('Please provide a reason for rejection.');
                document.getElementById('responseText').focus();
                return;
            }
            
            if (actionType === 'approve' && (!refundAmount || parseFloat(refundAmount) <= 0)) {
                alert('Please enter a valid refund amount.');
                document.getElementById('refundAmount').focus();
                return;
            }
            
            // Show loading state
            const submitBtn = this;
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Processing...';
            submitBtn.disabled = true;
            
            try {
                // Add action parameter
                formData.append('action', actionType);
                
                const response = await fetch('api/returns.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Show success message based on action
                    let successMessage = '';
                    switch (actionType) {
                        case 'approve':
                            successMessage = 'Return request approved successfully!';
                            break;
                        case 'reject':
                            successMessage = 'Return request rejected successfully!';
                            break;
                        case 'process':
                            successMessage = 'Return is now being processed!';
                            break;
                        case 'complete':
                            successMessage = 'Return completed successfully!';
                            break;
                        default:
                            successMessage = 'Return processed successfully!';
                    }
                    
                    bootstrap.Modal.getInstance(document.getElementById('actionModal')).hide();
                    loadReturns(document.querySelector('.filter-tab.active').dataset.status);
                    
                    // Show success notification
                    showNotification(successMessage, 'success');
                } else {
                    showNotification('Error: ' + (data.message || 'Failed to process return'), 'error');
                }
            } catch (error) {
                console.error('Error processing return:', error);
                showNotification('Error processing return: ' + error.message, 'error');
            } finally {
                // Reset button state
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        });
        
        // Notification system
        function showNotification(message, type = 'info') {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `alert alert-${type === 'error' ? 'danger' : type === 'success' ? 'success' : 'info'} alert-dismissible fade show position-fixed`;
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            notification.innerHTML = `
                <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(notification);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 5000);
        }
        
        // Refresh button with loading state
        document.getElementById('refreshReturnsBtn').addEventListener('click', function() {
            const btn = this;
            const originalText = btn.innerHTML;
            
            // Show loading state
            btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Refreshing...';
            btn.disabled = true;
            
            // Load returns
            loadReturns(document.querySelector('.filter-tab.active').dataset.status)
                .finally(() => {
                    // Reset button state
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                });
        });
        
        // Enhanced filter tabs with animation
        document.querySelectorAll('.filter-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                // Remove active class from all tabs
                document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
                // Add active class to clicked tab
                this.classList.add('active');
                
                // Add loading animation to returns list
                const returnsList = document.getElementById('returnsList');
                returnsList.innerHTML = `
                    <div class="col-12">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-3 text-muted">Loading returns...</p>
                        </div>
                    </div>
                `;
                
                // Load returns for selected status
                loadReturns(this.dataset.status);
            });
        });
        
        // Search functionality
        let searchTimeout;
        document.getElementById('searchReturns').addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const searchTerm = this.value.toLowerCase();
            
            searchTimeout = setTimeout(() => {
                const returnCards = document.querySelectorAll('.return-card');
                returnCards.forEach(card => {
                    const cardText = card.textContent.toLowerCase();
                    const shouldShow = cardText.includes(searchTerm);
                    card.closest('.col-md-6').style.display = shouldShow ? 'block' : 'none';
                });
            }, 300);
        });
        
        document.getElementById('searchBtn').addEventListener('click', function() {
            const searchTerm = document.getElementById('searchReturns').value.toLowerCase();
            const returnCards = document.querySelectorAll('.return-card');
            returnCards.forEach(card => {
                const cardText = card.textContent.toLowerCase();
                const shouldShow = cardText.includes(searchTerm);
                card.closest('.col-md-6').style.display = shouldShow ? 'block' : 'none';
            });
        });
        
        // Clear search functionality
        document.getElementById('searchReturns').addEventListener('keyup', function(e) {
            if (e.key === 'Escape') {
                this.value = '';
                const returnCards = document.querySelectorAll('.return-card');
                returnCards.forEach(card => {
                    card.closest('.col-md-6').style.display = 'block';
                });
            }
        });
        
        // Load initial data
        loadReturns();
    </script>
</body>
</html>
