<?php
/**
 * EPM_Logger - Logging utility for Estate Planning Manager
 * Supports log levels: ERROR, WARNING, INFO, DEBUG (PEAR style)
 */
class EPM_Logger {
    const ERROR = 1;
    const WARNING = 2;
    const INFO = 3;
    const DEBUG = 4;

    private static $log_file = null;
    private static $log_level = null;

    public static function set_log_level($level) {
        self::$log_level = $level;
    }

    public static function get_log_level() {
        if (self::$log_level !== null) return self::$log_level;
        $level = get_option('epm_log_level', self::ERROR);
        self::$log_level = $level;
        return $level;
    }

    public static function log($message, $level = self::INFO) {
        if ($level > self::get_log_level() && $level !== self::ERROR) return;
        if (!self::$log_file) {
            $plugin_dir = plugin_dir_path(__FILE__) . '../logs';
            if (!file_exists($plugin_dir)) @mkdir($plugin_dir, 0755, true);
            self::$log_file = $plugin_dir . '/epm.log';
        }
        $date = date('Y-m-d H:i:s');
        $level_str = self::level_to_string($level);
        $line = "[$date] [$level_str] $message\n";
        error_log($line, 3, self::$log_file);
    }

    private static function level_to_string($level) {
        switch ($level) {
            case self::ERROR: return 'ERROR';
            case self::WARNING: return 'WARNING';
            case self::INFO: return 'INFO';
            case self::DEBUG: return 'DEBUG';
            default: return 'LOG';
        }
    }
}
