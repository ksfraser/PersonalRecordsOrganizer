<?php
/**
 * EPM_AddContactModal
 * Renders the Add Contact modal HTML for Investments
 */
require_once __DIR__ . '/EPM_AbstractAddModal.php';
class EPM_AddContactModal extends EPM_AbstractAddModal {
    protected $modalKey = 'contact';
    protected function getModelClass() {
        return '\EstatePlanningManager\Models\ContactModel';
    }
    protected function getTitle() {
        return 'Add Contact';
    }
// End of EPM_AddContactModal class
}
