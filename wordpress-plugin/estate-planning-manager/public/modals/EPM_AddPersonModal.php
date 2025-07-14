<?php
/**
 * EPM_AddPersonModal
 * Renders the Add Person modal HTML
 */
require_once __DIR__ . '/EPM_AbstractAddModal.php';
class EPM_AddPersonModal extends EPM_AbstractAddModal {
    protected function getModelClass() {
        return '\EstatePlanningManager\Models\PersonModel';
    }
    protected function getTitle() {
        return 'Add Person';
    }
    protected function getModalId() { return 'epm-add-person-modal'; }
    protected function getFormId() { return 'epm-add-person-form'; }
    protected function getActionName() { return 'epm_add_person'; }
    protected function getNonceAction() { return 'epm_add_person'; }
    protected function getNonceName() { return 'epm_add_person_nonce'; }
}
