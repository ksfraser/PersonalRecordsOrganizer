<?php
/**
 * Admin page for managing bank location types (regions).
 */
class EPM_BankLocationTypesAdmin {
    // Menu registration is now handled in EPM_AdminMenuHandler
    public static function render_admin_page() {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_bank_location_types';
        // Handle add/edit
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['epm_location_action'])) {
            check_admin_referer('epm_manage_locations');
            $label = sanitize_text_field($_POST['location_label']);
            $value = sanitize_text_field($_POST['location_value']);
            $is_active = isset($_POST['is_active']) ? 1 : 0;
            $edit_id = isset($_POST['edit_id']) ? intval($_POST['edit_id']) : 0;
            if ($edit_id) {
                $wpdb->update($table, [ 'label' => $label, 'value' => $value, 'is_active' => $is_active ], [ 'id' => $edit_id ]);
            } else {
                $max_sort = (int)$wpdb->get_var("SELECT MAX(sort_order) FROM $table");
                $wpdb->insert($table, [ 'label' => $label, 'value' => $value, 'is_active' => $is_active, 'sort_order' => $max_sort + 10 ]);
            }
            echo '<div class="updated"><p>Location saved.</p></div>';
        }
        // Edit mode
        $edit_loc = null;
        if (isset($_GET['edit_id'])) {
            $edit_loc = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", intval($_GET['edit_id'])));
        }
        echo '<div class="wrap"><h1>Bank Locations</h1>';
        // Add/edit form
        echo '<form method="post" style="margin:20px 0;">';
        wp_nonce_field('epm_manage_locations');
        echo '<input type="hidden" name="epm_location_action" value="1">';
        if ($edit_loc) echo '<input type="hidden" name="edit_id" value="' . esc_attr($edit_loc->id) . '">';
        echo '<input type="text" name="location_label" value="' . esc_attr($edit_loc ? $edit_loc->label : '') . '" placeholder="Label (e.g. Canada)" required> ';
        echo '<input type="text" name="location_value" value="' . esc_attr($edit_loc ? $edit_loc->value : '') . '" placeholder="Value (e.g. canada)" required> ';
        echo '<label><input type="checkbox" name="is_active" value="1"' . ($edit_loc && $edit_loc->is_active ? ' checked' : '') . '> Active</label> ';
        echo '<button type="submit" class="button button-primary">' . ($edit_loc ? 'Update' : 'Add') . ' Location</button>';
        if ($edit_loc) echo ' <a href="' . admin_url('admin.php?page=' . EPM_BANK_SELECTORS_SLUG . '&tab=' . EPM_BANK_LOCATIONS_TAB) . '" class="button">Cancel</a>';
        echo '</form>';
        // List locations
        $locs = $wpdb->get_results("SELECT * FROM $table ORDER BY sort_order ASC");
        echo '<table class="widefat"><thead><tr><th>Label</th><th>Value</th><th>Active</th><th>Actions</th></tr></thead><tbody>';
        foreach ($locs as $loc) {
            echo '<tr>';
            echo '<td>' . esc_html($loc->label) . '</td>';
            echo '<td>' . esc_html($loc->value) . '</td>';
            echo '<td>' . ($loc->is_active ? 'Yes' : 'No') . '</td>';
            echo '<td><a href="' . admin_url('admin.php?page=' . EPM_BANK_SELECTORS_SLUG . '&tab=' . EPM_BANK_LOCATIONS_TAB . '&edit_id=' . $loc->id) . '" class="button">Edit</a></td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
        echo '</div>';
    }
}
