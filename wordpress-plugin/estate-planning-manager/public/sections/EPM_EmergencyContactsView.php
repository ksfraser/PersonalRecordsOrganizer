<?php
namespace EstatePlanningManager\Sections;

require_once __DIR__ . '/../models/EmergencyContactsModel.php';
use EstatePlanningManager\Models\EmergencyContactsModel;
use EPM_Shortcodes;


class EPM_EmergencyContactsView extends AbstractSectionView {
    public static function get_section_key() {
        return 'emergency_contacts';
    }
    public static function get_fields($shortcodes) {
        if ($shortcodes === null) {
            $shortcodes = \EPM_Shortcodes::instance();
        }
        return $shortcodes->get_form_sections()['emergency_contacts']['fields'];
    }
    public static function render($client_id, $readonly = false) {
        $instance = new self();
        $instance->renderSectionView($client_id, $readonly);
    }
    public function getModel() {
        return new EmergencyContactsModel();
    }
    public function getSection() {
        return 'emergency_contacts';
    }
}
