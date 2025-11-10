<?php
/**
 * Test Auto-Cancellation System
 * This script manually triggers the auto-cancellation process for testing
 */

require_once '../config/database.php';

echo "=== TESTING AUTO-CANCELLATION SYSTEM ===\n\n";

try {
    $pdo = getDBConnection();
    
    // Check for orders that should be auto-cancelled
    $stmt = $pdo->prepare("
        SELECT o.*, u.username as customer_name
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        WHERE o.status = 'Just Placed' 
        AND o.created_at < DATE_SUB(NOW(), INTERVAL 30 MINUTE)
        AND o.auto_cancel_processed = 0
    ");
    $stmt->execute();
    $pendingOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Found " . count($pendingOrders) . " orders eligible for auto-cancellation:\n";
    
    foreach ($pendingOrders as $order) {
        echo "- Order #{$order['id']} from {$order['customer_name']} (Created: {$order['created_at']})\n";
    }
    
    if (count($pendingOrders) > 0) {
        echo "\nTriggering auto-cancellation process...\n";
        
        // Call the auto-cancel API
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://localhost/CT2/api/auto_cancel.php');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200) {
            $result = json_decode($response, true);
            if ($result && $result['success']) {
                echo "✅ Auto-cancellation completed successfully!\n";
                echo "   - Cancelled orders: {$result['cancelled_orders']}\n";
                echo "   - Notifications sent: {$result['notifications_sent']}\n";
            } else {
                echo "❌ Auto-cancellation failed: " . ($result['message'] ?? 'Unknown error') . "\n";
            }
        } else {
            echo "❌ HTTP Error: {$httpCode}\n";
        }
    } else {
        echo "\n✅ No orders need auto-cancellation at this time.\n";
    }
    
    // Show current order statuses
    echo "\n=== CURRENT ORDER STATUSES ===\n";
    $stmt = $pdo->prepare("
        SELECT status, COUNT(*) as count 
        FROM orders 
        GROUP BY status 
        ORDER BY count DESC
    ");
    $stmt->execute();
    $statusCounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($statusCounts as $status) {
        echo "- {$status['status']}: {$status['count']} orders\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== TEST COMPLETED ===\n";
?>
