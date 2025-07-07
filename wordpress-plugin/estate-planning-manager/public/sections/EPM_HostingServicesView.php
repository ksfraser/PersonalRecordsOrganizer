<?php
namespace EstatePlanningManager\Sections;

require_once __DIR__ . '/../models/HostingServicesModel.php';
use EstatePlanningManager\Models\HostingServicesModel;

class EPM_HostingServicesView extends AbstractSectionView {
    public static function get_section_key() { return 'hosting_services'; }
    public static function get_fields($shortcodes = null) {
        $model = new HostingServicesModel();
        return $model->getFormFields();
    }
    public static function render($client_id, $readonly = false) {
        $instance = new self();
        $instance->renderSectionView($client_id, $readonly);
    }
    public function getModel() { return new HostingServicesModel(); }
    public function getSection() { return 'hosting_services'; }
}
