<?php
require_once 'config/database.php';

try {
    $pdo = getDBConnection();
    
    echo "Checking categories...\n";
    $result = $pdo->query('SELECT id, name FROM categories ORDER BY id');
    while($row = $result->fetch()) {
        echo "Category ID: " . $row['id'] . " - " . $row['name'] . "\n";
    }
    
    echo "\nChecking vendors...\n";
    $result = $pdo->query('SELECT id, name FROM vendors ORDER BY id');
    while($row = $result->fetch()) {
        echo "Vendor ID: " . $row['id'] . " - " . $row['name'] . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
