<?php
/**
 * Two-Factor Authentication (2FA) Database Setup
 * Creates tables for SMS OTP-based 2FA system
 */

require_once '../config/database.php';

try {
    $db = getCT1Connection();
    
    echo "Setting up 2FA tables...\n\n";
    
    // Create 2FA settings table
    $sql1 = "CREATE TABLE IF NOT EXISTS user_2fa_settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        is_enabled TINYINT(1) DEFAULT 0,
        phone_number VARCHAR(20) NOT NULL,
        phone_verified TINYINT(1) DEFAULT 0,
        backup_codes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY unique_user (user_id),
        FOREIGN KEY (user_id) REFERENCES vendors(id) ON DELETE CASCADE
    )";
    
    $db->exec($sql1);
    echo "✓ Created user_2fa_settings table\n";
    
    // Create OTP codes table
    $sql2 = "CREATE TABLE IF NOT EXISTS otp_codes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        phone_number VARCHAR(20) NOT NULL,
        otp_code VARCHAR(6) NOT NULL,
        purpose ENUM('login', 'phone_verification', 'password_reset') DEFAULT 'login',
        is_used TINYINT(1) DEFAULT 0,
        attempts INT DEFAULT 0,
        expires_at DATETIME NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_user_purpose (user_id, purpose),
        INDEX idx_expiry (expires_at)
    )";
    
    $db->exec($sql2);
    echo "✓ Created otp_codes table\n";
    
    // Create 2FA login attempts table
    $sql3 = "CREATE TABLE IF NOT EXISTS two_fa_attempts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        ip_address VARCHAR(45) NOT NULL,
        attempt_type ENUM('otp_sent', 'otp_verified', 'otp_failed', 'locked') DEFAULT 'otp_sent',
        status ENUM('success', 'failed') DEFAULT 'success',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_user_ip (user_id, ip_address),
        INDEX idx_created (created_at)
    )";
    
    $db->exec($sql3);
    echo "✓ Created two_fa_attempts table\n";
    
    // Add phone number column to vendors table if it doesn't exist
    try {
        $checkColumn = $db->query("SHOW COLUMNS FROM vendors LIKE 'phone_number'");
        if ($checkColumn->rowCount() == 0) {
            $db->exec("ALTER TABLE vendors ADD COLUMN phone_number VARCHAR(20) DEFAULT NULL AFTER email");
            echo "✓ Added phone_number column to vendors table\n";
        } else {
            echo "✓ phone_number column already exists in vendors table\n";
        }
    } catch (Exception $e) {
        echo "Note: " . $e->getMessage() . "\n";
    }
    
    echo "\n✅ 2FA setup completed successfully!\n";
    echo "\nNext steps:\n";
    echo "1. Configure SMS gateway credentials in api/sms_gateway.php\n";
    echo "2. Enable 2FA in user settings\n";
    echo "3. Test the OTP flow\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}


