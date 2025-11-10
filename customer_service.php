<?php
session_start();
require_once 'database/dbconnect.php';

// Set the current page for navigation highlighting
$currentPage = 'customer_service.php';

// Simple authentication check - set default admin session if not logged in
if (!isset($_SESSION['ct3_admin_id']) || !isset($_SESSION['ct3_authenticated']) || $_SESSION['ct3_authenticated'] !== true) {
    // Set default admin session for direct access
    $_SESSION['ct3_admin_id'] = 1;
    $_SESSION['ct3_authenticated'] = true;
    $_SESSION['ct3_full_name'] = 'CT3 Admin';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Service - CT3 Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }

        .main-content {
            margin-left: 250px;
            transition: margin-left 0.3s ease;
            min-height: 100vh;
            background-color: #f8f9fa;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            background-color: #343a40;
            z-index: 1000;
            transition: left 0.3s ease;
        }

        .main-header {
            position: fixed;
            top: 0;
            left: 250px;
            right: 0;
            height: 60px;
            background-color: #ffffff;
            border-bottom: 1px solid #dee2e6;
            z-index: 999;
            transition: left 0.3s ease;
        }

        .card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            background-color: #ffffff;
        }

        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            padding: 1rem;
            font-weight: 600;
        }

        .card-body {
            padding: 1.5rem;
        }

        .support-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
        }

        .support-card .card-header {
            background: rgba(255, 255, 255, 0.1);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
        }

        .ticket-item {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
        }

        .ticket-item:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .ticket-status {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
        }

        .status-open { background-color: #d4edda; color: #155724; }
        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-resolved { background-color: #d1ecf1; color: #0c5460; }

        .chat-container {
            height: 400px;
            overflow-y: auto;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1rem;
            background-color: #ffffff;
        }

        .message {
            margin-bottom: 1rem;
            padding: 0.75rem;
            border-radius: 8px;
            max-width: 80%;
        }

        .message.user {
            background-color: #007bff;
            color: white;
            margin-left: auto;
            text-align: right;
        }

        .message.agent {
            background-color: #e9ecef;
            color: #495057;
            border: 1px solid #dee2e6;
        }

        .message.system {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            text-align: center;
            margin: 0 auto;
        }

        .quick-action-btn {
            background: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .quick-action-btn:hover {
            background: #f8f9fa;
            border-color: #007bff;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-decoration: none;
            color: inherit;
        }

        .quick-action-icon {
            font-size: 2rem;
            color: #007bff;
            margin-bottom: 0.5rem;
        }

        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
        }

        .stats-number {
            font-size: 2rem;
            font-weight: bold;
        }

        .table {
            background-color: #ffffff;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.02);
        }

        .badge {
            font-size: 0.75rem;
            padding: 0.375rem 0.75rem;
        }

        .btn {
            border-radius: 6px;
            font-weight: 500;
        }

        .container-fluid {
            padding-top: 80px;
            max-width: 100%;
            overflow-x: hidden;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
            
            .main-header {
                left: 0;
            }
            
            .sidebar {
                left: -250px;
            }
            
            .container-fluid {
                padding-top: 60px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="main-header">
        <div class="d-flex justify-content-between align-items-center h-100 px-4">
            <div class="d-flex align-items-center">
                <button class="btn btn-link text-dark me-3" onclick="toggleSidebar()">
                    <i class="bi bi-list"></i>
                </button>
                <h4 class="mb-0">Customer Service</h4>
            </div>
            <div class="d-flex align-items-center">
                <span class="username"><?php echo htmlspecialchars($_SESSION['ct3_full_name']); ?></span>
            </div>
        </div>
    </header>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header p-3 border-bottom">
            <h5 class="text-white mb-0">RAEVOR</h5>
        </div>
        <ul class="sidebar-menu">
            <li class="sidebar-item">
                <a href="index.php" class="<?php if($currentPage == 'index.php') echo 'active'; ?>">
                    <i class="bi bi-house"></i>
                    <span class="menu-text">Dashboard</span>
                </a>
            </li>

            <!-- Subscription Packages -->
            <li class="sidebar-item has-submenu <?php if(in_array($currentPage, ['vendor_subscriptions.php', 'voucher_packages.php', 'manage_packages.php'])) echo 'active'; ?>">
                <a href="#" onclick="toggleSubmenu(this)">
                    <i class="bi bi-box-seam"></i>
                    <span class="menu-text">Subscription Packages</span>
                    <i class="bi bi-chevron-down submenu-toggle"></i>
                </a>
                <ul class="submenu">
                    <li><a href="vendor_subscriptions.php" class="<?php if($currentPage == 'vendor_subscriptions.php') echo 'active'; ?>"><span class="menu-text">Vendor Subscriptions</span></a></li>
                    <li><a href="voucher_packages.php" class="<?php if($currentPage == 'voucher_packages.php') echo 'active'; ?>"><span class="menu-text">Voucher Packages</span></a></li>
                    <li><a href="manage_packages.php" class="<?php if($currentPage == 'manage_packages.php') echo 'active'; ?>"><span class="menu-text">Manage Packages</span></a></li>
                </ul>
            </li>

            <!-- Commission Management -->
            <li class="sidebar-item has-submenu <?php if(in_array($currentPage, ['commission_rates.php','commission_reports.php'])) echo 'active'; ?>">
                <a href="#" onclick="toggleSubmenu(this)">
                    <i class="bi bi-percent"></i>
                    <span class="menu-text">Commission Management</span>
                    <i class="bi bi-chevron-down submenu-toggle"></i>
                </a>
                <ul class="submenu">
                    <li><a href="commission_rates.php" class="<?php if($currentPage == 'commission_rates.php') echo 'active'; ?>"><span class="menu-text">Commission Rates</span></a></li>
                    <li><a href="commission_reports.php" class="<?php if($currentPage == 'commission_reports.php') echo 'active'; ?>"><span class="menu-text">Commission Reports</span></a></li>
                </ul>
            </li>

            <!-- Delivery Management -->
            <li class="sidebar-item">
                <a href="delivery.php" class="<?php if($currentPage == 'delivery.php') echo 'active'; ?>">
                    <i class="bi bi-truck"></i>
                    <span class="menu-text">Delivery Management</span>
                </a>
            </li>

            <!-- Package Management -->
            <li class="sidebar-item">
                <a href="package_activation.php" class="<?php if($currentPage == 'package_activation.php') echo 'active'; ?>">
                    <i class="bi bi-box"></i>
                    <span class="menu-text">Package Management</span>
                </a>
            </li>

            <!-- Product Catalogue -->
            <li class="sidebar-item">
                <a href="catalogue.php" class="<?php if($currentPage == 'catalogue.php') echo 'active'; ?>">
                    <i class="bi bi-grid"></i>
                    <span class="menu-text">Product Catalogue</span>
                </a>
            </li>

            <!-- Shipment / Click & Call -->
            <li class="sidebar-item has-submenu <?php if(in_array($currentPage, ['shipment.php', 'customer_service.php'])) echo 'active'; ?>">
                <a href="#" onclick="toggleSubmenu(this)">
                    <i class="bi bi-telephone"></i>
                    <span class="menu-text">Shipment / Click & Call</span>
                    <i class="bi bi-chevron-up submenu-toggle"></i>
                </a>
                <ul class="submenu" style="display: block;">
                    <li><a href="shipment.php" class="<?php if($currentPage == 'shipment.php') echo 'active'; ?>"><span class="menu-text">Shipment Tracking</span></a></li>
                    <li><a href="customer_service.php" class="<?php if($currentPage == 'customer_service.php') echo 'active'; ?>"><span class="menu-text">Customer Service</span></a></li>
                </ul>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">Customer Service</h2>
                    <p class="text-muted mb-0">Manage customer support tickets and live chat</p>
                </div>
                <div>
                    <button class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>New Ticket
                    </button>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card stats-card">
                        <div class="card-body text-center">
                            <div class="stats-number">24</div>
                            <div>Open Tickets</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card">
                        <div class="card-body text-center">
                            <div class="stats-number">12</div>
                            <div>Pending</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card">
                        <div class="card-body text-center">
                            <div class="stats-number">156</div>
                            <div>Resolved Today</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card">
                        <div class="card-body text-center">
                            <div class="stats-number">4.8</div>
                            <div>Avg Rating</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Quick Actions -->
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-6">
                                    <a href="#" class="quick-action-btn" onclick="createNewTicket()">
                                        <div class="quick-action-icon">
                                            <i class="bi bi-ticket"></i>
                                        </div>
                                        <div class="text-center">
                                            <small>Create Ticket</small>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="#" class="quick-action-btn" onclick="viewAllTickets()">
                                        <div class="quick-action-icon">
                                            <i class="bi bi-list-ul"></i>
                                        </div>
                                        <div class="text-center">
                                            <small>View All</small>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="#" class="quick-action-btn" onclick="startLiveChat()">
                                        <div class="quick-action-icon">
                                            <i class="bi bi-chat-dots"></i>
                                        </div>
                                        <div class="text-center">
                                            <small>Live Chat</small>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="#" class="quick-action-btn" onclick="viewReports()">
                                        <div class="quick-action-icon">
                                            <i class="bi bi-graph-up"></i>
                                        </div>
                                        <div class="text-center">
                                            <small>Reports</small>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Tickets -->
                <div class="col-md-8 mb-4">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Recent Support Tickets</h5>
                            <button class="btn btn-sm btn-outline-primary">View All</button>
                        </div>
                        <div class="card-body">
                            <div class="ticket-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">Order Delivery Issue</h6>
                                        <p class="text-muted mb-1 small">Customer: John Doe - Order #12345</p>
                                        <small class="text-muted">2 hours ago</small>
                                    </div>
                                    <span class="ticket-status status-open">Open</span>
                                </div>
                            </div>
                            <div class="ticket-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">Payment Refund Request</h6>
                                        <p class="text-muted mb-1 small">Customer: Jane Smith - Order #12346</p>
                                        <small class="text-muted">4 hours ago</small>
                                    </div>
                                    <span class="ticket-status status-pending">Pending</span>
                                </div>
                            </div>
                            <div class="ticket-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">Product Inquiry</h6>
                                        <p class="text-muted mb-1 small">Customer: Mike Johnson - Product #789</p>
                                        <small class="text-muted">6 hours ago</small>
                                    </div>
                                    <span class="ticket-status status-resolved">Resolved</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Live Chat Support -->
            <div class="row">
                <div class="col-12">
                    <div id="messages" class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Live Chat Support</h5>
                        </div>
                        <div class="card-body">
                            <div class="chat-container" id="chatContainer">
                                <div class="message system">
                                    <strong>System:</strong> Welcome to customer support. How can we help you today?
                                </div>
                                <div class="message agent">
                                    <strong>Agent:</strong> Hello! I'm here to assist you. Please let me know what you need help with.
                                </div>
                            </div>
                            <div class="input-group mt-3">
                                <input type="text" class="form-control" placeholder="Type your message..." id="messageInput">
                                <button class="btn btn-primary" type="button" onclick="sendMessage()">
                                    <i class="bi bi-send"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('collapsed');
        }

        // Submenu toggle functionality
        function toggleSubmenu(element) {
            const parentLi = element.closest('li');
            const submenu = parentLi.querySelector('.submenu');
            const toggleIcon = element.querySelector('.submenu-toggle');
            
            if (submenu.style.display === 'block') {
                submenu.style.display = 'none';
                toggleIcon.className = 'bi bi-chevron-down submenu-toggle';
            } else {
                // Close all other submenus first
                document.querySelectorAll('.submenu').forEach(menu => {
                    menu.style.display = 'none';
                });
                document.querySelectorAll('.submenu-toggle').forEach(icon => {
                    icon.className = 'bi bi-chevron-down submenu-toggle';
                });
                
                // Open this submenu
                submenu.style.display = 'block';
                toggleIcon.className = 'bi bi-chevron-up submenu-toggle';
            }
        }

        // Quick action functions
        function createNewTicket() {
            alert('Creating new support ticket...');
        }

        function viewAllTickets() {
            alert('Opening all tickets view...');
        }

        function startLiveChat() {
            alert('Starting live chat session...');
        }

        function viewReports() {
            alert('Opening support reports...');
        }

        // Chat functionality
        function sendMessage() {
            const input = document.getElementById('messageInput');
            const message = input.value.trim();
            
            if (message) {
                const chatContainer = document.getElementById('chatContainer');
                const messageDiv = document.createElement('div');
                messageDiv.className = 'message user';
                messageDiv.innerHTML = `<strong>You:</strong> ${message}`;
                
                chatContainer.appendChild(messageDiv);
                chatContainer.scrollTop = chatContainer.scrollHeight;
                
                input.value = '';
                
                // Simulate agent response
                setTimeout(() => {
                    const agentMessage = document.createElement('div');
                    agentMessage.className = 'message agent';
                    agentMessage.innerHTML = '<strong>Agent:</strong> Thank you for your message. I will help you with that right away.';
                    
                    chatContainer.appendChild(agentMessage);
                    chatContainer.scrollTop = chatContainer.scrollHeight;
                }, 1000);
            }
        }

        // Allow Enter key to send message
        document.getElementById('messageInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });

        // Auto-focus messages panel when arriving with #messages anchor
        document.addEventListener('DOMContentLoaded', function() {
            if (window.location.hash === '#messages') {
                const panel = document.getElementById('messages');
                if (panel) {
                    panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
                const input = document.getElementById('messageInput');
                if (input) {
                    input.focus();
                }
            }
        });
    </script>
</body>
</html>
