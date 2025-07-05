<?php
/**
 * Handles the admin_menu action for EPM Log Viewer page
 */
class EPM_AdminMenuHandler {
    public static function handle() {
        add_menu_page(
            'EPM Log Viewer',
            'EPM Log',
            'manage_options',
            'epm-log-viewer',
            'epm_render_log_viewer',
            'dashicons-media-text',
            80
        );
    }
}
