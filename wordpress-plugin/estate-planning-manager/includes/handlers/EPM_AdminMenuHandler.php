<?php
/**
 * Handles the admin_menu action for EPM Log Viewer and other EPM admin pages
 */
class EPM_AdminMenuHandler {
    public static function handle() {
        // Log Viewer
        add_menu_page(
            'EPM Log Viewer',
            'EPM Log',
            'manage_options',
            'epm-log-viewer',
            'epm_render_log_viewer',
            'dashicons-media-text',
            80
        );
        // Unified Bank Selectors tabbed admin page
        add_submenu_page(
            'estate-planning-manager',
            'Bank Selectors',
            'Bank Selectors',
            'manage_options',
            'epm-bank-selectors',
            ['EPM_BankSelectorsAdmin', 'render_admin_page']
        );
    }
}
