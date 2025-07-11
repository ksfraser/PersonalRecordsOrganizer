<?php
namespace EstatePlanningManager\Models;

require_once __DIR__ . '/AbstractSectionModel.php';

require_once __DIR__ . '/Sanitizer.php';

use EstatePlanningManager\Models\PeopleModel;

if (!defined('ABSPATH')) exit;

class InsuranceModel extends AbstractSectionModel {
    // Use inherited getByClientId instance method.

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
        // Category/type logic
        $sanitized['insurance_category_id'] = isset($data['insurance_category_id']) ? Sanitizer::int($data['insurance_category_id']) : null;
        $sanitized['insurance_type_id'] = isset($data['insurance_type_id']) ? Sanitizer::int($data['insurance_type_id']) : null;
        // Xref logic
        $xref_type = isset($data['insurance_category_id']) ? $data['insurance_category_id'] : null;
        // Assume: 1=Life, 2=Auto, 3=House (should use lookup)
        if ($xref_type == 2) { // Auto
            $sanitized['vehicle_id'] = isset($data['vehicle_id']) ? Sanitizer::int($data['vehicle_id']) : null;
            $sanitized['real_estate_id'] = null;
            $sanitized['insured_contact_ids'] = null;
        } elseif ($xref_type == 3) { // House
            $sanitized['real_estate_id'] = isset($data['real_estate_id']) ? Sanitizer::int($data['real_estate_id']) : null;
            $sanitized['vehicle_id'] = null;
            $sanitized['insured_contact_ids'] = null;
        } elseif ($xref_type == 1) { // Life
            $sanitized['insured_contact_ids'] = isset($data['insured_contact_ids']) && is_array($data['insured_contact_ids']) ? array_map('intval', $data['insured_contact_ids']) : [];
            $sanitized['vehicle_id'] = null;
            $sanitized['real_estate_id'] = null;
        } else {
            $sanitized['vehicle_id'] = null;
            $sanitized['real_estate_id'] = null;
            $sanitized['insured_contact_ids'] = null;
        }
        // ...existing code for other fields...
        $sanitized['insurance_company'] = isset($data['insurance_company']) ? Sanitizer::text($data['insurance_company']) : null;
        $sanitized['policy_number'] = isset($data['policy_number']) ? Sanitizer::text($data['policy_number']) : null;
        $sanitized['beneficiary_contact_id'] = isset($data['beneficiary_contact_id']) ? Sanitizer::int($data['beneficiary_contact_id']) : null;
        $sanitized['advisor_person_id'] = isset($data['advisor_person_id']) ? Sanitizer::int($data['advisor_person_id']) : null;
        $sanitized['owner_contact_id'] = isset($data['owner_contact_id']) ? Sanitizer::int($data['owner_contact_id']) : null;
        $sanitized['is_group_insurance'] = isset($data['is_group_insurance']) ? (bool)$data['is_group_insurance'] : false;
        if ($sanitized['is_group_insurance']) {
            if (!empty($data['group_insurance_sponsor_org_id']) && is_numeric($data['group_insurance_sponsor_org_id'])) {
                $sanitized['group_insurance_sponsor_org_id'] = Sanitizer::int($data['group_insurance_sponsor_org_id']);
            } else {
                $sanitized['group_insurance_sponsor_org_id'] = null;
            }
        } else {
            $sanitized['group_insurance_sponsor_org_id'] = null;
        }
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
            [
                'name' => 'insurance_category_id',
                'label' => 'Category',
                'type' => 'select',
                'options' => function() {
                    return function_exists('epm_get_insurance_category_options') ? epm_get_insurance_category_options() : [];
                },
                'required' => true
            ],
            [
                'name' => 'insurance_type_id',
                'label' => 'Type',
                'type' => 'select',
                'options' => function($data = []) {
                    if (isset($data['insurance_category_id']) && function_exists('epm_get_insurance_type_options')) {
                        return epm_get_insurance_type_options($data['insurance_category_id']);
                    }
                    return [];
                },
                'required' => true
            ],
            [
                'name' => 'vehicle_id',
                'label' => 'Vehicle',
                'type' => 'select',
                'options' => function() {
                    return function_exists('epm_get_vehicle_dropdown_options') ? epm_get_vehicle_dropdown_options() : [];
                },
                'show_if' => ['insurance_category_id' => [2]],
                'required' => false
            ],
            [
                'name' => 'real_estate_id',
                'label' => 'Real Estate',
                'type' => 'select',
                'options' => function() {
                    return function_exists('epm_get_real_estate_dropdown_options') ? epm_get_real_estate_dropdown_options() : [];
                },
                'show_if' => ['insurance_category_id' => [3]],
                'required' => false
            ],
            [
                'name' => 'insured_contact_ids',
                'label' => 'Insured Person(s)',
                'type' => 'multiselect',
                'options' => function() {
                    return function_exists('epm_get_contact_dropdown_options') ? epm_get_contact_dropdown_options() : [];
                },
                'show_if' => ['insurance_category_id' => [1]],
                'required' => false
            ],
            // ...existing code for other fields...
            ['name' => 'insurance_company', 'label' => 'Insurance Company'],
            ['name' => 'policy_number', 'label' => 'Policy Number'],
            ['name' => 'beneficiary_contact_id', 'label' => 'Beneficiary (Contact)'],
            ['name' => 'advisor_person_id', 'label' => 'Advisor'],
            ['name' => 'owner_contact_id', 'label' => 'Owner (Contact)'],
            ['name' => 'is_group_insurance', 'label' => 'Is Group Insurance', 'type' => 'checkbox'],
            [
                'name' => 'group_insurance_sponsor_org_id',
                'label' => 'Group Insurance Sponsor',
                'type' => 'select',
                'options' => function() {
                    return function_exists('epm_get_organization_dropdown_options') ? epm_get_organization_dropdown_options() : [];
                },
                'show_if' => ['is_group_insurance' => true],
                'add_button' => true
            ],
        ];
        return $fields;
    }
    public static function get_section_key() {
        return 'insurance';
    }
    public static function getFieldDefinitions() {
        return [
            'insurance_category_id' => [
                'label' => 'Category',
                'type' => 'select',
                'options' => function() {
                    return function_exists('epm_get_insurance_category_options') ? epm_get_insurance_category_options() : [];
                },
                'required' => true,
                'db_type' => 'BIGINT UNSIGNED'
            ],
            'insurance_type_id' => [
                'label' => 'Type',
                'type' => 'select',
                'options' => function($data = []) {
                    if (isset($data['insurance_category_id']) && function_exists('epm_get_insurance_type_options')) {
                        return epm_get_insurance_type_options($data['insurance_category_id']);
                    }
                    return [];
                },
                'required' => true,
                'db_type' => 'BIGINT UNSIGNED'
            ],
            'vehicle_id' => [
                'label' => 'Vehicle',
                'type' => 'select',
                'options' => function() {
                    return function_exists('epm_get_vehicle_dropdown_options') ? epm_get_vehicle_dropdown_options() : [];
                },
                'required' => false,
                'db_type' => 'BIGINT UNSIGNED',
                'show_if' => ['insurance_category_id' => [2]]
            ],
            'real_estate_id' => [
                'label' => 'Real Estate',
                'type' => 'select',
                'options' => function() {
                    return function_exists('epm_get_real_estate_dropdown_options') ? epm_get_real_estate_dropdown_options() : [];
                },
                'required' => false,
                'db_type' => 'BIGINT UNSIGNED',
                'show_if' => ['insurance_category_id' => [3]]
            ],
            'insured_contact_ids' => [
                'label' => 'Insured Person(s)',
                'type' => 'multiselect',
                'options' => function() {
                    return function_exists('epm_get_contact_dropdown_options') ? epm_get_contact_dropdown_options() : [];
                },
                'required' => false,
                'db_type' => 'TEXT',
                'show_if' => ['insurance_category_id' => [1]]
            ],
            // ...existing code for other fields...
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
