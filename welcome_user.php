<?php
// Shared welcome block with circular shop logo
// Safe to include on any page header

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Try to load database only if helper isn't available yet
if (!function_exists('getDBConnection')) {
    require_once __DIR__ . '/../config/database.php';
}

$pdo = null;
try {
    $pdo = getDBConnection();
} catch (Throwable $e) {
    // Ignore gracefully; welcome will fallback to icon
}

$username = $_SESSION['username'] ?? 'User';
$role = ucfirst($_SESSION['role'] ?? '');
$vendorId = $_SESSION['vendor_id'] ?? null;
$logoUrl = '';

if ($pdo && $vendorId) {
    try {
        $stmt = $pdo->prepare('SELECT logo_url FROM vendors WHERE id = ?');
        $stmt->execute([$vendorId]);
        $row = $stmt->fetch();
        if (!empty($row['logo_url'])) {
            $logoUrl = $row['logo_url'];
        }
    } catch (Throwable $e) {
        // ignore
    }
}

// Check for temporary logo in session (fallback for missing database column)
if (empty($logoUrl) && isset($_SESSION['temp_logo_url']) && $_SESSION['temp_logo_vendor_id'] == $vendorId) {
    $logoUrl = $_SESSION['temp_logo_url'];
}
?>
<div class="d-flex align-items-center me-3">
    <?php if (!empty($logoUrl)): ?>
        <img src="<?php echo htmlspecialchars($logoUrl); ?>"
             alt="Shop Logo"
             style="width: 32px; height: 32px; object-fit: cover; border-radius: 50%; margin-right: 8px; border: 2px solid #e9ecef;">
    <?php else: ?>
        <i class="bi bi-person-circle me-1" style="font-size: 1.5rem;"></i>
    <?php endif; ?>
    <span class="text-muted">
        Welcome, <?php echo htmlspecialchars($username); ?>
        <?php if (!empty($role)): ?>
            <small class="text-muted">(<?php echo htmlspecialchars($role); ?>)</small>
        <?php endif; ?>
    </span>
    
</div>


