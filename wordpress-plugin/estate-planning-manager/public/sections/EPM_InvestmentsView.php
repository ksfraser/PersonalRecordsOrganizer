<?php
// EPM_InvestmentsView: Dedicated view class for the Investments section
if (!defined('ABSPATH')) exit;
require_once __DIR__ . '/AbstractSectionView.php';

class EPM_InvestmentsView extends AbstractSectionView {
    public static function get_section_key() {
        return 'investments';
    }
    public static function get_fields() {
        $shortcodes = EPM_Shortcodes::instance();
        return $shortcodes->get_form_sections()['investments']['fields'];
    }
    // Retain custom render for table of multiple records
    public static function render($client_id, $readonly = false) {
        global $wpdb;
        require_once dirname(__DIR__, 2) . '/includes/tables/InvestmentsTable.php';
        require_once dirname(__DIR__, 2) . '/includes/tables/PersonTable.php';
        require_once dirname(__DIR__, 2) . '/includes/tables/OrganizationTable.php';
        $table = $wpdb->prefix . 'epm_investments';
        $person_options = PersonTable::get_person_options($client_id);
        $org_options = OrganizationTable::get_org_options($client_id);
        $records = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE client_id = %d", $client_id));
        echo '<div class="epm-data-section" data-section="investments">';
        echo '<h3>Investment Accounts</h3>';
        if (empty($records)) {
            echo '<p class="epm-no-data">No investments recorded.</p>';
        } else {
            echo '<table class="epm-data-table">';
            echo '<tr><th>Type</th><th>Company</th><th>Account #</th><th>Beneficiary</th><th>Advisor</th><th>Lender</th></tr>';
            foreach ($records as $row) {
                $beneficiary = isset($person_options[$row->beneficiary_person_id]) ? $person_options[$row->beneficiary_person_id] : '';
                $advisor = isset($person_options[$row->advisor_person_id]) ? $person_options[$row->advisor_person_id] : '';
                $lender = '';
                if ($row->lender_type === 'person' && $row->lender_person_id) {
                    $lender = isset($person_options[$row->lender_person_id]) ? $person_options[$row->lender_person_id] : '';
                } elseif ($row->lender_type === 'organization' && $row->lender_org_id) {
                    $lender = isset($org_options[$row->lender_org_id]) ? $org_options[$row->lender_org_id] : '';
                }
                echo '<tr>';
                echo '<td>' . esc_html($row->investment_type) . '</td>';
                echo '<td>' . esc_html($row->financial_company) . '</td>';
                echo '<td>' . esc_html($row->account_number) . '</td>';
                echo '<td>' . esc_html($beneficiary) . '</td>';
                echo '<td>' . esc_html($advisor) . '</td>';
                echo '<td>' . esc_html($lender) . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }
        echo '</div>';
    }
}
