<?php
// Vendors API - Handle vendor/shop operations
require_once '../config/database.php';
require_once '../includes/notification_helper.php';

header('Content-Type: application/json');

try {
    $pdo = getDBConnection();
    
    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    
    switch ($action) {
        case 'upload_logo':
            uploadLogo($pdo);
            break;
            
        case 'update_shop':
            updateShop($pdo);
            break;
            
        case 'get_shop_details':
            getShopDetails($pdo);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
    
} catch (Exception $e) {
    error_log("Vendors API Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error occurred']);
}

function uploadLogo($pdo) {
    try {
        $vendor_id = $_POST['vendor_id'] ?? null;
        
        // Debug logging
        error_log("Logo upload - vendor_id: " . $vendor_id);
        error_log("Logo upload - POST data: " . print_r($_POST, true));
        
        // If no vendor_id provided or invalid, try to get from session
        if (!$vendor_id || $vendor_id == '0') {
            session_start();
            $vendor_id = $_SESSION['vendor_id'] ?? null;
            error_log("Logo upload - trying vendor_id from session: " . $vendor_id);
        }
        
        if (!$vendor_id || $vendor_id == '0') {
            echo json_encode(['success' => false, 'message' => 'Vendor ID is required and must be valid']);
            return;
        }
        
        // Check if vendor exists
        $stmt = $pdo->prepare("SELECT * FROM vendors WHERE id = ?");
        $stmt->execute([$vendor_id]);
        $vendor = $stmt->fetch();
        
        if (!$vendor) {
            echo json_encode(['success' => false, 'message' => 'Vendor not found']);
            return;
        }
        
        // Handle file upload
        if (!isset($_FILES['logo']) || $_FILES['logo']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
            return;
        }
        
        $file = $_FILES['logo'];
        
        // Validate file type
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $file_type = mime_content_type($file['tmp_name']);
        
        if (!in_array($file_type, $allowed_types)) {
            echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPEG, PNG, GIF, and WebP are allowed']);
            return;
        }
        
        // Validate file size (5MB max)
        if ($file['size'] > 5 * 1024 * 1024) {
            echo json_encode(['success' => false, 'message' => 'File too large. Maximum size is 5MB']);
            return;
        }
        
        // Create uploads/logos directory if it doesn't exist
        $upload_dir = '../uploads/logos/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        // Generate unique filename
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'logo_' . $vendor_id . '_' . time() . '.' . $file_extension;
        $file_path = $upload_dir . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            // Update vendor record with logo path
            $logo_url = 'uploads/logos/' . $filename;
            $stmt = $pdo->prepare("UPDATE vendors SET logo_url = ? WHERE id = ?");
            $stmt->execute([$logo_url, $vendor_id]);
            
            // Store in session for immediate display
            session_start();
            $_SESSION['temp_logo_url'] = $logo_url;
            $_SESSION['temp_logo_vendor_id'] = $vendor_id;
            
            echo json_encode([
                'success' => true, 
                'logo_url' => $logo_url
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to upload file']);
        }
        
    } catch (Exception $e) {
        error_log("Upload logo error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Upload failed: ' . $e->getMessage()]);
    }
}

function updateShop($pdo) {
    try {
        $vendor_id = $_POST['vendor_id'] ?? null;
        
        if (!$vendor_id) {
            echo json_encode(['success' => false, 'message' => 'Vendor ID is required']);
            return;
        }
        
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $website = trim($_POST['website'] ?? '');
        $description = trim($_POST['description'] ?? '');
        
        if (empty($name)) {
            echo json_encode(['success' => false, 'message' => 'Shop name is required']);
            return;
        }
        
        // Update vendor information
        $stmt = $pdo->prepare("UPDATE vendors SET 
            name = ?, 
            email = ?, 
            phone = ?, 
            address = ?, 
            website = ?, 
            description = ?,
            updated_at = NOW()
            WHERE id = ?");
        
        $stmt->execute([$name, $email, $phone, $address, $website, $description, $vendor_id]);
        
        echo json_encode([
            'success' => true, 
            'message' => 'Shop details updated successfully'
        ]);
        
    } catch (Exception $e) {
        error_log("Update shop error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Update failed: ' . $e->getMessage()]);
    }
}

function getShopDetails($pdo) {
    try {
        $vendor_id = $_GET['vendor_id'] ?? $_POST['vendor_id'] ?? null;
        
        if (!$vendor_id) {
            echo json_encode(['success' => false, 'message' => 'Vendor ID is required']);
            return;
        }
        
        $stmt = $pdo->prepare("SELECT * FROM vendors WHERE id = ?");
        $stmt->execute([$vendor_id]);
        $vendor = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$vendor) {
            echo json_encode(['success' => false, 'message' => 'Vendor not found']);
            return;
        }
        
        echo json_encode([
            'success' => true,
            'vendor' => $vendor
        ]);
        
    } catch (Exception $e) {
        error_log("Get shop details error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Failed to get shop details']);
    }
}
?>
