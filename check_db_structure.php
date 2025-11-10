<?php
require_once 'config/database.php';

try {
    $pdo = getDBConnection();
    
    echo "Checking database structure...\n";
    
    // Check if tables exist
    $tables = ['payments', 'payment_methods', 'payment_timeline', 'orders', 'order_items', 'products', 'categories', 'vendors', 'vouchers', 'returns', 'deliveries'];
    
    foreach ($tables as $table) {
        $result = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($result->rowCount() > 0) {
            echo "✓ Table '$table' exists\n";
            
            // Show columns
            $columns = $pdo->query("DESCRIBE $table");
            echo "  Columns: ";
            while ($col = $columns->fetch()) {
                echo $col['Field'] . " ";
            }
            echo "\n";
        } else {
            echo "✗ Table '$table' does not exist\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
