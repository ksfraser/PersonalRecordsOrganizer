<?php
// EPM_InsuranceView: Dedicated view class for the Insurance section
if (!defined('ABSPATH')) exit;
require_once __DIR__ . '/AbstractSectionView.php';

class EPM_InsuranceView extends AbstractSectionView {
    public static function get_section_key() {
        return 'insurance';
    }
    public static function get_fields() {
        $shortcodes = EPM_Shortcodes::instance();
        return $shortcodes->get_form_sections()['insurance']['fields'];
    }
}
