<?php
// EPM_RealEstateView: Dedicated view class for the Real Estate section
class EPM_RealEstateView {
    public static function render($client_id) {
        global $wpdb;
        require_once dirname(__DIR__, 2) . '/includes/tables/RealEstateTable.php';
        require_once dirname(__DIR__, 2) . '/includes/tables/PersonTable.php';
        $table = $wpdb->prefix . 'epm_real_estate';
        $person_options = PersonTable::get_person_options($client_id);
        $records = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE client_id = %d", $client_id));
        echo '<div class="epm-data-section" data-section="real_estate">';
        echo '<h3>Real Estate</h3>';
        if (empty($records)) {
            echo '<p class="epm-no-data">No real estate recorded.</p>';
        } else {
            echo '<table class="epm-data-table">';
            echo '<tr><th>Type</th><th>Title Held By</th><th>Address</th><th>Has Mortgage?</th><th>Lender</th></tr>';
            foreach ($records as $row) {
                $lender = isset($person_options[$row->lender_person_id]) ? $person_options[$row->lender_person_id] : '';
                echo '<tr>';
                echo '<td>' . esc_html($row->property_type) . '</td>';
                echo '<td>' . esc_html($row->title_held_by) . '</td>';
                echo '<td>' . esc_html($row->address) . '</td>';
                echo '<td>' . esc_html($row->has_mortgage) . '</td>';
                echo '<td>' . esc_html($lender) . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }
        echo '</div>';
    }
}
