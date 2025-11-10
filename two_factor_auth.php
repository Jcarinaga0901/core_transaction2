<?php
/**
 * Two-Factor Authentication API
 * Handles OTP generation, verification, and 2FA management
 */

session_start();
require_once '../config/database.php';
require_once 'sms_gateway.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    $db = getCT1Connection();
    
    switch ($action) {
        case 'generate_otp':
            generateOTP($db);
            break;
        
        case 'verify_otp':
            verifyOTP($db);
            break;
        
        case 'enable_2fa':
            enable2FA($db);
            break;
        
        case 'disable_2fa':
            disable2FA($db);
            break;
        
        case 'verify_phone':
            verifyPhone($db);
            break;
        
        case 'check_2fa_status':
            check2FAStatus($db);
            break;
        
        case 'resend_otp':
            resendOTP($db);
            break;
        
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

/**
 * Generate and send OTP
 */
function generateOTP($db) {
    $userId = $_POST['user_id'] ?? null;
    $phoneNumber = $_POST['phone_number'] ?? null;
    $purpose = $_POST['purpose'] ?? 'login';
    
    if (!$userId || !$phoneNumber) {
        echo json_encode(['success' => false, 'message' => 'User ID and phone number required']);
        return;
    }
    
    // Validate phone number
    if (!SMSGateway::validatePhoneNumber($phoneNumber)) {
        echo json_encode(['success' => false, 'message' => 'Invalid phone number format']);
        return;
    }
    
    // Check rate limiting (max 3 OTP per 15 minutes)
    $stmt = $db->prepare("
        SELECT COUNT(*) as count 
        FROM otp_codes 
        WHERE user_id = ? 
        AND created_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)
        AND purpose = ?
    ");
    $stmt->execute([$userId, $purpose]);
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    if ($count >= 3) {
        echo json_encode([
            'success' => false, 
            'message' => 'Too many OTP requests. Please wait 15 minutes.'
        ]);
        return;
    }
    
    // Generate 6-digit OTP
    $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    
    // Set expiry (5 minutes for login, 10 minutes for others)
    $expiryMinutes = $purpose === 'login' ? 5 : 10;
    $expiresAt = date('Y-m-d H:i:s', strtotime("+{$expiryMinutes} minutes"));
    
    // Save OTP to database
    $stmt = $db->prepare("
        INSERT INTO otp_codes (user_id, phone_number, otp_code, purpose, expires_at)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([$userId, $phoneNumber, $otpCode, $purpose, $expiresAt]);
    
    // Send SMS
    $smsGateway = new SMSGateway('mock'); // Change to 'semaphore' or 'twilio' in production
    $result = $smsGateway->sendOTP($phoneNumber, $otpCode, $purpose);
    
    if ($result['success']) {
        // Log attempt
        logAttempt($db, $userId, 'otp_sent', 'success');
        
        echo json_encode([
            'success' => true,
            'message' => 'OTP sent successfully',
            'expires_in' => $expiryMinutes * 60, // in seconds
            'debug' => $result // Remove in production
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to send OTP',
            'error' => $result['message']
        ]);
    }
}

/**
 * Verify OTP code
 */
function verifyOTP($db) {
    $userId = $_POST['user_id'] ?? null;
    $otpCode = $_POST['otp_code'] ?? null;
    $purpose = $_POST['purpose'] ?? 'login';
    
    if (!$userId || !$otpCode) {
        echo json_encode(['success' => false, 'message' => 'User ID and OTP code required']);
        return;
    }
    
    // Check account lockout (5 failed attempts in 30 minutes)
    $stmt = $db->prepare("
        SELECT COUNT(*) as count 
        FROM two_fa_attempts 
        WHERE user_id = ? 
        AND attempt_type = 'otp_failed'
        AND created_at > DATE_SUB(NOW(), INTERVAL 30 MINUTE)
    ");
    $stmt->execute([$userId]);
    $failedCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    if ($failedCount >= 5) {
        logAttempt($db, $userId, 'locked', 'failed');
        echo json_encode([
            'success' => false, 
            'message' => 'Account temporarily locked. Too many failed attempts.'
        ]);
        return;
    }
    
    // Find valid OTP
    $stmt = $db->prepare("
        SELECT * FROM otp_codes 
        WHERE user_id = ? 
        AND otp_code = ? 
        AND purpose = ?
        AND is_used = 0 
        AND expires_at > NOW()
        AND attempts < 3
        ORDER BY created_at DESC 
        LIMIT 1
    ");
    $stmt->execute([$userId, $otpCode, $purpose]);
    $otp = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$otp) {
        // Increment attempts for existing OTP
        $db->prepare("
            UPDATE otp_codes 
            SET attempts = attempts + 1 
            WHERE user_id = ? AND purpose = ? AND is_used = 0
        ")->execute([$userId, $purpose]);
        
        logAttempt($db, $userId, 'otp_failed', 'failed');
        
        echo json_encode([
            'success' => false,
            'message' => 'Invalid or expired OTP code',
            'attempts_remaining' => max(0, 3 - ($failedCount + 1))
        ]);
        return;
    }
    
    // Mark OTP as used
    $stmt = $db->prepare("UPDATE otp_codes SET is_used = 1 WHERE id = ?");
    $stmt->execute([$otp['id']]);
    
    // Log successful verification
    logAttempt($db, $userId, 'otp_verified', 'success');
    
    // Set session variable for 2FA verified
    $_SESSION['2fa_verified'] = true;
    $_SESSION['2fa_verified_at'] = time();
    
    echo json_encode([
        'success' => true,
        'message' => 'OTP verified successfully'
    ]);
}

/**
 * Enable 2FA for user
 */
function enable2FA($db) {
    $userId = $_POST['user_id'] ?? null;
    $phoneNumber = $_POST['phone_number'] ?? null;
    
    if (!$userId || !$phoneNumber) {
        echo json_encode(['success' => false, 'message' => 'User ID and phone number required']);
        return;
    }
    
    // Validate phone number
    if (!SMSGateway::validatePhoneNumber($phoneNumber)) {
        echo json_encode(['success' => false, 'message' => 'Invalid phone number format']);
        return;
    }
    
    // Generate backup codes
    $backupCodes = [];
    for ($i = 0; $i < 10; $i++) {
        $backupCodes[] = strtoupper(bin2hex(random_bytes(4)));
    }
    $backupCodesJson = json_encode($backupCodes);
    
    // Insert or update 2FA settings
    $stmt = $db->prepare("
        INSERT INTO user_2fa_settings (user_id, is_enabled, phone_number, phone_verified, backup_codes)
        VALUES (?, 1, ?, 0, ?)
        ON DUPLICATE KEY UPDATE 
        is_enabled = 1, 
        phone_number = VALUES(phone_number),
        backup_codes = VALUES(backup_codes)
    ");
    $stmt->execute([$userId, $phoneNumber, $backupCodesJson]);
    
    echo json_encode([
        'success' => true,
        'message' => '2FA enabled successfully',
        'backup_codes' => $backupCodes
    ]);
}

/**
 * Disable 2FA for user
 */
function disable2FA($db) {
    $userId = $_POST['user_id'] ?? null;
    
    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'User ID required']);
        return;
    }
    
    $stmt = $db->prepare("
        UPDATE user_2fa_settings 
        SET is_enabled = 0 
        WHERE user_id = ?
    ");
    $stmt->execute([$userId]);
    
    echo json_encode([
        'success' => true,
        'message' => '2FA disabled successfully'
    ]);
}

/**
 * Verify phone number
 */
function verifyPhone($db) {
    $userId = $_POST['user_id'] ?? null;
    $otpCode = $_POST['otp_code'] ?? null;
    
    if (!$userId || !$otpCode) {
        echo json_encode(['success' => false, 'message' => 'User ID and OTP code required']);
        return;
    }
    
    // Verify OTP
    $stmt = $db->prepare("
        SELECT * FROM otp_codes 
        WHERE user_id = ? 
        AND otp_code = ? 
        AND purpose = 'phone_verification'
        AND is_used = 0 
        AND expires_at > NOW()
        ORDER BY created_at DESC 
        LIMIT 1
    ");
    $stmt->execute([$userId, $otpCode]);
    $otp = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$otp) {
        echo json_encode(['success' => false, 'message' => 'Invalid or expired OTP code']);
        return;
    }
    
    // Mark OTP as used
    $db->prepare("UPDATE otp_codes SET is_used = 1 WHERE id = ?")->execute([$otp['id']]);
    
    // Mark phone as verified
    $db->prepare("
        UPDATE user_2fa_settings 
        SET phone_verified = 1 
        WHERE user_id = ?
    ")->execute([$userId]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Phone number verified successfully'
    ]);
}

/**
 * Check 2FA status for user
 */
function check2FAStatus($db) {
    $userId = $_GET['user_id'] ?? null;
    
    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'User ID required']);
        return;
    }
    
    $stmt = $db->prepare("
        SELECT is_enabled, phone_number, phone_verified 
        FROM user_2fa_settings 
        WHERE user_id = ?
    ");
    $stmt->execute([$userId]);
    $settings = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$settings) {
        echo json_encode([
            'success' => true,
            'is_enabled' => false,
            'phone_verified' => false
        ]);
        return;
    }
    
    echo json_encode([
        'success' => true,
        'is_enabled' => (bool)$settings['is_enabled'],
        'phone_number' => $settings['phone_number'],
        'phone_verified' => (bool)$settings['phone_verified']
    ]);
}

/**
 * Resend OTP
 */
function resendOTP($db) {
    generateOTP($db); // Reuse generate OTP function
}

/**
 * Log 2FA attempt
 */
function logAttempt($db, $userId, $attemptType, $status) {
    $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    
    $stmt = $db->prepare("
        INSERT INTO two_fa_attempts (user_id, ip_address, attempt_type, status)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$userId, $ipAddress, $attemptType, $status]);
}


