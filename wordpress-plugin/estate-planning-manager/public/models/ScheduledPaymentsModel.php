<?php
namespace EstatePlanningManager\Models;

require_once __DIR__ . '/AbstractSectionModel.php';

require_once __DIR__ . '/Sanitizer.php';

require_once __DIR__ . '/PeopleModel.php';

if (!defined('ABSPATH')) exit;

class ScheduledPaymentsModel extends AbstractSectionModel {
    // Use inherited getByClientId instance method.

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
        $payment_type_options = $this->getPaymentTypeOptions();
        $peopleOptions = $this->getPeopleOptions();
        $orgOptions = $this->getOrganizationOptions();
        return [
            ['name' => 'payment_type', 'label' => 'Payment Type', 'type' => 'select', 'options' => $payment_type_options],
            ['name' => 'person_org', 'label' => 'Paid To Type', 'type' => 'select', 'options' => [
                ['value' => 'person', 'label' => 'Person'],
                ['value' => 'organization', 'label' => 'Organization']
            ], 'help' => 'Select whether payment is to a person or organization'],
            ['name' => 'paid_to_person_id', 'label' => 'Paid To (Person)', 'type' => 'select', 'options' => $peopleOptions],
            ['name' => 'paid_to_org_id', 'label' => 'Paid To (Organization)', 'type' => 'select', 'options' => $orgOptions],
            ['name' => 'account_number', 'label' => 'Account Number', 'type' => 'text'],
            ['name' => 'is_automatic', 'label' => 'Is Automatic'],
            ['name' => 'amount', 'label' => 'Amount'],
            ['name' => 'due_date', 'label' => 'Due Date'],
        ];
    }

    protected function getPaymentTypeOptions() {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_scheduled_payment_types';
        $rows = $wpdb->get_results("SELECT value, label FROM $table WHERE is_active = 1 ORDER BY sort_order ASC, label ASC", ARRAY_A);
        $options = [];
        foreach ($rows as $row) {
            $options[] = [ 'value' => $row['value'], 'label' => $row['label'] ];
        }
        return $options;
    }
    protected function getPeopleOptions() {
        if (class_exists('EstatePlanningManager\\Models\\PeopleModel')) {
            $opts = [];
            foreach (\EstatePlanningManager\Models\PeopleModel::getAllForDropdown() as $person) {
                $opts[] = [ 'value' => $person['id'], 'label' => $person['full_name'] ];
            }
            return $opts;
        }
        return [];
    }
    protected function getOrganizationOptions() {
        if (class_exists('EstatePlanningManager\\Models\\OrganizationModel')) {
            $opts = [];
            foreach (\EstatePlanningManager\Models\OrganizationModel::getAllForDropdown() as $org) {
                $opts[] = [ 'value' => $org['id'], 'label' => $org['name'] ];
            }
            return $opts;
        }
        return [];
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
        $orgOptions = [];
        if (class_exists('EstatePlanningManager\\Models\\OrganizationModel')) {
            foreach (\EstatePlanningManager\Models\OrganizationModel::getAllForDropdown() as $org) {
                $orgOptions[$org['id']] = $org['name'];
            }
        }
        return [
            'payment_type' => [
                'label' => 'Payment Type',
                'type' => 'text',
                'required' => true,
                'db_type' => 'VARCHAR(100)'
            ],
            'person_org' => [
                'label' => 'Paid To Type',
                'type' => 'text',
                'required' => true,
                'db_type' => 'VARCHAR(20)'
            ],
            'paid_to_person_id' => [
                'label' => 'Paid To (Person)',
                'type' => 'select',
                'options' => $peopleOptions,
                'required' => false,
                'db_type' => 'BIGINT UNSIGNED'
            ],
            'paid_to_org_id' => [
                'label' => 'Paid To (Organization)',
                'type' => 'select',
                'options' => $orgOptions,
                'required' => false,
                'db_type' => 'BIGINT UNSIGNED'
            ],
            'account_number' => [
                'label' => 'Account Number',
                'type' => 'text',
                'required' => false,
                'db_type' => 'VARCHAR(100)'
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
