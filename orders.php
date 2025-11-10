<?php
// Suppress all output to prevent JSON corruption
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 0);

// Start output buffering to capture any unwanted output
ob_start();

header('Content-Type: application/json');
require_once '../config/database.php';
require_once '../includes/auth.php';

// Check if user is authenticated
if (!isset($_SESSION['user_id'])) {
    // Clean any unwanted output before sending JSON
    ob_clean();
    ob_clean();
echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    $pdo = getDBConnection();
    $user_id = $_SESSION['user_id'];
    $vendor_id = $_SESSION['vendor_id'] ?? null;

    switch ($action) {
        case 'dashboard_stats':
            // Get dashboard statistics
            $stats = [];
            
            // Total products
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM products");
            $stmt->execute();
            $stats['total_products'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
            
            // Total orders
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM orders");
            $stmt->execute();
            $stats['total_orders'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
            
            // Total sales
            $stmt = $pdo->prepare("SELECT SUM(total_amount) as total FROM orders WHERE status != 'Cancelled'");
            $stmt->execute();
            $stats['total_sales'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            
            // Pending orders
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM orders WHERE status = 'Just Placed'");
            $stmt->execute();
            $stats['pending_orders'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
            
            // Payment statistics
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM payments WHERE status = 'Captured'");
            $stmt->execute();
            $stats['verified_payments'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
            
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM payments WHERE status = 'Pending'");
            $stmt->execute();
            $stats['pending_payments'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
            
            ob_clean();
            ob_clean();
echo json_encode(['success' => true, 'stats' => $stats]);
            break;
            
        case 'recent_orders':
            $limit = $_GET['limit'] ?? 5;
            
            $stmt = $pdo->prepare("
                SELECT o.*, c.customer_name 
                FROM orders o 
                LEFT JOIN customers c ON o.user_id = c.id 
                ORDER BY o.created_at DESC 
                LIMIT ?
            ");
            $stmt->execute([$limit]);
            
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ob_clean();
echo json_encode(['success' => true, 'orders' => $orders]);
            break;
            
        case 'list':
		$status = $_GET['status'] ?? '';
            $limit = $_GET['limit'] ?? 50;
            
            $whereClause = '';
		$params = [];
		
            if ($status) {
                $whereClause = 'WHERE o.status = ?';
                $params[] = $status;
            }
            
            $stmt = $pdo->prepare("
                SELECT o.*, c.customer_name,
                       p.status as payment_status, p.method as payment_method, p.amount as payment_amount
                FROM orders o 
                LEFT JOIN customers c ON o.user_id = c.id 
                LEFT JOIN payments p ON o.id = p.order_id
                $whereClause
                ORDER BY o.created_at DESC 
                LIMIT ?
            ");
            $params[] = $limit;
		$stmt->execute($params);
            
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ob_clean();
            ob_clean();
echo json_encode(['success' => true, 'orders' => $orders]);
            break;
            
        case 'view':
            $orderId = $_GET['id'] ?? '';
            
            if (!$orderId) {
                ob_clean();
echo json_encode(['ok' => false, 'error' => 'Order ID is required']);
                break;
            }
            
            // Get order details
            $stmt = $pdo->prepare("
                SELECT o.*, c.customer_name, c.email as customer_email, c.phone as customer_phone
                FROM orders o 
                LEFT JOIN customers c ON o.user_id = c.id 
                WHERE o.id = ?
            ");
            $stmt->execute([$orderId]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$order) {
                ob_clean();
echo json_encode(['ok' => false, 'error' => 'Order not found']);
                break;
            }
            
            // Get order items
            $stmt = $pdo->prepare("
                SELECT oi.*, p.name as product_name, p.price as unit_price
                FROM order_items oi 
                LEFT JOIN products p ON oi.product_id = p.id 
                WHERE oi.order_id = ?
            ");
            $stmt->execute([$orderId]);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Calculate total
            $total = array_sum(array_column($items, 'line_total'));
            
            ob_clean();
            ob_clean();
echo json_encode([
                'ok' => true, 
                'order' => $order, 
                'items' => $items, 
                'total' => $total
            ]);
            break;
            
        case 'cancelled_items':
            $from = $_GET['from'] ?? date('Y-m-d', strtotime('-30 days'));
            $to = $_GET['to'] ?? date('Y-m-d');
            $q = $_GET['q'] ?? '';
            
            $where = "o.status = 'Cancelled'";
            $params = [];
            
            if ($from && $to) {
                $where .= " AND DATE(o.created_at) BETWEEN ? AND ?";
                $params[] = $from;
                $params[] = $to;
            }
            
            if ($q) {
                $where .= " AND (o.id LIKE ? OR u.username LIKE ?)";
                $searchTerm = "%{$q}%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            $stmt = $pdo->prepare("
                SELECT o.*, c.customer_name, 
                       oi.quantity, oi.price as unit_price, oi.price * oi.quantity as order_total,
                       o.cancel_reason, o.cancel_approved, o.cancel_rejected, o.cancel_actor,
                       o.auto_cancel_processed, o.cancelled_at
                FROM orders o
                LEFT JOIN customers c ON o.user_id = c.id
                LEFT JOIN order_items oi ON o.id = oi.order_id
                WHERE {$where}
                ORDER BY o.created_at DESC
            ");
            $stmt->execute($params);
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            ob_clean();
            ob_clean();
echo json_encode(['ok' => true, 'rows' => $orders]);
            break;
            
        case 'approve_cancellation':
            $orderId = $_POST['id'] ?? '';
            
            if (!$orderId) {
                ob_clean();
echo json_encode(['ok' => false, 'error' => 'Order ID is required']);
                break;
            }
            
            $stmt = $pdo->prepare("
                UPDATE orders 
                SET cancel_approved = 1, 
                    cancel_approved_at = NOW(),
                    cancel_actor = ?
                WHERE id = ?
            ");
            $stmt->execute([$user_id, $orderId]);
            
            ob_clean();
            ob_clean();
echo json_encode(['ok' => true, 'message' => 'Cancellation approved successfully']);
            break;
            
        case 'reject_cancellation':
            $orderId = $_POST['id'] ?? '';
            
            if (!$orderId) {
                ob_clean();
echo json_encode(['ok' => false, 'error' => 'Order ID is required']);
                break;
            }
            
            $stmt = $pdo->prepare("
                UPDATE orders 
                SET cancel_rejected = 1, 
                    cancel_rejected_at = NOW(),
                    cancel_actor = ?
                WHERE id = ?
            ");
            $stmt->execute([$user_id, $orderId]);
            
            ob_clean();
            echo json_encode(['ok' => true, 'message' => 'Cancellation rejected successfully']);
            break;
            
        case 'approve_order':
            $orderId = $_POST['id'] ?? '';
            
            if (!$orderId) {
                ob_clean();
                echo json_encode(['success' => false, 'message' => 'Order ID is required']);
                break;
            }
            
            // Update order status from "Just Placed" to "Processing"
            $stmt = $pdo->prepare("
                UPDATE orders 
                SET status = 'Processing'
                WHERE id = ? AND status = 'Just Placed'
            ");
            $stmt->execute([$orderId]);
            
            if ($stmt->rowCount() > 0) {
                ob_clean();
                echo json_encode(['success' => true, 'message' => 'Order approved and moved to processing']);
		} else {
                ob_clean();
                echo json_encode(['success' => false, 'message' => 'Order not found or already processed']);
            }
            break;
            
        case 'reject_order':
            $orderId = $_POST['id'] ?? '';
            $reason = $_POST['reason'] ?? 'Order rejected by seller';
            
            if (!$orderId) {
                ob_clean();
echo json_encode(['success' => false, 'message' => 'Order ID is required']);
                break;
            }
            
            // Update order status to "Cancelled"
            $stmt = $pdo->prepare("
                UPDATE orders 
                SET status = 'Cancelled', 
                    cancel_reason = ?,
                    cancel_rejected = 1,
                    cancel_rejected_at = NOW(),
                    cancel_actor = ?
                WHERE id = ? AND status = 'Just Placed'
            ");
            $stmt->execute([$reason, $user_id, $orderId]);
            
            if ($stmt->rowCount() > 0) {
                ob_clean();
echo json_encode(['success' => true, 'message' => 'Order rejected successfully']);
            } else {
                ob_clean();
echo json_encode(['success' => false, 'message' => 'Order not found or already processed']);
            }
            break;
            
        case 'send_to_delivery':
            $orderId = $_POST['id'] ?? '';
            $courier = $_POST['courier'] ?? 'J&T Express';
            $notes = $_POST['notes'] ?? '';
            $expectedDelivery = $_POST['expected_delivery'] ?? date('Y-m-d', strtotime('+3 days'));
            
            if (!$orderId) {
                ob_clean();
echo json_encode(['success' => false, 'message' => 'Order ID is required']);
                break;
            }
            
            // Update order status to "Shipped" and create delivery request
            $pdo->beginTransaction();
            
            try {
                // Update order status
                $stmt = $pdo->prepare("
                    UPDATE orders 
                    SET status = 'Shipped'
                    WHERE id = ? AND status = 'Processing'
                ");
                $stmt->execute([$orderId]);
                
                if ($stmt->rowCount() > 0) {
                    // Create delivery request
                    $stmt = $pdo->prepare("
                        INSERT INTO delivery_requests (
                            order_id, customer_name, customer_phone, delivery_address, 
                            delivery_date, delivery_time, status, delivery_fee, created_at
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([
                        $orderId,
                        'Customer Name', // This should be fetched from user data
                        '09171234567',
                        'Default Address',
                        $expectedDelivery,
                        '09:00-17:00',
                        'pending',
                        50.00,
                        date('Y-m-d H:i:s')
                    ]);

			$pdo->commit();
                    ob_clean();
echo json_encode(['success' => true, 'message' => 'Order sent to delivery management']);
                } else {
			$pdo->rollBack();
                    ob_clean();
echo json_encode(['success' => false, 'message' => 'Order not found or not in processing status']);
                }
            } catch (Exception $e) {
                $pdo->rollBack();
                throw $e;
            }
            break;
            
        case 'verify_payment':
            $orderId = $_POST['id'] ?? '';
            $transferReference = $_POST['transfer_reference'] ?? '';
            $amountReceived = $_POST['amount_received'] ?? 0;
            $bankAccount = $_POST['bank_account'] ?? '';
            $notes = $_POST['notes'] ?? '';
            
            if (!$orderId || !$transferReference || !$amountReceived || !$bankAccount) {
                ob_clean();
echo json_encode(['success' => false, 'message' => 'All fields are required']);
                break;
            }
            
            try {
                // Check if payment record exists
                $stmt = $pdo->prepare("SELECT id FROM payments WHERE order_id = ?");
                $stmt->execute([$orderId]);
                $paymentExists = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($paymentExists) {
                    // Update existing payment record
                    $stmt = $pdo->prepare("
                        UPDATE payments 
                        SET status = 'Captured', 
                            verified_at = NOW(),
                            verified_by = ?,
                            transfer_reference = ?,
                            amount_received = ?,
                            bank_account = ?,
                            verification_notes = ?
                        WHERE order_id = ?
                    ");
                    $stmt->execute([$user_id, $transferReference, $amountReceived, $bankAccount, $notes, $orderId]);
                } else {
                    // Create new payment record
                    $stmt = $pdo->prepare("
                        INSERT INTO payments (
                            order_id, status, method, amount, 
                            verified_at, verified_by, transfer_reference, 
                            amount_received, bank_account, verification_notes, created_at
                        ) VALUES (?, ?, ?, ?, NOW(), ?, ?, ?, ?, ?, NOW())
                    ");
                    $stmt->execute([
                        $orderId, 'Captured', 'Bank Transfer', $amountReceived,
                        $user_id, $transferReference, $amountReceived, $bankAccount, $notes
                    ]);
                }
                
                // Update order payment status
                $stmt = $pdo->prepare("
                    UPDATE orders 
                    SET payment_status = 'Verified'
                    WHERE id = ?
                ");
                $stmt->execute([$orderId]);
                
                ob_clean();
echo json_encode(['success' => true, 'message' => 'Payment verified successfully']);
            } catch (Exception $e) {
                ob_clean();
echo json_encode(['success' => false, 'message' => 'Failed to verify payment: ' . $e->getMessage()]);
            }
            break;
            
        case 'reject_payment':
            $orderId = $_POST['id'] ?? '';
            $reason = $_POST['reason'] ?? 'Payment verification failed';
            
            if (!$orderId) {
                ob_clean();
echo json_encode(['success' => false, 'message' => 'Order ID is required']);
                break;
            }
            
            try {
                // Check if payment record exists
                $stmt = $pdo->prepare("SELECT id FROM payments WHERE order_id = ?");
                $stmt->execute([$orderId]);
                $paymentExists = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($paymentExists) {
                    // Update existing payment record
                    $stmt = $pdo->prepare("
                        UPDATE payments 
                        SET status = 'Failed', 
                            rejected_at = NOW(),
                            rejected_by = ?,
                            rejection_reason = ?
                        WHERE order_id = ?
                    ");
                    $stmt->execute([$user_id, $reason, $orderId]);
                } else {
                    // Create new payment record
                    $stmt = $pdo->prepare("
                        INSERT INTO payments (
                            order_id, status, method, amount, 
                            rejected_at, rejected_by, rejection_reason, created_at
                        ) VALUES (?, ?, ?, ?, NOW(), ?, ?, NOW())
                    ");
                    $stmt->execute([
                        $orderId, 'Failed', 'Bank Transfer', 0,
                        $user_id, $reason
                    ]);
                }
                
                // Update order payment status
                $stmt = $pdo->prepare("
                    UPDATE orders 
                    SET payment_status = 'Rejected'
                    WHERE id = ?
                ");
                $stmt->execute([$orderId]);
                
                ob_clean();
echo json_encode(['success' => true, 'message' => 'Payment rejected successfully']);
            } catch (Exception $e) {
                ob_clean();
echo json_encode(['success' => false, 'message' => 'Failed to reject payment: ' . $e->getMessage()]);
            }
            break;
            
        case 'seed_cancelled':
            // Create sample cancelled orders for testing
            $sampleOrders = [
                [
                    'user_id' => 1,
                    'total_amount' => 1500.00,
                    'status' => 'Cancelled',
                    'cancel_reason' => 'Customer requested cancellation',
                    'cancel_approved' => 0,
                    'cancel_rejected' => 0,
                    'created_at' => date('Y-m-d H:i:s', strtotime('-2 days'))
                ],
                [
                    'user_id' => 2,
                    'total_amount' => 2500.00,
                    'status' => 'Cancelled',
                    'cancel_reason' => 'Auto-cancelled: Order not approved within 30 minutes',
                    'cancel_approved' => 1,
                    'cancel_rejected' => 0,
                    'auto_cancel_processed' => 1,
                    'created_at' => date('Y-m-d H:i:s', strtotime('-1 day'))
                ]
            ];
            
            foreach ($sampleOrders as $order) {
                $stmt = $pdo->prepare("
                    INSERT INTO orders (user_id, total_amount, status, cancel_reason, 
                                      cancel_approved, cancel_rejected, auto_cancel_processed, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $order['user_id'],
                    $order['total_amount'],
                    $order['status'],
                    $order['cancel_reason'],
                    $order['cancel_approved'],
                    $order['cancel_rejected'],
                    $order['auto_cancel_processed'] ?? 0,
                    $order['created_at']
                ]);
            }
            
            ob_clean();
echo json_encode(['ok' => true, 'message' => 'Sample cancelled orders created']);
				break;
            
        default:
            ob_clean();
            ob_clean();
echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    
} catch (Exception $e) {
    error_log("Orders API Error: " . $e->getMessage());
    ob_clean();
    ob_clean();
echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>
