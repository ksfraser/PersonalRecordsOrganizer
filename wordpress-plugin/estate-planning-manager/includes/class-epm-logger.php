<?php
namespace EstatePlanningManager;
/**
 * Logger Class for Estate Planning Manager
 * Handles all plugin logging, ensures log directory exists, supports configurable log file path.
 * @phpdoc
 * @class Logger
 * @description Centralized logger for plugin debug and audit logs.
 * @uml class EstatePlanningManager\Logger {
 *   +log($message)
 *   +setLogDir($dir)
 *   +getLogDir()
 * }
 */
class Logger {
    /**
     * Retrieve logs (file backend)
     * @param int $limit
     * @return array log lines
     */
    public static function getLogs($limit = 1000) {
        $dir = self::getLogDir();
        $file = $dir . '/' . self::$logFile;
        if (!file_exists($file) || !is_readable($file)) {
            return [];
        }
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($limit > 0 && count($lines) > $limit) {
            $lines = array_slice($lines, -$limit);
        }
        return $lines;
    }
    /**
     * Log a debug message (helper)
     */
    public static function debug($message) {
        self::log('[DEBUG] ' . $message);
    }
    /**
     * Log directory path
     * @var string
     */
    private static $logDir = null;
    /**
     * Log file name
     * @var string
     */
    private static $logFile = 'epm.log';
    /**
     * Set log directory
     * @param string $dir
     */
    public static function setLogDir($dir) {
        self::$logDir = rtrim($dir, '/');
    }
    /**
     * Get log directory (from DB option, fallback to default)
     * @return string
     */
    public static function getLogDir() {
        if (self::$logDir !== null) {
            return self::$logDir;
        }
        // Try DB option first
        if (function_exists('get_option')) {
            $dbDir = get_option('epm_log_dir');
            if (!empty($dbDir)) {
                self::$logDir = rtrim($dbDir, '/');
                return self::$logDir;
            }
        }
        // Fallback to default
        self::$logDir = dirname(__DIR__, 2) . '/logs';
        return self::$logDir;
    }
    /**
     * Write a message to the log file
     * @param string $message
     */
    public static function log($message) {
        $dir = self::getLogDir();
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        $file = $dir . '/' . self::$logFile;
        $datetime = date('Y-m-d H:i:s'); // Date and time
        file_put_contents($file, "[$datetime] $message\n", FILE_APPEND);
    }
}
