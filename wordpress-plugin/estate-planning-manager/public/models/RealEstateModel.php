<?php
namespace EstatePlanningManager\Models;

require_once __DIR__ . '/AbstractSectionModel.php';

require_once __DIR__ . '/Sanitizer.php';

if (!defined('ABSPATH')) exit;

class RealEstateModel extends AbstractSectionModel {
    public static function getByClientId($clientId) {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_real_estate';
        return $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE client_id = %d", $clientId));
    }

    /**
     * Validate and sanitize real estate data before insert/update
     * @param array $data
     * @return array [is_valid, errors, sanitized_data]
     */
    public static function validateData($data) {
        $errors = array();
        $sanitized = array();
        // Required: client_id
        if (empty($data['client_id']) || !is_numeric($data['client_id'])) {
            $errors[] = 'Client ID is required and must be numeric.';
        } else {
            $sanitized['client_id'] = \EstatePlanningManager\Models\Sanitizer::int($data['client_id']);
        }
        $sanitized['property_type'] = isset($data['property_type']) ? \EstatePlanningManager\Models\Sanitizer::text($data['property_type']) : null;
        $sanitized['title_held_by'] = isset($data['title_held_by']) ? \EstatePlanningManager\Models\Sanitizer::text($data['title_held_by']) : null;
        $sanitized['address'] = isset($data['address']) ? \EstatePlanningManager\Models\Sanitizer::textarea($data['address']) : null;
        $sanitized['has_mortgage'] = isset($data['has_mortgage']) ? \EstatePlanningManager\Models\Sanitizer::text($data['has_mortgage']) : null;
        $sanitized['mortgage_held_by'] = isset($data['mortgage_held_by']) ? \EstatePlanningManager\Models\Sanitizer::text($data['mortgage_held_by']) : null;
        $sanitized['lender_address'] = isset($data['lender_address']) ? \EstatePlanningManager\Models\Sanitizer::textarea($data['lender_address']) : null;
        $sanitized['lender_phone'] = isset($data['lender_phone']) ? \EstatePlanningManager\Models\Sanitizer::text($data['lender_phone']) : null;
        $sanitized['lender_email'] = isset($data['lender_email']) ? \EstatePlanningManager\Models\Sanitizer::email($data['lender_email']) : null;
        $sanitized['mortgage_location'] = isset($data['mortgage_location']) ? \EstatePlanningManager\Models\Sanitizer::textarea($data['mortgage_location']) : null;
        $sanitized['deed_location'] = isset($data['deed_location']) ? \EstatePlanningManager\Models\Sanitizer::textarea($data['deed_location']) : null;
        $sanitized['property_insurance_docs'] = isset($data['property_insurance_docs']) ? \EstatePlanningManager\Models\Sanitizer::textarea($data['property_insurance_docs']) : null;
        $sanitized['land_surveys'] = isset($data['land_surveys']) ? \EstatePlanningManager\Models\Sanitizer::textarea($data['land_surveys']) : null;
        $sanitized['tax_receipts'] = isset($data['tax_receipts']) ? \EstatePlanningManager\Models\Sanitizer::textarea($data['tax_receipts']) : null;
        $sanitized['leases'] = isset($data['leases']) ? \EstatePlanningManager\Models\Sanitizer::textarea($data['leases']) : null;
        $sanitized['accounting_docs'] = isset($data['accounting_docs']) ? \EstatePlanningManager\Models\Sanitizer::textarea($data['accounting_docs']) : null;
        $sanitized['lender_person_id'] = isset($data['lender_person_id']) ? \EstatePlanningManager\Models\Sanitizer::int($data['lender_person_id']) : null;
        // Add more fields as needed
        return [empty($errors), $errors, $sanitized];
    }

    public function getTableName() {
        global $wpdb;
        return $wpdb->prefix . 'epm_real_estate';
    }
    public function getOwnerIdForSection($section, $client_id) {
        // TODO: Implement sharing logic
        return $client_id;
    }
    public function getSummaryFields() {
        return ['id', 'property_address'];
    }
}
