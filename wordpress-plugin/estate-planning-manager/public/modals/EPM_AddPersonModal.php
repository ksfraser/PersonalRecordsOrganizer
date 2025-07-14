<?php
/**
 * EPM_AddPersonModal
 * Renders the Add Person modal HTML
 */
require_once __DIR__ . '/EPM_AbstractAddModal.php';
class EPM_AddPersonModal extends EPM_AbstractAddModal {
    protected $modalKey = 'person';
    protected function getModelClass() {
        return '\EstatePlanningManager\Models\PersonModel';
    }
    protected function getTitle() {
        return 'Add Person';
    }
}
}
