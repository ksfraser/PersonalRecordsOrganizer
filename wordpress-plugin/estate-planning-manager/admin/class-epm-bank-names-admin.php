<?php
/**
 * Admin page for managing bank names and regions.
 */
class EPM_BankNamesAdmin {
    public static function register() {
        add_action('admin_menu', [__CLASS__, 'add_admin_menu']);
    }
    public static function add_admin_menu() {
        add_submenu_page(
            'estate-planning-manager',
            'Manage Banks',
            'Manage Banks',
            'manage_options',
            'epm-manage-banks',
            [__CLASS__, 'render_admin_page']
        );
    }
    public static function render_admin_page() {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_bank_names';
        $regions = ['canada' => 'Canada', 'usa' => 'USA', 'europe' => 'Europe'];
        $region = isset($_GET['region']) ? sanitize_text_field($_GET['region']) : 'canada';
        // Handle add/edit
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['epm_bank_action'])) {
            check_admin_referer('epm_manage_banks');
            $name = sanitize_text_field($_POST['bank_name']);
            $is_active = isset($_POST['is_active']) ? 1 : 0;
            $edit_id = isset($_POST['edit_id']) ? intval($_POST['edit_id']) : 0;
            if ($edit_id) {
                $wpdb->update($table, [ 'name' => $name, 'is_active' => $is_active ], [ 'id' => $edit_id ]);
            } else {
                $max_sort = (int)$wpdb->get_var($wpdb->prepare("SELECT MAX(sort_order) FROM $table WHERE region = %s", $region));
                $wpdb->insert($table, [ 'region' => $region, 'name' => $name, 'is_active' => $is_active, 'sort_order' => $max_sort + 10 ]);
            }
            echo '<div class="updated"><p>Bank saved.</p></div>';
        }
        // Edit mode
        $edit_bank = null;
        if (isset($_GET['edit_id'])) {
            $edit_bank = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", intval($_GET['edit_id'])));
        }
        echo '<div class="wrap"><h1>Manage Banks</h1>';
        echo '<form method="get"><input type="hidden" name="page" value="epm-manage-banks">';
        echo '<select name="region" onchange="this.form.submit()">';
        foreach ($regions as $key => $label) {
            echo '<option value="' . esc_attr($key) . '"' . ($region === $key ? ' selected' : '') . '>' . esc_html($label) . '</option>';
        }
        echo '</select></form>';
        // Add/edit form
        echo '<form method="post" style="margin:20px 0;">';
        wp_nonce_field('epm_manage_banks');
        echo '<input type="hidden" name="epm_bank_action" value="1">';
        if ($edit_bank) echo '<input type="hidden" name="edit_id" value="' . esc_attr($edit_bank->id) . '">';
        echo '<input type="text" name="bank_name" value="' . esc_attr($edit_bank ? $edit_bank->name : '') . '" placeholder="Bank Name" required> ';
        echo '<label><input type="checkbox" name="is_active" value="1"' . ($edit_bank && $edit_bank->is_active ? ' checked' : '') . '> Active</label> ';
        echo '<button type="submit" class="button button-primary">' . ($edit_bank ? 'Update' : 'Add') . ' Bank</button>';
        if ($edit_bank) echo ' <a href="' . admin_url('admin.php?page=epm-manage-banks&region=' . $region) . '" class="button">Cancel</a>';
        echo '</form>';
        // List banks
        $banks = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE region = %s ORDER BY sort_order ASC", $region));
        echo '<table class="widefat"><thead><tr><th>Name</th><th>Active</th><th>Actions</th></tr></thead><tbody>';
        foreach ($banks as $bank) {
            echo '<tr>';
            echo '<td>' . esc_html($bank->name) . '</td>';
            echo '<td>' . ($bank->is_active ? 'Yes' : 'No') . '</td>';
            echo '<td><a href="' . admin_url('admin.php?page=epm-manage-banks&region=' . $region . '&edit_id=' . $bank->id) . '" class="button">Edit</a></td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
        echo '</div>';
    }
}
EPM_BankNamesAdmin::register();
