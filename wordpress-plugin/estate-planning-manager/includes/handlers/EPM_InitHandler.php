<?php
/**
 * Handles the init action for EPM log file creation and debug POST logging
 */
class EPM_InitHandler {
    public static function handle() {
        // Log POSTs
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            error_log('EPM DEBUG: GLOBAL POST handler reached. URI=' . $_SERVER['REQUEST_URI']);
        }
        // Ensure EPM log file exists and is writable
        $upload_dir = wp_upload_dir();
        $log_file = $upload_dir['basedir'] . '/epm-log.txt';
        if (!file_exists($log_file)) {
            @file_put_contents($log_file, "EPM Log initialized on " . date('Y-m-d H:i:s') . "\n");
            @chmod($log_file, 0664);
        }
    }
}
