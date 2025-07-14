<?php
/**
 * EPM_AddInstituteModal
 * Renders the Add Institute/Organization modal HTML
 */
require_once __DIR__ . '/EPM_AbstractAddModal.php';
class EPM_AddPasswordManagement extends EPM_AbstractAddModal {
    protected $modalKey = 'password_management';
    protected function getModelClass() {
        return '\EstatePlanningManager\Models\PasswordManagementModel';
    }
    protected function getTitle() {
        return 'Add Password Management Record';
    }
}
