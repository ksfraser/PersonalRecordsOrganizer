<?php
/**
 * Handles the log viewer admin page in the Estate Planning Manager plugin.
 */
class EPM_LogViewerHandler {
    public static function register() {
        add_action('admin_menu', [__CLASS__, 'register_log_viewer_page']);
    }

    public static function register_log_viewer_page() {
        add_menu_page(
            'EPM Log Viewer',
            'EPM Log',
            'manage_options',
            'epm-log-viewer',
            [__CLASS__, 'render_log_viewer'],
            'dashicons-media-text',
            80
        );
    }

    public static function render_log_viewer() {
        // Use Logger abstraction to get log location and logs
        if (!class_exists('EstatePlanningManager\\Logger')) {
            require_once dirname(__DIR__, 2) . '/class-epm-logger.php';
        }
        $log_dir = \EstatePlanningManager\Logger::getLogDir();
        $log_file = $log_dir . '/epm.log';
        echo '<div class="wrap"><h1>EPM Log Viewer</h1>';
        echo '<div style="margin-bottom:20px;padding:10px;background:#f9f9f9;border:1px solid #ccc;">';
        echo '<strong>Log Directory Setting:</strong> <code>' . esc_html($log_dir) . '</code><br>';
        echo '<strong>Log File:</strong> <code>' . esc_html($log_file) . '</code>';
        echo '</div>';
        $logs = \EstatePlanningManager\Logger::getLogs(1000);
        if (!empty($logs)) {
            echo '<pre style="background:#fff;max-height:600px;overflow:auto;border:1px solid #ccc;padding:10px;">';
            foreach ($logs as $line) {
                echo esc_html($line) . "\n";
            }
            echo '</pre>';
        } else {
            echo '<p>Log file not found or not readable: ' . esc_html($log_file) . '</p>';
        }
        echo '</div>';
    }
}
