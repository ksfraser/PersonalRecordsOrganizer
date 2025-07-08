<?php
// Insurance selectors for UI
if (!function_exists('epm_get_insurance_category_options')) {
    function epm_get_insurance_category_options() {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_insurance_category';
        $rows = $wpdb->get_results("SELECT id, name FROM $table ORDER BY id");
        $options = [];
        foreach ($rows as $row) {
            $options[$row->id] = $row->name;
        }
        return $options;
    }
}
if (!function_exists('epm_get_insurance_type_options')) {
    function epm_get_insurance_type_options($category_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_insurance_type';
        $rows = $wpdb->get_results($wpdb->prepare("SELECT id, name FROM $table WHERE category_id = %d ORDER BY id", $category_id));
        $options = [];
        foreach ($rows as $row) {
            $options[$row->id] = $row->name;
        }
        return $options;
    }
}
