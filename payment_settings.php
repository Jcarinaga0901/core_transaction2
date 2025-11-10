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
$vendor_id = $_SESSION['vendor_id'] ?? null;

// Get current payment methods
$payment_methods = [];
if ($vendor_id) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT * FROM payment_methods WHERE vendor_id = ? ORDER BY method_type");
        $stmt->execute([$vendor_id]);
        $payment_methods = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error fetching payment methods: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Settings - RAEVOR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
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

            <!-- Order Management -->
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
                    <li><a href="payment_monitoring.php"><span class="menu-text">Payment Monitoring</span></a></li>
                    <li><a href="payment_settings.php" class="active"><span class="menu-text">Payment Settings</span></a></li>
                </ul>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content" style="background-color: #f8f9fa; color: #212529;">
        <div class="container-fluid">
            <!-- Page Header -->
            <div class="page-header">
                <h1 class="page-title">Payment Settings</h1>
                <p class="page-subtitle">Configure your payment methods and gateway settings</p>
            </div>

            <!-- Payment Methods Configuration -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-credit-card me-2"></i>Payment Methods
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Bank Transfer -->
                                <div class="col-md-6 mb-4">
                                    <div class="card border">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="bankTransferToggle" checked>
                                                    <label class="form-check-label" for="bankTransferToggle">
                                                        <strong>Bank Transfer</strong>
                                                    </label>
                                                </div>
                                            </div>
                                            <form id="bankTransferForm">
                                                <div class="mb-3">
                                                    <label class="form-label">Bank Name</label>
                                                    <input type="text" class="form-control" name="bank_name" value="BDO" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Account Number</label>
                                                    <input type="text" class="form-control" name="account_number" value="1234567890" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Account Name</label>
                                                    <input type="text" class="form-control" name="account_name" value="RAEVOR Store" required>
                                                </div>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="bi bi-save me-1"></i>Save Settings
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- GCash -->
                                <div class="col-md-6 mb-4">
                                    <div class="card border">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="gcashToggle" checked>
                                                    <label class="form-check-label" for="gcashToggle">
                                                        <strong>GCash</strong>
                                                    </label>
                                                </div>
                                            </div>
                                            <form id="gcashForm">
                                                <div class="mb-3">
                                                    <label class="form-label">GCash Number</label>
                                                    <input type="text" class="form-control" name="gcash_number" value="09171234567" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Account Name</label>
                                                    <input type="text" class="form-control" name="gcash_name" value="RAEVOR Store" required>
                                                </div>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="bi bi-save me-1"></i>Save Settings
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- PayMaya -->
                                <div class="col-md-6 mb-4">
                                    <div class="card border">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="paymayaToggle" checked>
                                                    <label class="form-check-label" for="paymayaToggle">
                                                        <strong>PayMaya</strong>
                                                    </label>
                                                </div>
                                            </div>
                                            <form id="paymayaForm">
                                                <div class="mb-3">
                                                    <label class="form-label">PayMaya Number</label>
                                                    <input type="text" class="form-control" name="paymaya_number" value="09171234567" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Account Name</label>
                                                    <input type="text" class="form-control" name="paymaya_name" value="RAEVOR Store" required>
                                                </div>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="bi bi-save me-1"></i>Save Settings
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Credit Card -->
                                <div class="col-md-6 mb-4">
                                    <div class="card border">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="creditCardToggle">
                                                    <label class="form-check-label" for="creditCardToggle">
                                                        <strong>Credit Card</strong>
                                                    </label>
                                                </div>
                                            </div>
                                            <form id="creditCardForm">
                                                <div class="mb-3">
                                                    <label class="form-label">Gateway Provider</label>
                                                    <select class="form-select" name="gateway_provider">
                                                        <option value="paypal">PayPal</option>
                                                        <option value="stripe">Stripe</option>
                                                        <option value="paymongo">PayMongo</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Merchant ID</label>
                                                    <input type="text" class="form-control" name="merchant_id" placeholder="Enter merchant ID">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">API Key</label>
                                                    <input type="password" class="form-control" name="api_key" placeholder="Enter API key">
                                                </div>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="bi bi-save me-1"></i>Save Settings
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Gateway Settings -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-gear me-2"></i>Gateway Configuration
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Auto-verification</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="autoVerification">
                                            <label class="form-check-label" for="autoVerification">
                                                Automatically verify payments from trusted sources
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Payment Timeout</label>
                                        <select class="form-select" id="paymentTimeout">
                                            <option value="30">30 minutes</option>
                                            <option value="60" selected>1 hour</option>
                                            <option value="120">2 hours</option>
                                            <option value="240">4 hours</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Notification Settings</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="emailNotifications" checked>
                                            <label class="form-check-label" for="emailNotifications">
                                                Email notifications for new payments
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="smsNotifications">
                                            <label class="form-check-label" for="smsNotifications">
                                                SMS notifications for urgent payments
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Security Settings</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="requireVerification" checked>
                                            <label class="form-check-label" for="requireVerification">
                                                Require manual verification for large amounts
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="enableLogging" checked>
                                            <label class="form-check-label" for="enableLogging">
                                                Enable detailed payment logging
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end">
                                <button class="btn btn-success" onclick="saveGatewaySettings()">
                                    <i class="bi bi-save me-1"></i>Save All Settings
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Payment Methods Status -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-list-check me-2"></i>Active Payment Methods
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Payment Method</th>
                                            <th>Status</th>
                                            <th>Configuration</th>
                                            <th>Last Updated</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="paymentMethodsTable">
                                        <!-- Payment methods will be loaded here -->
                                    </tbody>
                                </table>
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
        menuItems.forEach(item => {
            const mainLink = item.querySelector('a');
            const submenuLinks = item.querySelectorAll('.submenu a');
                
            mainLink.addEventListener('click', function(e) {
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
        });

        // Load payment methods
        function loadPaymentMethods() {
            fetch('api/payments.php?action=get_payment_methods')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const tbody = document.getElementById('paymentMethodsTable');
                        tbody.innerHTML = '';
                        
                        data.methods.forEach(method => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>
                                    <i class="bi bi-credit-card me-2"></i>
                                    ${method.method_type.replace('_', ' ').toUpperCase()}
                                </td>
                                <td>
                                    <span class="badge bg-${method.is_active ? 'success' : 'secondary'}">
                                        ${method.is_active ? 'Active' : 'Inactive'}
                                    </span>
                                </td>
                                <td>
                                    ${method.bank_account_details ? 'Bank Account' : 
                                      method.ewallet_details ? 'E-Wallet' : 
                                      method.gateway_credentials ? 'Gateway' : 'Not Configured'}
                                </td>
                                <td>${new Date(method.updated_at).toLocaleDateString()}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="editPaymentMethod(${method.id})">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deletePaymentMethod(${method.id})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            `;
                            tbody.appendChild(row);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error loading payment methods:', error);
                });
        }

        // Save payment method
        function savePaymentMethod(formId, methodType) {
            const form = document.getElementById(formId);
            const formData = new FormData(form);
            formData.append('action', 'save_payment_method');
            formData.append('method_type', methodType);
            
            fetch('api/payments.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Payment method saved successfully!');
                    loadPaymentMethods();
                } else {
                    alert('Error saving payment method: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error saving payment method:', error);
                alert('Error saving payment method');
            });
        }

        // Save gateway settings
        function saveGatewaySettings() {
            const settings = {
                auto_verification: document.getElementById('autoVerification').checked,
                payment_timeout: document.getElementById('paymentTimeout').value,
                email_notifications: document.getElementById('emailNotifications').checked,
                sms_notifications: document.getElementById('smsNotifications').checked,
                require_verification: document.getElementById('requireVerification').checked,
                enable_logging: document.getElementById('enableLogging').checked
            };
            
            fetch('api/payments.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'save_gateway_settings',
                    settings: settings
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Gateway settings saved successfully!');
                } else {
                    alert('Error saving gateway settings: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error saving gateway settings:', error);
                alert('Error saving gateway settings');
            });
        }

        // Form submission handlers
        document.getElementById('bankTransferForm').addEventListener('submit', function(e) {
            e.preventDefault();
            savePaymentMethod('bankTransferForm', 'bank_transfer');
        });

        document.getElementById('gcashForm').addEventListener('submit', function(e) {
            e.preventDefault();
            savePaymentMethod('gcashForm', 'gcash');
        });

        document.getElementById('paymayaForm').addEventListener('submit', function(e) {
            e.preventDefault();
            savePaymentMethod('paymayaForm', 'paymaya');
        });

        document.getElementById('creditCardForm').addEventListener('submit', function(e) {
            e.preventDefault();
            savePaymentMethod('creditCardForm', 'credit_card');
        });

        // Load payment methods on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadPaymentMethods();
        });
    </script>
</body>
</html>
