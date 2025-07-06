<?php
namespace EstatePlanningManager\Sections;

require_once __DIR__ . '/../models/CharitableGiftsModel.php';
use EstatePlanningManager\Models\CharitableGiftsModel;

class EPM_CharitableGiftsView extends AbstractSectionView {
    public static function get_section_key() {
        return 'charitable_gifts';
    }
    public static function get_fields($shortcodes = null) {
        $model = new CharitableGiftsModel();
        return $model->getFormFields();
    }
    public static function render($client_id, $readonly = false) {
        $instance = new self();
        $instance->renderSectionView($client_id, $readonly);
    }
    public function getModel() {
        return new CharitableGiftsModel();
    }
    public function getSection() {
        return 'charitable_gifts';
    }
}
