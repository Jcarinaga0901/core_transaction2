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

    // Create returns table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS returns (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        customer_id INT NOT NULL,
        customer_name VARCHAR(100) NOT NULL,
        product_id INT NOT NULL,
        product_name VARCHAR(200) NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        reason TEXT NOT NULL,
        proof_images TEXT NULL,
        status ENUM('pending','approved','processing','rejected','completed') NOT NULL DEFAULT 'pending',
        response TEXT NULL,
        refund_amount DECIMAL(10,2) NULL,
        processed_by VARCHAR(100) NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME NULL,
        INDEX (order_id),
        INDEX (customer_id),
        INDEX (status),
        INDEX (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Create return_timeline table for tracking
    $pdo->exec("CREATE TABLE IF NOT EXISTS return_timeline (
        id INT AUTO_INCREMENT PRIMARY KEY,
        return_id INT NOT NULL,
        action VARCHAR(50) NOT NULL,
        notes TEXT NULL,
        created_by VARCHAR(100) NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (return_id) REFERENCES returns(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    switch ($action) {
        case 'list':
            $status = $_GET['status'] ?? 'all';
            
            $sql = "SELECT * FROM returns WHERE 1=1";
            $params = [];
            
            if ($vendor_id) {
                // Filter by vendor's products
                $sql .= " AND EXISTS (
                    SELECT 1 FROM orders o 
                    JOIN order_items oi ON oi.order_id = o.id 
                    JOIN products p ON p.id = oi.product_id 
                    WHERE o.id = returns.order_id AND p.vendor_id = ?
                )";
                $params[] = $vendor_id;
            }
            
            if ($status !== 'all') {
                $sql .= " AND status = ?";
                $params[] = $status;
            }
            
            $sql .= " ORDER BY created_at DESC LIMIT 100";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $returns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode(['success' => true, 'returns' => $returns]);
            break;
            
        case 'view':
            $id = (int)($_GET['id'] ?? 0);
            if ($id <= 0) {
                echo json_encode(['success' => false, 'message' => 'Invalid ID']);
                exit();
            }
            
            $stmt = $pdo->prepare("SELECT * FROM returns WHERE id = ?");
            $stmt->execute([$id]);
            $return = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$return) {
                echo json_encode(['success' => false, 'message' => 'Return not found']);
                exit();
            }
            
            // Get timeline
            $timelineStmt = $pdo->prepare("SELECT * FROM return_timeline WHERE return_id = ? ORDER BY created_at ASC");
            $timelineStmt->execute([$id]);
            $timeline = $timelineStmt->fetchAll(PDO::FETCH_ASSOC);
            $return['timeline'] = $timeline;
            
            echo json_encode(['success' => true, 'return' => $return]);
            break;
            
        case 'approve':
        case 'reject':
        case 'process':
            $return_id = (int)($_POST['return_id'] ?? 0);
            $response = trim($_POST['response'] ?? '');
            $refund_amount = $_POST['refund_amount'] ?? null;
            
            if ($return_id <= 0) {
                echo json_encode(['success' => false, 'message' => 'Invalid return ID']);
                exit();
            }
            
            // Get current return
            $stmt = $pdo->prepare("SELECT * FROM returns WHERE id = ?");
            $stmt->execute([$return_id]);
            $return = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$return) {
                echo json_encode(['success' => false, 'message' => 'Return not found']);
                exit();
            }
            
            // Check if action is valid for current status
            $validActions = [];
            switch ($return['status']) {
                case 'pending':
                    $validActions = ['approve', 'reject'];
                    break;
                case 'approved':
                    $validActions = ['process'];
                    break;
                case 'processing':
                    $validActions = ['complete'];
                    break;
            }
            
            if (!in_array($action, $validActions)) {
                echo json_encode(['success' => false, 'message' => 'Invalid action for current status']);
                exit();
            }
            
            // Update return status
            $newStatus = '';
            switch ($action) {
                case 'approve':
                    $newStatus = 'approved';
                    break;
                case 'reject':
                    $newStatus = 'rejected';
                    break;
                case 'process':
                    $newStatus = 'processing';
                    break;
            }
            
            $updateStmt = $pdo->prepare("UPDATE returns SET status = ?, response = ?, refund_amount = ?, processed_by = ?, updated_at = NOW() WHERE id = ?");
            $updateStmt->execute([
                $newStatus,
                $response,
                $refund_amount,
                $_SESSION['username'] ?? 'admin',
                $return_id
            ]);
            
            // Add timeline entry
            $timelineStmt = $pdo->prepare("INSERT INTO return_timeline (return_id, action, notes, created_by, created_at) VALUES (?, ?, ?, ?, NOW())");
            $timelineStmt->execute([
                $return_id,
                $action,
                $response,
                $_SESSION['username'] ?? 'admin'
            ]);
            
            echo json_encode(['success' => true, 'message' => 'Return processed successfully']);
            break;
            
        case 'complete':
            $return_id = (int)($_POST['return_id'] ?? 0);
            $response = trim($_POST['response'] ?? '');
            
            if ($return_id <= 0) {
                echo json_encode(['success' => false, 'message' => 'Invalid return ID']);
                exit();
            }
            
            // Update to completed
            $updateStmt = $pdo->prepare("UPDATE returns SET status = 'completed', response = ?, processed_by = ?, updated_at = NOW() WHERE id = ?");
            $updateStmt->execute([
                $response,
                $_SESSION['username'] ?? 'admin',
                $return_id
            ]);
            
            // Add timeline entry
            $timelineStmt = $pdo->prepare("INSERT INTO return_timeline (return_id, action, notes, created_by, created_at) VALUES (?, 'completed', ?, ?, NOW())");
            $timelineStmt->execute([
                $return_id,
                $response,
                $_SESSION['username'] ?? 'admin'
            ]);
            
            echo json_encode(['success' => true, 'message' => 'Return completed successfully']);
            break;
            
        case 'create_sample':
            // Create sample return data for testing
            $sampleReturns = [
                [
                    'order_id' => 1,
                    'customer_id' => 1,
                    'customer_name' => 'John Doe',
                    'product_id' => 1,
                    'product_name' => 'Classic Denim Jacket',
                    'amount' => 2598.00,
                    'reason' => 'Product arrived damaged with visible scratches on the surface',
                    'proof_images' => 'uploads/products/default.jpg,uploads/products/default.svg',
                    'status' => 'pending'
                ],
                [
                    'order_id' => 2,
                    'customer_id' => 2,
                    'customer_name' => 'Jane Smith',
                    'product_id' => 2,
                    'product_name' => 'Vintage T-Shirt',
                    'amount' => 1299.00,
                    'reason' => 'Wrong size received - ordered Large but received Medium',
                    'proof_images' => 'uploads/products/default.jpg',
                    'status' => 'approved'
                ],
                [
                    'order_id' => 3,
                    'customer_id' => 3,
                    'product_id' => 3,
                    'customer_name' => 'Mike Johnson',
                    'product_name' => 'Premium Hoodie',
                    'amount' => 2999.00,
                    'reason' => 'Product not as described - color is different from website',
                    'proof_images' => 'uploads/products/default.jpg,uploads/products/default.svg',
                    'status' => 'processing'
                ],
                [
                    'order_id' => 4,
                    'customer_id' => 4,
                    'product_id' => 4,
                    'customer_name' => 'Sarah Wilson',
                    'product_name' => 'Basic T-Shirt',
                    'amount' => 599.00,
                    'reason' => 'Changed mind - no longer need this item',
                    'proof_images' => '',
                    'status' => 'rejected'
                ],
                [
                    'order_id' => 5,
                    'customer_id' => 5,
                    'product_id' => 5,
                    'customer_name' => 'David Brown',
                    'product_name' => 'Designer Jeans',
                    'amount' => 2999.00,
                    'reason' => 'Defective product - button not working properly',
                    'proof_images' => 'uploads/products/default.jpg',
                    'status' => 'completed'
                ]
            ];
            
            $insertStmt = $pdo->prepare("INSERT INTO returns (order_id, customer_id, customer_name, product_id, product_name, amount, reason, proof_images, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            
            foreach ($sampleReturns as $return) {
                try {
                    $insertStmt->execute([
                        $return['order_id'],
                        $return['customer_id'],
                        $return['customer_name'],
                        $return['product_id'],
                        $return['product_name'],
                        $return['amount'],
                        $return['reason'],
                        $return['proof_images'],
                        $return['status']
                    ]);
                } catch (Exception $e) {
                    // Skip if already exists
                }
            }
            
            echo json_encode(['success' => true, 'message' => 'Sample data created']);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    
} catch (Exception $e) {
    error_log("Returns API Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
