<?php
namespace EstatePlanningManager\Models;

require_once __DIR__ . '/AbstractSectionModel.php';

require_once __DIR__ . '/Sanitizer.php';

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
        $sanitized['advisor'] = isset($data['advisor']) ? Sanitizer::text($data['advisor']) : null;
        $sanitized['owner'] = isset($data['owner']) ? Sanitizer::text($data['owner']) : null;
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
            ['name' => 'advisor', 'label' => 'Advisor'],
            ['name' => 'owner', 'label' => 'Owner'],
        ];
    }
    public static function get_section_key() {
        return 'insurance';
    }
}
