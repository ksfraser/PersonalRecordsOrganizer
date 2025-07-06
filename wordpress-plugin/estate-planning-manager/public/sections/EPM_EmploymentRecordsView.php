<?php
namespace EstatePlanningManager\Sections;

require_once __DIR__ . '/../models/EmploymentRecordsModel.php';
use EstatePlanningManager\Models\EmploymentRecordsModel;

class EPM_EmploymentRecordsView extends AbstractSectionView {
    public static function get_section_key() {
        return 'employment_records';
    }
    public static function get_fields($shortcodes = null) {
        return EmploymentRecordsModel::getFormFields();
    }
    public static function render($client_id, $readonly = false) {
        $instance = new self();
        $instance->renderSectionView($client_id, $readonly);
    }
    public function getModel() {
        return new EmploymentRecordsModel();
    }
    public function getSection() {
        return 'employment_records';
    }
}
