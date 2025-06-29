<?php
// EPM_BankingView: Dedicated view class for the Banking section
class EPM_BankingView {
    public static function render($client_id) {
        global $wpdb;
        require_once dirname(__DIR__, 2) . '/includes/tables/BankAccountsTable.php';
        require_once dirname(__DIR__, 2) . '/includes/tables/PersonTable.php';
        $table = $wpdb->prefix . 'epm_bank_accounts';
        $person_options = PersonTable::get_person_options($client_id);
        $records = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE client_id = %d", $client_id));
        echo '<div class="epm-data-section" data-section="banking">';
        echo '<h3>Bank Accounts</h3>';
        if (empty($records)) {
            echo '<p class="epm-no-data">No bank accounts recorded.</p>';
        } else {
            echo '<table class="epm-data-table">';
            echo '<tr><th>Bank</th><th>Account Type</th><th>Account #</th><th>Branch</th><th>Owner</th><th>Advisor</th></tr>';
            foreach ($records as $row) {
                $owner = isset($person_options[$row->owner_person_id]) ? $person_options[$row->owner_person_id] : '';
                $advisor = isset($person_options[$row->advisor_person_id]) ? $person_options[$row->advisor_person_id] : '';
                echo '<tr>';
                echo '<td>' . esc_html($row->bank) . '</td>';
                echo '<td>' . esc_html($row->account_type) . '</td>';
                echo '<td>' . esc_html($row->account_number) . '</td>';
                echo '<td>' . esc_html($row->branch) . '</td>';
                echo '<td>' . esc_html($owner) . '</td>';
                echo '<td>' . esc_html($advisor) . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }
        echo '</div>';
    }
}
