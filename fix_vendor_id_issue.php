<?php
require_once 'config/database.php';

try {
    $pdo = getDBConnection();
    
    echo "Checking current vendor situation...\n";
    
    // Check if there are vendors
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM vendors");
    $result = $stmt->fetch();
    echo "Current vendors count: " . $result['count'] . "\n";
    
    if ($result['count'] > 0) {
        echo "Existing vendors:\n";
        $stmt = $pdo->query("SELECT id, name FROM vendors LIMIT 5");
        while ($row = $stmt->fetch()) {
            echo "ID: " . $row['id'] . ", Name: " . $row['name'] . "\n";
        }
    }
    
    // Check if there are users with vendor_id
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE vendor_id IS NOT NULL");
    $result = $stmt->fetch();
    echo "Users with vendor_id: " . $result['count'] . "\n";
    
    if ($result['count'] > 0) {
        echo "Users with vendor_id:\n";
        $stmt = $pdo->query("SELECT id, username, vendor_id FROM users WHERE vendor_id IS NOT NULL LIMIT 5");
        while ($row = $stmt->fetch()) {
            echo "User ID: " . $row['id'] . ", Username: " . $row['username'] . ", Vendor ID: " . $row['vendor_id'] . "\n";
        }
    }
    
    // If no vendors exist, create a default vendor
    if ($result['count'] == 0) {
        echo "No vendors found. Creating default vendor...\n";
        $stmt = $pdo->prepare("INSERT INTO vendors (name, email, phone, address, description, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute(['Default Vendor', 'default@vendor.com', '1234567890', 'Default Address', 'Default vendor for system']);
        $defaultVendorId = $pdo->lastInsertId();
        echo "Default vendor created with ID: " . $defaultVendorId . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>

