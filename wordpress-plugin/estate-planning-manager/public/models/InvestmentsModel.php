<?php
namespace EstatePlanningManager\Models;

require_once __DIR__ . '/AbstractSectionModel.php';

require_once __DIR__ . '/Sanitizer.php';

require_once __DIR__ . '/PeopleModel.php';

use EstatePlanningManager\Models\PeopleModel;

if (!defined('ABSPATH')) exit;

class InvestmentsModel extends AbstractSectionModel {
    public static function getByClientId($clientId) {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_investments';
        return $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE client_id = %d", $clientId));
    }

    /**
     * Validate and sanitize investments data before insert/update
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
        $sanitized['investment_type'] = isset($data['investment_type']) ? Sanitizer::text($data['investment_type']) : null;
        $sanitized['financial_company'] = isset($data['financial_company']) ? Sanitizer::text($data['financial_company']) : null;
        $sanitized['account_number'] = isset($data['account_number']) ? Sanitizer::text($data['account_number']) : null;
        $sanitized['beneficiary_person_id'] = isset($data['beneficiary_person_id']) ? Sanitizer::int($data['beneficiary_person_id']) : null;
        $sanitized['advisor_person_id'] = isset($data['advisor_person_id']) ? Sanitizer::int($data['advisor_person_id']) : null;
        $sanitized['lender'] = isset($data['lender']) ? Sanitizer::text($data['lender']) : null;
        // Add more fields as needed
        return [empty($errors), $errors, $sanitized];
    }

    public function getTableName() {
        global $wpdb;
        return $wpdb->prefix . 'epm_investments';
    }
    public function getOwnerIdForSection($section, $client_id) {
        return $client_id;
    }
    public function getSummaryFields() {
        return ['id', 'investment_name'];
    }
    public function getFormFields() {
        return [
            ['name' => 'investment_type', 'label' => 'Investment Type'],
            ['name' => 'financial_company', 'label' => 'Financial Company'],
            ['name' => 'account_number', 'label' => 'Account Number'],
            ['name' => 'beneficiary_person_id', 'label' => 'Beneficiary'],
            ['name' => 'advisor_person_id', 'label' => 'Advisor'],
            ['name' => 'lender', 'label' => 'Lender'],
        ];
    }
    public static function get_section_key() {
        return 'investments';
    }
    public static function getFieldDefinitions() {
        return [
            'investment_type' => [
                'label' => 'Type of Investment',
                'type' => 'text',
                'required' => true,
                'db_type' => 'VARCHAR(100)'
            ],
            'financial_company' => [
                'label' => 'Financial Company',
                'type' => 'text',
                'required' => true,
                'db_type' => 'VARCHAR(255)'
            ],
            'account_number' => [
                'label' => 'Account Number',
                'type' => 'text',
                'required' => true,
                'db_type' => 'VARCHAR(255)'
            ],
            'beneficiary_person_id' => [
                'label' => 'Beneficiary',
                'type' => 'select',
                'options' => PeopleModel::getDropdownOptions(),
                'required' => false,
                'db_type' => 'BIGINT UNSIGNED'
            ],
            'advisor_person_id' => [
                'label' => 'Advisor',
                'type' => 'select',
                'options' => PeopleModel::getDropdownOptions(),
                'required' => false,
                'db_type' => 'BIGINT UNSIGNED'
            ],
            'lender' => [
                'label' => 'Lender',
                'type' => 'text',
                'required' => false,
                'db_type' => 'VARCHAR(255)'
            ],
        ];
    }
}
