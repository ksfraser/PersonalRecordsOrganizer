<?php
namespace EstatePlanningManager\Models;

require_once __DIR__ . '/AbstractSectionModel.php';

require_once __DIR__ . '/Sanitizer.php';

use EstatePlanningManager\Models\PeopleModel;

if (!defined('ABSPATH')) exit;

class InsuranceModel extends AbstractSectionModel {
    public static function getByClientId($clientId) {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_insurance';
        return $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE client_id = %d", $clientId));
    }

    /**
     * Validate and sanitize insurance data before insert/update
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
        $sanitized['insurance_category'] = isset($data['insurance_category']) ? Sanitizer::text($data['insurance_category']) : null;
        $sanitized['insurance_type'] = isset($data['insurance_type']) ? Sanitizer::text($data['insurance_type']) : null;
        $sanitized['insurance_company'] = isset($data['insurance_company']) ? Sanitizer::text($data['insurance_company']) : null;
        $sanitized['policy_number'] = isset($data['policy_number']) ? Sanitizer::text($data['policy_number']) : null;
        $sanitized['beneficiary'] = isset($data['beneficiary']) ? Sanitizer::text($data['beneficiary']) : null;
        $sanitized['advisor_person_id'] = isset($data['advisor_person_id']) ? Sanitizer::int($data['advisor_person_id']) : null;
        $sanitized['owner_person_id'] = isset($data['owner_person_id']) ? Sanitizer::int($data['owner_person_id']) : null;
        // Add more fields as needed
        return [empty($errors), $errors, $sanitized];
    }

    public function getTableName() {
        global $wpdb;
        return $wpdb->prefix . 'epm_insurance';
    }
    public function getOwnerIdForSection($section, $client_id) {
        return $client_id;
    }
    public function getSummaryFields() {
        return ['id', 'policy_number'];
    }
    public function getFormFields() {
        return [
            ['name' => 'insurance_category', 'label' => 'Insurance Category'],
            ['name' => 'insurance_type', 'label' => 'Insurance Type'],
            ['name' => 'insurance_company', 'label' => 'Insurance Company'],
            ['name' => 'policy_number', 'label' => 'Policy Number'],
            ['name' => 'beneficiary', 'label' => 'Beneficiary'],
            ['name' => 'advisor_person_id', 'label' => 'Advisor'],
            ['name' => 'owner_person_id', 'label' => 'Owner'],
        ];
    }
    public static function get_section_key() {
        return 'insurance';
    }
    public static function getFieldDefinitions() {
        return [
            'insurance_category' => [
                'label' => 'Insurance Category',
                'type' => 'text',
                'required' => true,
                'db_type' => 'VARCHAR(100)'
            ],
            'insurance_type' => [
                'label' => 'Insurance Type',
                'type' => 'text',
                'required' => true,
                'db_type' => 'VARCHAR(100)'
            ],
            'insurance_company' => [
                'label' => 'Insurance Company',
                'type' => 'text',
                'required' => true,
                'db_type' => 'VARCHAR(255)'
            ],
            'policy_number' => [
                'label' => 'Policy Number',
                'type' => 'text',
                'required' => true,
                'db_type' => 'VARCHAR(255)'
            ],
            'beneficiary' => [
                'label' => 'Beneficiary',
                'type' => 'text',
                'required' => false,
                'db_type' => 'VARCHAR(255)'
            ],
            'advisor_person_id' => [
                'label' => 'Advisor',
                'type' => 'select',
                'options' => PeopleModel::getDropdownOptions(),
                'required' => false,
                'db_type' => 'BIGINT UNSIGNED'
            ],
            'owner_person_id' => [
                'label' => 'Owner',
                'type' => 'select',
                'options' => PeopleModel::getDropdownOptions(),
                'required' => false,
                'db_type' => 'BIGINT UNSIGNED'
            ],
        ];
    }
}
