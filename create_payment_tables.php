<?php
require_once '../config/database.php';

try {
    $pdo = getDBConnection();
    
    // Create payments table
    $pdo->exec("CREATE TABLE IF NOT EXISTS payments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        vendor_id INT NOT NULL,
        customer_id INT NOT NULL,
        payment_method ENUM('bank_transfer', 'cod', 'ewallet', 'card') NOT NULL,
        payment_status ENUM('pending', 'proof_submitted', 'verified', 'paid', 'rejected', 'refunded', 'partial_payment') NOT NULL DEFAULT 'pending',
        amount DECIMAL(10,2) NOT NULL,
        currency VARCHAR(3) DEFAULT 'PHP',
        transaction_reference VARCHAR(100) NULL,
        proof_of_payment JSON NULL,
        payment_gateway_response JSON NULL,
        verified_by VARCHAR(100) NULL,
        verified_at DATETIME NULL,
        rejection_reason TEXT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
        INDEX (order_id),
        INDEX (vendor_id),
        INDEX (customer_id),
        INDEX (payment_status),
        INDEX (payment_method),
        INDEX (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Create payment_methods table
    $pdo->exec("CREATE TABLE IF NOT EXISTS payment_methods (
        id INT AUTO_INCREMENT PRIMARY KEY,
        vendor_id INT NOT NULL,
        method_type ENUM('bank_transfer', 'cod', 'ewallet', 'card') NOT NULL,
        is_active BOOLEAN DEFAULT TRUE,
        bank_account_details JSON NULL,
        ewallet_details JSON NULL,
        gateway_credentials JSON NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
        INDEX (vendor_id),
        INDEX (method_type),
        INDEX (is_active)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Create payment_timeline table
    $pdo->exec("CREATE TABLE IF NOT EXISTS payment_timeline (
        id INT AUTO_INCREMENT PRIMARY KEY,
        payment_id INT NOT NULL,
        action VARCHAR(50) NOT NULL,
        notes TEXT NULL,
        created_by VARCHAR(100) NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (payment_id) REFERENCES payments(id) ON DELETE CASCADE,
        INDEX (payment_id),
        INDEX (action),
        INDEX (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Create orders table if it doesn't exist (for foreign key reference)
    $pdo->exec("CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_number VARCHAR(50) UNIQUE NOT NULL,
        customer_id INT NOT NULL,
        vendor_id INT NOT NULL,
        total_amount DECIMAL(10,2) NOT NULL,
        status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') NOT NULL DEFAULT 'pending',
        shipping_address TEXT NOT NULL,
        billing_address TEXT NULL,
        notes TEXT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
        INDEX (customer_id),
        INDEX (vendor_id),
        INDEX (status),
        INDEX (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Create order_items table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        total_price DECIMAL(10,2) NOT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
        INDEX (order_id),
        INDEX (product_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    echo "Payment monitoring tables created successfully!\n";
    echo "- payments table\n";
    echo "- payment_methods table\n";
    echo "- payment_timeline table\n";
    echo "- orders table (if not exists)\n";
    echo "- order_items table (if not exists)\n";

} catch (PDOException $e) {
    echo "Error creating tables: " . $e->getMessage() . "\n";
}
?>
