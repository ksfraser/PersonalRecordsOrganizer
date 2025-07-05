<?php
/**
 * AJAX handler for dynamic bank name loading by location
 */
class EPM_GetBanksByLocationHandler {
    public static function register() {
        add_action('wp_ajax_epm_get_banks_by_location', [self::class, 'handle']);
    }
    public static function handle() {
        global $wpdb;
        $region = isset($_POST['location']) ? sanitize_text_field($_POST['location']) : '';
        $table = $wpdb->prefix . 'epm_bank_names';
        $results = $wpdb->get_results($wpdb->prepare("SELECT name FROM $table WHERE region = %s AND is_active = 1 ORDER BY sort_order ASC", $region));
        $banks = array();
        foreach ($results as $row) {
            $banks[] = $row->name;
        }
        wp_send_json_success($banks);
    }
}
