<?php
namespace EstatePlanningManager\Admin;

if (!defined('ABSPATH')) exit;

class EPM_Admin_Insurance_Category {
    public static function render() {
        echo '<div class="wrap"><h1>Insurance Categories</h1>';
        global $wpdb;
        $table = $wpdb->prefix . 'epm_insurance_category';
        // Handle add
        if (isset($_POST['epm_add_insurance_category']) && !empty($_POST['epm_insurance_category_name']) && check_admin_referer('epm_add_insurance_category', 'epm_add_insurance_category_nonce')) {
            $name = sanitize_text_field($_POST['epm_insurance_category_name']);
            $wpdb->insert($table, ['name' => $name]);
            echo '<div class="updated"><p>Category added.</p></div>';
        }
        // Handle edit
        if (isset($_POST['epm_edit_insurance_category']) && !empty($_POST['epm_insurance_category_name']) && isset($_POST['epm_insurance_category_id']) && check_admin_referer('epm_edit_insurance_category', 'epm_edit_insurance_category_nonce')) {
            $id = intval($_POST['epm_insurance_category_id']);
            $name = sanitize_text_field($_POST['epm_insurance_category_name']);
            $wpdb->update($table, ['name' => $name], ['id' => $id]);
            echo '<div class="updated"><p>Category updated.</p></div>';
        }
        // Handle delete
        if (isset($_POST['epm_delete_insurance_category']) && isset($_POST['epm_insurance_category_id']) && check_admin_referer('epm_delete_insurance_category', 'epm_delete_insurance_category_nonce')) {
            $id = intval($_POST['epm_insurance_category_id']);
            $wpdb->delete($table, ['id' => $id]);
            echo '<div class="updated"><p>Category deleted.</p></div>';
        }
        // Add form
        echo '<form method="post" style="margin-bottom:20px;">';
        wp_nonce_field('epm_add_insurance_category', 'epm_add_insurance_category_nonce');
        echo '<input type="text" name="epm_insurance_category_name" placeholder="New Category Name" required> ';
        echo '<button type="submit" name="epm_add_insurance_category" class="button button-primary">Add Category</button>';
        echo '</form>';
        // Edit form (if requested)
        if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
            $edit_id = intval($_GET['edit']);
            $edit_row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $edit_id));
            if ($edit_row) {
                echo '<form method="post" style="margin-bottom:20px;background:#f9f9f9;padding:10px;">';
                wp_nonce_field('epm_edit_insurance_category', 'epm_edit_insurance_category_nonce');
                echo '<input type="hidden" name="epm_insurance_category_id" value="' . esc_attr($edit_row->id) . '">';
                echo '<input type="text" name="epm_insurance_category_name" value="' . esc_attr($edit_row->name) . '" required> ';
                echo '<button type="submit" name="epm_edit_insurance_category" class="button">Update Category</button>';
                echo '</form>';
            }
        }
        // Table
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr><th>ID</th><th>Name</th><th>Actions</th></tr></thead><tbody>';
        $rows = $wpdb->get_results("SELECT * FROM $table");
        foreach ($rows as $row) {
            echo '<tr>';
            echo '<td>' . esc_html($row->id) . '</td>';
            echo '<td>' . esc_html($row->name) . '</td>';
            echo '<td>';
            echo '<a href="?page=epm-insurance-categories&edit=' . esc_attr($row->id) . '" class="button">Edit</a> ';
            echo '<form method="post" style="display:inline;" onsubmit="return confirm(\'Delete this category?\');">';
            wp_nonce_field('epm_delete_insurance_category', 'epm_delete_insurance_category_nonce');
            echo '<input type="hidden" name="epm_insurance_category_id" value="' . esc_attr($row->id) . '">';
            echo '<button type="submit" name="epm_delete_insurance_category" class="button button-link-delete">Delete</button>';
            echo '</form>';
            echo '</td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
        echo '</div>';
    }
}
