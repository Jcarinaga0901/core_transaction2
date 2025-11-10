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
$user_id = $_SESSION['user_id'];
$vendor_id = $_SESSION['vendor_id'] ?? null;

try {
    $pdo = getDBConnection();

    switch ($action) {
        case 'list_payments':
            $page = $_GET['page'] ?? 1;
            $limit = $_GET['limit'] ?? 10;
            $offset = ($page - 1) * $limit;
            $status = $_GET['status'] ?? '';
            $method = $_GET['method'] ?? '';
            $search = $_GET['search'] ?? '';
            $date_from = $_GET['date_from'] ?? '';
            $date_to = $_GET['date_to'] ?? '';
            
            $where_conditions = [];
            $params = [];
            
            if ($vendor_id) {
                $where_conditions[] = "p.vendor_id = ?";
                $params[] = $vendor_id;
            }
            
            if ($status) {
                $where_conditions[] = "p.payment_status = ?";
                $params[] = $status;
            }
            
            if ($method) {
                $where_conditions[] = "p.payment_method = ?";
                $params[] = $method;
            }
            
            if ($search) {
                $where_conditions[] = "(o.order_number LIKE ? OR p.transaction_reference LIKE ?)";
                $params[] = "%$search%";
                $params[] = "%$search%";
            }
            
            if ($date_from) {
                $where_conditions[] = "DATE(p.created_at) >= ?";
                $params[] = $date_from;
            }
            
            if ($date_to) {
                $where_conditions[] = "DATE(p.created_at) <= ?";
                $params[] = $date_to;
            }
            
            $where_clause = $where_conditions ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
            
            $sql = "SELECT p.*, o.order_number, o.total_amount, o.status as order_status,
                           c.name as customer_name, c.email as customer_email,
                           v.name as vendor_name
                    FROM payments p
                    LEFT JOIN orders o ON p.order_id = o.id
                    LEFT JOIN customers c ON p.customer_id = c.id
                    LEFT JOIN vendors v ON p.vendor_id = v.id
                    $where_clause
                    ORDER BY p.created_at DESC
                    LIMIT ? OFFSET ?";
            
            $params[] = $limit;
            $params[] = $offset;
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get total count
            $count_sql = "SELECT COUNT(*) as total FROM payments p LEFT JOIN orders o ON p.order_id = o.id $where_clause";
            $count_params = array_slice($params, 0, -2); // Remove limit and offset
            $count_stmt = $pdo->prepare($count_sql);
            $count_stmt->execute($count_params);
            $total = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            echo json_encode([
                'success' => true,
                'payments' => $payments,
                'pagination' => [
                    'page' => $page,
                    'limit' => $limit,
                    'total' => $total,
                    'pages' => ceil($total / $limit)
                ]
            ]);
            break;
            
        case 'get_payment_details':
            $payment_id = $_GET['payment_id'] ?? '';
            
            if (!$payment_id) {
                echo json_encode(['success' => false, 'message' => 'Payment ID required']);
                exit();
            }
            
            $stmt = $pdo->prepare("
                SELECT p.*, o.order_number, o.total_amount, o.status as order_status,
                       o.shipping_address, o.billing_address, o.notes as order_notes,
                       c.name as customer_name, c.email as customer_email, c.phone as customer_phone,
                       v.name as vendor_name, v.email as vendor_email
                FROM payments p
                LEFT JOIN orders o ON p.order_id = o.id
                LEFT JOIN customers c ON p.customer_id = c.id
                LEFT JOIN vendors v ON p.vendor_id = v.id
                WHERE p.id = ?" . ($vendor_id ? " AND p.vendor_id = ?" : "")
            );
            
            $params = [$payment_id];
            if ($vendor_id) {
                $params[] = $vendor_id;
            }
            
            $stmt->execute($params);
            $payment = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$payment) {
                echo json_encode(['success' => false, 'message' => 'Payment not found']);
                exit();
            }
            
            // Get payment timeline
            $timeline_stmt = $pdo->prepare("SELECT * FROM payment_timeline WHERE payment_id = ? ORDER BY created_at ASC");
            $timeline_stmt->execute([$payment_id]);
            $timeline = $timeline_stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $payment['timeline'] = $timeline;
            
            echo json_encode(['success' => true, 'payment' => $payment]);
            break;
            
        case 'verify_payment':
            $payment_id = $_POST['payment_id'] ?? '';
            $notes = $_POST['notes'] ?? '';
            
            if (!$payment_id) {
                echo json_encode(['success' => false, 'message' => 'Payment ID required']);
                exit();
            }
            
            $pdo->beginTransaction();
            
            try {
                // Update payment status
                $stmt = $pdo->prepare("UPDATE payments SET payment_status = 'verified', verified_by = ?, verified_at = NOW() WHERE id = ?" . ($vendor_id ? " AND vendor_id = ?" : ""));
                $params = [$user_id, $payment_id];
                if ($vendor_id) {
                    $params[] = $vendor_id;
                }
                $stmt->execute($params);
                
                if ($stmt->rowCount() === 0) {
                    throw new Exception('Payment not found or unauthorized');
                }
                
                // Add to timeline
                $timeline_stmt = $pdo->prepare("INSERT INTO payment_timeline (payment_id, action, notes, created_by) VALUES (?, 'verified', ?, ?)");
                $timeline_stmt->execute([$payment_id, $notes, $user_id]);
                
                $pdo->commit();
                echo json_encode(['success' => true, 'message' => 'Payment verified successfully']);
                
            } catch (Exception $e) {
                $pdo->rollBack();
                throw $e;
            }
            break;
            
        case 'reject_payment':
            $payment_id = $_POST['payment_id'] ?? '';
            $rejection_reason = $_POST['rejection_reason'] ?? '';
            
            if (!$payment_id || !$rejection_reason) {
                echo json_encode(['success' => false, 'message' => 'Payment ID and rejection reason required']);
                exit();
            }
            
            $pdo->beginTransaction();
            
            try {
                // Update payment status
                $stmt = $pdo->prepare("UPDATE payments SET payment_status = 'rejected', rejection_reason = ?, verified_by = ?, verified_at = NOW() WHERE id = ?" . ($vendor_id ? " AND vendor_id = ?" : ""));
                $params = [$rejection_reason, $user_id, $payment_id];
                if ($vendor_id) {
                    $params[] = $vendor_id;
                }
                $stmt->execute($params);
                
                if ($stmt->rowCount() === 0) {
                    throw new Exception('Payment not found or unauthorized');
                }
                
                // Add to timeline
                $timeline_stmt = $pdo->prepare("INSERT INTO payment_timeline (payment_id, action, notes, created_by) VALUES (?, 'rejected', ?, ?)");
                $timeline_stmt->execute([$payment_id, $rejection_reason, $user_id]);
                
                $pdo->commit();
                echo json_encode(['success' => true, 'message' => 'Payment rejected successfully']);
                
            } catch (Exception $e) {
                $pdo->rollBack();
                throw $e;
            }
            break;
            
        case 'process_refund':
            $payment_id = $_POST['payment_id'] ?? '';
            $refund_amount = $_POST['refund_amount'] ?? '';
            $refund_reason = $_POST['refund_reason'] ?? '';
            
            if (!$payment_id || !$refund_amount || !$refund_reason) {
                echo json_encode(['success' => false, 'message' => 'Payment ID, refund amount, and reason required']);
                exit();
            }
            
            $pdo->beginTransaction();
            
            try {
                // Update payment status
                $stmt = $pdo->prepare("UPDATE payments SET payment_status = 'refunded', amount = ? WHERE id = ?" . ($vendor_id ? " AND vendor_id = ?" : ""));
                $params = [$refund_amount, $payment_id];
                if ($vendor_id) {
                    $params[] = $vendor_id;
                }
                $stmt->execute($params);
                
                if ($stmt->rowCount() === 0) {
                    throw new Exception('Payment not found or unauthorized');
                }
                
                // Add to timeline
                $timeline_stmt = $pdo->prepare("INSERT INTO payment_timeline (payment_id, action, notes, created_by) VALUES (?, 'refunded', ?, ?)");
                $timeline_stmt->execute([$payment_id, "Refunded ₱$refund_amount - $refund_reason", $user_id]);
                
                $pdo->commit();
                echo json_encode(['success' => true, 'message' => 'Refund processed successfully']);
                
            } catch (Exception $e) {
                $pdo->rollBack();
                throw $e;
            }
            break;
            
        case 'upload_proof':
            $payment_id = $_POST['payment_id'] ?? '';
            $receipt_number = $_POST['receipt_number'] ?? '';
            $bank_reference = $_POST['bank_reference'] ?? '';
            
            if (!$payment_id) {
                echo json_encode(['success' => false, 'message' => 'Payment ID required']);
                exit();
            }
            
            // Handle file uploads
            $proof_images = [];
            if (isset($_FILES['proof_images'])) {
                $upload_dir = '../uploads/payment_proofs/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                foreach ($_FILES['proof_images']['tmp_name'] as $key => $tmp_name) {
                    if ($_FILES['proof_images']['error'][$key] === UPLOAD_ERR_OK) {
                        $filename = 'proof_' . $payment_id . '_' . time() . '_' . $key . '.jpg';
                        $filepath = $upload_dir . $filename;
                        
                        if (move_uploaded_file($tmp_name, $filepath)) {
                            $proof_images[] = 'uploads/payment_proofs/' . $filename;
                        }
                    }
                }
            }
            
            $proof_data = [
                'images' => $proof_images,
                'receipt_number' => $receipt_number,
                'bank_reference' => $bank_reference,
                'uploaded_at' => date('Y-m-d H:i:s')
            ];
            
            $pdo->beginTransaction();
            
            try {
                // Update payment with proof
                $stmt = $pdo->prepare("UPDATE payments SET payment_status = 'proof_submitted', proof_of_payment = ? WHERE id = ?" . ($vendor_id ? " AND vendor_id = ?" : ""));
                $params = [json_encode($proof_data), $payment_id];
                if ($vendor_id) {
                    $params[] = $vendor_id;
                }
                $stmt->execute($params);
                
                if ($stmt->rowCount() === 0) {
                    throw new Exception('Payment not found or unauthorized');
                }
                
                // Add to timeline
                $timeline_stmt = $pdo->prepare("INSERT INTO payment_timeline (payment_id, action, notes, created_by) VALUES (?, 'proof_submitted', ?, ?)");
                $timeline_stmt->execute([$payment_id, "Payment proof uploaded", $user_id]);
                
                $pdo->commit();
                echo json_encode(['success' => true, 'message' => 'Payment proof uploaded successfully']);
                
            } catch (Exception $e) {
                $pdo->rollBack();
                throw $e;
            }
            break;
            
        case 'payment_stats':
            $date_from = $_GET['date_from'] ?? date('Y-m-01'); // First day of current month
            $date_to = $_GET['date_to'] ?? date('Y-m-d'); // Today
            
            $where_clause = "WHERE DATE(p.created_at) BETWEEN ? AND ?";
            $params = [$date_from, $date_to];
            
            if ($vendor_id) {
                $where_clause .= " AND p.vendor_id = ?";
                $params[] = $vendor_id;
            }
            
            // Total payments
            $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM payments p $where_clause");
            $stmt->execute($params);
            $total_payments = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Pending payments
            $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM payments p $where_clause AND p.payment_status IN ('pending', 'proof_submitted')");
            $stmt->execute($params);
            $pending_payments = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Verified payments
            $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM payments p $where_clause AND p.payment_status = 'verified'");
            $stmt->execute($params);
            $verified_payments = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Total amount
            $stmt = $pdo->prepare("SELECT SUM(amount) as total FROM payments p $where_clause AND p.payment_status = 'verified'");
            $stmt->execute($params);
            $total_amount = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            
            // Payment method breakdown
            $stmt = $pdo->prepare("SELECT payment_method, COUNT(*) as count, SUM(amount) as total FROM payments p $where_clause GROUP BY payment_method");
            $stmt->execute($params);
            $method_breakdown = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Status breakdown
            $stmt = $pdo->prepare("SELECT payment_status, COUNT(*) as count FROM payments p $where_clause GROUP BY payment_status");
            $stmt->execute($params);
            $status_breakdown = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'stats' => [
                    'total_payments' => $total_payments,
                    'pending_payments' => $pending_payments,
                    'verified_payments' => $verified_payments,
                    'total_amount' => $total_amount,
                    'method_breakdown' => $method_breakdown,
                    'status_breakdown' => $status_breakdown
                ]
            ]);
            break;
            
        case 'payment_gateway_webhook':
            // Handle automatic payment verification from gateways
            $gateway = $_POST['gateway'] ?? '';
            $transaction_id = $_POST['transaction_id'] ?? '';
            $status = $_POST['status'] ?? '';
            $amount = $_POST['amount'] ?? '';
            
            if (!$gateway || !$transaction_id) {
                echo json_encode(['success' => false, 'message' => 'Gateway and transaction ID required']);
                exit();
            }
            
            // Find payment by transaction reference
            $stmt = $pdo->prepare("SELECT * FROM payments WHERE transaction_reference = ?");
            $stmt->execute([$transaction_id]);
            $payment = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$payment) {
                echo json_encode(['success' => false, 'message' => 'Payment not found']);
                exit();
            }
            
            $pdo->beginTransaction();
            
            try {
                $new_status = ($status === 'success') ? 'verified' : 'rejected';
                $gateway_response = json_encode([
                    'gateway' => $gateway,
                    'transaction_id' => $transaction_id,
                    'status' => $status,
                    'amount' => $amount,
                    'webhook_received_at' => date('Y-m-d H:i:s')
                ]);
                
                $stmt = $pdo->prepare("UPDATE payments SET payment_status = ?, payment_gateway_response = ?, verified_at = NOW() WHERE id = ?");
                $stmt->execute([$new_status, $gateway_response, $payment['id']]);
                
                // Add to timeline
                $timeline_stmt = $pdo->prepare("INSERT INTO payment_timeline (payment_id, action, notes, created_by) VALUES (?, ?, ?, ?)");
                $timeline_stmt->execute([$payment['id'], $new_status, "Automatic verification from $gateway", 'system']);
                
                $pdo->commit();
                echo json_encode(['success' => true, 'message' => 'Payment status updated']);
                
            } catch (Exception $e) {
                $pdo->rollBack();
                throw $e;
            }
            break;
            
        case 'get_payment_methods':
            // Get payment methods for vendor
            $stmt = $pdo->prepare("SELECT * FROM payment_methods WHERE vendor_id = ? ORDER BY method_type");
            $stmt->execute([$vendor_id]);
            $methods = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode(['success' => true, 'methods' => $methods]);
            break;
            
        case 'save_payment_method':
            // Save payment method configuration
            $method_type = $_POST['method_type'] ?? '';
            $is_active = isset($_POST['is_active']) ? 1 : 0;
            
            if (!$method_type) {
                echo json_encode(['success' => false, 'message' => 'Method type is required']);
                break;
            }
            
            // Prepare data based on method type
            $bank_account_details = null;
            $ewallet_details = null;
            $gateway_credentials = null;
            
            if ($method_type === 'bank_transfer') {
                $bank_account_details = json_encode([
                    'bank' => $_POST['bank_name'] ?? '',
                    'account' => $_POST['account_number'] ?? '',
                    'account_name' => $_POST['account_name'] ?? ''
                ]);
            } elseif (in_array($method_type, ['gcash', 'paymaya'])) {
                $ewallet_details = json_encode([
                    'number' => $_POST['gcash_number'] ?? $_POST['paymaya_number'] ?? '',
                    'name' => $_POST['gcash_name'] ?? $_POST['paymaya_name'] ?? ''
                ]);
            } elseif ($method_type === 'credit_card') {
                $gateway_credentials = json_encode([
                    'gateway' => $_POST['gateway_provider'] ?? '',
                    'merchant_id' => $_POST['merchant_id'] ?? '',
                    'api_key' => $_POST['api_key'] ?? ''
                ]);
            }
            
            // Check if method already exists
            $stmt = $pdo->prepare("SELECT id FROM payment_methods WHERE vendor_id = ? AND method_type = ?");
            $stmt->execute([$vendor_id, $method_type]);
            $existing = $stmt->fetch();
            
            if ($existing) {
                // Update existing method
                $stmt = $pdo->prepare("
                    UPDATE payment_methods 
                    SET is_active = ?, bank_account_details = ?, ewallet_details = ?, gateway_credentials = ?, updated_at = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([$is_active, $bank_account_details, $ewallet_details, $gateway_credentials, $existing['id']]);
            } else {
                // Insert new method
                $stmt = $pdo->prepare("
                    INSERT INTO payment_methods (vendor_id, method_type, is_active, bank_account_details, ewallet_details, gateway_credentials)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$vendor_id, $method_type, $is_active, $bank_account_details, $ewallet_details, $gateway_credentials]);
            }
            
            echo json_encode(['success' => true, 'message' => 'Payment method saved successfully']);
            break;
            
        case 'save_gateway_settings':
            // Save gateway settings
            $input = json_decode(file_get_contents('php://input'), true);
            $settings = $input['settings'] ?? [];
            
            // Store settings in session or database
            $_SESSION['payment_settings'] = $settings;
            
            echo json_encode(['success' => true, 'message' => 'Gateway settings saved successfully']);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
