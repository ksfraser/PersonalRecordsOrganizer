<?php
/**
 * Estate Planning Manager - Settings Admin Page
 *
 * Allows admin to configure plugin settings, including log file path.
 *
 * @namespace EstatePlanningManager
 * @package EstatePlanningManager
 * @phpdoc
 * @class EPM_Admin_Settings
 * @description Admin settings page for Estate Planning Manager plugin.
 * @uml class EstatePlanningManager\EPM_Admin_Settings {
 *   +init()
 *   +render_settings_page()
 * }
 */

namespace EstatePlanningManager;

class EPM_Admin_Settings {
    /**
     * Initialize settings page
     */
    public static function init() {
        add_action('admin_menu', [__CLASS__, 'add_settings_page']);
        add_action('admin_init', [__CLASS__, 'register_settings']);
    }

    /**
     * Add settings page to menu
     */
    public static function add_settings_page() {
        add_options_page(
            'Estate Planning Manager Settings',
            'Estate Planning Manager',
            'manage_options',
            'epm-settings',
            [__CLASS__, 'render_settings_page']
        );
    }

    /**
     * Register settings
     */
    public static function register_settings() {
        register_setting('epm_settings_group', 'epm_log_dir');
    }

    /**
     * Render settings page
     */
    public static function render_settings_page() {
        ?>
        <div class="wrap">
            <h1>Estate Planning Manager Settings</h1>
            <form method="post" action="options.php">
                <?php settings_fields('epm_settings_group'); ?>
                <?php do_settings_sections('epm_settings_group'); ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Log File Directory</th>
                        <td>
                            <input type="text" name="epm_log_dir" value="<?php echo esc_attr(get_option('epm_log_dir', dirname(__DIR__, 2) . '/logs')); ?>" size="50" />
                            <p class="description">Directory for plugin logs. Default: <code><?php echo dirname(__DIR__, 2) . '/logs'; ?></code></p>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}

// Initialize settings page
if (is_admin()) {
    \EstatePlanningManager\EPM_Admin_Settings::init();
}
