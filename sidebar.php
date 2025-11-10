<!-- Sidebar -->
<div class="sidebar">
    <div class="sidebar-header">
        <h3>Core Transaction 2</h3>
        <p style="margin: 5px 0 0 0; font-size: 0.8rem; opacity: 0.8;">Seller Dashboard</p>
    </div>
    
    <ul class="sidebar-menu">
        <li class="sidebar-item <?php if($currentPage == 'index.php') echo 'active'; ?>">
            <a href="index.php">
                <i class="bi bi-speedometer2"></i>
                <span class="menu-text">Dashboard</span>
            </a>
        </li>
        <li class="sidebar-item <?php if($currentPage == 'order.php') echo 'active'; ?>">
            <a href="order.php">
                <i class="bi bi-cart-check"></i>
                <span class="menu-text">Order Management</span>
            </a>
        </li>
        <li class="sidebar-item <?php if($currentPage == 'delivery.php') echo 'active'; ?>">
            <a href="delivery.php">
                <i class="bi bi-truck"></i>
                <span class="menu-text">Delivery Management</span>
            </a>
        </li>
        <li class="sidebar-item <?php if($currentPage == 'product_catalog.php') echo 'active'; ?>">
            <a href="product_catalog.php">
                <i class="bi bi-box-seam"></i>
                <span class="menu-text">Product Catalog</span>
            </a>
        </li>
        <li class="sidebar-item <?php if($currentPage == 'my_shop.php') echo 'active'; ?>">
            <a href="my_shop.php">
                <i class="bi bi-shop"></i>
                <span class="menu-text">My Shop</span>
            </a>
        </li>
        <li class="sidebar-item <?php if($currentPage == 'sales_reports.php') echo 'active'; ?>">
            <a href="sales_reports.php">
                <i class="bi bi-graph-up"></i>
                <span class="menu-text">Sales Reports</span>
            </a>
        </li>
        <li class="sidebar-item <?php if($currentPage == 'commission_reports.php') echo 'active'; ?>">
            <a href="commission_reports.php">
                <i class="bi bi-currency-dollar"></i>
                <span class="menu-text">Commission Reports</span>
            </a>
        </li>
        <li class="sidebar-item <?php if($currentPage == 'payment_monitoring.php') echo 'active'; ?>">
            <a href="payment_monitoring.php">
                <i class="bi bi-credit-card"></i>
                <span class="menu-text">Payment Monitoring</span>
            </a>
        </li>
        <li class="sidebar-item <?php if($currentPage == 'return_management.php') echo 'active'; ?>">
            <a href="return_management.php">
                <i class="bi bi-arrow-return-left"></i>
                <span class="menu-text">Return Management</span>
            </a>
        </li>
        <li class="sidebar-item <?php if($currentPage == 'seller_vouchers.php') echo 'active'; ?>">
            <a href="seller_vouchers.php">
                <i class="bi bi-gift"></i>
                <span class="menu-text">Seller Vouchers</span>
            </a>
        </li>
        <li class="sidebar-item <?php if($currentPage == 'vendor_requests.php') echo 'active'; ?>">
            <a href="vendor_requests.php">
                <i class="bi bi-inbox"></i>
                <span class="menu-text">Pending Products</span>
            </a>
        </li>
        <li class="sidebar-item <?php if($currentPage == 'security_settings.php') echo 'active'; ?>">
            <a href="security_settings.php">
                <i class="bi bi-shield-lock"></i>
                <span class="menu-text">Security & 2FA</span>
            </a>
        </li>
    </ul>
</div>


