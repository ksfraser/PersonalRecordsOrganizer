<?php
/**
 * EPM_Admin_Log_Viewer - Admin log viewer for Estate Planning Manager
 */
class EPM_Admin_Log_Viewer {
    public static function add_menu() {
        add_submenu_page(
            'options-general.php',
            'EPM Log Viewer',
            'EPM Log Viewer',
            'manage_options',
            'epm-log-viewer',
            [self::class, 'render_page']
        );
    }
    public static function render_page() {
        $log_file = plugin_dir_path(__FILE__) . '../logs/epm.log';
        echo '<div class="wrap"><h1>EPM Log Viewer</h1>';
        if (file_exists($log_file)) {
            echo '<pre style="background:#222;color:#eee;padding:20px;max-height:600px;overflow:auto;font-size:13px;">';
            echo esc_html(file_get_contents($log_file));
            echo '</pre>';
            echo '<form method="post"><button type="submit" name="epm_clear_log" class="button">Clear Log</button></form>';
            if (isset($_POST['epm_clear_log'])) {
                file_put_contents($log_file, '');
                echo '<div class="updated"><p>Log cleared.</p></div>';
            }
        } else {
            echo '<p>No log file found.</p>';
        }
        echo '</div>';
    }
}
add_action('admin_menu', ['EPM_Admin_Log_Viewer', 'add_menu']);
