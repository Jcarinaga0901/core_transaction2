<?php
require_once 'config/database.php';

try {
    $pdo = getDBConnection();
    
    echo "Checking payments...\n";
    $result = $pdo->query('SELECT id, order_id, amount, status FROM payments ORDER BY id');
    while($row = $result->fetch()) {
        echo "Payment ID: " . $row['id'] . " - Order ID: " . $row['order_id'] . " - Amount: " . $row['amount'] . " - Status: " . $row['status'] . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
