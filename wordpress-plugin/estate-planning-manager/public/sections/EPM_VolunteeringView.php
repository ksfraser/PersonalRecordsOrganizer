<?php
namespace EstatePlanningManager\Sections;

require_once __DIR__ . '/../models/VolunteeringModel.php';
use EstatePlanningManager\Models\VolunteeringModel;

class EPM_VolunteeringView extends AbstractSectionView {
    public static function get_section_key() {
        return 'volunteering';
    }
    public static function get_fields($shortcodes = null) {
        return VolunteeringModel::getFormFields();
    }
    public static function render($client_id, $readonly = false) {
        $instance = new self();
        $instance->renderSectionView($client_id, $readonly);
    }
    public function getModel() {
        return new VolunteeringModel();
    }
    public function getSection() {
        return 'volunteering';
    }
}
