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
        $sanitized['beneficiary_contact_id'] = isset($data['beneficiary_contact_id']) ? Sanitizer::int($data['beneficiary_contact_id']) : null;
        $sanitized['advisor_person_id'] = isset($data['advisor_person_id']) ? Sanitizer::int($data['advisor_person_id']) : null;
        $sanitized['owner_contact_id'] = isset($data['owner_contact_id']) ? Sanitizer::int($data['owner_contact_id']) : null;
        // New fields
        $sanitized['is_group_insurance'] = isset($data['is_group_insurance']) ? (bool)$data['is_group_insurance'] : false;
        if ($sanitized['is_group_insurance']) {
            if (!empty($data['group_insurance_sponsor_org_id']) && is_numeric($data['group_insurance_sponsor_org_id'])) {
                $sanitized['group_insurance_sponsor_org_id'] = Sanitizer::int($data['group_insurance_sponsor_org_id']);
            } else {
                $sanitized['group_insurance_sponsor_org_id'] = null;
                // Optionally require sponsor if group insurance is set
                // $errors[] = 'Group Insurance Sponsor is required for group insurance.';
            }
        } else {
            $sanitized['group_insurance_sponsor_org_id'] = null;
        }
        // Remove old fields
        // ...existing code...
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
        $fields = [
            ['name' => 'insurance_category', 'label' => 'Insurance Category'],
            ['name' => 'insurance_type', 'label' => 'Insurance Type'],
            ['name' => 'insurance_company', 'label' => 'Insurance Company'],
            ['name' => 'policy_number', 'label' => 'Policy Number'],
            ['name' => 'beneficiary_contact_id', 'label' => 'Beneficiary (Contact)'],
            ['name' => 'advisor_person_id', 'label' => 'Advisor'],
            ['name' => 'owner_contact_id', 'label' => 'Owner (Contact)'],
            // New fields
            ['name' => 'is_group_insurance', 'label' => 'Is Group Insurance', 'type' => 'checkbox'],
            [
                'name' => 'group_insurance_sponsor_org_id',
                'label' => 'Group Insurance Sponsor',
                'type' => 'select',
                'options' => function() {
                    return function_exists('epm_get_organization_dropdown_options') ? epm_get_organization_dropdown_options() : [];
                },
                'show_if' => ['is_group_insurance' => true],
                'add_button' => true // For Add Sponsor modal
            ],
        ];
        return $fields;
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
            'beneficiary_contact_id' => [
                'label' => 'Beneficiary (Contact)',
                'type' => 'select',
                'options' => function() {
                    return function_exists('epm_get_contact_dropdown_options') ? epm_get_contact_dropdown_options() : [];
                },
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
            'owner_contact_id' => [
                'label' => 'Owner (Contact)',
                'type' => 'select',
                'options' => function() {
                    return function_exists('epm_get_contact_dropdown_options') ? epm_get_contact_dropdown_options() : [];
                },
                'required' => false,
                'db_type' => 'BIGINT UNSIGNED'
            ],
            // New fields
            'is_group_insurance' => [
                'label' => 'Is Group Insurance',
                'type' => 'checkbox',
                'required' => false,
                'db_type' => 'TINYINT(1) DEFAULT 0'
            ],
            'group_insurance_sponsor_org_id' => [
                'label' => 'Group Insurance Sponsor',
                'type' => 'select',
                'options' => function() {
                    return function_exists('epm_get_organization_dropdown_options') ? epm_get_organization_dropdown_options() : [];
                },
                'required' => false,
                'db_type' => 'BIGINT UNSIGNED',
                'show_if' => ['is_group_insurance' => true],
                'add_button' => true
            ],
        ];
    }
}
