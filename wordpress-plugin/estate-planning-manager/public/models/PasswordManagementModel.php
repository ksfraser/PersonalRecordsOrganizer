<?php
namespace EstatePlanningManager\Models;

require_once __DIR__ . '/AbstractSectionModel.php';

if (!defined('ABSPATH')) exit;

class PasswordManagementModel extends AbstractSectionModel {
    /**
     * Create the password management table if it does not exist
     */
    public static function createTable($charset_collate) {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_password_management';
        $sql = "CREATE TABLE IF NOT EXISTS $table (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            client_id BIGINT UNSIGNED NOT NULL,
            suitecrm_guid VARCHAR(36) DEFAULT NULL,
            wp_record_id BIGINT UNSIGNED DEFAULT NULL,
            storage_location VARCHAR(255) NOT NULL,
            storage_type VARCHAR(100) NOT NULL,
            key_file VARCHAR(255) DEFAULT NULL,
            service_name VARCHAR(255) NOT NULL,
            username VARCHAR(255) NOT NULL,
            email VARCHAR(255) DEFAULT NULL,
            password VARCHAR(255) NOT NULL,
            password_hint VARCHAR(255) DEFAULT NULL,
            notes TEXT DEFAULT NULL,
            created DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            KEY idx_client (client_id)
        ) ENGINE=InnoDB $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    public function getTableName() {
        global $wpdb;
        return $wpdb->prefix . 'epm_password_management';
    }
    public function getAllRecordsForClient($client_id) {
        global $wpdb;
        $table = $this->getTableName();
        $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE client_id = %d", $client_id), ARRAY_A);
        return $results ? $results : [];
    }
    public function getSummaryFields() {
        return ['id', 'service_name', 'username'];
    }
    public function getFormFields($client_id = null) {
        $storage_type_options = $this->getStorageTypeOptions();
        return [
            ['name' => 'storage_location', 'label' => 'Storage Location', 'type' => 'text', 'help' => 'Where is this password stored? (e.g., Password Manager, Notebook, File, etc.)'],
            ['name' => 'storage_type', 'label' => 'Storage Type', 'type' => 'select', 'options' => $storage_type_options, 'help' => 'Type of storage (select or specify)'],
            ['name' => 'key_file', 'label' => 'Key File (if applicable)', 'type' => 'text', 'help' => 'Path or description of key file, if used'],
            ['name' => 'service_name', 'label' => 'Service Name'],
            ['name' => 'username', 'label' => 'Username'],
            ['name' => 'email', 'label' => 'Email'],
            ['name' => 'password', 'label' => 'Password'],
            ['name' => 'password_hint', 'label' => 'Password Hint'],
            ['name' => 'notes', 'label' => 'Notes'],
        ];
    }

    protected function getStorageTypeOptions() {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_password_storage_types';
        $rows = $wpdb->get_results("SELECT value, label FROM $table WHERE is_active = 1 ORDER BY sort_order ASC, label ASC", ARRAY_A);
        $options = [];
        foreach ($rows as $row) {
            $options[] = [ 'value' => $row['value'], 'label' => $row['label'] ];
        }
        return $options;
    }

    public static function get_section_key() {
        return 'password_management';
    }
    public static function getFieldDefinitions() {
        return [
            'suitecrm_guid' => [ 'label' => 'SuiteCRM GUID', 'type' => 'text', 'required' => false, 'db_type' => 'VARCHAR(36)' ],
            'wp_record_id' => [ 'label' => 'WP Record ID', 'type' => 'number', 'required' => false, 'db_type' => 'BIGINT(20)' ],
            'client_id' => [ 'label' => 'Client ID', 'type' => 'number', 'required' => true, 'db_type' => 'BIGINT(20)' ],
            'storage_location' => [ 'label' => 'Storage Location', 'type' => 'text', 'required' => true, 'db_type' => 'VARCHAR(255)' ],
            'storage_type' => [ 'label' => 'Storage Type', 'type' => 'text', 'required' => true, 'db_type' => 'VARCHAR(100)' ],
            'key_file' => [ 'label' => 'Key File', 'type' => 'text', 'required' => false, 'db_type' => 'VARCHAR(255)' ],
            'service_name' => [ 'label' => 'Service Name', 'type' => 'text', 'required' => true, 'db_type' => 'VARCHAR(255)' ],
            'username' => [ 'label' => 'Username', 'type' => 'text', 'required' => true, 'db_type' => 'VARCHAR(255)' ],
            'email' => [ 'label' => 'Email', 'type' => 'email', 'required' => false, 'db_type' => 'VARCHAR(255)' ],
            'password' => [ 'label' => 'Password', 'type' => 'text', 'required' => true, 'db_type' => 'VARCHAR(255)' ],
            'password_hint' => [ 'label' => 'Password Hint', 'type' => 'text', 'required' => false, 'db_type' => 'VARCHAR(255)' ],
            'notes' => [ 'label' => 'Notes', 'type' => 'textarea', 'required' => false, 'db_type' => 'TEXT' ],
        ];
    }
}
