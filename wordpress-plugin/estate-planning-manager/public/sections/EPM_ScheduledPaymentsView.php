<?php
// EPM_ScheduledPaymentsView: Dedicated view class for the Scheduled Payments section
class EPM_ScheduledPaymentsView {
    public static function render($client_id) {
        global $wpdb;
        require_once dirname(__DIR__, 2) . '/includes/tables/ScheduledPaymentsTable.php';
        require_once dirname(__DIR__, 2) . '/includes/tables/OrganizationTable.php';
        $table = $wpdb->prefix . 'epm_scheduled_payments';
        $org_options = OrganizationTable::get_org_options($client_id);
        $records = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE client_id = %d", $client_id));
        echo '<div class="epm-data-section" data-section="scheduled_payments">';
        echo '<h3>Scheduled Payments</h3>';
        if (empty($records)) {
            echo '<p class="epm-no-data">No scheduled payments recorded.</p>';
        } else {
            echo '<table class="epm-data-table">';
            echo '<tr><th>Type</th><th>Paid To</th><th>Automatic?</th><th>Amount</th><th>Due Date</th></tr>';
            foreach ($records as $row) {
                $org = isset($org_options[$row->paid_to_org_id]) ? $org_options[$row->paid_to_org_id] : '';
                echo '<tr>';
                echo '<td>' . esc_html($row->payment_type) . '</td>';
                echo '<td>' . esc_html($org) . '</td>';
                echo '<td>' . esc_html($row->is_automatic) . '</td>';
                echo '<td>' . esc_html($row->amount) . '</td>';
                echo '<td>' . esc_html($row->due_date) . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }
        echo '</div>';
    }
}
