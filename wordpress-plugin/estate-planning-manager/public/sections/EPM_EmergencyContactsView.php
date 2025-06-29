<?php
namespace EstatePlanningManager\Sections;

use EPM_Shortcodes;

require_once __DIR__ . '/AbstractSectionView.php';

class EPM_EmergencyContactsView extends AbstractSectionView {
    public static function get_section_key() {
        return 'emergency_contacts';
    }
    public static function get_fields($shortcodes) {
        return $shortcodes->get_form_sections()['emergency_contacts']['fields'];
    }
}
