<?php
// EPM_RealEstateView: Dedicated view class for the Real Estate section
if (!defined('ABSPATH')) exit;
require_once __DIR__ . '/AbstractSectionView.php';

class EPM_RealEstateView extends AbstractSectionView {
    public static function get_section_key() {
        return 'real_estate';
    }
    public static function get_fields() {
        $shortcodes = EPM_Shortcodes::instance();
        return $shortcodes->get_form_sections()['real_estate']['fields'];
    }
}
