<?php
require_once 'config/database.php';

try {
    $pdo = getDBConnection();
    
    echo "Checking orders...\n";
    $result = $pdo->query('SELECT id, total_amount, status FROM orders ORDER BY id');
    while($row = $result->fetch()) {
        echo "Order ID: " . $row['id'] . " - Amount: " . $row['total_amount'] . " - Status: " . $row['status'] . "\n";
    }
    
    echo "\nChecking products...\n";
    $result = $pdo->query('SELECT id, name FROM products ORDER BY id');
    while($row = $result->fetch()) {
        echo "Product ID: " . $row['id'] . " - " . $row['name'] . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
