<?php
require_once '../config/database.php';

try {
    $pdo = getDBConnection();
    
    // Check if we have any existing commissions
    $checkCommissions = $pdo->query("SELECT COUNT(*) as count FROM commissions");
    $commissionCount = $checkCommissions->fetch(PDO::FETCH_ASSOC)['count'];
    
    if ($commissionCount > 0) {
        echo "✅ Commissions already exist ($commissionCount records).\n";
        echo "Sample commission data:\n";
        
        $stmt = $pdo->query("
            SELECT 
                c.id,
                c.commission_amount,
                c.commission_rate,
                c.settled,
                c.created_at,
                o.id as order_id,
                o.status as order_status
            FROM commissions c
            LEFT JOIN order_items oi ON c.order_item_id = oi.id
            LEFT JOIN orders o ON oi.order_id = o.id
            ORDER BY c.created_at DESC
            LIMIT 5
        ");
        
        $commissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($commissions as $comm) {
            echo "- Commission #{$comm['id']}: ₱" . number_format($comm['commission_amount'], 2) . 
                 " (Rate: " . ($comm['commission_rate'] * 100) . "%) - " . 
                 ($comm['settled'] ? 'Settled' : 'Pending') . 
                 " - Order #{$comm['order_id']} ({$comm['order_status']})\n";
        }
    } else {
        echo "No commissions found. Creating sample data...\n";
        
        // Get some existing order items to create commissions for
        $stmt = $pdo->query("
            SELECT oi.id as order_item_id, oi.order_id, oi.price, oi.quantity, o.total_amount, o.status
            FROM order_items oi
            JOIN orders o ON oi.order_id = o.id
            WHERE o.status = 'Delivered'
            LIMIT 5
        ");
        
        $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($orderItems)) {
            echo "No delivered orders found. Please deliver some orders first.\n";
            exit;
        }
        
        foreach ($orderItems as $item) {
            $commissionRate = 0.10; // 10% commission
            $commissionAmount = $item['total_amount'] * $commissionRate;
            
            $stmt = $pdo->prepare("
                INSERT INTO commissions (
                    order_item_id, vendor_id, commission_rate, commission_amount, 
                    settled, created_at
                ) VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $item['order_item_id'],
                1, // Default vendor
                $commissionRate,
                $commissionAmount,
                0, // Not settled
                date('Y-m-d H:i:s', strtotime('-' . rand(1, 30) . ' days'))
            ]);
            
            echo "✅ Created commission for Order #{$item['order_id']}: ₱" . number_format($commissionAmount, 2) . "\n";
        }
    }
    
    echo "\n🎉 Commission system is ready!\n";
    echo "You can now view commission reports in the Commission Reports page.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
