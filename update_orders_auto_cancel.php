<?php
require_once '../config/database.php';

try {
    $pdo = getDBConnection();
    
    echo "Updating orders table for auto-cancellation system...\n";
    
    // Add columns for auto-cancellation tracking
    $columns = [
        'auto_cancel_processed' => 'TINYINT(1) DEFAULT 0 COMMENT "Whether auto-cancellation has been processed"',
        'cancelled_at' => 'DATETIME NULL COMMENT "When the order was cancelled"',
        'cancel_approved' => 'TINYINT(1) DEFAULT 0 COMMENT "Whether cancellation was approved by seller"',
        'cancel_rejected' => 'TINYINT(1) DEFAULT 0 COMMENT "Whether cancellation was rejected by seller"',
        'cancel_approved_at' => 'DATETIME NULL COMMENT "When cancellation was approved"',
        'cancel_rejected_at' => 'DATETIME NULL COMMENT "When cancellation was rejected"',
        'cancel_actor' => 'INT NULL COMMENT "User ID who approved/rejected cancellation"'
    ];
    
    foreach ($columns as $column => $definition) {
        try {
            $stmt = $pdo->prepare("ALTER TABLE orders ADD COLUMN {$column} {$definition}");
            $stmt->execute();
            echo "✓ Added column: {$column}\n";
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
                echo "⚠ Column {$column} already exists\n";
            } else {
                echo "✗ Error adding column {$column}: " . $e->getMessage() . "\n";
            }
        }
    }
    
    // Create index for auto-cancellation queries
    try {
        $stmt = $pdo->prepare("CREATE INDEX idx_orders_auto_cancel ON orders (status, created_at, auto_cancel_processed)");
        $stmt->execute();
        echo "✓ Created index for auto-cancellation queries\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate key name') !== false) {
            echo "⚠ Index already exists\n";
        } else {
            echo "✗ Error creating index: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n✅ Orders table updated successfully for auto-cancellation system!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
