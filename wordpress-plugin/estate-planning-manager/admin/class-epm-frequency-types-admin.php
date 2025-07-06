<?php
// Admin class for managing frequency types (formerly gift frequency types)
class EPM_FrequencyTypesAdmin {
    const SLUG = 'epm_frequency_types';
    public static function render() {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_frequency_types';
        $rows = $wpdb->get_results("SELECT * FROM $table ORDER BY sort_order ASC, label ASC", ARRAY_A);
        echo '<div class="wrap"><h2>Frequency Types</h2>';
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
