<?php 
session_start();
require_once 'includes/auth.php';
requireAuth('login.php');
$currentPage = basename($_SERVER['PHP_SELF']); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Vouchers - Core Transaction 2</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap" rel="stylesheet">
    <link href="css/styles.css?v=<?php echo filemtime('css/styles.css'); ?>" rel="stylesheet">
</head>
<body>
    <header class="main-header">
        <div class="sidebar-toggle"><i class="bi bi-list"></i></div>
        <h1 style="font-family: 'Great Vibes', cursive !important; font-size: 1.5rem; font-weight: 350; letter-spacing: 2px; text-shadow: 2px 2px 4px rgba(0,0,0,0.2);"><a href="index.php" class="brand-link">RAEVOR</a></h1>
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

    <div class="sidebar">
        <div class="sidebar-header">
            <h3>Core Transaction 2</h3>
            <p style="margin: 5px 0 0 0; font-size: 0.8rem; opacity: 0.8;">Seller Dashboard</p>
        </div>
        <ul class="sidebar-nav">
            <li class="sidebar-item <?php if($currentPage == 'index.php') echo 'active'; ?>"><a href="index.php"><i class="bi bi-speedometer2"></i><span class="menu-text">Dashboard</span></a></li>
            <li class="sidebar-item has-submenu <?php if(in_array($currentPage, ['product_catalog.php','my_shop.php','vendor_requests.php'])) echo 'active'; ?>">
                <a href="product_catalog.php"><i class="bi bi-shop"></i><span class="menu-text">Product & Shop Management</span><i class="bi bi-chevron-down submenu-icon"></i></a>
                <ul class="submenu">
                    <li><a href="product_catalog.php" class="<?php if($currentPage == 'product_catalog.php') echo 'active'; ?>"><span class="menu-text">Product Catalog</span></a></li>
                    <li><a href="my_shop.php" class="<?php if($currentPage == 'my_shop.php') echo 'active'; ?>"><span class="menu-text">My Shop</span></a></li>
                    <li><a href="vendor_requests.php" class="<?php if($currentPage == 'vendor_requests.php') echo 'active'; ?>"><span class="menu-text">Pending Products</span></a></li>
                </ul>
            </li>
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
            <li class="sidebar-item <?php if($currentPage == 'delivery.php') echo 'active'; ?>"><a href="delivery.php"><i class="bi bi-truck"></i><span class="menu-text">Delivery Management</span></a></li>
            <li class="sidebar-item <?php if($currentPage == 'sales_reports.php' || $currentPage == 'commission_reports.php') echo 'active'; ?>">
                <a href="sales_reports.php"><i class="bi bi-graph-up"></i><span class="menu-text">Reports</span></a>
            </li>
            <li class="sidebar-item <?php if($currentPage == 'seller_documents.php') echo 'active'; ?>"><a href="seller_documents.php"><i class="bi bi-file-earmark-check"></i><span class="menu-text">Business Documents</span></a></li>
            <li class="sidebar-item <?php if($currentPage == 'payment_monitoring.php') echo 'active'; ?>"><a href="payment_monitoring.php"><i class="bi bi-credit-card"></i><span class="menu-text">Payment Monitoring</span></a></li>
            <li class="sidebar-item <?php if($currentPage == 'seller_vouchers.php') echo 'active'; ?>"><a href="seller_vouchers.php"><i class="bi bi-gift"></i><span class="menu-text">Seller Vouchers</span></a></li>
        </ul>
    </div>

    <div class="main-content" style="margin-left: 250px; padding: 2rem; min-height: calc(100vh - 60px); background: #f8f9fa;">
        <div class="container-fluid">
            <style>
                .page-header { background:#fff; border:1px solid #e9ecef; border-radius:12px; padding:16px 20px; 
                    box-shadow:0 4px 12px rgba(0,0,0,0.06); margin-top:20px; margin-bottom:16px; }
                .page-title { color:#212529; font-weight:700; font-size:1.75rem; margin:0 0 4px 0; }
                .page-subtitle { color:#6c757d; font-size:0.95rem; margin:0; }
                
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
            <div class="page-header d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="page-title">Seller Vouchers</h2>
                    <p class="page-subtitle">Create vouchers and manage approvals and activation</p>
                </div>
                <div class="text-end">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createVoucherModal">
                        <i class="bi bi-plus-circle me-1"></i>Create Voucher
                    </button>
                </div>
            </div>

            <ul class="nav nav-tabs mb-3" id="voucherTabs" role="tablist">
                <li class="nav-item" role="presentation"><button class="nav-link active" id="tab-shop" data-bs-toggle="tab" data-bs-target="#pane-shop" type="button" role="tab"><i class="bi bi-shop me-1"></i>Shop Vouchers</button></li>
                <li class="nav-item" role="presentation"><button class="nav-link" id="tab-my" data-bs-toggle="tab" data-bs-target="#pane-my" type="button" role="tab"><i class="bi bi-collection me-1"></i>My Shop Vouchers</button></li>
            </ul>

            <div class="tab-content">
                <!-- Shop Vouchers (approved) -->
                <div class="tab-pane fade show active" id="pane-shop" role="tabpanel" aria-labelledby="tab-shop">
                    <div class="card"><div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0" id="shopTable">
                                <thead class="table-light"><tr>
                                    <th>Name</th><th>Type</th><th>Value</th><th>Validity</th><th>Status</th>
                                </tr></thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div></div>
                </div>

                <!-- My Shop Vouchers (all + toggle) -->
                <div class="tab-pane fade" id="pane-my" role="tabpanel" aria-labelledby="tab-my">
                    <div class="card"><div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0" id="myTable">
                                <thead class="table-light"><tr>
                                    <th>Name</th><th>Type</th><th>Value</th><th>Validity</th><th>Status</th><th>Active</th><th class="text-end">Actions</th>
                                </tr></thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Voucher Modal -->
    <div class="modal fade" id="createVoucherModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create New Voucher</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="createForm" class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Voucher Name</label>
                            <input name="name" class="form-control" placeholder="e.g., 10% OFF on Tops" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Type</label>
                            <select name="type" class="form-select">
                                <option value="percent">Percentage Off (10-50%)</option>
                                <option value="fixed">Fixed Amount Off</option>
                                <option value="delivery">Delivery Voucher</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Discount Value</label>
                            <input name="discount_value" type="number" step="0.01" class="form-control" placeholder="e.g., 10 or 100" required>
                            <div class="form-text">For percentage: enter 10 for 10%. For fixed amount: enter 100 for ₱100 off.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Minimum Spend (optional)</label>
                            <input name="min_spend" type="number" step="0.01" class="form-control" placeholder="e.g., 500">
                            <div class="form-text">Minimum purchase amount to use this voucher</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Maximum Discount (optional)</label>
                            <input name="max_discount" type="number" step="0.01" class="form-control" placeholder="e.g., 200">
                            <div class="form-text">Maximum discount amount (useful for percentage vouchers)</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Validity Period</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input name="start_date" type="datetime-local" class="form-control" placeholder="Start Date">
                                </div>
                                <div class="col-6">
                                    <input name="end_date" type="datetime-local" class="form-control" placeholder="End Date">
                                </div>
                            </div>
                            <div class="form-text">Leave empty for no time restrictions</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description (optional)</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Describe the voucher terms and conditions..."></textarea>
                        </div>
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Voucher Creation Process:</strong><br>
                                1. Fill out the voucher details above<br>
                                2. Click "Submit for Approval"<br>
                                3. Admin will review and approve/reject your voucher<br>
                                4. Approved vouchers will appear in "Shop Vouchers" tab<br>
                                5. You can activate/deactivate approved vouchers in "My Shop Vouchers" tab
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="submitVoucherBtn">
                        <i class="bi bi-send me-1"></i>Submit for Approval
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <div class="mb-3">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                    </div>
                    <h4 class="text-success mb-2">Voucher Created Successfully!</h4>
                    <p class="text-muted mb-3">Your voucher has been submitted for admin approval.</p>
                    <div class="alert alert-info text-start">
                        <small>
                            <i class="bi bi-info-circle me-1"></i>
                            <strong>What happens next?</strong><br>
                            • Your voucher is now pending admin review<br>
                            • Once approved, it will appear in "Shop Vouchers"<br>
                            • You can activate/deactivate it in "My Shop Vouchers"
                        </small>
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal">
                        <i class="bi bi-check-lg me-1"></i>Got it!
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelector('.sidebar-toggle').addEventListener('click', function(){ document.body.classList.toggle('sidebar-collapsed'); });
        
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

        // Create voucher modal submission
        document.getElementById('submitVoucherBtn').addEventListener('click', async (e) => {
            const form = document.getElementById('createForm');
            const fd = new FormData(form);
            fd.append('action','create');
            
            try {
                const res = await fetch('api/vouchers.php', { method: 'POST', body: fd });
                const data = await res.json();
                
                if (data.ok) {
                    // Close create modal
                    bootstrap.Modal.getInstance(document.getElementById('createVoucherModal')).hide();
                    form.reset();
                    
                    // Show success modal
                    const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                    successModal.show();
                    
                    // Refresh data
                    loadShop();
                    loadMine();
                } else {
                    alert('Error: ' + (data.error || 'Failed to submit voucher'));
                }
            } catch (error) {
                alert('Error submitting voucher: ' + error.message);
            }
        });

        function fmtValue(v) { return Number(v||0).toLocaleString(undefined,{minimumFractionDigits:0, maximumFractionDigits:2}); }
        function fmtDate(a,b){ const s=a?new Date(a).toLocaleString():''; const e=b?new Date(b).toLocaleString():''; return (s||e)? (s+' - '+e):'—'; }

        async function loadShop(){
            const res = await fetch('api/vouchers.php?action=shop_list');
            const data = await res.json();
            const tb = document.querySelector('#shopTable tbody');
            tb.innerHTML = '';
            if (!data.ok || !data.vouchers || data.vouchers.length===0){ tb.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">No approved vouchers yet. Create one.</td></tr>'; return; }
            for (const v of data.vouchers){
                const tr = document.createElement('tr');
                tr.innerHTML = `<td>${v.name}</td><td>${v.type}</td><td>${v.type==='percent'?fmtValue(v.discount_value)+'%':'₱'+fmtValue(v.discount_value)}</td><td>${fmtDate(v.start_date,v.end_date)}</td><td><span class="badge bg-success">Approved</span></td>`;
                tb.appendChild(tr);
            }
        }

        async function loadMine(){
            const res = await fetch('api/vouchers.php?action=my_list');
            const data = await res.json();
            const tb = document.querySelector('#myTable tbody');
            tb.innerHTML = '';
            if (!data.ok || !data.vouchers || data.vouchers.length===0){ tb.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">No vouchers yet. Create one.</td></tr>'; return; }
            for (const v of data.vouchers){
                const badge = v.status==='approved'?'success':(v.status==='rejected'?'danger':'warning');
                const tr = document.createElement('tr');
                tr.innerHTML = `<td>${v.name}</td><td>${v.type}</td><td>${v.type==='percent'?fmtValue(v.discount_value)+'%':'₱'+fmtValue(v.discount_value)}</td><td>${fmtDate(v.start_date,v.end_date)}</td><td><span class="badge bg-${badge}">${v.status}</span></td><td>${v.status==='approved'? (v.is_active?'<span class="badge bg-primary">Active</span>':'<span class="badge bg-secondary">Inactive</span>') : '—'}</td><td class="text-end">${v.status==='approved'?`<button class="btn btn-sm btn-outline-${v.is_active?'secondary':'primary'}" data-action="toggle" data-id="${v.id}" data-active="${v.is_active?0:1}">${v.is_active?'Deactivate':'Activate'}</button>`:''}</td>`;
                tb.appendChild(tr);
            }
        }

        document.getElementById('voucherTabs').addEventListener('click', (e)=>{
            const btn=e.target.closest('button'); if(!btn) return;
            if (btn.id==='tab-shop') loadShop();
            if (btn.id==='tab-my') loadMine();
        });

        document.addEventListener('click', async (e)=>{
            const b=e.target.closest('button[data-action="toggle"]'); if(!b) return;
            const id=b.getAttribute('data-id'); const active=b.getAttribute('data-active');
            const fd=new FormData(); fd.append('action','toggle_active'); fd.append('id', id); fd.append('active', active);
            const res = await fetch('api/vouchers.php', { method:'POST', body: fd });
            const data = await res.json();
            if (data.ok){ loadMine(); } else { alert(data.error || 'Failed'); }
        });

        // Initial
        loadShop();
        loadMine();
    </script>
</body>
</html>


