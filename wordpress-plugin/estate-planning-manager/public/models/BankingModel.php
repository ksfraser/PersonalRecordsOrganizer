<?php
namespace EstatePlanningManager\Models;

require_once __DIR__ . '/Sanitizer.php';
require_once __DIR__ . '/AbstractSectionModel.php';
require_once __DIR__ . '/AccountTypesModel.php';
require_once __DIR__ . '/PeopleModel.php';

if (!defined('ABSPATH')) exit;

/**
 * Class BankingModel
 * Model for managing bank account records.
 *
 * @phpdoc
 * @uml
 * class BankingModel extends AbstractSectionModel {
 *   +static createTable($charset_collate)
 * }
 */
class BankingModel extends AbstractSectionModel {
    // Table creation is handled by BankAccountsTable. This method is intentionally omitted to avoid duplication.
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
        $sanitized['owner_person_id'] = isset($data['owner_person_id']) ? Sanitizer::int($data['owner_person_id']) : null;
        $sanitized['advisor_person_id'] = isset($data['advisor_person_id']) ? Sanitizer::int($data['advisor_person_id']) : null;
        // Add more fields as needed
        return [empty($errors), $errors, $sanitized];
    }

    public function getTableName() {
        global $wpdb;
        return $wpdb->prefix . 'epm_bank_accounts';
    }
        $epm_log_file = dirname(__DIR__, 2) . '/logs/epm.log';
        file_put_contents($epm_log_file, "EPM DEBUG: BankingModel getAllRecordsForClient SQL: " . $wpdb->last_query . "\n", FILE_APPEND);
        file_put_contents($epm_log_file, "EPM DEBUG: BankingModel getAllRecordsForClient Results: " . print_r($results, true) . "\n", FILE_APPEND);
        return $results;
    }

    public function getOwnerIdForSection($section, $client_id) {
        return $client_id;
    }

    public function getSummaryFields() {
        return ['id', 'bank', 'account_type', 'account_number'];
    }

    public function getFormFields() {
        $fields = self::getFieldDefinitions();
        $out = [];
        foreach ($fields as $name => $def) {
            if (!is_string($name) || $name === '' || !is_array($def)) continue;
            $out[] = array_merge(['name' => $name], $def);
        }
        return $out;
    }

    public static function get_section_key() {
        return 'banking';
    }

    public static function getFieldDefinitions() {
        // Get account type options from AccountTypesModel
        $accountTypeOptions = [];
        if (class_exists('EstatePlanningManager\\Models\\AccountTypesModel')) {
            foreach (\EstatePlanningManager\Models\AccountTypesModel::getDefaultRows() as $row) {
                $accountTypeOptions[$row['value']] = $row['label'];
            }
        }
        // Get people options for selectors
        $peopleOptions = [];
        $defaultAdvisorId = null;
        if (class_exists('EstatePlanningManager\\Models\\PeopleModel')) {
            $peopleOptions = [];
            foreach (\EstatePlanningManager\Models\PeopleModel::getAllForDropdown() as $person) {
                $peopleOptions[$person['id']] = $person['full_name'];
            }
            $defaultAdvisor = \EstatePlanningManager\Models\PeopleModel::getDefaultAdvisor();
            if ($defaultAdvisor) {
                $defaultAdvisorId = $defaultAdvisor['id'];
            }
        }
        return [
            'bank' => [
                'label' => 'Bank',
                'type' => 'text',
                'required' => true,
                'db_type' => 'VARCHAR(255)',
                'deprecated' => true, // Marking as deprecated for UI
            ],
            'account_type' => [
                'label' => 'Account Type',
                'type' => 'select',
                'options' => $accountTypeOptions,
                'required' => true,
                'db_type' => 'VARCHAR(100)'
            ],
            'account_number' => [
                'label' => 'Account Number',
                'type' => 'text',
                'required' => true,
                'db_type' => 'VARCHAR(255)'
            ],
            'branch' => [
                'label' => 'Branch',
                'type' => 'text',
                'required' => false,
                'db_type' => 'VARCHAR(255)'
            ],
            'owner_person_id' => [
                'label' => 'Owner',
                'type' => 'select',
                'options' => $peopleOptions,
                'required' => false,
                'db_type' => 'BIGINT UNSIGNED'
            ],
            'advisor_person_id' => [
                'label' => 'Advisor',
                'type' => 'select',
                'options' => $peopleOptions,
                'required' => false,
                'db_type' => 'BIGINT UNSIGNED',
                'default' => $defaultAdvisorId
            ],
            'bank_location' => [
                'label' => 'Bank Location',
                'type' => 'select',
                'options' => [
                    'canada' => 'Canada',
                    'usa' => 'USA',
                    'europe' => 'Europe',
                ],
                'required' => true,
                'db_type' => 'VARCHAR(32)'
            ],
            'bank_name' => [
                'label' => 'Bank Name',
                'type' => 'select',
                'options' => [], // To be dynamically loaded by location in UI
                'required' => true,
                'db_type' => 'VARCHAR(255)'
            ],
        ];
    }
}
