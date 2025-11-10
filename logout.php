<?php
session_start();

// Security headers
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

// Handle AJAX logout request
if (isset($_POST['action']) && $_POST['action'] === 'logout') {
    // Log logout if user was authenticated
    if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
        $username = $_SESSION['username'] ?? 'unknown';
        $user_ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        error_log("User logout: " . $username . " from IP: " . $user_ip);
    }

    // Destroy session and all session data
    session_unset();
    session_destroy();

    // Clear session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
    exit();
}

// If not an AJAX request, redirect to login
header('Location: login.php');
exit();
?>
