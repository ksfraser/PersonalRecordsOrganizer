<?php
namespace EstatePlanningManager\Models;

require_once __DIR__ . '/Sanitizer.php';

if (!defined('ABSPATH')) exit;

class ScheduledPaymentsModel {
    public static function getByClientId($clientId) {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_scheduled_payments';
        return $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE client_id = %d", $clientId));
    }

    /**
     * Validate and sanitize scheduled payments data before insert/update
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
        $sanitized['payment_type'] = isset($data['payment_type']) ? Sanitizer::text($data['payment_type']) : null;
        $sanitized['paid_to'] = isset($data['paid_to']) ? Sanitizer::text($data['paid_to']) : null;
        $sanitized['is_automatic'] = isset($data['is_automatic']) ? Sanitizer::text($data['is_automatic']) : null;
        $sanitized['amount'] = isset($data['amount']) ? Sanitizer::text($data['amount']) : null;
        $sanitized['due_date'] = isset($data['due_date']) ? Sanitizer::text($data['due_date']) : null;
        // Add more fields as needed
        return [empty($errors), $errors, $sanitized];
    }
}
