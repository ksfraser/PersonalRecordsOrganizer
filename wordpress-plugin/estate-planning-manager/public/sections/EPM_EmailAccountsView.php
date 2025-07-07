<?php
namespace EstatePlanningManager\Sections;

require_once __DIR__ . '/../models/EmailAccountsModel.php';
use EstatePlanningManager\Models\EmailAccountsModel;

class EPM_EmailAccountsView extends AbstractSectionView {
    public static function get_section_key() { return 'email_accounts'; }
    public static function get_fields($shortcodes = null) {
        $model = new EmailAccountsModel();
        return $model->getFormFields();
    }
    public static function render($client_id, $readonly = false) {
        $instance = new self();
        $instance->renderSectionView($client_id, $readonly);
    }
    public function getModel() { return new EmailAccountsModel(); }
    public function getSection() { return 'email_accounts'; }
}
