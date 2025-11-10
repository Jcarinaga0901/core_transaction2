<?php
// Auto-cancellation system for orders
require_once '../config/database.php';
require_once '../includes/notification_helper.php';

header('Content-Type: application/json');

try {
    $pdo = getDBConnection();
    
    // Get orders that are pending for more than 30 minutes
    $stmt = $pdo->prepare("
        SELECT o.*, u.username as customer_name, u.email as customer_email
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        WHERE o.status = 'Just Placed' 
        AND o.created_at < DATE_SUB(NOW(), INTERVAL 30 MINUTE)
        AND o.auto_cancel_processed = 0
    ");
    $stmt->execute();
    $pendingOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $cancelledCount = 0;
    $notificationsSent = 0;
    
    foreach ($pendingOrders as $order) {
        // Update order status to cancelled
        $updateStmt = $pdo->prepare("
            UPDATE orders 
            SET status = 'Cancelled', 
                cancelled_at = NOW(), 
                cancel_reason = 'Auto-cancelled: Order not approved within 30 minutes',
                auto_cancel_processed = 1
            WHERE id = ?
        ");
        $updateStmt->execute([$order['id']]);
        
        // Restore product stock
        $itemsStmt = $pdo->prepare("
            SELECT oi.product_id, oi.quantity, p.stock_quantity
            FROM order_items oi
            LEFT JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = ?
        ");
        $itemsStmt->execute([$order['id']]);
        $orderItems = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($orderItems as $item) {
            if ($item['stock_quantity'] !== null) {
                $newStock = $item['stock_quantity'] + $item['quantity'];
                $stockStmt = $pdo->prepare("UPDATE products SET stock_quantity = ? WHERE id = ?");
                $stockStmt->execute([$newStock, $item['product_id']]);
            }
        }
        
        // Send notification to customer
        try {
            // Create notification for customer
            createNotification(
                $order['user_id'],
                'order_auto_cancelled',
                'Order Auto-Cancelled',
                "Your order #{$order['id']} has been automatically cancelled due to seller inactivity.",
                'order.php?id=' . $order['id']
            );
            
            $notificationsSent++;
            
        } catch (Exception $e) {
            error_log("Error sending customer notification: " . $e->getMessage());
        }
        
        // Send notification to seller
        try {
            $sellerMessage = "
                <h3>Order Auto-Cancelled</h3>
                <p>Dear {$order['seller_name']},</p>
                <p>Order #{$order['id']} has been automatically cancelled because it was not approved within 30 minutes.</p>
                <p><strong>Order Details:</strong></p>
                <ul>
                    <li>Order ID: #{$order['id']}</li>
                    <li>Customer: {$order['customer_name']}</li>
                    <li>Total Amount: ₱" . number_format($order['total_amount'], 2) . "</li>
                    <li>Cancellation Reason: Auto-cancelled due to seller inactivity</li>
                </ul>
                <p>Please ensure to check and approve orders promptly to avoid auto-cancellations.</p>
                <p><strong>RAEVOR Team</strong></p>
            ";
            
            // Create notification for seller (if vendor_id exists)
            if (isset($order['vendor_id']) && $order['vendor_id']) {
                createNotification(
                    $order['vendor_id'],
                    'order_auto_cancelled_seller',
                    'Order Auto-Cancelled',
                    "Order #{$order['id']} was auto-cancelled due to inactivity. Please check orders promptly.",
                    'order.php?id=' . $order['id']
                );
            }
            
        } catch (Exception $e) {
            error_log("Error sending seller notification: " . $e->getMessage());
        }
        
        $cancelledCount++;
    }
    
    // Log the auto-cancellation process
    error_log("Auto-cancellation process completed: {$cancelledCount} orders cancelled, {$notificationsSent} notifications sent");
    
    echo json_encode([
        'success' => true,
        'message' => "Auto-cancellation completed",
        'cancelled_orders' => $cancelledCount,
        'notifications_sent' => $notificationsSent,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
} catch (Exception $e) {
    error_log("Auto-cancellation error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Auto-cancellation failed: ' . $e->getMessage()
    ]);
}
?>
