<?php
// Database configuration

// ===== HOSTINGER ONLINE DATABASE (PRIMARY) =====
define('HOSTINGER_DB_HOST', 'localhost');
define('HOSTINGER_DB_NAME', 'u454260355_sample');
define('HOSTINGER_DB_USER', 'u454260355_sample');
define('HOSTINGER_DB_PASS', 'IT3202is?');

// Local database settings (fallback)
define('LOCAL_DB_HOST', 'localhost');
define('LOCAL_DB_NAME', 'core_transaction2');
define('LOCAL_DB_HR_NAME', 'hr_core');
define('LOCAL_DB_USER', 'root');
define('LOCAL_DB_PASS', '');

// Environment-based database selection
$isLocal = !isset($_SERVER['HTTP_HOST']) || (
    strpos($_SERVER['HTTP_HOST'] ?? '', 'localhost') !== false ||
    strpos($_SERVER['HTTP_HOST'] ?? '', '127.0.0.1') !== false ||
    strpos($_SERVER['HTTP_HOST'] ?? '', 'xampp') !== false
);

if ($isLocal) {
    // Use local database for development
    define('DB_HOST', LOCAL_DB_HOST);
    define('DB_NAME', LOCAL_DB_NAME);
    define('DB_USER', LOCAL_DB_USER);
    define('DB_PASS', LOCAL_DB_PASS);
} else {
    // Use Hostinger database for production
    define('DB_HOST', HOSTINGER_DB_HOST);
    define('DB_NAME', HOSTINGER_DB_NAME);
    define('DB_USER', HOSTINGER_DB_USER);
    define('DB_PASS', HOSTINGER_DB_PASS);
}

define('DB_CHARSET', 'utf8mb4');

// Aliases for CT1 (for clarity in code)
define('CT1_DB_HOST', DB_HOST);
define('CT1_DB_NAME', DB_NAME);
define('CT1_DB_USER', DB_USER);
define('CT1_DB_PASS', DB_PASS);
define('CT1_DB_CHARSET', DB_CHARSET);

/**
 * Get database connection (Hostinger primary, local fallback)
 * @return PDO
 * @throws PDOException
 */
function getCT1Connection(): PDO
{
    static $pdo = null;
    
    if ($pdo === null) {
        // Try Hostinger first
        try {
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                HOSTINGER_DB_HOST,
                HOSTINGER_DB_NAME,
                DB_CHARSET
            );
            
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ];
            
            $pdo = new PDO($dsn, HOSTINGER_DB_USER, HOSTINGER_DB_PASS, $options);
            
        } catch (PDOException $e) {
            // If Hostinger fails, try local database
            try {
                $dsn = sprintf(
                    'mysql:host=%s;dbname=%s;charset=%s',
                    LOCAL_DB_HOST,
                    LOCAL_DB_NAME,
                    DB_CHARSET
                );
                
                $options = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
                ];
                
                $pdo = new PDO($dsn, LOCAL_DB_USER, LOCAL_DB_PASS, $options);
                
            } catch (PDOException $e2) {
                // Log the error (in production, don't display database errors to users)
                error_log('Database Connection Error: ' . $e2->getMessage());
                throw new PDOException('Database connection failed. Please check your configuration.');
            }
        }
    }
    
    return $pdo;
}

/**
 * Get database connection (default to CT1 for backward compatibility)
 * @return PDO
 * @throws PDOException
 */
function getDBConnection(): PDO
{
    return getCT1Connection();
}

/**
 * Check if CT2 database is available
 * @return bool True if CT2 connection is available, false otherwise
 */
function isCT2Available(): bool
{
    // For now, return false since CT2 integration is not fully implemented
    // This prevents the browse-home.php from trying to access CT2 data
    return false;
}

// Create a global $pdo variable for files that use it directly
try {
    $pdo = getDBConnection();
} catch (PDOException $e) {
    // Suppress error output to prevent JSON corruption
    error_log('Global PDO connection error: ' . $e->getMessage());
    // Don't die here - let individual files handle the error
}

