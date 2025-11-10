<?php
header('Content-Type: application/json');
require_once '../config/database.php';
require_once '../includes/auth.php';

// Check if user is authenticated
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$action = $_GET['action'] ?? '';

try {
    $pdo = getDBConnection();
    $user_id = $_SESSION['user_id'];
    $vendor_id = $_SESSION['vendor_id'] ?? null;

    switch ($action) {
        case 'sales_summary':
            // Get comprehensive sales summary with payment and delivery data
            $where_clause = "WHERE 1=1";
            if ($vendor_id) {
                $where_clause .= " AND vendor_id = ?";
            }
            
            $stmt = $pdo->prepare("
                SELECT 
                    COUNT(*) as total_orders,
                    SUM(total_amount) as total_sales,
                    AVG(total_amount) as average_order_value,
                    SUM(CASE WHEN status = 'Delivered' THEN total_amount ELSE 0 END) as completed_sales,
                    SUM(CASE WHEN status = 'Processing' THEN total_amount ELSE 0 END) as processing_sales,
                    SUM(CASE WHEN status = 'Shipped' THEN total_amount ELSE 0 END) as shipped_sales,
                    COUNT(CASE WHEN status = 'Delivered' THEN 1 END) as delivered_orders,
                    COUNT(CASE WHEN status = 'Processing' THEN 1 END) as processing_orders,
                    COUNT(CASE WHEN status = 'Shipped' THEN 1 END) as shipped_orders,
                    COUNT(CASE WHEN status = 'Cancelled' THEN 1 END) as cancelled_orders
                FROM orders 
                $where_clause
            ");
            
            $params = [];
            if ($vendor_id) {
                $params[] = $vendor_id;
            }
            $stmt->execute($params);
            $summary = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Get payment summary
            $payment_where = "WHERE 1=1";
            if ($vendor_id) {
                $payment_where .= " AND vendor_id = ?";
            }
            
            $stmt = $pdo->prepare("
                SELECT 
                    COUNT(*) as total_payments,
                    SUM(amount) as total_payment_amount,
                    COUNT(CASE WHEN payment_status = 'verified' THEN 1 END) as verified_payments,
                    COUNT(CASE WHEN payment_status = 'pending' THEN 1 END) as pending_payments,
                    COUNT(CASE WHEN payment_status = 'rejected' THEN 1 END) as rejected_payments,
                    SUM(CASE WHEN payment_status = 'verified' THEN amount ELSE 0 END) as verified_amount
                FROM payments 
                $payment_where
            ");
            
            $stmt->execute($params);
            $payment_summary = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Get delivery summary
            $delivery_where = "WHERE 1=1";
            if ($vendor_id) {
                $delivery_where .= " AND vendor_id = ?";
            }
            
            $stmt = $pdo->prepare("
                SELECT 
                    COUNT(*) as total_deliveries,
                    COUNT(CASE WHEN status = 'delivered' THEN 1 END) as successful_deliveries,
                    COUNT(CASE WHEN status = 'in_transit' THEN 1 END) as in_transit_deliveries,
                    COUNT(CASE WHEN status = 'pending_pickup' THEN 1 END) as pending_pickup_deliveries,
                    AVG(CASE WHEN status = 'delivered' AND delivered_at IS NOT NULL 
                        THEN TIMESTAMPDIFF(HOUR, created_at, delivered_at) END) as avg_delivery_hours
                FROM delivery_requests 
                $delivery_where
            ");
            
        $stmt->execute($params);
            $delivery_summary = $stmt->fetch(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true, 
                'summary' => $summary,
                'payment_summary' => $payment_summary,
                'delivery_summary' => $delivery_summary
            ]);
            break;
            
        case 'order_status_breakdown':
            $stmt = $pdo->prepare("
                SELECT status, COUNT(*) as count 
                FROM orders 
                GROUP BY status
            ");
            $stmt->execute();
            
            $breakdown = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'breakdown' => $breakdown]);
            break;
            
        case 'delivery_kpis':
            $stmt = $pdo->prepare("
                SELECT 
                    COUNT(*) as total_deliveries,
                    SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as successful_deliveries,
                    SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed_deliveries,
                    AVG(CASE WHEN status = 'delivered' THEN DATEDIFF(delivered_at, created_at) END) as avg_delivery_time
                FROM delivery_requests
            ");
            $stmt->execute();
            
            $kpis = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'kpis' => $kpis]);
            break;
            
        case 'inventory_low_stock':
            $threshold = $_GET['threshold'] ?? 10;
            
            $stmt = $pdo->prepare("
                SELECT p.*, v.name as vendor_name 
                FROM products p 
                LEFT JOIN vendors v ON p.vendor_id = v.id 
                WHERE p.stock_quantity <= ?
                ORDER BY p.stock_quantity ASC
            ");
            $stmt->execute([$threshold]);
            
            $low_stock = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'low_stock' => $low_stock]);
            break;
            
        case 'commission_summary':
            $stmt = $pdo->prepare("
                SELECT 
                    COUNT(*) as total_commissions,
                    SUM(commission_amount) as total_commission_amount,
                    AVG(commission_rate) as average_commission_rate,
                    SUM(CASE WHEN settled = 1 THEN commission_amount ELSE 0 END) as settled_commissions,
                    SUM(CASE WHEN settled = 0 THEN commission_amount ELSE 0 END) as pending_commissions
                FROM commissions
            ");
            $stmt->execute();
            
            $summary = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'summary' => $summary]);
            break;
            
        case 'commission_breakdown':
            $stmt = $pdo->prepare("
                SELECT 
                    v.name as vendor_name,
                    COUNT(*) as commission_count,
                    SUM(commission_amount) as total_commission,
                    AVG(commission_rate) as avg_rate
                FROM commissions c
                LEFT JOIN vendors v ON c.vendor_id = v.id
                GROUP BY c.vendor_id, v.name
                ORDER BY total_commission DESC
            ");
            $stmt->execute();
            
            $breakdown = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'breakdown' => $breakdown]);
            break;
            
        case 'commission_report':
            $from = $_GET['from'] ?? date('Y-m-01');
            $to = $_GET['to'] ?? date('Y-m-d');
            
            // Check if commissions table exists
            $checkTable = $pdo->query("SHOW TABLES LIKE 'commissions'");
            if ($checkTable->rowCount() == 0) {
                echo json_encode([
                    'ok' => false, 
                    'note' => 'Commission system not yet initialized. Commissions will appear here once orders are delivered.',
                    'source' => 'system'
                ]);
                break;
            }
            
            $stmt = $pdo->prepare("
                SELECT 
                    DATE(c.created_at) as period,
                    c.commission_amount,
                    c.commission_rate,
                    o.id as order_no,
                    oi.quantity,
                    oi.price * oi.quantity as gross_amount,
                    o.status as order_status,
                    o.created_at as order_date
                FROM commissions c
                LEFT JOIN order_items oi ON c.order_item_id = oi.id
                LEFT JOIN orders o ON oi.order_id = o.id
                WHERE c.created_at BETWEEN ? AND ?
                ORDER BY c.created_at DESC
            ");
            $stmt->execute([$from . ' 00:00:00', $to . ' 23:59:59']);
            
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'ok' => true,
                'rows' => $rows,
                'note' => count($rows) . ' commission records found for the selected period'
            ]);
            break;
            
        case 'product_analytics':
            $stmt = $pdo->prepare("
                SELECT 
                    p.name as product_name,
                    pa.views,
                    pa.clicks,
                    pa.conversions,
                    pa.revenue,
                    ROUND((pa.clicks / pa.views) * 100, 2) as click_rate,
                    ROUND((pa.conversions / pa.clicks) * 100, 2) as conversion_rate
                FROM product_analytics pa
                LEFT JOIN products p ON pa.product_id = p.id
                ORDER BY pa.revenue DESC
            ");
            $stmt->execute();
            
            $analytics = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'analytics' => $analytics]);
            break;
            
        case 'connected_order_data':
            // Get orders with their payment and delivery status
            $stmt = $pdo->prepare("
                SELECT 
                    o.id,
                    o.total_amount,
                    o.status as order_status,
                    o.created_at as order_date,
                    p.payment_status,
                    p.payment_method,
                    p.amount as payment_amount,
                    p.verified_at,
                    d.status as delivery_status,
                    d.delivery_fee,
                    d.delivered_at,
                    c.customer_name,
                    c.email as customer_email
                FROM orders o
                LEFT JOIN payments p ON o.id = p.order_id
                LEFT JOIN delivery_requests d ON o.id = d.order_id
                LEFT JOIN customers c ON o.user_id = c.id
                ORDER BY o.created_at DESC
                LIMIT 50
            ");
            
            $stmt->execute();
            $connected_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode(['success' => true, 'connected_data' => $connected_data]);
            break;
            
        case 'payment_delivery_timeline':
            // Get timeline of orders from payment to delivery
            $stmt = $pdo->prepare("
                SELECT 
                    o.id as order_id,
                    o.total_amount,
                    o.status as order_status,
                    o.created_at as order_placed,
                    p.payment_status,
                    p.verified_at as payment_verified,
                    d.status as delivery_status,
                    d.created_at as delivery_created,
                    d.delivered_at as delivery_completed,
                    TIMESTAMPDIFF(HOUR, o.created_at, p.verified_at) as payment_processing_hours,
                    TIMESTAMPDIFF(HOUR, p.verified_at, d.delivered_at) as delivery_hours,
                    TIMESTAMPDIFF(HOUR, o.created_at, d.delivered_at) as total_fulfillment_hours
                FROM orders o
                LEFT JOIN payments p ON o.id = p.order_id
                LEFT JOIN delivery_requests d ON o.id = d.order_id
                WHERE o.status IN ('Delivered', 'Shipped', 'Processing')
                AND p.payment_status = 'verified'
                ORDER BY o.created_at DESC
                LIMIT 30
            ");
            $stmt->execute();
            $timeline = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode(['success' => true, 'timeline' => $timeline]);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    
} catch (Exception $e) {
    error_log("Reports API Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>
