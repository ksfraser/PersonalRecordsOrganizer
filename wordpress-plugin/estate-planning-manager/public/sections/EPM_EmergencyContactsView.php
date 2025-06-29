<?php
// View class for Emergency Contacts section
if (!defined('ABSPATH')) exit;
require_once __DIR__ . '/AbstractSectionView.php';

class EPM_EmergencyContactsView extends AbstractSectionView {
    public static function get_section_key() {
        return 'emergency_contacts';
    }
    public static function get_fields() {
        $shortcodes = EPM_Shortcodes::instance();
        return $shortcodes->get_form_sections()['emergency_contacts']['fields'];
    }
}
