<?php
namespace EstatePlanningManager\Models;

require_once __DIR__ . '/AbstractSectionModel.php';
require_once __DIR__ . '/Sanitizer.php';

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
        $sanitized['vehicle'] = isset($data['vehicle']) ? Sanitizer::text($data['vehicle']) : null;
        $sanitized['own_or_lease'] = isset($data['own_or_lease']) ? Sanitizer::text($data['own_or_lease']) : null;
        $sanitized['owner'] = isset($data['owner']) ? Sanitizer::text($data['owner']) : null;
        $sanitized['registration_location'] = isset($data['registration_location']) ? Sanitizer::textarea($data['registration_location']) : null;
        // Add more fields as needed
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
}
