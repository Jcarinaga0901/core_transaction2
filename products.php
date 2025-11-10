<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');
require_once '../config/database.php';
require_once '../includes/auth.php';

// Check if user is authenticated
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    $pdo = getDBConnection();
    $user_id = $_SESSION['user_id'];
    $vendor_id = $_SESSION['vendor_id'] ?? null;
    
    // Always verify and fix vendor_id from user record to ensure consistency
    $stmt = $pdo->prepare("SELECT vendor_id FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    
    if ($user && $user['vendor_id']) {
        // Use vendor_id from database (most reliable)
        $vendor_id = $user['vendor_id'];
        $_SESSION['vendor_id'] = $vendor_id;
        error_log("API: Using vendor_id from database: " . $vendor_id);
    } else {
        // If user has no vendor_id, assign to first available vendor
        $stmt = $pdo->query("SELECT id FROM vendors ORDER BY id LIMIT 1");
        $vendor = $stmt->fetch();
        if ($vendor) {
            $vendor_id = $vendor['id'];
            $_SESSION['vendor_id'] = $vendor_id;
            
            // Update user record with vendor_id
            $stmt = $pdo->prepare("UPDATE users SET vendor_id = ? WHERE id = ?");
            $stmt->execute([$vendor_id, $user_id]);
            error_log("API: Assigned user to vendor_id: " . $vendor_id);
        } else {
            error_log("API: No vendors available");
            echo json_encode(['success' => false, 'message' => 'No vendors available. Please contact administrator.']);
            exit();
        }
    }

    switch ($action) {
        case 'top_products':
            $limit = $_GET['limit'] ?? 5;
            
            if ($vendor_id) {
                $stmt = $pdo->prepare("
                    SELECT p.*, 
                           COALESCE(SUM(oi.quantity), 0) as total_sold
                    FROM products p 
                    LEFT JOIN order_items oi ON p.id = oi.product_id 
                    WHERE p.vendor_id = ? 
                    GROUP BY p.id 
                    ORDER BY total_sold DESC, p.name ASC 
                    LIMIT ?
                ");
                $stmt->execute([$vendor_id, $limit]);
            } else {
                $stmt = $pdo->prepare("
                    SELECT p.*, 
                           COALESCE(SUM(oi.quantity), 0) as total_sold
                    FROM products p 
                    LEFT JOIN order_items oi ON p.id = oi.product_id 
                    GROUP BY p.id 
                    ORDER BY total_sold DESC, p.name ASC 
                    LIMIT ?
                ");
                $stmt->execute([$limit]);
            }
            
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'products' => $products]);
            break;
            
        case 'list':
            $page = $_GET['page'] ?? 1;
            $limit = $_GET['limit'] ?? 10;
            $offset = ($page - 1) * $limit;
            
            if ($vendor_id) {
                $stmt = $pdo->prepare("
                    SELECT p.*, v.name as vendor_name 
                    FROM products p 
                    LEFT JOIN vendors v ON p.vendor_id = v.id 
                    WHERE p.vendor_id = ? 
                    ORDER BY p.created_at DESC 
                    LIMIT ? OFFSET ?
                ");
                $stmt->execute([$vendor_id, $limit, $offset]);
            } else {
                $stmt = $pdo->prepare("
                    SELECT p.*, v.name as vendor_name 
                    FROM products p 
                    LEFT JOIN vendors v ON p.vendor_id = v.id 
                    ORDER BY p.created_at DESC 
                    LIMIT ? OFFSET ?
                ");
                $stmt->execute([$limit, $offset]);
            }
            
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'products' => $products]);
            break;
            
        case 'create':
            // Handle product creation
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $price = $_POST['price'] ?? 0;
            $stock_quantity = $_POST['stock_quantity'] ?? 0; // Fixed field name
            $category_id = $_POST['category_id'] ?? null;
            $subcategory_id = $_POST['subcategory_id'] ?? null;
            $custom_category_name = $_POST['custom_category_name'] ?? '';
            $custom_subcategory_name = $_POST['custom_subcategory_name'] ?? '';
            
            // Debug: Log the vendor_id being used
            error_log("Creating product with vendor_id: " . $vendor_id);
            error_log("Product data: name=" . $name . ", price=" . $price . ", category_id=" . $category_id . ", subcategory_id=" . $subcategory_id);
            
            // Validate required fields
            if (empty($name) || empty($price)) {
                echo json_encode(['success' => false, 'message' => 'Product name and price are required']);
                exit();
            }
            
            // Validate vendor_id exists
            if (!$vendor_id) {
                error_log("ERROR: vendor_id is null or empty");
                echo json_encode(['success' => false, 'message' => 'Vendor ID is required but not found']);
                exit();
            }
            
            // Verify vendor exists in database
            $stmt = $pdo->prepare("SELECT id FROM vendors WHERE id = ?");
            $stmt->execute([$vendor_id]);
            $vendorCheck = $stmt->fetch();
            if (!$vendorCheck) {
                error_log("ERROR: vendor_id " . $vendor_id . " does not exist in vendors table");
                echo json_encode(['success' => false, 'message' => 'Invalid vendor ID: ' . $vendor_id]);
                exit();
            }
            
            // Handle custom category creation
            if (!empty($custom_category_name) && empty($category_id)) {
                $stmt = $pdo->prepare("INSERT INTO categories (name, is_active) VALUES (?, 1)");
                $stmt->execute([$custom_category_name]);
                $category_id = $pdo->lastInsertId();
            }
            
            // Handle custom subcategory creation
            if (!empty($custom_subcategory_name) && empty($subcategory_id) && $category_id) {
                $stmt = $pdo->prepare("INSERT INTO subcategories (name, category_id) VALUES (?, ?)");
                $stmt->execute([$custom_subcategory_name, $category_id]);
                $subcategory_id = $pdo->lastInsertId();
            }
            
            // Handle image upload
            $image_path = 'uploads/products/default.svg'; // Default image
            if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = '../uploads/products/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $file_extension = pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION);
                $filename = 'p_' . uniqid() . '.' . $file_extension;
                $file_path = $upload_dir . $filename;
                
                if (move_uploaded_file($_FILES['image_file']['tmp_name'], $file_path)) {
                    $image_path = 'uploads/products/' . $filename;
                }
            } elseif (!empty($_POST['image_url'])) {
                // Use provided image URL
                $image_path = $_POST['image_url'];
            }
            
            // Insert product with 'pending' status for admin approval
            $stmt = $pdo->prepare("
                INSERT INTO products (name, description, price, stock_quantity, category_id, subcategory_id, image_url, vendor_id, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
            ");
            $stmt->execute([$name, $description, $price, $stock_quantity, $category_id, $subcategory_id, $image_path, $vendor_id]);
            
            echo json_encode(['success' => true, 'message' => 'Product submitted for admin approval']);
            break;
            
        case 'update':
            // Handle product update
            $id = $_POST['id'] ?? '';
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $price = $_POST['price'] ?? 0;
            $stock_quantity = $_POST['stock_quantity'] ?? 0;
            $category_id = $_POST['category_id'] ?? null;
            $subcategory_id = $_POST['subcategory_id'] ?? null;
            $custom_category_name = $_POST['custom_category_name'] ?? '';
            $custom_subcategory_name = $_POST['custom_subcategory_name'] ?? '';
            
            if (empty($id) || empty($name) || empty($price)) {
                echo json_encode(['success' => false, 'message' => 'Product ID, name and price are required']);
                exit();
            }
            
            // Handle custom category creation
            if (!empty($custom_category_name) && empty($category_id)) {
                $stmt = $pdo->prepare("INSERT INTO categories (name, is_active) VALUES (?, 1)");
                $stmt->execute([$custom_category_name]);
                $category_id = $pdo->lastInsertId();
            }
            
            // Handle custom subcategory creation
            if (!empty($custom_subcategory_name) && empty($subcategory_id) && $category_id) {
                $stmt = $pdo->prepare("INSERT INTO subcategories (name, category_id) VALUES (?, ?)");
                $stmt->execute([$custom_subcategory_name, $category_id]);
                $subcategory_id = $pdo->lastInsertId();
            }
            
            // Handle image upload
            $image_path = null; // Don't update image if not provided
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = '../uploads/products/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $filename = 'p_' . uniqid() . '.' . $file_extension;
                $file_path = $upload_dir . $filename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $file_path)) {
                    $image_path = 'uploads/products/' . $filename;
                }
            }
            
            // Update product
            if ($image_path) {
                $stmt = $pdo->prepare("
                    UPDATE products 
                    SET name = ?, description = ?, price = ?, stock_quantity = ?, category_id = ?, subcategory_id = ?, image_url = ?, updated_at = NOW()
                    WHERE id = ? AND vendor_id = ?
                ");
                $stmt->execute([$name, $description, $price, $stock_quantity, $category_id, $subcategory_id, $image_path, $id, $vendor_id]);
            } else {
                $stmt = $pdo->prepare("
                    UPDATE products 
                    SET name = ?, description = ?, price = ?, stock_quantity = ?, category_id = ?, subcategory_id = ?, updated_at = NOW()
                    WHERE id = ? AND vendor_id = ?
                ");
                $stmt->execute([$name, $description, $price, $stock_quantity, $category_id, $subcategory_id, $id, $vendor_id]);
            }
            
            echo json_encode(['success' => true, 'message' => 'Product updated successfully']);
            break;
            
        case 'delete':
            // Handle product deletion
            $id = $_POST['id'] ?? '';
            
            if (empty($id)) {
                echo json_encode(['success' => false, 'message' => 'Product ID is required']);
                exit();
            }
            
            $stmt = $pdo->prepare("DELETE FROM products WHERE id = ? AND vendor_id = ?");
            $stmt->execute([$id, $vendor_id]);
            
            echo json_encode(['success' => true, 'message' => 'Product deleted successfully']);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    
} catch (Exception $e) {
    error_log("Products API Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
