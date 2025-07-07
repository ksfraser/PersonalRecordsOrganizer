<?php
namespace EstatePlanningManager\Sections;

require_once __DIR__ . '/../models/OnlineAccountsModel.php';
use EstatePlanningManager\Models\OnlineAccountsModel;

class EPM_OnlineAccountsView extends AbstractSectionView {
    public static function get_section_key() { return 'online_accounts'; }
    public static function get_fields($shortcodes = null) {
        $model = new OnlineAccountsModel();
        return $model->getFormFields();
    }
    public static function render($client_id, $readonly = false) {
        $instance = new self();
        $instance->renderSectionView($client_id, $readonly);
    }
    public function getModel() { return new OnlineAccountsModel(); }
    public function getSection() { return 'online_accounts'; }
}
