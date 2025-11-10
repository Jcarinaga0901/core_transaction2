<?php
require_once '../config/database.php';

try {
    $pdo = getDBConnection();
    
    // Check if commissions table exists
    $checkTable = $pdo->query("SHOW TABLES LIKE 'commissions'");
    if ($checkTable->rowCount() == 0) {
        echo "Creating commissions table...\n";
        
        $createTable = "
            CREATE TABLE commissions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                order_item_id INT NOT NULL,
                vendor_id INT NOT NULL,
                commission_rate DECIMAL(5,4) NOT NULL DEFAULT 0.1000,
                commission_amount DECIMAL(10,2) NOT NULL,
                settled TINYINT(1) NOT NULL DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_vendor_id (vendor_id),
                INDEX idx_order_item_id (order_item_id),
                INDEX idx_settled (settled),
                INDEX idx_created_at (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
        
        $pdo->exec($createTable);
        echo "✅ Commissions table created successfully!\n";
    } else {
        echo "✅ Commissions table already exists.\n";
    }
    
    // Check if delivery_requests table exists
    $checkDeliveryTable = $pdo->query("SHOW TABLES LIKE 'delivery_requests'");
    if ($checkDeliveryTable->rowCount() == 0) {
        echo "Creating delivery_requests table...\n";
        
        $createDeliveryTable = "
            CREATE TABLE delivery_requests (
                id INT AUTO_INCREMENT PRIMARY KEY,
                order_id INT NOT NULL,
                customer_name VARCHAR(255) NOT NULL,
                customer_phone VARCHAR(20) NOT NULL,
                delivery_address TEXT NOT NULL,
                delivery_date DATE NOT NULL,
                delivery_time VARCHAR(50) NOT NULL,
                status ENUM('pending', 'in_transit', 'delivered', 'failed', 'returned') DEFAULT 'pending',
                delivery_fee DECIMAL(10,2) DEFAULT 0.00,
                tracking_number VARCHAR(100) NULL,
                courier_name VARCHAR(100) NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_order_id (order_id),
                INDEX idx_status (status),
                INDEX idx_created_at (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
        
        $pdo->exec($createDeliveryTable);
        echo "✅ Delivery requests table created successfully!\n";
    } else {
        echo "✅ Delivery requests table already exists.\n";
    }
    
    echo "\n🎉 Database setup complete! Commission system is ready.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
