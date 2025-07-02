<?php
namespace EstatePlanningManager\Models;

require_once __DIR__ . '/AbstractSectionModel.php';

require_once __DIR__ . '/Sanitizer.php';

if (!defined('ABSPATH')) exit;

class ScheduledPaymentsModel extends AbstractSectionModel {
    public static function getByClientId($clientId) {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_scheduled_payments';
        return $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE client_id = %d", $clientId));
    }

    /**
     * Validate and sanitize scheduled payments data before insert/update
     * @param array $data
     * @return array [is_valid, errors, sanitized_data]
     */
    public static function validateData($data) {
        $errors = array();
        $sanitized = array();
        if (empty($data['client_id']) || !is_numeric($data['client_id'])) {
            $errors[] = 'Client ID is required and must be numeric.';
        } else {
            $sanitized['client_id'] = Sanitizer::int($data['client_id']);
        }
        $sanitized['payment_type'] = isset($data['payment_type']) ? Sanitizer::text($data['payment_type']) : null;
        $sanitized['paid_to'] = isset($data['paid_to']) ? Sanitizer::text($data['paid_to']) : null;
        $sanitized['is_automatic'] = isset($data['is_automatic']) ? Sanitizer::text($data['is_automatic']) : null;
        $sanitized['amount'] = isset($data['amount']) ? Sanitizer::text($data['amount']) : null;
        $sanitized['due_date'] = isset($data['due_date']) ? Sanitizer::text($data['due_date']) : null;
        // Add more fields as needed
        return [empty($errors), $errors, $sanitized];
    }

    public function getTableName() {
        global $wpdb;
        return $wpdb->prefix . 'epm_scheduled_payments';
    }
    public function getOwnerIdForSection($section, $client_id) {
        return $client_id;
    }
    public function getSummaryFields() {
        return ['id', 'payment_name'];
    }
    public function getFormFields() {
        return [
            ['name' => 'payment_type', 'label' => 'Payment Type'],
            ['name' => 'paid_to', 'label' => 'Paid To'],
            ['name' => 'is_automatic', 'label' => 'Is Automatic'],
            ['name' => 'amount', 'label' => 'Amount'],
            ['name' => 'due_date', 'label' => 'Due Date'],
        ];
    }
    public static function get_section_key() {
        return 'scheduled_payments';
    }
    public static function getFieldDefinitions() {
        return [
            'payment_amount' => [
                'label' => 'Payment Amount',
                'type' => 'text',
                'required' => true,
                'db_type' => 'VARCHAR(255)'
            ],
            'payment_date' => [
                'label' => 'Payment Date',
                'type' => 'date',
                'required' => true,
                'db_type' => 'DATE'
            ],
        ];
    }
}
