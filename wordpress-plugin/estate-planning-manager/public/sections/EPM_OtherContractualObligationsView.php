<?php
namespace EstatePlanningManager\Sections;


use EstatePlanningManager\Models\OtherContractualObligationsModel;
require_once __DIR__ . '/../models/OtherContractualObligationsModel.php';
require_once __DIR__ . '/AbstractSectionView.php';

class EPM_OtherContractualObligationsView extends AbstractSectionView {
    public static function get_section_key() {
        return 'other_contracts';
    }
    public static function get_fields($shortcodes) {
        return [
            ['name' => 'obligation_type', 'label' => 'Obligation Type', 'type' => 'text'],
            ['name' => 'counterparty', 'label' => 'Counterparty', 'type' => 'text'],
            ['name' => 'amount', 'label' => 'Amount', 'type' => 'text'],
            ['name' => 'start_date', 'label' => 'Start Date', 'type' => 'date'],
            ['name' => 'end_date', 'label' => 'End Date', 'type' => 'date'],
            ['name' => 'document_location', 'label' => 'Document Location', 'type' => 'text'],
            ['name' => 'description', 'label' => 'Description', 'type' => 'textarea'],
        ];
    }
    protected function getModel() {
        return new OtherContractualObligationsModel();
    }
    protected function getSection() {
        return 'other_contracts';
    }
    public static function render($client_id, $readonly = false) {
        $view = new self();
        $view->renderSectionView($client_id, $readonly);
    }
}
