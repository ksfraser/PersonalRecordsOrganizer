<?php
/**
 * EPM_AddInstituteModal
 * Renders the Add Institute/Organization modal HTML
 */
require_once __DIR__ . '/EPM_AbstractAddModal.php';
class EPM_AddInstituteModal extends EPM_AbstractAddModal {
    protected function getModelClass() {
        return '\EstatePlanningManager\Models\InstituteModel';
    }
    protected function getTitle() {
        return 'Add Institute/Organization';
    }
    protected $modalKey = 'institute';
    protected function getModalId() { return 'epm-add-' . $this->modalKey . '-modal'; }
    protected function getFormId() { return 'epm-add-' . $this->modalKey . '-form'; }
    protected function getActionName() { return 'epm_add_' . $this->modalKey; }
    protected function getNonceAction() { return 'epm_add_' . $this->modalKey; }
    protected function getNonceName() { return 'epm_add_' . $this->modalKey . '_nonce'; }
}
