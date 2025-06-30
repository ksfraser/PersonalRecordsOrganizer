<?php
namespace EstatePlanningManager\Models;

require_once __DIR__ . '/Sanitizer.php';

if (!defined('ABSPATH')) exit;

class InvestmentsModel {
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
        $sanitized['beneficiary'] = isset($data['beneficiary']) ? Sanitizer::text($data['beneficiary']) : null;
        $sanitized['advisor'] = isset($data['advisor']) ? Sanitizer::text($data['advisor']) : null;
        $sanitized['lender'] = isset($data['lender']) ? Sanitizer::text($data['lender']) : null;
        // Add more fields as needed
        return [empty($errors), $errors, $sanitized];
    }
}
