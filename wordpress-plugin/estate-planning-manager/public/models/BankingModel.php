<?php
namespace EstatePlanningManager\Models;

require_once __DIR__ . '/Sanitizer.php';
require_once __DIR__ . '/AbstractSectionModel.php';

if (!defined('ABSPATH')) exit;

class BankingModel extends AbstractSectionModel {
    public static function getByClientId($clientId) {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_bank_accounts';
        return $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE client_id = %d", $clientId));
    }

    /**
     * Validate and sanitize banking data before insert/update
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
        $sanitized['bank'] = isset($data['bank']) ? Sanitizer::text($data['bank']) : null;
        $sanitized['account_type'] = isset($data['account_type']) ? Sanitizer::text($data['account_type']) : null;
        $sanitized['account_number'] = isset($data['account_number']) ? Sanitizer::text($data['account_number']) : null;
        $sanitized['branch'] = isset($data['branch']) ? Sanitizer::text($data['branch']) : null;
        $sanitized['owner'] = isset($data['owner']) ? Sanitizer::text($data['owner']) : null;
        $sanitized['advisor'] = isset($data['advisor']) ? Sanitizer::text($data['advisor']) : null;
        // Add more fields as needed
        return [empty($errors), $errors, $sanitized];
    }

    public function getTableName() {
        global $wpdb;
        return $wpdb->prefix . 'epm_bank_accounts';
    }

    public function getOwnerIdForSection($section, $client_id) {
        return $client_id;
    }

    public function getSummaryFields() {
        return ['id', 'account_name']; // Example, override as needed
    }

    public function getFormFields() {
        return [
            ['name' => 'bank', 'label' => 'Bank'],
            ['name' => 'account_type', 'label' => 'Account Type'],
            ['name' => 'account_number', 'label' => 'Account Number'],
            ['name' => 'branch', 'label' => 'Branch'],
            ['name' => 'owner', 'label' => 'Owner'],
            ['name' => 'advisor', 'label' => 'Advisor'],
        ];
    }
}
