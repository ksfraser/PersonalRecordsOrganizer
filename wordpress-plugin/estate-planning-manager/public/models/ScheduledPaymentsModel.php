<?php
namespace EstatePlanningManager\Models;

require_once __DIR__ . '/AbstractSectionModel.php';

require_once __DIR__ . '/Sanitizer.php';

require_once __DIR__ . '/PeopleModel.php';

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
        $sanitized['paid_to_person_id'] = isset($data['paid_to_person_id']) ? Sanitizer::int($data['paid_to_person_id']) : null;
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
        $peopleOptions = [];
        if (class_exists('EstatePlanningManager\\Models\\PeopleModel')) {
            foreach (\EstatePlanningManager\Models\PeopleModel::getAllForDropdown() as $person) {
                $peopleOptions[$person['id']] = $person['full_name'];
            }
        }
        return [
            'payment_type' => [
                'label' => 'Payment Type',
                'type' => 'text',
                'required' => true,
                'db_type' => 'VARCHAR(100)'
            ],
            'paid_to_person_id' => [
                'label' => 'Paid To',
                'type' => 'select',
                'options' => $peopleOptions,
                'required' => false,
                'db_type' => 'BIGINT UNSIGNED'
            ],
            'is_automatic' => [
                'label' => 'Is Automatic',
                'type' => 'text',
                'required' => false,
                'db_type' => 'VARCHAR(10)'
            ],
            'amount' => [
                'label' => 'Amount',
                'type' => 'text',
                'required' => false,
                'db_type' => 'VARCHAR(100)'
            ],
            'due_date' => [
                'label' => 'Due Date',
                'type' => 'text',
                'required' => false,
                'db_type' => 'VARCHAR(100)'
            ],
        ];
    }
}
