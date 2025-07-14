<?php
/**
 * EPM_AddPaymentTypeModal
 * Renders the Add Payment Type modal HTML for Scheduled Payments
 */
require_once __DIR__ . '/EPM_AbstractAddModal.php';
class EPM_AddPaymentTypeModal extends EPM_AbstractAddModal {
    protected $modalKey = 'payment_type';
    protected function getModelClass() {
        return '\EstatePlanningManager\Models\PaymentTypeModel';
    }
    protected function getTitle() {
        return 'Add Scheduled Payment Type';
    }
}
