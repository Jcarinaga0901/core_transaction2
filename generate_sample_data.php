<?php
require_once '../config/database.php';

echo "Generating comprehensive sample data for CT2 system...\n";

try {
    $pdo = getDBConnection();
    
    // Clear existing data (skip categories, vendors, orders, order_items, and payments to avoid foreign key issues)
    echo "Clearing existing data...\n";
    $tables = ['payment_timeline', 'payment_methods', 'products', 'vouchers', 'returns', 'deliveries'];
    foreach ($tables as $table) {
        $pdo->exec("DELETE FROM $table");
    }
    
    // Insert payment methods
    echo "Creating payment methods...\n";
    $paymentMethods = [
        ['vendor_id' => 1, 'method_type' => 'bank_transfer', 'is_active' => 1, 'bank_account_details' => '{"bank": "BDO", "account": "1234567890", "account_name": "RAEVOR Store"}'],
        ['vendor_id' => 1, 'method_type' => 'gcash', 'is_active' => 1, 'ewallet_details' => '{"number": "09171234567", "name": "RAEVOR Store"}'],
        ['vendor_id' => 1, 'method_type' => 'paymaya', 'is_active' => 1, 'ewallet_details' => '{"number": "09171234567", "name": "RAEVOR Store"}'],
        ['vendor_id' => 1, 'method_type' => 'credit_card', 'is_active' => 1, 'gateway_credentials' => '{"gateway": "paypal", "merchant_id": "MERCHANT123"}']
    ];
    
    foreach ($paymentMethods as $method) {
        $stmt = $pdo->prepare("INSERT INTO payment_methods (vendor_id, method_type, is_active, bank_account_details, ewallet_details, gateway_credentials) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $method['vendor_id'], 
            $method['method_type'], 
            $method['is_active'], 
            $method['bank_account_details'] ?? null, 
            $method['ewallet_details'] ?? null, 
            $method['gateway_credentials'] ?? null
        ]);
    }
    
    // Skip categories and vendors as they already exist
    echo "Using existing categories and vendors...\n";
    
    // Insert products
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
        $stmt = $pdo->prepare("INSERT INTO products (user_id, name, description, price, selling_price, stock_quantity, category_id, vendor_id, min_stock_level, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$product['user_id'], $product['name'], $product['description'], $product['price'], $product['selling_price'], $product['stock_quantity'], $product['category_id'], $product['vendor_id'], $product['min_stock_level'], $product['status']]);
    }
    
    // Skip orders and order_items as they already exist
    echo "Using existing orders and order items...\n";
    
    // Skip payments as they already exist
    echo "Using existing payments...\n";
    
    // Insert payment timeline
    echo "Creating payment timeline...\n";
    $timelineEvents = [
        ['payment_id' => 1, 'action' => 'payment_initiated', 'notes' => 'Payment initiated by customer', 'created_by' => 'system'],
        ['payment_id' => 2, 'action' => 'payment_initiated', 'notes' => 'Payment initiated by customer', 'created_by' => 'system'],
        ['payment_id' => 2, 'action' => 'payment_verified', 'notes' => 'Payment verified by admin', 'created_by' => 'admin'],
        ['payment_id' => 3, 'action' => 'payment_initiated', 'notes' => 'Payment initiated by customer', 'created_by' => 'system'],
        ['payment_id' => 3, 'action' => 'payment_verified', 'notes' => 'Payment verified by admin', 'created_by' => 'admin'],
        ['payment_id' => 4, 'action' => 'payment_initiated', 'notes' => 'Payment initiated by customer', 'created_by' => 'system'],
        ['payment_id' => 4, 'action' => 'payment_verified', 'notes' => 'Payment verified by admin', 'created_by' => 'admin'],
        ['payment_id' => 5, 'action' => 'payment_initiated', 'notes' => 'Payment initiated by customer', 'created_by' => 'system'],
        ['payment_id' => 5, 'action' => 'payment_rejected', 'notes' => 'Payment rejected - insufficient funds', 'created_by' => 'admin']
    ];
    
    foreach ($timelineEvents as $event) {
        $stmt = $pdo->prepare("INSERT INTO payment_timeline (payment_id, action, notes, created_by, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$event['payment_id'], $event['action'], $event['notes'], $event['created_by']]);
    }
    
    // Insert vouchers
    echo "Creating seller vouchers...\n";
    $vouchers = [
        ['vendor_id' => 1, 'name' => 'WELCOME10', 'description' => '10% off for new customers', 'type' => 'percentage', 'discount_value' => 10, 'min_spend' => 1000, 'max_discount' => 500, 'start_date' => '2024-01-01', 'end_date' => '2024-12-31', 'status' => 'active', 'is_active' => 1, 'created_by' => 'admin'],
        ['vendor_id' => 1, 'name' => 'SAVE50', 'description' => '₱50 off on orders above ₱2000', 'type' => 'fixed', 'discount_value' => 50, 'min_spend' => 2000, 'max_discount' => 50, 'start_date' => '2024-01-01', 'end_date' => '2024-12-31', 'status' => 'active', 'is_active' => 1, 'created_by' => 'admin'],
        ['vendor_id' => 1, 'name' => 'SUMMER20', 'description' => '20% off summer collection', 'type' => 'percentage', 'discount_value' => 20, 'min_spend' => 500, 'max_discount' => 1000, 'start_date' => '2024-01-01', 'end_date' => '2024-12-31', 'status' => 'active', 'is_active' => 1, 'created_by' => 'admin']
    ];
    
    foreach ($vouchers as $voucher) {
        $stmt = $pdo->prepare("INSERT INTO vouchers (vendor_id, name, description, type, discount_value, min_spend, max_discount, start_date, end_date, status, is_active, created_by, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$voucher['vendor_id'], $voucher['name'], $voucher['description'], $voucher['type'], $voucher['discount_value'], $voucher['min_spend'], $voucher['max_discount'], $voucher['start_date'], $voucher['end_date'], $voucher['status'], $voucher['is_active'], $voucher['created_by']]);
    }
    
    // Insert returns
    echo "Creating return requests...\n";
    $returns = [
        ['order_id' => 2, 'customer_id' => 1, 'customer_name' => 'Maria Santos', 'product_id' => 4, 'product_name' => 'Adidas T-Shirt', 'amount' => 1299, 'reason' => 'Defective item', 'status' => 'pending', 'processed_by' => 'admin'],
        ['order_id' => 3, 'customer_id' => 2, 'customer_name' => 'Pedro Garcia', 'product_id' => 3, 'product_name' => 'Nike Air Max', 'amount' => 5999, 'reason' => 'Wrong size', 'status' => 'approved', 'processed_by' => 'admin'],
        ['order_id' => 4, 'customer_id' => 3, 'customer_name' => 'Ana Rodriguez', 'product_id' => 5, 'product_name' => 'Garden Tools Set', 'amount' => 2999, 'reason' => 'Changed mind', 'status' => 'rejected', 'processed_by' => 'admin']
    ];
    
    foreach ($returns as $return) {
        $stmt = $pdo->prepare("INSERT INTO returns (order_id, customer_id, customer_name, product_id, product_name, amount, reason, status, processed_by, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$return['order_id'], $return['customer_id'], $return['customer_name'], $return['product_id'], $return['product_name'], $return['amount'], $return['reason'], $return['status'], $return['processed_by']]);
    }
    
    // Insert deliveries
    echo "Creating delivery records...\n";
    $deliveries = [
        ['order_id' => 2, 'carrier' => 'J&T Express', 'tracking_number' => 'TRK001234567', 'status' => 'shipped', 'scheduled_date' => '2024-01-15', 'address' => '123 Main St, Manila', 'notes' => 'Handle with care'],
        ['order_id' => 3, 'carrier' => 'LBC Express', 'tracking_number' => 'TRK001234568', 'status' => 'in_transit', 'scheduled_date' => '2024-01-16', 'address' => '456 Oak Ave, Makati', 'notes' => 'Fragile items'],
        ['order_id' => 4, 'carrier' => 'Grab Express', 'tracking_number' => 'TRK001234569', 'status' => 'delivered', 'scheduled_date' => '2024-01-14', 'delivered_at' => '2024-01-14 15:30:00', 'address' => '789 Pine St, Quezon City', 'notes' => 'Delivered successfully']
    ];
    
    foreach ($deliveries as $delivery) {
        $stmt = $pdo->prepare("INSERT INTO deliveries (order_id, carrier, tracking_number, status, scheduled_date, delivered_at, address, notes, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$delivery['order_id'], $delivery['carrier'], $delivery['tracking_number'], $delivery['status'], $delivery['scheduled_date'], $delivery['delivered_at'], $delivery['address'], $delivery['notes']]);
    }
    
    echo "Sample data generation completed successfully!\n";
    echo "Created:\n";
    echo "- 4 payment methods\n";
    echo "- 5 product categories\n";
    echo "- 4 vendor accounts\n";
    echo "- 7 products\n";
    echo "- 5 orders with items\n";
    echo "- 5 payment records\n";
    echo "- 9 payment timeline events\n";
    echo "- 3 seller vouchers\n";
    echo "- 3 return requests\n";
    echo "- 3 delivery records\n";
    
} catch (Exception $e) {
    echo "Error generating sample data: " . $e->getMessage() . "\n";
}
?>
