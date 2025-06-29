<?php
// EPM_InsuranceView: Dedicated view class for the Insurance section
class EPM_InsuranceView {
    public static function render($client_id) {
        global $wpdb;
        require_once dirname(__DIR__, 2) . '/includes/tables/InsuranceTable.php';
        require_once dirname(__DIR__, 2) . '/includes/tables/PersonTable.php';
        $table = $wpdb->prefix . 'epm_insurance';
        $person_options = PersonTable::get_person_options($client_id);
        $records = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE client_id = %d", $client_id));
        echo '<div class="epm-data-section" data-section="insurance">';
        echo '<h3>Insurance Policies</h3>';
        if (empty($records)) {
            echo '<p class="epm-no-data">No insurance policies recorded.</p>';
        } else {
            echo '<table class="epm-data-table">';
            echo '<tr><th>Category</th><th>Type</th><th>Company</th><th>Policy #</th><th>Beneficiary</th><th>Advisor</th><th>Owner</th></tr>';
            foreach ($records as $row) {
                $beneficiary = isset($person_options[$row->beneficiary_person_id]) ? $person_options[$row->beneficiary_person_id] : '';
                $advisor = isset($person_options[$row->advisor_person_id]) ? $person_options[$row->advisor_person_id] : '';
                $owner = isset($person_options[$row->owner_person_id]) ? $person_options[$row->owner_person_id] : '';
                echo '<tr>';
                echo '<td>' . esc_html($row->insurance_category) . '</td>';
                echo '<td>' . esc_html($row->insurance_type) . '</td>';
                echo '<td>' . esc_html($row->insurance_company) . '</td>';
                echo '<td>' . esc_html($row->policy_number) . '</td>';
                echo '<td>' . esc_html($beneficiary) . '</td>';
                echo '<td>' . esc_html($advisor) . '</td>';
                echo '<td>' . esc_html($owner) . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }
        echo '</div>';
    }
}
