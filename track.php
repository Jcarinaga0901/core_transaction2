<?php 
session_start();
require_once 'config/database.php';

// Get tracking code from URL
$trackingCode = $_GET['code'] ?? '';

if (!$trackingCode) {
    header('Location: index.php');
    exit;
}

try {
    $pdo = getDBConnection();
    
    // Get tracking information
    $stmt = $pdo->prepare("
        SELECT 
            dr.*,
            o.id as order_id,
            o.total_amount,
            o.status as order_status,
            o.created_at as order_date,
            u.username as customer_name,
            u.email as customer_email,
            v.name as seller_name
        FROM delivery_requests dr
        LEFT JOIN orders o ON dr.order_id = o.id
        LEFT JOIN users u ON o.user_id = u.id
        LEFT JOIN vendors v ON dr.vendor_id = v.id
        WHERE dr.tracking_number = ? OR dr.id = ?
    ");
    $stmt->execute([$trackingCode, str_replace('TRK', '', $trackingCode)]);
    $tracking = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$tracking) {
        $error = "Tracking number not found";
    }
    
} catch (Exception $e) {
    error_log("Tracking Error: " . $e->getMessage());
    $error = "Error retrieving tracking information";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Package - <?php echo $trackingCode; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .tracking-container {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .tracking-header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .tracking-title {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .tracking-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        
        .tracking-content {
            padding: 30px;
        }
        
        .status-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 4px solid #007bff;
        }
        
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 25px;
            font-size: 1rem;
            font-weight: bold;
            margin-bottom: 15px;
        }
        
        .status-pending { background: #fff3cd; color: #856404; }
        .status-in-transit { background: #d1ecf1; color: #0c5460; }
        .status-delivered { background: #d4edda; color: #155724; }
        .status-failed { background: #f8d7da; color: #721c24; }
        
        .timeline {
            position: relative;
            padding-left: 30px;
        }
        
        .timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #dee2e6;
        }
        
        .timeline-item {
            position: relative;
            margin-bottom: 25px;
        }
        
        .timeline-item:last-child {
            margin-bottom: 0;
        }
        
        .timeline-marker {
            position: absolute;
            left: -30px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 3px solid #fff;
            box-shadow: 0 0 0 2px #dee2e6;
        }
        
        .timeline-item.active .timeline-marker {
            background: #007bff;
            box-shadow: 0 0 0 2px #007bff;
        }
        
        .timeline-item.completed .timeline-marker {
            background: #28a745;
            box-shadow: 0 0 0 2px #28a745;
        }
        
        .timeline-content {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .timeline-title {
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        
        .timeline-time {
            color: #666;
            font-size: 0.9rem;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 20px;
        }
        
        .info-card {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
        }
        
        .info-card h6 {
            color: #007bff;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .logo-text {
            font-family: 'Great Vibes', cursive;
            font-size: 2rem;
            font-weight: 400;
            color: white;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body>
    <div class="tracking-container">
        <div class="tracking-header">
            <div class="logo-text">RAEVOR</div>
            <div class="tracking-title">Package Tracking</div>
            <div class="tracking-subtitle">Track your package in real-time</div>
        </div>
        
        <div class="tracking-content">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <?php echo $error; ?>
                </div>
            <?php else: ?>
                <!-- Tracking Status -->
                <div class="status-card">
                    <div class="status-badge status-<?php echo $tracking['status']; ?>">
                        <?php echo strtoupper(str_replace('_', ' ', $tracking['status'])); ?>
                    </div>
                    <h5>Package Status</h5>
                    <p class="mb-0">Your package is currently <strong><?php echo str_replace('_', ' ', $tracking['status']); ?></strong></p>
                </div>
                
                <!-- Tracking Timeline -->
                <h6 class="mb-3"><i class="bi bi-clock-history me-2"></i>Tracking Timeline</h6>
                <div class="timeline">
                    <?php
                    $timeline = [
                        [
                            'status' => 'delivered',
                            'title' => 'Package Delivered',
                            'time' => $tracking['status'] === 'delivered' ? date('M d, Y H:i', strtotime($tracking['updated_at'])) : 'Expected: ' . date('M d, Y', strtotime($tracking['delivery_date'])),
                            'active' => $tracking['status'] === 'delivered',
                            'completed' => $tracking['status'] === 'delivered'
                        ],
                        [
                            'status' => 'in_transit',
                            'title' => 'Out for Delivery',
                            'time' => $tracking['status'] === 'in_transit' ? date('M d, Y H:i', strtotime($tracking['updated_at'])) : 'Expected: ' . date('M d, Y', strtotime($tracking['delivery_date'])),
                            'active' => $tracking['status'] === 'in_transit',
                            'completed' => in_array($tracking['status'], ['delivered'])
                        ],
                        [
                            'status' => 'pending',
                            'title' => 'Package Picked Up',
                            'time' => $tracking['status'] === 'pending' ? date('M d, Y H:i', strtotime($tracking['created_at'])) : 'Expected: ' . date('M d, Y', strtotime($tracking['created_at'])),
                            'active' => $tracking['status'] === 'pending',
                            'completed' => in_array($tracking['status'], ['in_transit', 'delivered'])
                        ],
                        [
                            'status' => 'processing',
                            'title' => 'Order Processed',
                            'time' => date('M d, Y H:i', strtotime($tracking['order_date'])),
                            'active' => false,
                            'completed' => true
                        ]
                    ];
                    
                    foreach ($timeline as $item):
                    ?>
                    <div class="timeline-item <?php echo $item['active'] ? 'active' : ($item['completed'] ? 'completed' : ''); ?>">
                        <div class="timeline-marker"></div>
                        <div class="timeline-content">
                            <div class="timeline-title"><?php echo $item['title']; ?></div>
                            <div class="timeline-time"><?php echo $item['time']; ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Package Information -->
                <div class="info-grid">
                    <div class="info-card">
                        <h6><i class="bi bi-box me-2"></i>Package Details</h6>
                        <p><strong>Tracking Number:</strong><br><?php echo $tracking['tracking_number'] ?? 'TRK' . str_pad($tracking['id'], 8, '0', STR_PAD_LEFT); ?></p>
                        <p><strong>Order Number:</strong><br>#<?php echo $tracking['order_id']; ?></p>
                        <p><strong>Courier:</strong><br><?php echo $tracking['courier_name'] ?? 'J&T Express'; ?></p>
                    </div>
                    
                    <div class="info-card">
                        <h6><i class="bi bi-geo-alt me-2"></i>Delivery Information</h6>
                        <p><strong>Delivery Address:</strong><br><?php echo $tracking['delivery_address']; ?></p>
                        <p><strong>Expected Delivery:</strong><br><?php echo date('M d, Y', strtotime($tracking['delivery_date'])); ?></p>
                        <p><strong>Time Window:</strong><br><?php echo $tracking['delivery_time']; ?></p>
                    </div>
                </div>
                
                <!-- Customer Information -->
                <div class="info-card mt-3">
                    <h6><i class="bi bi-person me-2"></i>Customer Information</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Name:</strong> <?php echo $tracking['customer_name']; ?></p>
                            <p><strong>Phone:</strong> <?php echo $tracking['customer_phone']; ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Email:</strong> <?php echo $tracking['customer_email']; ?></p>
                            <p><strong>Order Date:</strong> <?php echo date('M d, Y', strtotime($tracking['order_date'])); ?></p>
                        </div>
                    </div>
                </div>
                
                <!-- Contact Information -->
                <div class="alert alert-info mt-4">
                    <h6><i class="bi bi-telephone me-2"></i>Need Help?</h6>
                    <p class="mb-2">If you have any questions about your package, please contact our customer service:</p>
                    <p class="mb-0">
                        <strong>Phone:</strong> +63 912 345 6789<br>
                        <strong>Email:</strong> support@raevor.com
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        // Auto-refresh every 30 seconds
        setTimeout(function() {
            location.reload();
        }, 30000);
    </script>
</body>
</html>
