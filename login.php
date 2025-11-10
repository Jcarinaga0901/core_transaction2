<?php
// Enhanced session security - MUST be set before session_start()
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // Set to 1 only if using HTTPS
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Strict');

// Error reporting for production (disabled for security)
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Start session after setting ini values
session_start();

// Include database configuration
require_once 'config/database.php';

// Check if user is already logged in
if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
    header('Location: index.php');
    exit();
}

// Localhost bypass for development (skip OTP verification)
$isLocalhost = (
    $_SERVER['HTTP_HOST'] === 'localhost' || 
    $_SERVER['HTTP_HOST'] === '127.0.0.1' || 
    strpos($_SERVER['HTTP_HOST'], 'localhost') !== false ||
    strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false
);

// Auto-clear expired pending sessions (10 minutes timeout)
if (isset($_SESSION['pending_user_id'])) {
    $pending_time = $_SESSION['pending_login_time'] ?? 0;
    if (time() - $pending_time > 600) { // 10 minutes
        unset($_SESSION['pending_user_id']);
        unset($_SESSION['pending_username']);
        unset($_SESSION['pending_email']);
        unset($_SESSION['pending_role']);
        unset($_SESSION['pending_vendor_id']);
        unset($_SESSION['pending_login_time']);
    }
}

// Email Configuration (from CT1)
$email_config = [
    'smtp_host' => 'smtp.gmail.com',
    'smtp_port' => 587,
    'smtp_username' => 'raevor32002is@gmail.com',
    'smtp_password' => 'vaou pzqc bntf yrrk',
    'from_email' => 'raevor32002is@gmail.com',
    'from_name' => 'RAEVOR',
    'debug_mode' => false
];

// OTP Configuration
$otp_config = [
    'expiry_minutes' => 10,
    'max_attempts' => 3,
    'length' => 6
];

// Function to generate OTP
function generateOTP($length = 6) {
    return str_pad(random_int(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
}

// Include PHPMailer classes at the top level
require_once 'vendor/phpmailer/phpmailer/src/Exception.php';
require_once 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once 'vendor/phpmailer/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Function to send OTP email using PHPMailer (from CT1)
function sendOTPEmail($email, $otpCode, $userName = '', $type = 'login') {
    global $email_config;
    
    try {
        $mail = new PHPMailer(true);
        
        // Server settings
        $mail->isSMTP();
        $mail->Host = $email_config['smtp_host'];
        $mail->SMTPAuth = true;
        $mail->Username = $email_config['smtp_username'];
        $mail->Password = $email_config['smtp_password'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $email_config['smtp_port'];
        
        // Recipients
        $mail->setFrom($email_config['from_email'], $email_config['from_name']);
        $mail->addAddress($email);
        
        // Content
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        
        // Set subject and body based on type (from CT1)
        switch ($type) {
            case 'signup':
                $mail->Subject = 'Verify Your RAEVOR Account';
                $mail->Body = getSignupOTPBody($otpCode, $userName);
                break;
            case 'login':
                $mail->Subject = 'RAEVOR Login Verification';
                $mail->Body = getLoginOTPBody($otpCode, $userName);
                break;
            case 'password_reset':
                $mail->Subject = 'RAEVOR Password Reset';
                $mail->Body = getPasswordResetOTPBody($otpCode, $userName);
                break;
            default:
                $mail->Subject = 'RAEVOR Verification Code';
                $mail->Body = getDefaultOTPBody($otpCode, $userName);
        }
        
        // Send email
        $result = $mail->send();
        
        if ($result) {
            error_log("OTP email sent successfully to: $email");
            return true;
        } else {
            error_log("Failed to send OTP email to: $email");
            return false;
        }
        
    } catch (Exception $e) {
        error_log("OTP email error: " . $e->getMessage());
        return false;
    }
}

// Email template functions from CT1
function getSignupOTPBody($otpCode, $userName) {
    $displayName = $userName ?: 'User';
    
    return "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Verify Your RAEVOR Account</title>
        <style>
            body { 
                font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; 
                line-height: 1.6; 
                color: #333; 
                max-width: 600px; 
                margin: 0 auto; 
                padding: 0;
                background-color: #f5f5f5;
            }
            .email-container {
                background-color: #f8f6f7;
                padding: 0;
            }
            .header { 
                background-color: #000000; 
                color: white; 
                padding: 30px; 
                text-align: center;
                margin: 0;
            }
            .header h1 {
                font-size: 32px;
                font-weight: 900;
                margin: 0 0 12px 0;
                letter-spacing: 1px;
                text-transform: uppercase;
                font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            }
            .header h2 {
                font-size: 18px;
                font-weight: 400;
                margin: 0;
                color: white;
                text-transform: uppercase;
                font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            }
            .content { 
                background-color: #f8f6f7; 
                padding: 30px; 
                color: #000000;
                text-align: left;
            }
            .greeting {
                color: #000000;
                font-size: 16px;
                margin-bottom: 16px;
                font-weight: 400;
                font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            }
            .message {
                color: #000000;
                font-size: 14px;
                margin-bottom: 25px;
                line-height: 1.5;
                font-weight: 400;
                font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            }
            .otp-code { 
                background-color: white; 
                color: black; 
                font-size: 42px; 
                font-weight: 900; 
                text-align: center; 
                padding: 24px 32px; 
                border: 2px solid #000000; 
                margin: 25px auto; 
                letter-spacing: 6px;
                width: fit-content;
                font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
                display: block;
            }
            .warning {
                color: #000000;
                font-size: 14px;
                margin: 20px 0;
                line-height: 1.5;
                font-weight: 400;
                font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            }
            .security-notice {
                color: #000000;
                font-size: 14px;
                margin-top: 20px;
                line-height: 1.5;
                font-weight: 400;
                font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            }
            .ellipsis {
                color: #000000;
                font-size: 16px;
                margin-top: 30px;
                font-weight: 400;
                font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            }
        </style>
    </head>
    <body>
        <div class='email-container'>
            <div class='header'>
                <h1>RAEVOR</h1>
                <h2>Account Verification</h2>
            </div>
            <div class='content'>
                <div class='greeting'>Hello $displayName,</div>
                <div class='message'>Thank you for signing up with RAEVOR. Please use the OTP code below to complete your account verification:</div>
                
                <div class='otp-code'>$otpCode</div>
                
                <div class='warning'>This code will expire in 10 minutes for security purposes.</div>
                <div class='security-notice'>If you didn't create an account with RAEVOR, please ignore this email.</div>
                <div class='ellipsis'>...</div>
            </div>
        </div>
    </body>
    </html>";
}

function getLoginOTPBody($otpCode, $userName) {
    $displayName = $userName ?: 'User';
    
    return "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>RAEVOR Login Verification</title>
        <style>
            body { 
                font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; 
                line-height: 1.6; 
                color: #333; 
                max-width: 600px; 
                margin: 0 auto; 
                padding: 0;
                background-color: #f5f5f5;
            }
            .email-container {
                background-color: #f8f6f7;
                padding: 0;
            }
            .header { 
                background-color: #000000; 
                color: white; 
                padding: 30px; 
                text-align: center;
                margin: 0;
            }
            .header h1 {
                font-size: 32px;
                font-weight: 900;
                margin: 0 0 12px 0;
                letter-spacing: 1px;
                text-transform: uppercase;
                font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            }
            .header h2 {
                font-size: 18px;
                font-weight: 400;
                margin: 0;
                color: white;
                text-transform: uppercase;
                font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            }
            .content { 
                background-color: #f8f6f7; 
                padding: 30px; 
                color: #000000;
                text-align: left;
            }
            .greeting {
                color: #000000;
                font-size: 16px;
                margin-bottom: 16px;
                font-weight: 400;
                font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            }
            .message {
                color: #000000;
                font-size: 14px;
                margin-bottom: 25px;
                line-height: 1.5;
                font-weight: 400;
                font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            }
            .otp-code { 
                background-color: white; 
                color: black; 
                font-size: 42px; 
                font-weight: 900; 
                text-align: center; 
                padding: 24px 32px; 
                border: 2px solid #000000; 
                margin: 25px auto; 
                letter-spacing: 6px;
                width: fit-content;
                font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
                display: block;
            }
            .warning {
                color: #000000;
                font-size: 14px;
                margin: 20px 0;
                line-height: 1.5;
                font-weight: 400;
                font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            }
            .security-notice {
                color: #000000;
                font-size: 14px;
                margin-top: 20px;
                line-height: 1.5;
                font-weight: 400;
                font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            }
            .ellipsis {
                color: #000000;
                font-size: 16px;
                margin-top: 30px;
                font-weight: 400;
                font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            }
        </style>
    </head>
    <body>
        <div class='email-container'>
            <div class='header'>
                <h1>RAEVOR</h1>
                <h2>Login Verification</h2>
            </div>
            <div class='content'>
                <div class='greeting'>Hello $displayName,</div>
                <div class='message'>You requested to log in to your RAEVOR account. Please use the OTP code below to complete your login:</div>
                
                <div class='otp-code'>$otpCode</div>
                
                <div class='warning'>This code will expire in 10 minutes for security purposes.</div>
                <div class='security-notice'>If you didn't request this login, please secure your account immediately.</div>
                <div class='ellipsis'>...</div>
            </div>
        </div>
    </body>
    </html>";
}

function getPasswordResetOTPBody($otpCode, $userName) {
    $displayName = $userName ?: 'User';
    
    return "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>RAEVOR Password Reset</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
            .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
            .otp-code { background: #667eea; color: white; font-size: 32px; font-weight: bold; text-align: center; padding: 20px; border-radius: 8px; margin: 20px 0; letter-spacing: 5px; }
            .footer { text-align: center; margin-top: 20px; color: #666; font-size: 14px; }
        </style>
    </head>
    <body>
        <div class='header'>
            <h1>Password Reset Request</h1>
            <p>Reset your RAEVOR account password</p>
        </div>
        <div class='content'>
            <h2>Hello $displayName!</h2>
            <p>You requested to reset your RAEVOR account password. Use the verification code below to proceed:</p>
            
            <div class='otp-code'>$otpCode</div>
            
            <p><strong>Important:</strong></p>
            <ul>
                <li>This code will expire in 10 minutes</li>
                <li>Enter this code to reset your password</li>
                <li>If you didn't request this reset, please ignore this email</li>
            </ul>
            
            <p>Best regards,<br>The RAEVOR Team</p>
        </div>
        <div class='footer'>
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>&copy; 2025 RAEVOR. All rights reserved.</p>
        </div>
    </body>
    </html>";
}

function getDefaultOTPBody($otpCode, $userName) {
    $displayName = $userName ?: 'User';
    
    return "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>RAEVOR Verification Code</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
            .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
            .otp-code { background: #667eea; color: white; font-size: 32px; font-weight: bold; text-align: center; padding: 20px; border-radius: 8px; margin: 20px 0; letter-spacing: 5px; }
            .footer { text-align: center; margin-top: 20px; color: #666; font-size: 14px; }
        </style>
    </head>
    <body>
        <div class='header'>
            <h1>RAEVOR Verification</h1>
            <p>Your verification code</p>
        </div>
        <div class='content'>
            <h2>Hello $displayName!</h2>
            <p>Please use the verification code below to complete your request:</p>
            
            <div class='otp-code'>$otpCode</div>
            
            <p><strong>Note:</strong> This code will expire in 10 minutes.</p>
            
            <p>Best regards,<br>The RAEVOR Team</p>
        </div>
        <div class='footer'>
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>&copy; 2025 RAEVOR. All rights reserved.</p>
        </div>
    </body>
    </html>";
}

// Function to authenticate user
function authenticateUser($username, $password) {
    try {
        $pdo = getDBConnection();
        
        // Prepare statement to prevent SQL injection
        $stmt = $pdo->prepare("SELECT id, username, password, email, role, vendor_id FROM users WHERE (username = ? OR email = ?) AND role IN ('seller', 'manager', 'staff')");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    } catch (PDOException $e) {
        error_log("Login authentication error: " . $e->getMessage());
        return false;
    }
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember_me = isset($_POST['remember_me']) ? true : false;
    $user_ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    
    // Input validation
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        // Authenticate user
        $user = authenticateUser($username, $password);
        
        if ($user) {
            // Generate OTP
            $otpCode = generateOTP();
            $expiresAt = date('Y-m-d H:i:s', time() + ($otp_config['expiry_minutes'] * 60));
            
            // Store OTP in database
            try {
                $pdo = getDBConnection();
                $stmt = $pdo->prepare("
                    INSERT INTO otp_codes (user_id, email, code, type, method, expires_at, ip_address, user_agent) 
                    VALUES (?, ?, ?, 'login_verification', 'email', ?, ?, ?)
                ");
                $stmt->execute([
                    $user['id'], 
                    $user['email'], 
                    $otpCode, 
                    $expiresAt,
                    $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                    $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
                ]);
                
                // Check if running on localhost - bypass OTP verification
                if ($isLocalhost) {
                    // Direct login for localhost development
                    session_regenerate_id(true);
                    $_SESSION['authenticated'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
                    $_SESSION['vendor_id'] = $user['vendor_id'];
            $_SESSION['login_time'] = time();
            
            // Handle "Save User" - store username in cookie for 30 days
                    if (isset($_POST['remember_me'])) {
                        setcookie('saved_username', $_SESSION['username'], time() + (30 * 24 * 60 * 60), '/', '', false, true);
            } else {
                setcookie('saved_username', '', time() - 3600, '/', '', false, true);
                    }
                    
                    header('Location: index.php');
                    exit();
                } else {
                    // Send OTP email for production
                    if (sendOTPEmail($user['email'], $otpCode, $user['username'], 'login')) {
                        // Store user data in session for OTP verification
                        $_SESSION['pending_user_id'] = $user['id'];
                        $_SESSION['pending_username'] = $user['username'];
                        $_SESSION['pending_email'] = $user['email'];
                        $_SESSION['pending_role'] = $user['role'];
                        $_SESSION['pending_vendor_id'] = $user['vendor_id'];
                        $_SESSION['pending_login_time'] = time(); // Store timestamp for timeout
                        
                        $success = "Verification code sent to your email: " . $user['email'];
                    } else {
                        $error = "Failed to send verification code. Please try again.";
                    }
                }
                
            } catch (Exception $e) {
                $error = 'Failed to send verification code. Please try again.';
                error_log("OTP send error: " . $e->getMessage());
            }
        } else {
            $error = 'Invalid username or password.';
            error_log("Failed login attempt for username: $username from IP: $user_ip");
        }
    }
}

// Handle OTP verification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_otp'])) {
    $otpCode = trim($_POST['otp_code'] ?? '');
    
    if (empty($otpCode) || strlen($otpCode) !== 6) {
        $error = 'Please enter a valid 6-digit code.';
    } else {
        try {
            $pdo = getDBConnection();
            
            // Check if OTP exists and is valid
            $stmt = $pdo->prepare("
                SELECT id, expires_at, is_used FROM otp_codes 
                WHERE user_id = ? AND code = ? AND type = 'login_verification' 
                ORDER BY created_at DESC LIMIT 1
            ");
            $stmt->execute([$_SESSION['pending_user_id'], $otpCode]);
            $otp = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($otp) {
                // Check if OTP is expired
                if (strtotime($otp['expires_at']) < time()) {
                    $error = 'Verification code has expired. Please request a new one.';
                } elseif ($otp['is_used']) {
                    $error = 'This verification code has already been used. Please request a new one.';
                } else {
                    // Mark OTP as used
                    $stmt = $pdo->prepare("UPDATE otp_codes SET is_used = TRUE WHERE id = ?");
                    $stmt->execute([$otp['id']]);
                    
                    // Complete login
                    session_regenerate_id(true);
                    $_SESSION['authenticated'] = true;
                    $_SESSION['user_id'] = $_SESSION['pending_user_id'];
                    $_SESSION['username'] = $_SESSION['pending_username'];
                    $_SESSION['email'] = $_SESSION['pending_email'];
                    $_SESSION['role'] = $_SESSION['pending_role'];
                    $_SESSION['vendor_id'] = $_SESSION['pending_vendor_id'];
                    $_SESSION['login_time'] = time();
                    
                    // Clear pending session data
                    unset($_SESSION['pending_user_id']);
                    unset($_SESSION['pending_username']);
                    unset($_SESSION['pending_email']);
                    unset($_SESSION['pending_role']);
                    unset($_SESSION['pending_vendor_id']);
                
                    // Handle "Save User" - store username in cookie for 30 days
                    if (isset($_POST['remember_me'])) {
                        setcookie('saved_username', $_SESSION['username'], time() + (30 * 24 * 60 * 60), '/', '', false, true);
                    } else {
                        setcookie('saved_username', '', time() - 3600, '/', '', false, true);
                    }
            
            // Log successful login
                    error_log("User logged in: {$_SESSION['username']} from IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
            
            // Redirect to main dashboard
            header('Location: index.php');
            exit();
                }
        } else {
                $error = 'Invalid verification code. Please check and try again.';
            }
        } catch (Exception $e) {
            $error = 'Verification failed. Please try again.';
            error_log("OTP verification error: " . $e->getMessage());
        }
    }
}

// Handle resend OTP
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resend_otp'])) {
    if (isset($_SESSION['pending_user_id'])) {
        try {
            $pdo = getDBConnection();
            
            // Get user info
            $stmt = $pdo->prepare("SELECT username, email FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['pending_user_id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                // Generate new OTP
                $otpCode = generateOTP();
                $expiresAt = date('Y-m-d H:i:s', time() + ($otp_config['expiry_minutes'] * 60));
                
                // Store new OTP in database
                $stmt = $pdo->prepare("
                    INSERT INTO otp_codes (user_id, email, code, type, method, expires_at, ip_address, user_agent) 
                    VALUES (?, ?, ?, 'login_verification', 'email', ?, ?, ?)
                ");
                $stmt->execute([
                    $_SESSION['pending_user_id'], 
                    $user['email'], 
                    $otpCode, 
                    $expiresAt,
                    $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                    $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
                ]);
                
                // Send new OTP email
                if (sendOTPEmail($user['email'], $otpCode, $user['username'], 'login')) {
                    $success = "New verification code sent to your email: " . $user['email'];
                } else {
                    $error = "Failed to send new verification code. Please try again.";
                }
            } else {
                $error = "User not found. Please login again.";
            }
        } catch (Exception $e) {
            $error = 'Failed to resend verification code. Please try again.';
            error_log("Resend OTP error: " . $e->getMessage());
        }
    } else {
        $error = "No pending login session. Please login again.";
    }
}

// Get saved username from cookie
$saved_username = $_COOKIE['saved_username'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RAEVOR - Seller Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }

        .login-container {
            background: #ffffff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 900px;
            display: flex;
            overflow: hidden;
            min-height: 500px;
        }

        .info-panel {
            flex: 2;
            background: linear-gradient(135deg, #000000 0%, #333333 100%);
            padding: 50px 40px;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .info-panel::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 20% 20%, rgba(255,255,255,0.1) 2px, transparent 2px),
                radial-gradient(circle at 80% 80%, rgba(255,255,255,0.05) 1px, transparent 1px);
            background-size: 30px 30px, 20px 20px;
        }

        .info-panel h1 {
            font-size: 2.5rem;
            font-weight: bold;
            color: #ffffff;
            margin: 0 0 30px 0;
            position: relative;
            z-index: 1;
        }

        .info-panel p {
            color: #ffffff;
            font-size: 1.1rem;
            margin: 0 0 40px 0;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }

        .features-list {
            list-style: none;
            padding: 0;
            margin: 0;
            position: relative;
            z-index: 1;
        }

        .features-list li {
            color: #ffffff;
            font-size: 1rem;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            opacity: 0.9;
        }

        .features-list li i {
            width: 20px;
            height: 20px;
            background: rgba(255,255,255,0.2);
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 0.8rem;
            border: 1px solid rgba(255,255,255,0.3);
            color: #ffffff;
        }

        .login-panel {
            flex: 1;
            background: #ffffff;
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-header {
            margin-bottom: 30px;
        }

        .login-logo {
            font-family: 'Great Vibes', cursive;
            font-size: 2rem;
            font-weight: 350;
            letter-spacing: 3px;
            color: #000000;
            margin: 0;
        }

        .login-title {
            font-size: 2rem;
            font-weight: bold;
            color: #000000;
            margin-bottom: 30px;
        }

        .form-floating {
            margin-bottom: 20px;
        }

        .form-control {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            font-size: 1rem;
            background: #ffffff;
        }

        .form-control:focus {
            border-color: #000000;
            box-shadow: 0 0 0 0.2rem rgba(0, 0, 0, 0.1);
        }

        .form-floating > label {
            color: #6c757d;
            font-weight: 500;
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .form-check {
            display: flex;
            align-items: center;
        }

        .form-check-input {
            margin-right: 8px;
        }

        .forgot-password {
            color: #000000;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .forgot-password:hover {
            text-decoration: underline;
        }

        .btn-login {
            background: #000000;
            border: none;
            border-radius: 8px;
            padding: 15px;
            font-size: 1.1rem;
            font-weight: bold;
            color: white;
            width: 100%;
            cursor: pointer;
        }

        .btn-login:hover {
            background: #333333;
        }

        .alert {
            border-radius: 8px;
            border: none;
            padding: 15px;
            margin-bottom: 20px;
            background: #f8d7da;
            color: #721c24;
        }

        .alert-success {
            background: #d1edff;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                max-width: 500px;
            }
            
            .info-panel {
                flex: none;
                min-height: 300px;
                padding: 30px 25px;
            }
            
            .login-panel {
                flex: none;
                padding: 30px 25px;
            }
            
            .info-panel h1 {
                font-size: 2rem;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 10px;
            }
            
            .login-container {
                margin: 0;
                border-radius: 10px;
            }
            
            .info-panel,
            .login-panel {
                padding: 20px 15px;
            }
            
            .info-panel h1 {
                font-size: 1.8rem;
            }
            
            .login-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Information Panel (Left Side) -->
        <div class="info-panel">
            <h1>RAEVOR<br>Seller Portal</h1>
            <p>Clothing E-Commerce Management System</p>
            <ul class="features-list">
                <li><i class="bi bi-graph-up"></i>Sales Analytics</li>
                <li><i class="bi bi-cart-check"></i>Order Management</li>
                <li><i class="bi bi-shield-check"></i>Secure Access</li>
                <li><i class="bi bi-truck"></i>Delivery Management</li>
            </ul>
        </div>

        <!-- Login Panel (Right Side) -->
        <div class="login-panel">
            <div class="login-header">
                <div class="login-logo">RAEVOR</div>
            </div>

            <h2 class="login-title">Login</h2>

            <?php if (isset($error)): ?>
                <div class="alert">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($success)): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['timeout'])): ?>
                <div class="alert">
                    Your session has expired. Please login again.
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error']) && $_GET['error'] === 'unauthorized'): ?>
                <div class="alert">
                    Access denied. You don't have permission to access that resource.
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['pending_user_id']) && !$isLocalhost): ?>
                <!-- OTP Verification Form -->
                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                    <h5 class="mb-3" style="color: #000; font-weight: bold;">🔐 Email Verification Required</h5>
                    <p class="text-muted mb-4">Please enter the 6-digit verification code sent to your email.</p>
                    
                    <form method="POST" action="login.php">
                        <div class="mb-3">
                            <label class="form-label">Enter 6-digit verification code</label>
                            <input type="text" class="form-control text-center" name="otp_code" 
                                   placeholder="000000" maxlength="6" required 
                                   style="font-size: 24px; letter-spacing: 8px; font-weight: bold;">
            </div>

                        <button type="submit" name="verify_otp" class="btn btn-login">
                            <i class="bi bi-shield-check me-1"></i>Verify & Login
                        </button>
                        
                        <div style="text-align: center; margin-top: 15px;">
                            <button type="submit" name="resend_otp" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-arrow-clockwise me-1"></i>Resend Code
                            </button>
                        </div>
                        
                        <div style="text-align: center; margin-top: 15px; font-size: 12px; color: #6c757d;">
                            <i class="bi bi-info-circle me-1"></i>Check your email for the verification code
                            <br><small id="otp-timer" style="color: #dc3545; font-weight: bold;"></small>
                        </div>
                        
                        <div style="text-align: center; margin-top: 10px;">
                            <a href="clear_session.php" style="color: #6c757d; text-decoration: none; font-size: 12px;">
                                <i class="bi bi-arrow-left me-1"></i>Back to Username/Password Login
                            </a>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <!-- Localhost Development Notice -->
                <?php if ($isLocalhost): ?>
                <div class="alert alert-info mb-4" style="background: #e3f2fd; border: 1px solid #2196f3; color: #0d47a1;">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Development Mode:</strong> OTP verification is disabled on localhost. You'll be logged in directly after entering your credentials.
                </div>
                <?php endif; ?>
                
                <!-- Regular Login Form -->
            <form method="POST" action="login.php">
                <div class="form-floating">
                    <input type="text" 
                           class="form-control" 
                           id="username" 
                           name="username" 
                           placeholder="Enter username"
                           value="<?php echo htmlspecialchars($saved_username); ?>"
                           required>
                    <label for="username">Username</label>
                </div>

                <div class="form-floating">
                    <input type="password" 
                           class="form-control" 
                           id="password" 
                           name="password" 
                           placeholder="Enter password"
                           required>
                    <label for="password">Password</label>
                </div>

                <div class="form-options">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="rememberMe" name="remember_me" <?php echo !empty($saved_username) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="rememberMe">
                            Save User
                        </label>
                    </div>
                        <a href="forgot_password.php" class="forgot-password">FORGOT PASSWORD?</a>
                </div>

                <button type="submit" name="login" class="btn btn-login">
                    LOGIN
                </button>
            </form>
            <?php endif; ?>

            <div style="text-align: center; margin-top: 25px; padding-top: 25px; border-top: 1px solid #dee2e6;">
                <p style="color: #6c757d; margin-bottom: 10px;">Don't have an account?</p>
                <a href="register.php" style="display: inline-block; padding: 12px 30px; background: linear-gradient(135deg, #6c757d 0%, #495057 100%); color: white; text-decoration: none; border-radius: 8px; font-weight: bold; transition: all 0.3s ease;">
                    <i class="bi bi-person-plus me-2"></i>Become a Seller
                </a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-focus username field if empty, password field if username is pre-filled
            const usernameField = document.getElementById('username');
            const passwordField = document.getElementById('password');
            
            if (usernameField && passwordField) {
            if (usernameField.value.trim() !== '') {
                // Username is pre-filled, focus on password
                passwordField.focus();
            } else {
                // Username is empty, focus on username
                usernameField.focus();
                }
            }
            
            // Auto-focus OTP input if present
            const otpInput = document.querySelector('input[name="otp_code"]');
            if (otpInput) {
                otpInput.focus();
            }
            
            // OTP Countdown Timer
            const otpTimer = document.getElementById('otp-timer');
            if (otpTimer) {
                let timeLeft = 600; // 10 minutes in seconds
                
                function updateTimer() {
                    const minutes = Math.floor(timeLeft / 60);
                    const seconds = timeLeft % 60;
                    
                    if (timeLeft > 0) {
                        otpTimer.textContent = `Code expires in: ${minutes}:${seconds.toString().padStart(2, '0')}`;
                        timeLeft--;
                    } else {
                        otpTimer.textContent = 'Code has expired. Please resend.';
                        otpTimer.style.color = '#dc3545';
                    }
                }
                
                updateTimer();
                setInterval(updateTimer, 1000);
            }
        });
    </script>
</body>
</html>
