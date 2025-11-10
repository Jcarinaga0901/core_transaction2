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

// Get waybill ID from URL
$waybillId = $_GET['id'] ?? null;
$printMode = isset($_GET['print']) ? true : false;

if (!$waybillId) {
    header('Location: delivery.php');
    exit;
}

// Generate unique tracking number
$parcelTrackingNumber = 'TRK' . str_pad($waybillId, 8, '0', STR_PAD_LEFT);
function generateWaybillNumber($deliveryId) {
    $prefix = 'WB';
    $timestamp = date('Ymd');
    $id = str_pad($deliveryId, 6, '0', STR_PAD_LEFT);
    return $prefix . $timestamp . $id;
}


$trackingNumber = 'TRK' . str_pad($waybillId, 8, '0', STR_PAD_LEFT);
$waybillNumber = 'WB' . str_pad($waybillId, 8, '0', STR_PAD_LEFT);
$parcelTrackingNumber = 'TRK' . str_pad($waybillId, 8, '0', STR_PAD_LEFT);

try {
    $pdo = getDBConnection();
    
    // Get waybill details
    $stmt = $pdo->prepare("
        SELECT 
            dr.*,
            o.id as order_id,
            o.total_amount,
            o.status as order_status,
            o.created_at as order_date,
            u.username as customer_name,
            u.email as customer_email,
            v.name as seller_name,
            v.address as seller_address,
            v.phone as seller_phone,
            v.email as seller_email
        FROM delivery_requests dr
        LEFT JOIN orders o ON dr.order_id = o.id
        LEFT JOIN users u ON o.user_id = u.id
        LEFT JOIN vendors v ON dr.vendor_id = v.id
        WHERE dr.id = ?
    ");
    $stmt->execute([$waybillId]);
    $waybill = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$waybill) {
        header('Location: delivery.php');
        exit;
    }
    
    // Get order items
    $stmt = $pdo->prepare("
        SELECT 
            oi.*,
            p.name as product_name,
            p.description as product_description,
            p.image_url
        FROM order_items oi
        LEFT JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$waybill['order_id']]);
    $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    error_log("Waybill Error: " . $e->getMessage());
    header('Location: delivery.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Waybill #<?php echo $waybillId; ?> - Core Transaction 2</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        
        .waybill-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border: 2px solid #000;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        .waybill-header {
            background: white;
            padding: 15px;
            border-bottom: 2px solid #000;
        }
        
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .logo-section {
            display: flex;
            align-items: center;
        }
        
        .logo-text {
            font-family: Arial, sans-serif;
            font-size: 1.8rem;
            font-weight: bold;
            color: #e74c3c;
            margin-right: 10px;
        }
        
        .location-info {
            text-align: right;
            font-size: 0.9rem;
        }
        
        .tracking-section {
            text-align: center;
            margin: 20px 0;
        }
        
        .tracking-number {
            font-size: 2rem;
            font-weight: bold;
            color: #000;
            margin: 10px 0;
        }
        
        .barcode-section {
            text-align: center;
            margin: 20px 0;
        }
        
        .barcode {
            font-family: 'Courier New', monospace;
            font-size: 1.2rem;
            letter-spacing: 2px;
            margin: 10px 0;
        }
        
        .order-id {
            font-size: 0.9rem;
            color: #666;
        }
        
        .waybill-content {
            padding: 20px;
        }
        
        .item-details {
            background: #f8f9fa;
            padding: 15px;
            margin: 15px 0;
            border: 1px solid #ddd;
        }
        
        .item-details h6 {
            font-weight: bold;
            margin-bottom: 10px;
            color: #000;
        }
        
        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        
        .detail-label {
            font-weight: bold;
            color: #000;
        }
        
        .detail-value {
            color: #333;
        }
        
        .tracking-code {
            font-size: 1.5rem;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
            color: #000;
        }
        
        .address-section {
            margin: 20px 0;
        }
        
        .address-card {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
        }
        
        .address-title {
            font-weight: bold;
            font-size: 1.1rem;
            margin-bottom: 10px;
            color: #000;
        }
        
        .address-info {
            line-height: 1.4;
        }
        
        .postcode {
            font-size: 1.2rem;
            font-weight: bold;
            color: #000;
            margin: 10px 0;
        }
        
        .barcode-large {
            text-align: center;
            margin: 20px 0;
            font-family: 'Courier New', monospace;
            font-size: 1.1rem;
            letter-spacing: 1px;
        }
        
        .qr-section {
            text-align: center;
            margin: 20px 0;
        }
        
        .qr-code {
            width: 100px;
            height: 100px;
            border: 1px solid #ddd;
            display: inline-block;
            margin: 10px;
        }
        
        .footer-section {
            background: #f8f9fa;
            padding: 15px;
            margin-top: 20px;
            border: 1px solid #ddd;
        }
        
        .footer-text {
            font-size: 0.9rem;
            line-height: 1.4;
            color: #666;
        }
        
        .courier-copy {
            background: #f0f0f0;
            padding: 15px;
            margin-top: 20px;
            border: 1px solid #ccc;
        }
        
        .courier-copy h6 {
            font-weight: bold;
            margin-bottom: 10px;
            color: #000;
        }
        
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .products-table th,
        .products-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        
        .products-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #333;
        }
        
        .product-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
        }
        
        .qr-section {
            text-align: center;
            margin: 30px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .qr-code {
            width: 150px;
            height: 150px;
            margin: 0 auto 15px;
            border: 2px solid #007bff;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
        }
        
        .tracking-info {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: bold;
        }
        
        .status-pending { background: #fff3cd; color: #856404; }
        .status-in-transit { background: #d1ecf1; color: #0c5460; }
        .status-delivered { background: #d4edda; color: #155724; }
        .status-failed { background: #f8d7da; color: #721c24; }
        
        .waybill-footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #dee2e6;
        }
        
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
        
        @media print {
            body { background: white; }
            .print-button { display: none; }
            .waybill-container { box-shadow: none; }
        }
        
        .logo-section {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
        
        .logo-text {
            font-family: 'Great Vibes', cursive;
            font-size: 2.5rem;
            font-weight: 400;
            color: white;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body>
    <?php if (!$printMode): ?>
    <div class="mb-3">
        <button class="btn btn-outline-secondary btn-sm me-2" onclick="downloadWaybill()" title="Download PDF">
            <i class="bi bi-download"></i>
        </button>
        <button class="btn btn-primary" onclick="window.print()">
            <i class="bi bi-printer"></i> Print Waybill
        </button>
    </div>
    <?php endif; ?>
    
    <div class="waybill-container">
        <!-- Waybill Header -->
        <div class="waybill-header">
            <div class="header-top">
                <div class="logo-section">
                    <div class="logo-text">RAEVOR</div>
                    <div style="font-size: 0.9rem; color: #666;">EXPRESS</div>
                </div>
                <div class="location-info">
                    <div><strong>Manila</strong></div>
                    <div>Send Date: <?php echo date('Y-m-d'); ?></div>
                </div>
            </div>
            
            <div class="tracking-section">
                <div class="tracking-number"><?php echo $parcelTrackingNumber; ?></div>
                <div class="barcode-section">
                    <img src="generate_barcode.php?code=<?php echo $parcelTrackingNumber; ?>&width=300&height=80" alt="Barcode" style="max-width: 100%; height: auto;">
                    <div class="order-id">Waybill ID: <?php echo 'WB' . str_pad($waybillId, 8, '0', STR_PAD_LEFT); ?></div>
                </div>
            </div>
        </div>
        
        <!-- Waybill Content -->
        <div class="waybill-content">
            <!-- Item Details -->
            <div class="item-details">
                <h6>ITEM DETAILS:</h6>
                <div class="details-grid">
                    <div>
                        <div class="detail-row">
                            <span class="detail-label">Date:</span>
                            <span class="detail-value"><?php echo date('d/m/Y', strtotime($waybill['created_at'])); ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Weight:</span>
                            <span class="detail-value"><?php echo number_format(rand(1, 5) / 10, 1); ?> KG</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Actual:</span>
                            <span class="detail-value"><?php echo number_format(rand(1, 5) / 10, 1); ?> KG</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Volumetric:</span>
                            <span class="detail-value">KG</span>
                        </div>
                    </div>
                    <div>
                        <div class="detail-row">
                            <span class="detail-label">Product:</span>
                            <span class="detail-value"><?php echo $orderItems[0]['product_name'] ?? 'General Merchandise'; ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Type:</span>
                            <span class="detail-value">PARCEL</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Description:</span>
                            <span class="detail-value"><?php echo $orderItems[0]['product_description'] ?? 'Online Purchase'; ?></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="tracking-code"><?php echo $parcelTrackingNumber; ?></div>
            
            <!-- Address Information -->
            <div class="address-section">
                <div class="row">
                    <div class="col-md-6">
                        <div class="address-card">
                            <div class="address-title">FROM:</div>
                            <div class="address-info">
                                <strong><?php echo $waybill['seller_name'] ?? 'RAEVOR Store'; ?></strong><br>
                                <?php echo $waybill['seller_address'] ?? '123 Business District, Manila, Philippines'; ?><br>
                                TEL NO.: <?php echo $waybill['seller_phone'] ?? '+63 912 345 6789'; ?><br>
                                FAX NO.: 
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="address-card">
                            <div class="address-title">TO:</div>
                            <div class="address-info">
                                <strong><?php echo $waybill['customer_name']; ?></strong><br>
                                <?php echo $waybill['delivery_address']; ?><br>
                                TEL NO.: <?php echo $waybill['customer_phone']; ?><br>
                                TEL NO. 2: 
                            </div>
                            <div class="postcode">POSTCODE: <?php echo rand(1000, 9999); ?></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- QR Code Section -->
            <div class="qr-section">
                <div class="row">
                    <div class="col-md-6">
                        <div class="barcode-large">
                            <img src="generate_barcode.php?code=<?php echo $parcelTrackingNumber; ?>&width=250&height=60" alt="Barcode" style="max-width: 100%; height: auto;">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="qr-code" id="qrCode">
                            <div class="text-center">
                                <i class="bi bi-qr-code" style="font-size: 2rem; color: #007bff;"></i>
                                <div style="font-size: 0.8rem; margin-top: 5px;">Loading QR Code...</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Products Information -->
            <div class="section-title">
                <i class="bi bi-box me-2"></i>Products & Items
            </div>
            
            <table class="products-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Product</th>
                        <th>Description</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orderItems as $item): ?>
                    <tr>
                        <td>
                            <img src="<?php echo $item['image_url'] ?? 'uploads/products/default.jpg'; ?>" 
                                 alt="Product" class="product-image">
                        </td>
                        <td>
                            <strong><?php echo $item['product_name']; ?></strong>
                        </td>
                        <td><?php echo $item['product_description'] ?? 'No description available'; ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>₱<?php echo number_format($item['price'], 2); ?></td>
                        <td><strong>₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr style="background-color: #f8f9fa; font-weight: bold;">
                        <td colspan="5" style="text-align: right;">Total Amount:</td>
                        <td>₱<?php echo number_format($waybill['total_amount'], 2); ?></td>
                    </tr>
                    <tr style="background-color: #e3f2fd;">
                        <td colspan="5" style="text-align: right;">Delivery Fee:</td>
                        <td>₱<?php echo number_format($waybill['delivery_fee'], 2); ?></td>
                    </tr>
                    <tr style="background-color: #d4edda; font-weight: bold; font-size: 1.1rem;">
                        <td colspan="5" style="text-align: right;">Grand Total:</td>
                        <td>₱<?php echo number_format($waybill['total_amount'] + $waybill['delivery_fee'], 2); ?></td>
                    </tr>
                </tfoot>
            </table>
            
            <!-- QR Code Section -->
            <div class="qr-section">
                <h6><i class="bi bi-qr-code me-2"></i>Tracking QR Code</h6>
                <div class="qr-code" id="qrCode">
                    <div class="text-center">
                        <i class="bi bi-qr-code" style="font-size: 3rem; color: #007bff;"></i>
                        <div style="font-size: 0.8rem; margin-top: 5px;">Loading QR Code...</div>
                    </div>
                </div>
                <p class="mb-0">
                    <strong>Scan to Track:</strong> <?php echo $waybill['tracking_number'] ?? 'TRK' . str_pad($waybillId, 8, '0', STR_PAD_LEFT); ?>
                </p>
                <small class="text-muted">Use this QR code to track your package status</small>
            </div>
            
            <!-- Tracking Information -->
            <div class="tracking-info">
                <h6><i class="bi bi-geo-alt me-2"></i>Tracking Information</h6>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Current Status:</strong> <?php echo strtoupper(str_replace('_', ' ', $waybill['status'])); ?></p>
                        <p><strong>Last Updated:</strong> <?php echo date('M d, Y H:i', strtotime($waybill['updated_at'])); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Expected Delivery:</strong> <?php echo date('M d, Y', strtotime($waybill['delivery_date'])); ?></p>
                        <p><strong>Time Window:</strong> <?php echo $waybill['delivery_time']; ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer Section -->
        <div class="footer-section">
            <div class="footer-text">
                <p><strong>RAEVOR EXPRESS</strong> - Professional Logistics & Courier Services</p>
                <p>Customer Service: +63 912 345 6789 | Email: support@raevor.com</p>
                <p><strong>Terms & Conditions:</strong> By agreeing in printing this waybill, I hereby acknowledge that I have read and agree to the provisions set forth in this consignment note/airway bill and to the RAEVOR Express terms and conditions.</p>
            </div>
        </div>
        
        <!-- Courier Copy -->
        <div class="courier-copy">
            <h6>Courier Copy</h6>
            <div class="row">
                <div class="col-md-6">
                    <div class="address-info">
                        <strong>To: <?php echo $waybill['customer_name']; ?></strong><br>
                        <?php echo $waybill['delivery_address']; ?><br>
                        Tel No: <?php echo $waybill['customer_phone']; ?><br>
                        Tel No 2: 
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="barcode-large">
                        <img src="generate_barcode.php?code=<?php echo $parcelTrackingNumber; ?>&width=200&height=50" alt="Barcode" style="max-width: 100%; height: auto;">
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- QR Code Generation Script -->
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
    <script>
        // Generate QR Code
        document.addEventListener('DOMContentLoaded', function() {
            const trackingNumber = '<?php echo $waybill['tracking_number'] ?? 'TRK' . str_pad($waybillId, 8, '0', STR_PAD_LEFT); ?>';
            const trackingUrl = `${window.location.origin}/track.php?code=${trackingNumber}`;
            
            QRCode.toCanvas(document.getElementById('qrCode'), trackingUrl, {
                width: 150,
                height: 150,
                margin: 2,
                color: {
                    dark: '#000000',
                    light: '#FFFFFF'
                }
            }, function (error) {
                if (error) {
                    console.error('QR Code generation failed:', error);
                    document.getElementById('qrCode').innerHTML = `
                        <div class="text-center">
                            <i class="bi bi-qr-code" style="font-size: 3rem; color: #007bff;"></i>
                            <div style="font-size: 0.8rem; margin-top: 5px;">QR Code Error</div>
                        </div>
                    `;
                }
            });
        });
        
        // Print functionality
        function printWaybill() {
            window.print();
        }

        // Download waybill as PDF
        function downloadWaybill() {
            // Create a temporary link to download the waybill
            const link = document.createElement('a');
            link.href = `waybill.php?id=<?php echo $waybillId; ?>&download=1`;
            link.download = `waybill_<?php echo $waybillId; ?>.pdf`;
            link.target = '_blank';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
</body>
</html>

