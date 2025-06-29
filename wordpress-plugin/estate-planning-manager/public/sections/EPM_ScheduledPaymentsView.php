<?php
// EPM_ScheduledPaymentsView: Dedicated view class for the Scheduled Payments section
if (!defined('ABSPATH')) exit;
require_once __DIR__ . '/AbstractSectionView.php';

class EPM_ScheduledPaymentsView extends AbstractSectionView {
    public static function get_section_key() {
        return 'scheduled_payments';
    }
    public static function get_fields() {
        $shortcodes = EPM_Shortcodes::instance();
        return $shortcodes->get_form_sections()['scheduled_payments']['fields'];
    }
}
