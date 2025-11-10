<?php
/**
 * Customer Messaging System for Auto-Cancellation
 * Sends automated messages to customers about order cancellations
 */

require_once '../config/database.php';
require_once '../includes/notification_helper.php';

header('Content-Type: application/json');

try {
    $pdo = getDBConnection();
    
    // Get cancelled orders that need customer notification
    $stmt = $pdo->prepare("
        SELECT o.*, c.name as customer_name, c.email as customer_email, c.phone as customer_phone,
               v.name as seller_name, v.email as seller_email
        FROM orders o
        LEFT JOIN customers c ON o.customer_id = c.id
        LEFT JOIN vendors v ON o.vendor_id = v.id
        WHERE o.status = 'cancelled' 
        AND o.customer_notified = 0
        AND o.cancelled_at IS NOT NULL
    ");
    $stmt->execute();
    $cancelledOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $notificationsSent = 0;
    
    foreach ($cancelledOrders as $order) {
        try {
            // Create detailed cancellation message
            $isAutoCancelled = $order['auto_cancel_processed'] == 1;
            $cancellationType = $isAutoCancelled ? 'Auto-Cancelled' : 'Cancelled';
            
            $message = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
                    <div style='background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 20px; border-radius: 10px 10px 0 0; text-align: center;'>
                        <h2 style='margin: 0; font-size: 24px;'>Order {$cancellationType}</h2>
                        <p style='margin: 10px 0 0 0; opacity: 0.9;'>RAEVOR E-commerce Platform</p>
                    </div>
                    
                    <div style='background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px;'>
                        <p style='color: #333; font-size: 16px; margin-bottom: 20px;'>
                            Dear <strong>{$order['customer_name']}</strong>,
                        </p>
                        
                        <p style='color: #555; font-size: 14px; line-height: 1.6; margin-bottom: 20px;'>
                            " . ($isAutoCancelled ? 
                                "Your order has been automatically cancelled because it was not approved by the seller within 30 minutes of placement." :
                                "Your order has been cancelled as requested."
                            ) . "
                        </p>
                        
                        <div style='background: white; padding: 20px; border-radius: 8px; border-left: 4px solid #667eea; margin: 20px 0;'>
                            <h3 style='color: #333; margin-top: 0; font-size: 18px;'>Order Details</h3>
                            <table style='width: 100%; border-collapse: collapse;'>
                                <tr>
                                    <td style='padding: 8px 0; color: #666; font-weight: bold;'>Order ID:</td>
                                    <td style='padding: 8px 0; color: #333;'>#{$order['id']}</td>
                                </tr>
                                <tr>
                                    <td style='padding: 8px 0; color: #666; font-weight: bold;'>Total Amount:</td>
                                    <td style='padding: 8px 0; color: #333; font-weight: bold;'>₱" . number_format($order['total_amount'], 2) . "</td>
                                </tr>
                                <tr>
                                    <td style='padding: 8px 0; color: #666; font-weight: bold;'>Cancellation Reason:</td>
                                    <td style='padding: 8px 0; color: #333;'>{$order['cancel_reason']}</td>
                                </tr>
                                <tr>
                                    <td style='padding: 8px 0; color: #666; font-weight: bold;'>Cancelled On:</td>
                                    <td style='padding: 8px 0; color: #333;'>" . date('M d, Y \a\t g:i A', strtotime($order['cancelled_at'])) . "</td>
                                </tr>
                            </table>
                        </div>
                        
                        " . ($isAutoCancelled ? "
                        <div style='background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 8px; margin: 20px 0;'>
                            <h4 style='color: #856404; margin: 0 0 10px 0; font-size: 16px;'>
                                <i class='bi bi-info-circle'></i> Why was my order auto-cancelled?
                            </h4>
                            <p style='color: #856404; margin: 0; font-size: 14px; line-height: 1.5;'>
                                Orders are automatically cancelled if sellers don't approve them within 30 minutes. 
                                This ensures quick processing and prevents delays. You can place a new order anytime!
                            </p>
                        </div>
                        " : "") . "
                        
                        <div style='background: #d1ecf1; border: 1px solid #bee5eb; padding: 15px; border-radius: 8px; margin: 20px 0;'>
                            <h4 style='color: #0c5460; margin: 0 0 10px 0; font-size: 16px;'>
                                <i class='bi bi-credit-card'></i> Refund Information
                            </h4>
                            <p style='color: #0c5460; margin: 0; font-size: 14px; line-height: 1.5;'>
                                Your payment will be automatically refunded to your original payment method within 3-5 business days. 
                                You will receive a confirmation email once the refund is processed.
                            </p>
                        </div>
                        
                        <div style='text-align: center; margin: 30px 0;'>
                            <a href='#' style='background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 12px 30px; text-decoration: none; border-radius: 25px; font-weight: bold; display: inline-block;'>
                                Place New Order
                            </a>
                        </div>
                        
                        <p style='color: #666; font-size: 12px; text-align: center; margin-top: 30px;'>
                            Thank you for choosing RAEVOR!<br>
                            If you have any questions, please contact our support team.
                        </p>
                    </div>
                </div>
            ";
            
            // Create notification for customer
            createNotification(
                $order['customer_id'],
                'order_cancelled',
                'Order Cancelled',
                "Your order #{$order['id']} has been " . strtolower($cancellationType) . ".",
                'order.php?id=' . $order['id']
            );
            
            // Mark as notified
            $updateStmt = $pdo->prepare("UPDATE orders SET customer_notified = 1 WHERE id = ?");
            $updateStmt->execute([$order['id']]);
            
            $notificationsSent++;
            
        } catch (Exception $e) {
            error_log("Error sending customer notification for order {$order['id']}: " . $e->getMessage());
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => "Customer messaging completed",
        'notifications_sent' => $notificationsSent,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
} catch (Exception $e) {
    error_log("Customer messaging error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Customer messaging failed: ' . $e->getMessage()
    ]);
}
?>
