<?php
namespace EstatePlanningManager\Admin;

if (!defined('ABSPATH')) exit;

class EPM_Admin_Insurance_Type {
    public static function render() {
        echo '<div class="wrap"><h1>Insurance Types</h1>';
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr><th>ID</th><th>Name</th><th>Category</th></tr></thead><tbody>';
        global $wpdb;
        $type_table = $wpdb->prefix . 'epm_insurance_type';
        $cat_table = $wpdb->prefix . 'epm_insurance_category';
        $rows = $wpdb->get_results("SELECT t.id, t.name, c.name as category FROM $type_table t LEFT JOIN $cat_table c ON t.category_id = c.id");
        foreach ($rows as $row) {
            echo '<tr><td>' . esc_html($row->id) . '</td><td>' . esc_html($row->name) . '</td><td>' . esc_html($row->category) . '</td></tr>';
        }
        echo '</tbody></table>';
        echo '</div>';
    }
}
