<?php
/**
 * EPM_AddAdvisorModal
 * Renders the Add Advisor modal HTML
 */
require_once __DIR__ . '/EPM_AbstractAddModal.php';
class EPM_AddAdvisorModal extends EPM_AbstractAddModal {
    protected function getModelClass() {
        return '\EstatePlanningManager\Models\AdvisorModel';
    }
    protected function getTitle() {
        return 'Add New Advisor';
    }
    protected function getModalId() { return 'epm-add-advisor-modal'; }
    protected function getFormId() { return 'epm-add-advisor-form'; }
    protected function getActionName() { return 'epm_add_advisor'; }
    protected function getNonceAction() { return 'epm_add_advisor'; }
    protected function getNonceName() { return 'epm_add_advisor_nonce'; }
}
