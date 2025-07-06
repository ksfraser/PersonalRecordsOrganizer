<?php

// This file has been renamed to class-epm-frequency-types-admin.php. Please update any references accordingly.


if (!defined('ABSPATH')) exit;

class EPM_FrequencyTypesAdmin {
    const SLUG = 'epm_gift_frequency_types';
    public static function render() {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_gift_frequency_types';
        $rows = $wpdb->get_results("SELECT * FROM $table ORDER BY sort_order ASC, label ASC", ARRAY_A);
        echo '<div class="wrap"><h2>Gift Frequency Types</h2>';
        echo '<table class="widefat"><thead><tr><th>Label</th><th>Value</th><th>Active</th></tr></thead><tbody>';
        foreach ($rows as $row) {
            echo '<tr>';
            echo '<td>' . esc_html($row['label']) . '</td>';
            echo '<td>' . esc_html($row['value']) . '</td>';
            echo '<td>' . ($row['is_active'] ? 'Yes' : 'No') . '</td>';
            echo '</tr>';
        }
        echo '</tbody></table></div>';
    }
}

