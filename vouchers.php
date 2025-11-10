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

    // Create vouchers table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS vouchers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        vendor_id INT NOT NULL,
        name VARCHAR(120) NOT NULL,
        description TEXT NULL,
        type ENUM('percent','fixed','delivery') NOT NULL DEFAULT 'percent',
        discount_value DECIMAL(10,2) NOT NULL DEFAULT 0,
        min_spend DECIMAL(10,2) NULL,
        max_discount DECIMAL(10,2) NULL,
        start_date DATETIME NULL,
        end_date DATETIME NULL,
        status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
        is_active TINYINT(1) NOT NULL DEFAULT 0,
        created_by VARCHAR(120) NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME NULL,
        INDEX (vendor_id),
        INDEX (status),
        INDEX (is_active)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    switch ($action) {
        case 'create':
            $name = trim($_POST['name'] ?? '');
            $type = $_POST['type'] ?? 'percent';
            $discount = (float)($_POST['discount_value'] ?? 0);
            $minSpend = $_POST['min_spend'] !== '' ? (float)$_POST['min_spend'] : null;
            $maxDiscount = $_POST['max_discount'] !== '' ? (float)$_POST['max_discount'] : null;
            $start = trim($_POST['start_date'] ?? '');
            $end = trim($_POST['end_date'] ?? '');
            $desc = trim($_POST['description'] ?? '');

            if ($name === '' || $discount <= 0) {
                echo json_encode(['success' => false, 'message' => 'Invalid input']);
                exit();
            }
            if ($type === 'percent' && ($discount < 1 || $discount > 100)) {
                echo json_encode(['success' => false, 'message' => 'Percent must be between 1 and 100']);
                exit();
            }

            $stmt = $pdo->prepare("INSERT INTO vouchers (vendor_id, name, description, type, discount_value, min_spend, max_discount, start_date, end_date, status, is_active, created_by, created_at) VALUES (?,?,?,?,?,?,?,?,?,'pending',0,?,NOW())");
            $stmt->execute([
                $vendor_id, $name, $desc, $type, $discount, $minSpend, $maxDiscount,
                $start !== '' ? $start : null,
                $end !== '' ? $end : null,
                $_SESSION['username'] ?? 'user'
            ]);
            
            $voucherId = (int)$pdo->lastInsertId();
            echo json_encode(['success' => true, 'id' => $voucherId]);
            break;
            
        case 'list':
            $stmt = $pdo->prepare("SELECT * FROM vouchers WHERE vendor_id = ? ORDER BY created_at DESC LIMIT 300");
            $stmt->execute([$vendor_id]);
            $vouchers = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'vouchers' => $vouchers]);
            break;
            
        case 'shop_list':
            $stmt = $pdo->prepare("SELECT * FROM vouchers WHERE vendor_id = ? AND status = 'approved' ORDER BY updated_at DESC, created_at DESC LIMIT 300");
            $stmt->execute([$vendor_id]);
            $vouchers = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'vouchers' => $vouchers]);
            break;
            
        case 'toggle_active':
            $id = (int)($_POST['id'] ?? 0);
            $active = (int)($_POST['active'] ?? 0) ? 1 : 0;
            if ($id <= 0) { 
                echo json_encode(['success' => false, 'message' => 'Invalid ID']);
                exit();
            }
            
            $check = $pdo->prepare("SELECT status, vendor_id FROM vouchers WHERE id = ?");
            $check->execute([$id]);
            $row = $check->fetch();
            if (!$row || (int)$row['vendor_id'] !== (int)$vendor_id) { 
                echo json_encode(['success' => false, 'message' => 'Not found']);
                exit();
            }
            if ($row['status'] !== 'approved') { 
                echo json_encode(['success' => false, 'message' => 'Only approved vouchers can be activated']);
                exit();
            }
            
            $pdo->prepare("UPDATE vouchers SET is_active = ?, updated_at = NOW() WHERE id = ?")->execute([$active, $id]);
            echo json_encode(['success' => true]);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    
} catch (Exception $e) {
    error_log("Vouchers API Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>
