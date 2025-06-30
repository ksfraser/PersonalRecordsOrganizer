<?php
namespace EstatePlanningManager\Models;

require_once __DIR__ . '/Sanitizer.php';

if (!defined('ABSPATH')) exit;

class PersonalPropertyModel {
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
}
