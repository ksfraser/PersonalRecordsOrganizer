<?php
namespace EstatePlanningManager\Models;

require_once __DIR__ . '/AbstractSectionModel.php';
require_once __DIR__ . '/Sanitizer.php';
require_once __DIR__ . '/PeopleModel.php';

if (!defined('ABSPATH')) exit;

class AutoModel extends AbstractSectionModel {
    public static function getByClientId($clientId) {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_auto_property';
        return $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE client_id = %d", $clientId));
    }

    /**
     * Validate and sanitize auto property data before insert/update
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
        $sanitized['type'] = isset($data['type']) ? Sanitizer::text($data['type']) : null;
        $sanitized['vehicle'] = isset($data['vehicle']) ? Sanitizer::text($data['vehicle']) : null;
        $sanitized['model'] = isset($data['model']) ? Sanitizer::text($data['model']) : null;
        $sanitized['own_or_lease'] = isset($data['own_or_lease']) ? Sanitizer::text($data['own_or_lease']) : null;
        $sanitized['legal_document_location'] = isset($data['legal_document_location']) ? Sanitizer::text($data['legal_document_location']) : null;
        $sanitized['registration_location'] = isset($data['registration_location']) ? Sanitizer::text($data['registration_location']) : null;
        $sanitized['insurance_policy_location'] = isset($data['insurance_policy_location']) ? Sanitizer::text($data['insurance_policy_location']) : null;
        $sanitized['bill_of_sale_location'] = isset($data['bill_of_sale_location']) ? Sanitizer::text($data['bill_of_sale_location']) : null;
        $sanitized['owner_person_id'] = isset($data['owner_person_id']) ? Sanitizer::int($data['owner_person_id']) : null;
        return [empty($errors), $errors, $sanitized];
    }

    public function getTableName() {
        global $wpdb;
        return $wpdb->prefix . 'epm_auto_property';
    }

    public function getAllRecordsForClient($client_id) {
        global $wpdb;
        $table = $this->getTableName();
        $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE client_id = %d", $client_id), \ARRAY_A);
        return $results ? $results : [];
    }

    public function getOwnerIdForSection($section, $client_id) {
        return $client_id;
    }

    public function getSummaryFields() {
        return ['id', 'auto_name'];
    }

    public function getFormFields() {
        return [
            ['name' => 'type', 'label' => 'Type'],
            ['name' => 'vehicle', 'label' => 'Vehicle'],
            ['name' => 'model', 'label' => 'Model'],
            ['name' => 'own_or_lease', 'label' => 'Own or Lease'],
            ['name' => 'legal_document_location', 'label' => 'Legal Document Location'],
            ['name' => 'registration_location', 'label' => 'Registration Location'],
            ['name' => 'insurance_policy_location', 'label' => 'Insurance Policy Location'],
            ['name' => 'bill_of_sale_location', 'label' => 'Bill of Sale Location'],
            ['name' => 'owner_person_id', 'label' => 'Owner'],
        ];
    }

    public static function get_section_key() {
        return 'auto_property';
    }

    public static function getFieldDefinitions() {
        return [
            'type' => [
                'label' => 'Type',
                'type' => 'text',
                'required' => true,
                'db_type' => 'VARCHAR(100)'
            ],
            'vehicle' => [
                'label' => 'Vehicle',
                'type' => 'text',
                'required' => true,
                'db_type' => 'VARCHAR(255)'
            ],
            'model' => [
                'label' => 'Model',
                'type' => 'text',
                'required' => false,
                'db_type' => 'VARCHAR(100)'
            ],
            'own_or_lease' => [
                'label' => 'Own or Lease',
                'type' => 'text',
                'required' => false,
                'db_type' => 'VARCHAR(100)'
            ],
            'legal_document_location' => [
                'label' => 'Legal Document Location',
                'type' => 'text',
                'required' => false,
                'db_type' => 'VARCHAR(255)'
            ],
            'registration_location' => [
                'label' => 'Registration Location',
                'type' => 'text',
                'required' => false,
                'db_type' => 'VARCHAR(255)'
            ],
            'insurance_policy_location' => [
                'label' => 'Insurance Policy Location',
                'type' => 'text',
                'required' => false,
                'db_type' => 'VARCHAR(255)'
            ],
            'bill_of_sale_location' => [
                'label' => 'Bill of Sale Location',
                'type' => 'text',
                'required' => false,
                'db_type' => 'VARCHAR(255)'
            ],
            'owner_person_id' => [
                'label' => 'Owner',
                'type' => 'select',
                'options' => \EstatePlanningManager\Models\PeopleModel::getDropdownOptions(),
                'required' => false,
                'db_type' => 'BIGINT UNSIGNED'
            ],
        ];
    }
}
