<?php
header('Content-Type: application/json');
require_once '../config/database.php';
require_once '../includes/auth.php';

// Check if user is authenticated
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    $pdo = getDBConnection();
    $user_id = $_SESSION['user_id'];
    $vendor_id = $_SESSION['vendor_id'] ?? null;

    switch ($action) {
        case 'list':
            $page = $_GET['page'] ?? 1;
            $limit = $_GET['limit'] ?? 10;
            $offset = ($page - 1) * $limit;
            
            $stmt = $pdo->prepare("
                SELECT dr.*, o.id as order_number, u.username as customer_name 
                FROM delivery_requests dr 
                LEFT JOIN orders o ON dr.order_id = o.id 
                LEFT JOIN users u ON o.user_id = u.id 
                ORDER BY dr.created_at DESC 
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$limit, $offset]);
            
            $deliveries = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Keep original status for backend processing
            // Frontend will handle status display
            
            echo json_encode(['success' => true, 'deliveries' => $deliveries]);
            break;
            
        case 'summary':
            $stmt = $pdo->prepare("
                SELECT 
                    COUNT(*) as total_deliveries,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'in_transit' THEN 1 ELSE 0 END) as in_transit,
                    SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered,
                    SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed
                FROM delivery_requests
            ");
            $stmt->execute();
            
            $summary = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'summary' => $summary]);
            break;
            
        case 'update_status':
            $deliveryId = $_POST['delivery_id'] ?? $_POST['id'] ?? '';
            $newStatus = $_POST['status'] ?? '';
            
            if (!$deliveryId || !$newStatus) {
                echo json_encode(['success' => false, 'message' => 'Delivery ID and status are required']);
                break;
            }
            
            $validStatuses = ['pending', 'in_transit', 'delivered', 'failed'];
            if (!in_array($newStatus, $validStatuses)) {
                echo json_encode(['success' => false, 'message' => 'Invalid status']);
                break;
            }
            
            // Check if delivery_requests table exists
            $checkTable = $pdo->query("SHOW TABLES LIKE 'delivery_requests'");
            if ($checkTable->rowCount() == 0) {
                echo json_encode(['success' => false, 'message' => 'Delivery requests table not found']);
                break;
            }
            
            // Check if delivery exists
            $checkDelivery = $pdo->prepare("SELECT id FROM delivery_requests WHERE id = ?");
            $checkDelivery->execute([$deliveryId]);
            if ($checkDelivery->rowCount() == 0) {
                echo json_encode(['success' => false, 'message' => 'Delivery not found']);
                break;
            }
            
            $pdo->beginTransaction();
            
            try {
                // Update delivery status
                $stmt = $pdo->prepare("
                    UPDATE delivery_requests 
                    SET status = ?, updated_at = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([$newStatus, $deliveryId]);
                
                if ($stmt->rowCount() > 0) {
                    // If delivered, update order status and create commission
                    if ($newStatus === 'delivered') {
                        // Update order status to "Delivered" (optional - don't fail if orders table doesn't exist)
                        try {
                            $stmt = $pdo->prepare("
                                UPDATE orders o
                                JOIN delivery_requests dr ON o.id = dr.order_id
                                SET o.status = 'Delivered', o.updated_at = NOW()
                                WHERE dr.id = ?
                            ");
                            $stmt->execute([$deliveryId]);
                        } catch (Exception $orderError) {
                            // Log order update error but don't fail the delivery update
                            error_log("Order status update failed: " . $orderError->getMessage());
                        }
                        
                        // Create commission record (optional - don't fail if commission table doesn't exist)
                        try {
                            $stmt = $pdo->prepare("
                                SELECT o.id as order_id, o.total_amount, oi.id as order_item_id
                                FROM orders o
                                JOIN delivery_requests dr ON o.id = dr.order_id
                                JOIN order_items oi ON o.id = oi.order_id
                                WHERE dr.id = ?
                                LIMIT 1
                            ");
                            $stmt->execute([$deliveryId]);
                            $orderData = $stmt->fetch(PDO::FETCH_ASSOC);
                            
                            if ($orderData) {
                                // Check if commissions table exists
                                $checkTable = $pdo->query("SHOW TABLES LIKE 'commissions'");
                                if ($checkTable->rowCount() > 0) {
                                    $commissionRate = 0.10; // 10% commission
                                    $commissionAmount = $orderData['total_amount'] * $commissionRate;
                                    
                                    // Get vendor_id from session or use default
                                    $vendorId = $vendor_id ?? 1;
                                    
                                    $stmt = $pdo->prepare("
                                        INSERT INTO commissions (
                                            order_item_id, vendor_id, commission_rate, commission_amount, 
                                            settled, created_at
                                        ) VALUES (?, ?, ?, ?, ?, ?)
                                    ");
                                    $stmt->execute([
                                        $orderData['order_item_id'],
                                        $vendorId,
                                        $commissionRate,
                                        $commissionAmount,
                                        0, // Not settled
                                        date('Y-m-d H:i:s')
                                    ]);
                                }
                            }
                        } catch (Exception $commissionError) {
                            // Log commission error but don't fail the delivery update
                            error_log("Commission creation failed: " . $commissionError->getMessage());
                        }
                    }
                    
                    $pdo->commit();
                    
                    // Get updated summary data
                    $summaryStmt = $pdo->prepare("
                        SELECT 
                            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                            SUM(CASE WHEN status = 'in_transit' THEN 1 ELSE 0 END) as in_transit,
                            SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered,
                            SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed
                        FROM delivery_requests
                    ");
                    $summaryStmt->execute();
                    $summary = $summaryStmt->fetch(PDO::FETCH_ASSOC);
                    
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Delivery status updated successfully',
                        'summary' => $summary
                    ]);
        } else {
                    $pdo->rollBack();
                    echo json_encode(['success' => false, 'message' => 'Delivery not found']);
                }
            } catch (Exception $e) {
                $pdo->rollBack();
                throw $e;
            }
            break;
            
        case 'create_delivery':
            $orderId = $_POST['order_id'] ?? '';
            $customerName = $_POST['customer_name'] ?? '';
            $customerPhone = $_POST['customer_phone'] ?? '';
            $deliveryAddress = $_POST['delivery_address'] ?? '';
            $deliveryDate = $_POST['delivery_date'] ?? '';
            $deliveryTime = $_POST['delivery_time'] ?? '';
            $deliveryFee = $_POST['delivery_fee'] ?? 50.00;
            
            if (!$orderId || !$customerName || !$deliveryAddress) {
                echo json_encode(['success' => false, 'message' => 'Required fields are missing']);
                break;
            }
            
            $stmt = $pdo->prepare("
                INSERT INTO delivery_requests (
                    order_id, customer_name, customer_phone, delivery_address, 
                    delivery_date, delivery_time, status, delivery_fee, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $orderId,
                $customerName,
                $customerPhone,
                $deliveryAddress,
                $deliveryDate,
                $deliveryTime,
                'pending',
                $deliveryFee,
                date('Y-m-d H:i:s')
            ]);
            
            echo json_encode(['success' => true, 'message' => 'Delivery request created successfully']);
            break;
            
        default:
            // Debug information
            $debugInfo = [
                'received_action' => $action,
                'get_params' => $_GET,
                'post_params' => $_POST,
                'method' => $_SERVER['REQUEST_METHOD']
            ];
            error_log("Delivery API Debug: " . json_encode($debugInfo));
            echo json_encode(['success' => false, 'message' => 'Invalid action: ' . $action]);
    }
    
} catch (Exception $e) {
    error_log("Delivery API Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>
