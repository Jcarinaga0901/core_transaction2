<?php 
session_start();
$currentPage = basename($_SERVER['PHP_SELF']);

// Authentication and database setup
require_once 'includes/auth.php';
require_once 'config/database.php';

// Ensure user is authenticated
requireAuth();

// Get current user details
$user = getCurrentUser();
$vendorId = $user['vendor_id'] ?? null;

// Fetch vendor/shop details for logo display
$shopDetails = null;
if ($vendorId) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT * FROM vendors WHERE id = ?");
        $stmt->execute([$vendorId]);
        $shopDetails = $stmt->fetch();
        
        // Check for temporary logo in session (fallback for missing database column)
        if (empty($shopDetails['logo_url']) && isset($_SESSION['temp_logo_url']) && $_SESSION['temp_logo_vendor_id'] == $vendorId) {
            $shopDetails['logo_url'] = $_SESSION['temp_logo_url'];
        }
        
        // Debug: Log shop details
        error_log("Shop Details fetched: " . json_encode($shopDetails));
    } catch (Exception $e) {
        error_log("Error fetching shop details: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commission Reports - Core Transaction 2</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap" rel="stylesheet">
    <link href="css/styles.css?v=<?php echo filemtime('css/styles.css'); ?>" rel="stylesheet">
    <style>
        /* Use dark text for readability on light background */
        .main-content,
        .main-content h1,
        .main-content h2,
        .main-content h3,
        .main-content p,
        .main-content label.form-label,
        .main-content .fw-semibold,
        .main-content .text-muted,
        .main-content .text { color: #212529 !important; }

        /* Make card titles visible */
        .main-content .card .text-muted { color: #6c757d !important; }

        /* Keep tables readable using dark text */
        .main-content .table,
        .main-content .table * { color: #212529 !important; }

        /* Make KPI values black in all four boxes */
        .main-content #kpiGross,
        .main-content #kpiRate,
        .main-content #kpiCommission,
        .main-content #kpiOrders { color: #212529 !important; }

        /* Chart styling */
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }

        .chart-card {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        .chart-card .card-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .chart-card .card-body {
            padding: 20px;
        }

        /* Responsive chart adjustments */
        @media (max-width: 768px) {
            .chart-container {
                height: 250px;
            }
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
            <p style="margin: 5px 0 0 0; font-size: 0.8rem; opacity: 0.8;">Seller Dashboard</p>
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
            <li class="sidebar-item has-submenu <?php if(in_array($currentPage, ['order.php','cancelled_orders.php','return_management.php'])) echo 'active'; ?>">
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
                    <li><a href="sales_reports.php" class="<?php if($currentPage == 'sales_reports.php') echo 'active'; ?>"><i class="bi bi-bar-chart-line me-2"></i><span class="menu-text">Sales Reports</span></a></li>
                    <li><a href="commission_reports.php" class="<?php if($currentPage == 'commission_reports.php') echo 'active'; ?>"><i class="bi bi-cash-coin me-2"></i><span class="menu-text">Commission Reports</span></a></li>
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
            <div class="page-header">
                <h2 class="page-title">Commission Reports</h2>
                <p class="page-subtitle">Per-item commission from successful deliveries (Core 3)</p>
            </div>

            <form id="reportFilters" class="row g-2 mb-3">
                <div class="col-md-3">
                    <label class="form-label">From</label>
                    <input type="date" class="form-control" name="from" id="from">
                </div>
                <div class="col-md-3">
                    <label class="form-label">To</label>
                    <input type="date" class="form-control" name="to" id="to">
                </div>
                <div class="col-md-2 d-grid align-end">
                    <label class="form-label" style="visibility:hidden">Action</label>
                    <button class="btn btn-primary" type="submit"><i class="bi bi-arrow-repeat me-1"></i>Refresh</button>
                </div>
                <div class="col-md-1 d-grid align-end">
                    <label class="form-label" style="visibility:hidden">Print</label>
                    <button class="btn btn-success btn-sm" type="button" onclick="printCommissionReport()"><i class="bi bi-printer me-1"></i>Print</button>
                </div>
                <div class="col-md-1 d-grid align-end">
                    <label class="form-label" style="visibility:hidden">Download</label>
                    <button class="btn btn-outline-success btn-sm" type="button" onclick="downloadCommissionReport()"><i class="bi bi-file-earmark-arrow-down me-1"></i>Download</button>
                </div>
            </form>

            <div class="row" id="kpiRow">
                <div class="col-md-3 mb-3">
                    <div class="card"><div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-muted small">Total Gross</div>
                                <div class="fs-4 fw-semibold" id="kpiGross">₱0.00</div>
                            </div>
                            <i class="bi bi-currency-dollar text-primary" style="font-size: 2rem; opacity: 0.7;"></i>
                        </div>
                    </div></div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card"><div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-muted small">Rate Commission</div>
                                <div class="fs-4 fw-semibold" id="kpiRate">0%</div>
                            </div>
                            <i class="bi bi-percent text-success" style="font-size: 2rem; opacity: 0.7;"></i>
                        </div>
                    </div></div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card"><div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-muted small">Total Commission</div>
                                <div class="fs-4 fw-semibold" id="kpiCommission">₱0.00</div>
                            </div>
                            <i class="bi bi-cash-coin text-warning" style="font-size: 2rem; opacity: 0.7;"></i>
                        </div>
                    </div></div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card"><div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-muted small">Orders</div>
                                <div class="fs-4 fw-semibold" id="kpiOrders">0</div>
                            </div>
                            <i class="bi bi-cart-check text-info" style="font-size: 2rem; opacity: 0.7;"></i>
                        </div>
                    </div></div>
                </div>
            </div>

            <!-- Commission Charts Section -->
            <div class="row mb-4">
                <div class="col-lg-8">
                    <div class="card chart-card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>Commission Trend Analysis</h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="commissionTrendChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card chart-card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="bi bi-pie-chart me-2"></i>Commission Distribution</h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="commissionDistributionChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="tblCommission">
                    <thead class="table-light"><tr><th>Date</th><th>Orders</th><th>Items</th><th>Gross</th><th>Rate</th><th>Commission</th></tr></thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="small text-muted" id="commissionNote"></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <?php include 'includes/logout_modal.php'; ?>
    <script>
        const fmtPeso = v => '₱' + Number(v || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        function getDates() {
            const today = new Date();
            const to = document.getElementById('to');
            const from = document.getElementById('from');
            if (!to.value) to.valueAsDate = today;
            if (!from.value) { const d = new Date(); d.setDate(d.getDate() - 30); from.valueAsDate = d; }
            return { from: from.value, to: to.value };
        }
        // Chart instances
        let trendChart = null;
        let distributionChart = null;

        async function loadCommission() {
            const { from, to } = getDates();
            const res = await fetch(`api/reports.php?action=commission_report&from=${encodeURIComponent(from)}&to=${encodeURIComponent(to)}`);
            const data = await res.json();
            const tbody = document.querySelector('#tblCommission tbody');
            const totalEl = document.getElementById('kpiCommission');
            const grossEl = document.getElementById('kpiGross');
            const rateEl = document.getElementById('kpiRate');
            const ordersEl = document.getElementById('kpiOrders');
            const noteEl = document.getElementById('commissionNote');
            tbody.innerHTML=''; noteEl.textContent='';
            if (!data.ok) return;
            if (data.note) noteEl.textContent = data.note + (data.source ? ` (source: ${data.source})` : '');
            let total = 0, items = 0, gross = 0; const ordersSet = new Set();
            const byDate = {}; // date: {gross, commission, items, orders}
            if (!data.rows || data.rows.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4"><i class="bi bi-cash-coin" style="font-size: 2.2rem; display: block; margin-bottom: 0.5rem;"></i>No commission data for the selected period</td></tr>';
                totalEl.textContent = '₱0.00'; grossEl.textContent = '₱0.00'; rateEl.textContent = '0%'; ordersEl.textContent = '0';
                // Clear charts if no data
                clearCharts();
                return;
            }
            data.rows.forEach(r => {
                const comm = Number(r.commission_amount || 0);
                const qty = Number(r.quantity || 0);
                const g = Number(r.gross_amount || 0);
                total += comm; items += qty; gross += g; if (r.order_no) ordersSet.add(r.order_no);
                const d = r.period || '';
                if (!byDate[d]) byDate[d] = { gross:0, commission:0, items:0, orders:new Set() };
                byDate[d].gross += g; byDate[d].commission += comm; byDate[d].items += qty; if (r.order_no) byDate[d].orders.add(r.order_no);
            });
            // KPIs
            totalEl.textContent = fmtPeso(total);
            grossEl.textContent = fmtPeso(gross);
            const ordersCount = ordersSet.size; ordersEl.textContent = String(ordersCount);
            const rateAll = gross > 0 ? ((total / gross) * 100) : 0; rateEl.textContent = rateAll.toFixed(2) + '%';

            // Summarize table by date
            const labels = Object.keys(byDate).sort();
            tbody.innerHTML = '';
            labels.forEach(k => {
                const d = byDate[k];
                const orders = d.orders.size;
                const rate = d.gross > 0 ? ((d.commission / d.gross) * 100) : 0;
                const tr = document.createElement('tr');
                tr.innerHTML = `<td>${k}</td><td>${orders}</td><td>${d.items}</td><td>${fmtPeso(d.gross)}</td><td>${rate.toFixed(2)}%</td><td>${fmtPeso(d.commission)}</td>`;
                tbody.appendChild(tr);
            });

            // Create charts with the data
            createTrendChart(labels, byDate);
            createDistributionChart(byDate);
        }

        function clearCharts() {
            if (trendChart) {
                trendChart.destroy();
                trendChart = null;
            }
            if (distributionChart) {
                distributionChart.destroy();
                distributionChart = null;
            }
        }

        function createTrendChart(labels, byDate) {
            const ctx = document.getElementById('commissionTrendChart');
            if (!ctx) return;

            // Destroy existing chart
            if (trendChart) {
                trendChart.destroy();
            }

            const grossData = labels.map(label => byDate[label].gross);
            const commissionData = labels.map(label => byDate[label].commission);

            trendChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Gross Amount (₱)',
                        data: grossData,
                        borderColor: 'rgb(54, 162, 235)',
                        backgroundColor: 'rgba(54, 162, 235, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }, {
                        label: 'Commission (₱)',
                        data: commissionData,
                        borderColor: 'rgb(255, 99, 132)',
                        backgroundColor: 'rgba(255, 99, 132, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 20
                            }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ₱' + context.parsed.y.toLocaleString('en-PH', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    });
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Date'
                            }
                        },
                        y: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Amount (₱)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return '₱' + value.toLocaleString();
                                }
                            }
                        }
                    },
                    interaction: {
                        mode: 'nearest',
                        axis: 'x',
                        intersect: false
                    }
                }
            });
        }

        function createDistributionChart(byDate) {
            const ctx = document.getElementById('commissionDistributionChart');
            if (!ctx) return;

            // Destroy existing chart
            if (distributionChart) {
                distributionChart.destroy();
            }

            const labels = Object.keys(byDate);
            const commissionData = labels.map(label => byDate[label].commission);
            const grossData = labels.map(label => byDate[label].gross);

            // Calculate percentages
            const totalCommission = commissionData.reduce((a, b) => a + b, 0);
            const totalGross = grossData.reduce((a, b) => a + b, 0);

            if (totalCommission === 0 && totalGross === 0) {
                // Show empty state
                ctx.parentElement.innerHTML = '<div class="text-center text-muted py-4"><i class="bi bi-pie-chart" style="font-size: 2rem; opacity: 0.3;"></i><p class="mt-2">No data to display</p></div>';
                return;
            }

            distributionChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: commissionData,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.8)',
                            'rgba(54, 162, 235, 0.8)',
                            'rgba(255, 205, 86, 0.8)',
                            'rgba(75, 192, 192, 0.8)',
                            'rgba(153, 102, 255, 0.8)',
                            'rgba(255, 159, 64, 0.8)',
                            'rgba(199, 199, 199, 0.8)',
                            'rgba(83, 102, 255, 0.8)'
                        ],
                        borderColor: [
                            'rgb(255, 99, 132)',
                            'rgb(54, 162, 235)',
                            'rgb(255, 205, 86)',
                            'rgb(75, 192, 192)',
                            'rgb(153, 102, 255)',
                            'rgb(255, 159, 64)',
                            'rgb(199, 199, 199)',
                            'rgb(83, 102, 255)'
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
                                usePointStyle: true,
                                padding: 15,
                                font: {
                                    size: 11
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
                                    return label + ': ₱' + value.toLocaleString() + ' (' + percentage + '%)';
                                }
                            }
                        }
                    }
                }
            });
        }
        function printCommissionReport() {
            const { from, to } = getDates();
            const kpiData = {
                gross: document.getElementById('kpiGross').textContent,
                rate: document.getElementById('kpiRate').textContent,
                commission: document.getElementById('kpiCommission').textContent,
                orders: document.getElementById('kpiOrders').textContent
            };
            
            const table = document.getElementById('tblCommission');
            const tableHTML = table.outerHTML;
            
            // Get seller info from PHP session and shop details
            const sellerInfo = {
                username: '<?php echo htmlspecialchars($_SESSION['username'] ?? 'N/A'); ?>',
                email: '<?php echo htmlspecialchars($_SESSION['email'] ?? 'N/A'); ?>',
                role: '<?php echo htmlspecialchars($_SESSION['role'] ?? 'N/A'); ?>',
                shopName: '<?php echo htmlspecialchars($shopDetails['shop_name'] ?? $shopDetails['business_name'] ?? 'N/A'); ?>',
                logoUrl: '<?php echo htmlspecialchars($shopDetails['logo_url'] ?? ''); ?>'
            };
            
            // Debug: Log seller info to console
            console.log('Seller Info for Print:', sellerInfo);
            console.log('Logo URL:', sellerInfo.logoUrl);
            
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Commission Report - ${from} to ${to}</title>
                    <style>
                        body { 
                            font-family: 'Times New Roman', serif; 
                            margin: 0; 
                            padding: 20px; 
                            color: #000; 
                            background: #fff;
                            line-height: 1.4;
                        }
                        .invoice-header { 
                            border-bottom: 3px solid #000; 
                            padding-bottom: 15px; 
                            margin-bottom: 20px; 
                        }
                        .company-info { 
                            text-align: center; 
                            margin-bottom: 20px; 
                        }
                        .company-logo { 
                            font-family: 'Times New Roman', serif; 
                            font-size: 2.5rem; 
                            font-weight: bold; 
                            color: #000; 
                            margin-bottom: 5px;
                            text-transform: uppercase;
                            letter-spacing: 3px;
                        }
                        .company-tagline { 
                            font-size: 0.9rem; 
                            color: #666; 
                            font-style: italic;
                        }
                        .invoice-title { 
                            font-size: 1.8rem; 
                            font-weight: bold; 
                            text-align: center; 
                            margin: 20px 0;
                            text-transform: uppercase;
                        }
                        .invoice-details { 
                            display: flex; 
                            justify-content: space-between; 
                            margin-bottom: 30px;
                            border: 1px solid #000;
                            padding: 15px;
                        }
                        .invoice-from, .invoice-to { 
                            flex: 1; 
                            padding: 0 10px;
                        }
                        .invoice-from h4, .invoice-to h4 { 
                            margin: 0 0 10px 0; 
                            font-size: 1.1rem; 
                            text-transform: uppercase;
                            border-bottom: 1px solid #000;
                            padding-bottom: 5px;
                        }
                        .invoice-info { 
                            font-size: 0.9rem; 
                            line-height: 1.6;
                        }
                        .invoice-period { 
                            text-align: center; 
                            margin: 20px 0; 
                            font-size: 1.1rem; 
                            font-weight: bold;
                        }
                        .summary-section { 
                            margin: 25px 0; 
                            border: 2px solid #000;
                        }
                        .summary-title { 
                            background: #000; 
                            color: #fff; 
                            padding: 10px; 
                            margin: 0; 
                            font-size: 1.1rem; 
                            text-transform: uppercase;
                            text-align: center;
                        }
                        .kpi-summary { 
                            display: grid; 
                            grid-template-columns: repeat(4, 1fr); 
                            gap: 0;
                        }
                        .kpi-item { 
                            border-right: 1px solid #000; 
                            padding: 15px; 
                            text-align: center;
                        }
                        .kpi-item:last-child { 
                            border-right: none; 
                        }
                        .kpi-label { 
                            font-size: 0.8rem; 
                            font-weight: bold; 
                            margin-bottom: 5px; 
                            text-transform: uppercase;
                        }
                        .kpi-value { 
                            font-size: 1.2rem; 
                            font-weight: bold; 
                            color: #000;
                        }
                        table { 
                            width: 100%; 
                            border-collapse: collapse; 
                            margin: 20px 0;
                            border: 2px solid #000;
                        }
                        th, td { 
                            border: 1px solid #000; 
                            padding: 12px 8px; 
                            text-align: left; 
                            font-size: 0.9rem;
                        }
                        th { 
                            background: #f0f0f0; 
                            font-weight: bold; 
                            text-transform: uppercase;
                            text-align: center;
                        }
                        .footer { 
                            margin-top: 40px; 
                            text-align: center; 
                            font-size: 0.8rem; 
                            border-top: 1px solid #000;
                            padding-top: 15px;
                        }
                        .seller-logo { 
                            width: 50px; 
                            height: 50px; 
                            object-fit: cover; 
                            border-radius: 50%; 
                            border: 2px solid #000;
                            margin: 5px 0;
                        }
                        @media print { 
                            body { margin: 0; padding: 15px; }
                            .invoice-header { page-break-inside: avoid; }
                            .summary-section { page-break-inside: avoid; }
                        }
                    </style>
                </head>
                <body>
                    <div class="invoice-header">
                        <div class="company-info">
                            <div class="company-logo">RAEVOR</div>
                            <div class="company-tagline">Commission Report Invoice</div>
                        </div>
                        
                        <div class="invoice-title">Commission Report</div>
                        
                        <div class="invoice-details">
                            <div class="invoice-from">
                                <h4>From</h4>
                                <div class="invoice-info">
                                    <strong>RAEVOR</strong><br>
                                    Core Transaction 2<br>
                                    Commission Management System<br>
                                    <em>Per-item commission from successful deliveries (Core 3)</em>
                                </div>
                            </div>
                            <div class="invoice-to">
                                <h4>To</h4>
                                <div class="invoice-info">
                                    ${sellerInfo.logoUrl && sellerInfo.logoUrl !== '' ? `<img src="${sellerInfo.logoUrl}" alt="Shop Logo" class="seller-logo"><br>` : ''}
                                    <strong>${sellerInfo.shopName}</strong><br>
                                    Seller: ${sellerInfo.username}<br>
                                    Email: ${sellerInfo.email}<br>
                                    Role: ${sellerInfo.role}
                                </div>
                            </div>
                        </div>
                        
                        <div class="invoice-period">
                            <strong>Report Period: ${from} to ${to}</strong>
                        </div>
                    </div>
                    
                    <div class="summary-section">
                        <h3 class="summary-title">Commission Summary</h3>
                        <div class="kpi-summary">
                            <div class="kpi-item">
                                <div class="kpi-label">Total Gross</div>
                                <div class="kpi-value">${kpiData.gross}</div>
                            </div>
                            <div class="kpi-item">
                                <div class="kpi-label">Commission Rate</div>
                                <div class="kpi-value">${kpiData.rate}</div>
                            </div>
                            <div class="kpi-item">
                                <div class="kpi-label">Total Commission</div>
                                <div class="kpi-value">${kpiData.commission}</div>
                            </div>
                            <div class="kpi-item">
                                <div class="kpi-label">Total Orders</div>
                                <div class="kpi-value">${kpiData.orders}</div>
                            </div>
                        </div>
                    </div>
                    
                    ${tableHTML}
                    
                    <div class="footer">
                        <p><strong>Invoice Generated:</strong> ${new Date().toLocaleDateString()} at ${new Date().toLocaleTimeString()}</p>
                        <p><strong>System:</strong> Core Transaction 2 - Commission Reports</p>
                        <p><em>This is an official commission report generated by RAEVOR system.</em></p>
                    </div>
                </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.focus();
            setTimeout(() => {
                printWindow.print();
                printWindow.close();
            }, 500);
        }

        function downloadCommissionReport() {
            const { from, to } = getDates();
            const kpiData = {
                gross: document.getElementById('kpiGross').textContent,
                rate: document.getElementById('kpiRate').textContent,
                commission: document.getElementById('kpiCommission').textContent,
                orders: document.getElementById('kpiOrders').textContent
            };
            
            const table = document.getElementById('tblCommission');
            const tableHTML = table.outerHTML;
            
            // Get seller info from PHP session and shop details
            const sellerInfo = {
                username: '<?php echo htmlspecialchars($_SESSION['username'] ?? 'N/A'); ?>',
                email: '<?php echo htmlspecialchars($_SESSION['email'] ?? 'N/A'); ?>',
                role: '<?php echo htmlspecialchars($_SESSION['role'] ?? 'N/A'); ?>',
                shopName: '<?php echo htmlspecialchars($shopDetails['shop_name'] ?? $shopDetails['business_name'] ?? 'N/A'); ?>',
                logoUrl: '<?php echo htmlspecialchars($shopDetails['logo_url'] ?? ''); ?>'
            };
            
            // Create HTML content for DOC conversion
            const htmlContent = `
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <title>Commission Report - ${from} to ${to}</title>
                    <style>
                        body { 
                            font-family: 'Times New Roman', serif; 
                            margin: 0; 
                            padding: 20px; 
                            color: #000; 
                            background: #fff;
                            line-height: 1.4;
                        }
                        .invoice-header { 
                            border-bottom: 3px solid #000; 
                            padding-bottom: 15px; 
                            margin-bottom: 20px; 
                        }
                        .company-info { 
                            text-align: center; 
                            margin-bottom: 20px; 
                        }
                        .company-logo { 
                            font-family: 'Times New Roman', serif; 
                            font-size: 2.5rem; 
                            font-weight: bold; 
                            color: #000; 
                            margin-bottom: 5px;
                            text-transform: uppercase;
                            letter-spacing: 3px;
                        }
                        .company-tagline { 
                            font-size: 0.9rem; 
                            color: #666; 
                            font-style: italic;
                        }
                        .invoice-title { 
                            font-size: 1.8rem; 
                            font-weight: bold; 
                            text-align: center; 
                            margin: 20px 0;
                            text-transform: uppercase;
                        }
                        .invoice-details { 
                            display: flex; 
                            justify-content: space-between; 
                            margin-bottom: 30px;
                            border: 1px solid #000;
                            padding: 15px;
                        }
                        .invoice-from, .invoice-to { 
                            flex: 1; 
                            padding: 0 10px;
                        }
                        .invoice-from h4, .invoice-to h4 { 
                            margin: 0 0 10px 0; 
                            font-size: 1.1rem; 
                            text-transform: uppercase;
                            border-bottom: 1px solid #000;
                            padding-bottom: 5px;
                        }
                        .invoice-info { 
                            font-size: 0.9rem; 
                            line-height: 1.6;
                        }
                        .invoice-period { 
                            text-align: center; 
                            margin: 20px 0; 
                            font-size: 1.1rem; 
                            font-weight: bold;
                        }
                        .summary-section { 
                            margin: 25px 0; 
                            border: 2px solid #000;
                        }
                        .summary-title { 
                            background: #000; 
                            color: #fff; 
                            padding: 10px; 
                            margin: 0; 
                            font-size: 1.1rem; 
                            text-transform: uppercase;
                            text-align: center;
                        }
                        .kpi-summary { 
                            display: grid; 
                            grid-template-columns: repeat(4, 1fr); 
                            gap: 0;
                        }
                        .kpi-item { 
                            border-right: 1px solid #000; 
                            padding: 15px; 
                            text-align: center;
                        }
                        .kpi-item:last-child { 
                            border-right: none; 
                        }
                        .kpi-label { 
                            font-size: 0.8rem; 
                            font-weight: bold; 
                            margin-bottom: 5px; 
                            text-transform: uppercase;
                        }
                        .kpi-value { 
                            font-size: 1.2rem; 
                            font-weight: bold; 
                            color: #000;
                        }
                        table { 
                            width: 100%; 
                            border-collapse: collapse; 
                            margin: 20px 0;
                            border: 2px solid #000;
                        }
                        th, td { 
                            border: 1px solid #000; 
                            padding: 12px 8px; 
                            text-align: left; 
                            font-size: 0.9rem;
                        }
                        th { 
                            background: #f0f0f0; 
                            font-weight: bold; 
                            text-transform: uppercase;
                            text-align: center;
                        }
                        .footer { 
                            margin-top: 40px; 
                            text-align: center; 
                            font-size: 0.8rem; 
                            border-top: 1px solid #000;
                            padding-top: 15px;
                        }
                        .seller-logo { 
                            width: 50px; 
                            height: 50px; 
                            object-fit: cover; 
                            border-radius: 50%; 
                            border: 2px solid #000;
                            margin: 5px 0;
                        }
                    </style>
                </head>
                <body>
                    <div class="invoice-header">
                        <div class="company-info">
                            <div class="company-logo">RAEVOR</div>
                            <div class="company-tagline">Commission Report Invoice</div>
                        </div>
                        
                        <div class="invoice-title">Commission Report</div>
                        
                        <div class="invoice-details">
                            <div class="invoice-from">
                                <h4>From</h4>
                                <div class="invoice-info">
                                    <strong>RAEVOR</strong><br>
                                    Core Transaction 2<br>
                                    Commission Management System<br>
                                    <em>Per-item commission from successful deliveries (Core 3)</em>
                                </div>
                            </div>
                            <div class="invoice-to">
                                <h4>To</h4>
                                <div class="invoice-info">
                                    ${sellerInfo.logoUrl && sellerInfo.logoUrl !== '' ? `<img src="${sellerInfo.logoUrl}" alt="Shop Logo" class="seller-logo"><br>` : ''}
                                    <strong>${sellerInfo.shopName}</strong><br>
                                    Seller: ${sellerInfo.username}<br>
                                    Email: ${sellerInfo.email}<br>
                                    Role: ${sellerInfo.role}
                                </div>
                            </div>
                        </div>
                        
                        <div class="invoice-period">
                            <strong>Report Period: ${from} to ${to}</strong>
                        </div>
                    </div>
                    
                    <div class="summary-section">
                        <h3 class="summary-title">Commission Summary</h3>
                        <div class="kpi-summary">
                            <div class="kpi-item">
                                <div class="kpi-label">Total Gross</div>
                                <div class="kpi-value">${kpiData.gross}</div>
                            </div>
                            <div class="kpi-item">
                                <div class="kpi-label">Commission Rate</div>
                                <div class="kpi-value">${kpiData.rate}</div>
                            </div>
                            <div class="kpi-item">
                                <div class="kpi-label">Total Commission</div>
                                <div class="kpi-value">${kpiData.commission}</div>
                            </div>
                            <div class="kpi-item">
                                <div class="kpi-label">Total Orders</div>
                                <div class="kpi-value">${kpiData.orders}</div>
                            </div>
                        </div>
                    </div>
                    
                    ${tableHTML}
                    
                    <div class="footer">
                        <p><strong>Invoice Generated:</strong> ${new Date().toLocaleDateString()} at ${new Date().toLocaleTimeString()}</p>
                        <p><strong>System:</strong> Core Transaction 2 - Commission Reports</p>
                        <p><em>This is an official commission report generated by RAEVOR system.</em></p>
                    </div>
                </body>
                </html>
            `;
            
            // Create and download the file
            const blob = new Blob([htmlContent], { type: 'application/msword' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `Commission_Report_${from}_to_${to}.doc`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }

        document.getElementById('reportFilters').addEventListener('submit', (e) => { e.preventDefault(); loadCommission(); });
        loadCommission();
        document.querySelector('.sidebar-toggle').addEventListener('click', function(){ document.body.classList.toggle('sidebar-collapsed'); });
    </script>
</body>
</html>


