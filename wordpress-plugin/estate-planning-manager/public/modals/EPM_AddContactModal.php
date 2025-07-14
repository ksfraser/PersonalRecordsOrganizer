<?php
/**
 * EPM_AddContactModal
 * Renders the Add Contact modal HTML for Investments
 */
require_once __DIR__ . '/EPM_AbstractAddModal.php';
class EPM_AddContactModal extends EPM_AbstractAddModal {
    protected function getModelClass() {
        return '\EstatePlanningManager\Models\ContactModel';
    }
    protected function getTitle() {
        return 'Add Contact';
    }
    protected function getModalId() { return 'epm-add-contact-modal'; }
    protected function getFormId() { return 'epm-add-contact-form'; }
    protected function getActionName() { return 'epm_add_contact'; }
    protected function getNonceAction() { return 'epm_add_contact'; }
    protected function getNonceName() { return 'epm_add_contact_nonce'; }
}
