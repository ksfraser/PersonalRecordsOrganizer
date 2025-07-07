<?php
if (!defined('ABSPATH')) exit;

class EPM_PasswordStorageTypesAdmin {
    const SLUG = 'epm_password_storage_types';
    public static function render() {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_password_storage_types';
        // Handle add/edit/delete actions
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && current_user_can('manage_options')) {
            if (isset($_POST['epm_add_storage_type_nonce']) && wp_verify_nonce($_POST['epm_add_storage_type_nonce'], 'epm_add_storage_type')) {
                $value = sanitize_key($_POST['value']);
                $label = sanitize_text_field($_POST['label']);
                $is_active = isset($_POST['is_active']) ? 1 : 0;
                if ($value && $label) {
                    $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE value = %s", $value));
                    if (!$exists) {
                        $wpdb->insert($table, [
                            'value' => $value,
                            'label' => $label,
                            'is_active' => $is_active,
                            'sort_order' => 0
                        ]);
                        echo '<div class="updated"><p>Type added.</p></div>';
                    } else {
                        echo '<div class="error"><p>Type value already exists.</p></div>';
                    }
                }
            }
            if (isset($_POST['epm_edit_storage_type_nonce']) && wp_verify_nonce($_POST['epm_edit_storage_type_nonce'], 'epm_edit_storage_type')) {
                $id = intval($_POST['id']);
                $label = sanitize_text_field($_POST['label']);
                $is_active = isset($_POST['is_active']) ? 1 : 0;
                if ($id && $label) {
                    $wpdb->update($table, [
                        'label' => $label,
                        'is_active' => $is_active
                    ], ['id' => $id]);
                    echo '<div class="updated"><p>Type updated.</p></div>';
                }
            }
            if (isset($_POST['epm_delete_storage_type_nonce']) && wp_verify_nonce($_POST['epm_delete_storage_type_nonce'], 'epm_delete_storage_type')) {
                $id = intval($_POST['id']);
                if ($id) {
                    $wpdb->delete($table, ['id' => $id]);
                    echo '<div class="updated"><p>Type deleted.</p></div>';
                }
            }
        }
        $rows = $wpdb->get_results("SELECT * FROM $table ORDER BY sort_order ASC, label ASC", ARRAY_A);
        echo '<div class="wrap"><h2>Password Storage Types</h2>';
        // Add form
        echo '<h3>Add New Type</h3>';
        echo '<form method="post">';
        wp_nonce_field('epm_add_storage_type', 'epm_add_storage_type_nonce');
        echo '<label>Value: <input type="text" name="value" required></label> ';
        echo '<label>Label: <input type="text" name="label" required></label> ';
        echo '<label><input type="checkbox" name="is_active" checked> Active</label> ';
        echo '<button type="submit" class="button button-primary">Add</button>';
        echo '</form>';
        // Table
        echo '<h3>Existing Types</h3>';
        echo '<table class="widefat"><thead><tr><th>Label</th><th>Value</th><th>Active</th><th>Actions</th></tr></thead><tbody>';
        foreach ($rows as $row) {
            echo '<tr>';
            echo '<form method="post" style="display:inline">';
            wp_nonce_field('epm_edit_storage_type', 'epm_edit_storage_type_nonce');
            echo '<td><input type="text" name="label" value="' . esc_attr($row['label']) . '" required></td>';
            echo '<td>' . esc_html($row['value']) . '</td>';
            echo '<td><input type="checkbox" name="is_active"' . ($row['is_active'] ? ' checked' : '') . '></td>';
            echo '<td>';
            echo '<input type="hidden" name="id" value="' . intval($row['id']) . '">';
            echo '<button type="submit" class="button">Update</button>';
            echo '</form>';
            echo '<form method="post" style="display:inline;margin-left:5px;">';
            wp_nonce_field('epm_delete_storage_type', 'epm_delete_storage_type_nonce');
            echo '<input type="hidden" name="id" value="' . intval($row['id']) . '">';
            echo '<button type="submit" class="button" onclick="return confirm(\'Delete this type?\')">Delete</button>';
            echo '</form>';
            echo '</td>';
            echo '</tr>';
        }
        echo '</tbody></table></div>';
    }
}
