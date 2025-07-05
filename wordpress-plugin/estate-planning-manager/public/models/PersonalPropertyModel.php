<?php
namespace EstatePlanningManager\Models;

require_once __DIR__ . '/AbstractSectionModel.php';

require_once __DIR__ . '/Sanitizer.php';
require_once __DIR__ . '/PeopleModel.php';

if (!defined('ABSPATH')) exit;

class PersonalPropertyModel extends AbstractSectionModel {
    public static function getByClientId($clientId) {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_personal_property';
        return $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE client_id = %d", $clientId));
    }

    /**
     * Validate and sanitize personal property data before insert/update
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
        $sanitized['suitecrm_guid'] = isset($data['suitecrm_guid']) ? Sanitizer::text($data['suitecrm_guid']) : null;
        $sanitized['wp_record_id'] = isset($data['wp_record_id']) ? Sanitizer::int($data['wp_record_id']) : null;
        $sanitized['property_type'] = isset($data['property_type']) ? Sanitizer::text($data['property_type']) : null;
        $sanitized['item_type'] = isset($data['item_type']) ? Sanitizer::text($data['item_type']) : null;
        $sanitized['vehicle_model'] = isset($data['vehicle_model']) ? Sanitizer::text($data['vehicle_model']) : null;
        $sanitized['own_or_lease'] = isset($data['own_or_lease']) ? Sanitizer::text($data['own_or_lease']) : null;
        $sanitized['legal_document'] = isset($data['legal_document']) ? Sanitizer::textarea($data['legal_document']) : null;
        $sanitized['registration_location'] = isset($data['registration_location']) ? Sanitizer::textarea($data['registration_location']) : null;
        $sanitized['insurance_policy_location'] = isset($data['insurance_policy_location']) ? Sanitizer::textarea($data['insurance_policy_location']) : null;
        $sanitized['bill_of_sale_location'] = isset($data['bill_of_sale_location']) ? Sanitizer::textarea($data['bill_of_sale_location']) : null;
        $sanitized['location'] = isset($data['location']) ? Sanitizer::textarea($data['location']) : null;
        $sanitized['safe_deposit_box_location'] = isset($data['safe_deposit_box_location']) ? Sanitizer::text($data['safe_deposit_box_location']) : null;
        $sanitized['box_access_names'] = isset($data['box_access_names']) ? Sanitizer::textarea($data['box_access_names']) : null;
        $sanitized['keys_location'] = isset($data['keys_location']) ? Sanitizer::textarea($data['keys_location']) : null;
        $sanitized['contents_list_location'] = isset($data['contents_list_location']) ? Sanitizer::textarea($data['contents_list_location']) : null;
        $sanitized['owner_person_id'] = isset($data['owner_person_id']) ? Sanitizer::int($data['owner_person_id']) : null;
        $sanitized['auto_model_id'] = isset($data['auto_model_id']) ? Sanitizer::int($data['auto_model_id']) : null;
        // Add more fields as needed
        return [empty($errors), $errors, $sanitized];
    }

    public function getTableName() {
        global $wpdb;
        return $wpdb->prefix . 'epm_personal_property';
    }
    public function getOwnerIdForSection($section, $client_id) {
        return $client_id;
    }
    public function getSummaryFields() {
        return ['id', 'property_description'];
    }
    public function getFormFields() {
        return [
            ['name' => 'property_type', 'label' => 'Property Type'],
            ['name' => 'item_type', 'label' => 'Item Type'],
            ['name' => 'vehicle_model', 'label' => 'Vehicle Model'],
            ['name' => 'own_or_lease', 'label' => 'Own or Lease'],
            ['name' => 'legal_document', 'label' => 'Legal Document'],
            ['name' => 'registration_location', 'label' => 'Registration Location'],
            ['name' => 'insurance_policy_location', 'label' => 'Insurance Policy Location'],
            ['name' => 'bill_of_sale_location', 'label' => 'Bill of Sale Location'],
            ['name' => 'location', 'label' => 'Location'],
            ['name' => 'safe_deposit_box_location', 'label' => 'Safe Deposit Box Location'],
            ['name' => 'box_access_names', 'label' => 'Box Access Names'],
            ['name' => 'keys_location', 'label' => 'Keys Location'],
            ['name' => 'contents_list_location', 'label' => 'Contents List Location'],
        ];
    }
    public static function get_section_key() {
        return 'personal_property';
    }
    public static function getFieldDefinitions() {
        $peopleOptions = [];
        if (class_exists('EstatePlanningManager\\Models\\PeopleModel')) {
            foreach (\EstatePlanningManager\Models\PeopleModel::getAllForDropdown() as $person) {
                $peopleOptions[$person['id']] = $person['full_name'];
            }
        }
        return [
            'property_description' => [
                'label' => 'Property Description',
                'type' => 'text',
                'required' => true,
                'db_type' => 'VARCHAR(255)'
            ],
            'property_value' => [
                'label' => 'Property Value',
                'type' => 'text',
                'required' => true,
                'db_type' => 'VARCHAR(255)'
            ],
            'owner_person_id' => [
                'label' => 'Owner',
                'type' => 'select',
                'options' => $peopleOptions,
                'required' => false,
                'db_type' => 'BIGINT UNSIGNED'
            ],
        ];
    }
}
