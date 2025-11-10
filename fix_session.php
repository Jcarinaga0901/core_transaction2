<?php
session_start();
require_once 'config/database.php';

echo "<h2>Session Fix Tool</h2>";

try {
    $pdo = getDBConnection();
    
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        echo "<p><strong>Current user_id:</strong> " . $user_id . "</p>";
        echo "<p><strong>Current session vendor_id:</strong> " . ($_SESSION['vendor_id'] ?? 'NOT SET') . "</p>";
        
        // Get correct vendor_id from database
        $stmt = $pdo->prepare("SELECT vendor_id FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
        
        if ($user && $user['vendor_id']) {
            $correct_vendor_id = $user['vendor_id'];
            echo "<p><strong>Correct vendor_id from database:</strong> " . $correct_vendor_id . "</p>";
            
            // Update session with correct vendor_id
            $_SESSION['vendor_id'] = $correct_vendor_id;
            echo "<p style='color: green;'><strong>✓ Session vendor_id updated to:</strong> " . $correct_vendor_id . "</p>";
            
            // Verify vendor exists
            $stmt = $pdo->prepare("SELECT name FROM vendors WHERE id = ?");
            $stmt->execute([$correct_vendor_id]);
            $vendor = $stmt->fetch();
            
            if ($vendor) {
                echo "<p style='color: green;'><strong>✓ Vendor verified:</strong> " . $vendor['name'] . "</p>";
            } else {
                echo "<p style='color: red;'><strong>✗ ERROR:</strong> Vendor ID " . $correct_vendor_id . " does not exist!</p>";
            }
        } else {
            echo "<p style='color: red;'><strong>✗ ERROR:</strong> User has no vendor_id in database!</p>";
        }
    } else {
        echo "<p style='color: red;'><strong>✗ ERROR:</strong> No user_id in session. Please log in first.</p>";
    }
    
    echo "<hr>";
    echo "<p><a href='product_catalog.php'>← Back to Product Catalog</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'><strong>ERROR:</strong> " . $e->getMessage() . "</p>";
}
?>
