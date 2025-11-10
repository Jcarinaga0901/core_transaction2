<?php
/**
 * Auto-cancellation Cron Job
 * This script should be run every 5 minutes via cron job
 * 
 * Cron job example:
 * 0,5,10,15,20,25,30,35,40,45,50,55 * * * * /usr/bin/php /path/to/CT2/scripts/auto_cancel_cron.php
 */

// Set timezone
date_default_timezone_set('Asia/Manila');

// Log the execution
$logFile = __DIR__ . '/../logs/auto_cancel.log';
$logDir = dirname($logFile);

// Create logs directory if it doesn't exist
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}

function writeLog($message) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[{$timestamp}] {$message}\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
    echo $logMessage;
}

writeLog("Starting auto-cancellation process...");

try {
    // Include the auto-cancel API
    require_once __DIR__ . '/../api/auto_cancel.php';
    
    writeLog("Auto-cancellation process completed successfully");
    
} catch (Exception $e) {
    writeLog("ERROR: Auto-cancellation process failed - " . $e->getMessage());
    exit(1);
}
?>
