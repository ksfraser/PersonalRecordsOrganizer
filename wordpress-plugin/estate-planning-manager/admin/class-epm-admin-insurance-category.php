<?php
namespace EstatePlanningManager\Admin;

if (!defined('ABSPATH')) exit;

class EPM_Admin_Insurance_Category {
    public static function render() {
        echo '<div class="wrap"><h1>Insurance Categories</h1>';
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr><th>ID</th><th>Name</th></tr></thead><tbody>';
        global $wpdb;
        $table = $wpdb->prefix . 'epm_insurance_category';
        $rows = $wpdb->get_results("SELECT * FROM $table");
        foreach ($rows as $row) {
            echo '<tr><td>' . esc_html($row->id) . '</td><td>' . esc_html($row->name) . '</td></tr>';
        }
        echo '</tbody></table>';
        echo '</div>';
    }
}
