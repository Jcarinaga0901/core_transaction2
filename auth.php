<?php
/**
 * Authentication Helper Functions
 * Include this file on pages that require authentication
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is authenticated
 */
function isAuthenticated() {
    return isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true;
}

/**
 * Get current user information
 */
function getCurrentUser() {
    if (!isAuthenticated()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'] ?? null,
        'username' => $_SESSION['username'] ?? null,
        'email' => $_SESSION['email'] ?? null,
        'role' => $_SESSION['role'] ?? null,
        'login_time' => $_SESSION['login_time'] ?? null
    ];
}

/**
 * Check if user has specific role
 */
function hasRole($requiredRole) {
    if (!isAuthenticated()) {
        return false;
    }
    
    $userRole = $_SESSION['role'] ?? '';
    
    // Role hierarchy: seller > manager > staff > customer
    $roleHierarchy = [
        'seller' => 4,
        'manager' => 3,
        'staff' => 2,
        'customer' => 1
    ];
    
    $userLevel = $roleHierarchy[$userRole] ?? 0;
    $requiredLevel = $roleHierarchy[$requiredRole] ?? 0;
    
    return $userLevel >= $requiredLevel;
}

/**
 * Require authentication - redirect to login if not authenticated
 */
function requireAuth($redirectTo = 'login.php') {
    if (!isAuthenticated()) {
        header("Location: $redirectTo");
        exit();
    }
    
    // Check if user is in pending 2FA state (should not happen but safety check)
    if (isset($_SESSION['pending_user_id'])) {
        // User is in 2FA pending state, redirect back to login
        header("Location: login.php");
        exit();
    }
}

/**
 * Require specific role - redirect if user doesn't have required role
 */
function requireRole($requiredRole, $redirectTo = 'login.php') {
    requireAuth($redirectTo);
    
    if (!hasRole($requiredRole)) {
        // Log unauthorized access attempt
        $user = getCurrentUser();
        $username = $user['username'] ?? 'unknown';
        $userRole = $user['role'] ?? 'none';
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        
        error_log("Unauthorized access attempt: $username ($userRole) tried to access $requiredRole content from IP: $ip");
        
        header("Location: index.php?error=unauthorized");
        exit();
    }
}

/**
 * Check session timeout (optional - set session timeout)
 */
function checkSessionTimeout($timeoutMinutes = 60) {
    if (isAuthenticated()) {
        $loginTime = $_SESSION['login_time'] ?? time();
        $currentTime = time();
        
        // Check if session has expired
        if (($currentTime - $loginTime) > ($timeoutMinutes * 60)) {
            // Log session timeout
            $user = getCurrentUser();
            $username = $user['username'] ?? 'unknown';
            error_log("Session timeout for user: $username");
            
            // Destroy session
            session_unset();
            session_destroy();
            
            header("Location: login.php?timeout=1");
            exit();
        }
        
        // Update login time for activity
        $_SESSION['login_time'] = $currentTime;
    }
}

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Get CSRF token input field HTML
 */
function getCSRFField() {
    $token = generateCSRFToken();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}

/**
 * Display user-friendly role name
 */
function getRoleName($role) {
    $roleNames = [
        'seller' => 'Seller',
        'manager' => 'Manager',
        'staff' => 'Staff Member',
        'customer' => 'Customer'
    ];
    
    return $roleNames[$role] ?? 'Unknown';
}
?>
