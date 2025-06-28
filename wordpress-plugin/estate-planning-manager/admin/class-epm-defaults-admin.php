<?php
// Admin screen for managing advisor defaults in EPM
if (!defined('ABSPATH')) exit;

class EPM_Defaults_Admin {
    public static function init() {
        add_action('admin_menu', [__CLASS__, 'add_menu']);
    }

    public static function add_menu() {
        add_menu_page(
            'EPM Defaults',
            'EPM Defaults',
            'edit_users',
            'epm-defaults',
            [__CLASS__, 'render_page'],
            'dashicons-admin-generic',
            80
        );
    }

    public static function render_page() {
        if (!current_user_can('edit_users')) {
            wp_die('You do not have permission to access this page.');
        }
        $db = EPM_Database::instance();
        $user_id = get_current_user_id();
        $fields = [
            'advisor_name' => 'Advisor Name',
            'advisor_email' => 'Advisor Email',
            'advisor_phone' => 'Advisor Phone',
            'advisor_address' => 'Advisor Address',
            // Add more as needed
        ];
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && check_admin_referer('epm_defaults_save', 'epm_defaults_nonce')) {
            foreach ($fields as $name => $label) {
                $val = isset($_POST[$name]) ? sanitize_text_field($_POST[$name]) : '';
                $db->set_advisor_default($user_id, $name, $val);
            }
            echo '<div class="updated"><p>Defaults saved.</p></div>';
        }
        echo '<div class="wrap"><h1>EPM Advisor Defaults</h1>';
        echo '<form method="post">';
        wp_nonce_field('epm_defaults_save', 'epm_defaults_nonce');
        echo '<table class="form-table">';
        foreach ($fields as $name => $label) {
            $val = esc_attr($db->get_advisor_default($user_id, $name));
            echo '<tr><th><label for="' . esc_attr($name) . '">' . esc_html($label) . '</label></th>';
            echo '<td><input type="text" id="' . esc_attr($name) . '" name="' . esc_attr($name) . '" value="' . $val . '" class="regular-text" /></td></tr>';
        }
        echo '</table>';
        echo '<p><input type="submit" class="button button-primary" value="Save Defaults"></p>';
        echo '</form></div>';
    }
}

EPM_Defaults_Admin::init();
