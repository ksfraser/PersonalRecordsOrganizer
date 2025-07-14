<?php
/**
 * EPM_AddAdvisorModal
 * Renders the Add Advisor modal HTML
 */
require_once __DIR__ . '/EPM_AbstractAddModal.php';
class EPM_AddAdvisorModal extends EPM_AbstractAddModal {
    protected $modalKey = 'advisor';
    protected function getModelClass() {
        return '\EstatePlanningManager\Models\AdvisorModel';
    }
    protected function getTitle() {
        return 'Add New Advisor';
    }
// End of EPM_AddAdvisorModal class
}
