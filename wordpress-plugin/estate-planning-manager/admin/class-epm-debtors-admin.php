<?php
if (!defined('ABSPATH')) exit;

class EPM_DebtorsAdmin {
    const SLUG = 'epm_debtors';
    public static function render() {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_debtors';
        $rows = $wpdb->get_results("SELECT * FROM $table ORDER BY created DESC", ARRAY_A);
        echo '<div class="wrap"><h2>Debtors</h2>';
        echo '<table class="widefat"><thead><tr><th>ID</th><th>Type</th><th>Contact</th><th>Account #</th><th>Amount</th><th>Scheduled Payment</th><th>Date</th><th>Description</th></tr></thead><tbody>';
        foreach ($rows as $row) {
            echo '<tr>';
            echo '<td>' . intval($row['id']) . '</td>';
            echo '<td>' . esc_html($row['person_org']) . '</td>';
            echo '<td>' . esc_html($row['contact_id']) . '</td>';
            echo '<td>' . esc_html($row['account_number']) . '</td>';
            echo '<td>' . esc_html($row['amount']) . '</td>';
            echo '<td>' . esc_html($row['scheduled_payment_id']) . '</td>';
            echo '<td>' . esc_html($row['date_of_loan']) . '</td>';
            echo '<td>' . esc_html($row['description']) . '</td>';
            echo '</tr>';
        }
        echo '</tbody></table></div>';
    }
}
