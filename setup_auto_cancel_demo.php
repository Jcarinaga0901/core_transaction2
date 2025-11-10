<?php
/**
 * Setup Auto-Cancellation Demo
 * Creates sample orders and demonstrates the auto-cancellation system
 */

require_once '../config/database.php';

echo "=== SETTING UP AUTO-CANCELLATION DEMO ===\n\n";

try {
    $pdo = getDBConnection();
    
    // Create sample pending orders that will be auto-cancelled
    echo "Creating sample orders for auto-cancellation demo...\n";
    
    $sampleOrders = [
        [
            'user_id' => 1,
            'total_amount' => 1200.00,
            'status' => 'Just Placed',
            'created_at' => date('Y-m-d H:i:s', strtotime('-35 minutes')), // 35 minutes ago - should be auto-cancelled
            'auto_cancel_processed' => 0
        ],
        [
            'user_id' => 1,
            'total_amount' => 2500.00,
            'status' => 'Just Placed',
            'created_at' => date('Y-m-d H:i:s', strtotime('-25 minutes')), // 25 minutes ago - should be auto-cancelled
            'auto_cancel_processed' => 0
        ],
        [
            'user_id' => 1,
            'total_amount' => 800.00,
            'status' => 'Just Placed',
            'created_at' => date('Y-m-d H:i:s', strtotime('-10 minutes')), // 10 minutes ago - should NOT be auto-cancelled
            'auto_cancel_processed' => 0
        ]
    ];
    
    foreach ($sampleOrders as $order) {
        $stmt = $pdo->prepare("
            INSERT INTO orders (user_id, total_amount, status, created_at, auto_cancel_processed)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $order['user_id'],
            $order['total_amount'],
            $order['status'],
            $order['created_at'],
            $order['auto_cancel_processed']
        ]);
        
        $orderId = $pdo->lastInsertId();
        echo "✓ Created order #{$orderId} (Created: {$order['created_at']}, Amount: ₱{$order['total_amount']})\n";
        
        // Add order items
        $stmt = $pdo->prepare("
            INSERT INTO order_items (order_id, product_id, quantity, price)
            VALUES (?, 1, 2, ?)
        ");
        $stmt->execute([$orderId, $order['total_amount'] / 2]);
    }
    
    echo "\n=== DEMO SETUP COMPLETE ===\n";
    echo "Sample orders created with different timestamps:\n";
    echo "- 2 orders older than 30 minutes (will be auto-cancelled)\n";
    echo "- 1 order newer than 30 minutes (will remain pending)\n\n";
    
    echo "To test the auto-cancellation system:\n";
    echo "1. Go to the Cancelled Orders page\n";
    echo "2. Click 'Check Auto-Cancel' button\n";
    echo "3. Or run: php scripts/test_auto_cancel.php\n\n";
    
    echo "To set up automatic cron job (every 5 minutes):\n";
    echo "Add this to your crontab:\n";
    echo "*/5 * * * * /usr/bin/php " . realpath('scripts/auto_cancel_cron.php') . "\n\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
