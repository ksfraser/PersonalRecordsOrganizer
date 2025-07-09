<?php
namespace EstatePlanningManager\Admin;

if (!defined('ABSPATH')) exit;

class EPM_Admin_Insurance_Type {
    public static function render() {
        echo '<div class="wrap"><h1>Insurance Types</h1>';
        global $wpdb;
        $type_table = $wpdb->prefix . 'epm_insurance_type';
        $cat_table = $wpdb->prefix . 'epm_insurance_category';
        // Handle add
        if (isset($_POST['epm_add_insurance_type']) && !empty($_POST['epm_insurance_type_name']) && !empty($_POST['epm_insurance_type_category']) && check_admin_referer('epm_add_insurance_type', 'epm_add_insurance_type_nonce')) {
            $name = sanitize_text_field($_POST['epm_insurance_type_name']);
            $category_id = intval($_POST['epm_insurance_type_category']);
            $wpdb->insert($type_table, ['name' => $name, 'category_id' => $category_id]);
            echo '<div class="updated"><p>Type added.</p></div>';
        }
        // Handle edit
        if (isset($_POST['epm_edit_insurance_type']) && !empty($_POST['epm_insurance_type_name']) && !empty($_POST['epm_insurance_type_category']) && isset($_POST['epm_insurance_type_id']) && check_admin_referer('epm_edit_insurance_type', 'epm_edit_insurance_type_nonce')) {
            $id = intval($_POST['epm_insurance_type_id']);
            $name = sanitize_text_field($_POST['epm_insurance_type_name']);
            $category_id = intval($_POST['epm_insurance_type_category']);
            $wpdb->update($type_table, ['name' => $name, 'category_id' => $category_id], ['id' => $id]);
            echo '<div class="updated"><p>Type updated.</p></div>';
        }
        // Handle delete
        if (isset($_POST['epm_delete_insurance_type']) && isset($_POST['epm_insurance_type_id']) && check_admin_referer('epm_delete_insurance_type', 'epm_delete_insurance_type_nonce')) {
            $id = intval($_POST['epm_insurance_type_id']);
            $wpdb->delete($type_table, ['id' => $id]);
            echo '<div class="updated"><p>Type deleted.</p></div>';
        }
        // Add form
        $categories = $wpdb->get_results("SELECT * FROM $cat_table");
        echo '<form method="post" style="margin-bottom:20px;">';
        wp_nonce_field('epm_add_insurance_type', 'epm_add_insurance_type_nonce');
        echo '<input type="text" name="epm_insurance_type_name" placeholder="New Type Name" required> ';
        echo '<select name="epm_insurance_type_category" required><option value="">Select Category</option>';
        foreach ($categories as $cat) {
            echo '<option value="' . esc_attr($cat->id) . '">' . esc_html($cat->name) . '</option>';
        }
        echo '</select> ';
        echo '<button type="submit" name="epm_add_insurance_type" class="button button-primary">Add Type</button>';
        echo '</form>';
        // Edit form (if requested)
        if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
            $edit_id = intval($_GET['edit']);
            $edit_row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $type_table WHERE id = %d", $edit_id));
            if ($edit_row) {
                echo '<form method="post" style="margin-bottom:20px;background:#f9f9f9;padding:10px;">';
                wp_nonce_field('epm_edit_insurance_type', 'epm_edit_insurance_type_nonce');
                echo '<input type="hidden" name="epm_insurance_type_id" value="' . esc_attr($edit_row->id) . '">';
                echo '<input type="text" name="epm_insurance_type_name" value="' . esc_attr($edit_row->name) . '" required> ';
                echo '<select name="epm_insurance_type_category" required>';
                foreach ($categories as $cat) {
                    $selected = $edit_row->category_id == $cat->id ? 'selected' : '';
                    echo '<option value="' . esc_attr($cat->id) . '" ' . $selected . '>' . esc_html($cat->name) . '</option>';
                }
                echo '</select> ';
                echo '<button type="submit" name="epm_edit_insurance_type" class="button">Update Type</button>';
                echo '</form>';
            }
        }
        // Table
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr><th>ID</th><th>Name</th><th>Category</th><th>Actions</th></tr></thead><tbody>';
        $rows = $wpdb->get_results("SELECT t.id, t.name, t.category_id, c.name as category FROM $type_table t LEFT JOIN $cat_table c ON t.category_id = c.id");
        foreach ($rows as $row) {
            echo '<tr>';
            echo '<td>' . esc_html($row->id) . '</td>';
            echo '<td>' . esc_html($row->name) . '</td>';
            echo '<td>' . esc_html($row->category) . '</td>';
            echo '<td>';
            echo '<a href="?page=epm-insurance-types&edit=' . esc_attr($row->id) . '" class="button">Edit</a> ';
            echo '<form method="post" style="display:inline;" onsubmit="return confirm(\'Delete this type?\');">';
            wp_nonce_field('epm_delete_insurance_type', 'epm_delete_insurance_type_nonce');
            echo '<input type="hidden" name="epm_insurance_type_id" value="' . esc_attr($row->id) . '">';
            echo '<button type="submit" name="epm_delete_insurance_type" class="button button-link-delete">Delete</button>';
            echo '</form>';
            echo '</td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
        echo '</div>';
    }
}
