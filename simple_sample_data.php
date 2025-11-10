<?php
require_once '../config/database.php';

echo "Generating essential sample data for CT2 system...\n";

try {
    $pdo = getDBConnection();
    
    echo "Creating essential sample data...\n";
    
    // Insert payment methods (if not exists)
    echo "Creating payment methods...\n";
    $paymentMethods = [
        ['vendor_id' => 1, 'method_type' => 'bank_transfer', 'is_active' => 1, 'bank_account_details' => '{"bank": "BDO", "account": "1234567890", "account_name": "RAEVOR Store"}'],
        ['vendor_id' => 1, 'method_type' => 'gcash', 'is_active' => 1, 'ewallet_details' => '{"number": "09171234567", "name": "RAEVOR Store"}'],
        ['vendor_id' => 1, 'method_type' => 'paymaya', 'is_active' => 1, 'ewallet_details' => '{"number": "09171234567", "name": "RAEVOR Store"}'],
        ['vendor_id' => 1, 'method_type' => 'credit_card', 'is_active' => 1, 'gateway_credentials' => '{"gateway": "paypal", "merchant_id": "MERCHANT123"}']
    ];
    
    foreach ($paymentMethods as $method) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO payment_methods (vendor_id, method_type, is_active, bank_account_details, ewallet_details, gateway_credentials) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $method['vendor_id'], 
            $method['method_type'], 
            $method['is_active'], 
            $method['bank_account_details'] ?? null, 
            $method['ewallet_details'] ?? null, 
            $method['gateway_credentials'] ?? null
        ]);
    }
    
    // Insert products (if not exists)
    echo "Creating products...\n";
    $products = [
        ['user_id' => 1, 'name' => 'iPhone 15 Pro', 'description' => 'Latest iPhone with advanced features', 'price' => 89990, 'selling_price' => 89990, 'stock_quantity' => 50, 'category_id' => 21, 'vendor_id' => 27, 'min_stock_level' => 10, 'status' => 'active'],
        ['user_id' => 1, 'name' => 'Samsung Galaxy S24', 'description' => 'Premium Android smartphone', 'price' => 69990, 'selling_price' => 69990, 'stock_quantity' => 30, 'category_id' => 21, 'vendor_id' => 27, 'min_stock_level' => 5, 'status' => 'active'],
        ['user_id' => 1, 'name' => 'Nike Air Max', 'description' => 'Comfortable running shoes', 'price' => 5999, 'selling_price' => 5999, 'stock_quantity' => 100, 'category_id' => 22, 'vendor_id' => 28, 'min_stock_level' => 20, 'status' => 'active'],
        ['user_id' => 1, 'name' => 'Adidas T-Shirt', 'description' => 'Comfortable cotton t-shirt', 'price' => 1299, 'selling_price' => 1299, 'stock_quantity' => 200, 'category_id' => 22, 'vendor_id' => 28, 'min_stock_level' => 50, 'status' => 'active'],
        ['user_id' => 1, 'name' => 'Garden Tools Set', 'description' => 'Complete gardening tool set', 'price' => 2999, 'selling_price' => 2999, 'stock_quantity' => 75, 'category_id' => 23, 'vendor_id' => 29, 'min_stock_level' => 15, 'status' => 'active'],
        ['user_id' => 1, 'name' => 'Basketball', 'description' => 'Official size basketball', 'price' => 1999, 'selling_price' => 1999, 'stock_quantity' => 50, 'category_id' => 24, 'vendor_id' => 30, 'min_stock_level' => 10, 'status' => 'active'],
        ['user_id' => 1, 'name' => 'Programming Book', 'description' => 'Learn Python programming', 'price' => 1999, 'selling_price' => 1999, 'stock_quantity' => 25, 'category_id' => 25, 'vendor_id' => 27, 'min_stock_level' => 5, 'status' => 'active']
    ];
    
    foreach ($products as $product) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO products (user_id, name, description, price, selling_price, stock_quantity, category_id, vendor_id, min_stock_level, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$product['user_id'], $product['name'], $product['description'], $product['price'], $product['selling_price'], $product['stock_quantity'], $product['category_id'], $product['vendor_id'], $product['min_stock_level'], $product['status']]);
    }
    
    // Insert vouchers (if not exists)
    echo "Creating seller vouchers...\n";
    $vouchers = [
        ['vendor_id' => 1, 'name' => 'WELCOME10', 'description' => '10% off for new customers', 'type' => 'percentage', 'discount_value' => 10, 'min_spend' => 1000, 'max_discount' => 500, 'start_date' => '2024-01-01', 'end_date' => '2024-12-31', 'status' => 'active', 'is_active' => 1, 'created_by' => 'admin'],
        ['vendor_id' => 1, 'name' => 'SAVE50', 'description' => '₱50 off on orders above ₱2000', 'type' => 'fixed', 'discount_value' => 50, 'min_spend' => 2000, 'max_discount' => 50, 'start_date' => '2024-01-01', 'end_date' => '2024-12-31', 'status' => 'active', 'is_active' => 1, 'created_by' => 'admin'],
        ['vendor_id' => 1, 'name' => 'SUMMER20', 'description' => '20% off summer collection', 'type' => 'percentage', 'discount_value' => 20, 'min_spend' => 500, 'max_discount' => 1000, 'start_date' => '2024-01-01', 'end_date' => '2024-12-31', 'status' => 'active', 'is_active' => 1, 'created_by' => 'admin']
    ];
    
    foreach ($vouchers as $voucher) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO vouchers (vendor_id, name, description, type, discount_value, min_spend, max_discount, start_date, end_date, status, is_active, created_by, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$voucher['vendor_id'], $voucher['name'], $voucher['description'], $voucher['type'], $voucher['discount_value'], $voucher['min_spend'], $voucher['max_discount'], $voucher['start_date'], $voucher['end_date'], $voucher['status'], $voucher['is_active'], $voucher['created_by']]);
    }
    
    echo "Essential sample data generation completed successfully!\n";
    echo "Created:\n";
    echo "- 4 payment methods\n";
    echo "- 7 products\n";
    echo "- 3 seller vouchers\n";
    
} catch (Exception $e) {
    echo "Error generating sample data: " . $e->getMessage() . "\n";
}
?>
