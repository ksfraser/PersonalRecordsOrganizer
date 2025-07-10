<?php
namespace EstatePlanningManager\Models;

require_once __DIR__ . '/AbstractSectionModel.php';

if (!defined('ABSPATH')) exit;

class OnlineAccountsModel extends AbstractSectionModel {
    /**
     * Create the online accounts table if it does not exist
     */
    public static function createTable($charset_collate) {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_online_accounts';
        $sql = "CREATE TABLE IF NOT EXISTS $table (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            client_id BIGINT UNSIGNED NOT NULL,
            suitecrm_guid VARCHAR(36) DEFAULT NULL,
            wp_record_id BIGINT UNSIGNED DEFAULT NULL,
            account_name VARCHAR(255) NOT NULL,
            account_number VARCHAR(100) DEFAULT NULL,
            username VARCHAR(255) NOT NULL,
            email VARCHAR(255) DEFAULT NULL,
            password VARCHAR(255) NOT NULL,
            url VARCHAR(255) DEFAULT NULL,
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
        return $wpdb->prefix . 'epm_online_accounts';
    }
    public function getAllRecordsForClient($client_id) {
        global $wpdb;
        $table = $this->getTableName();
        $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE client_id = %d", $client_id), ARRAY_A);
        return $results ? $results : [];
    }
    public function getSummaryFields() {
        return ['id', 'account_name', 'username'];
    }
    public function getFormFields($client_id = null) {
        return [
            ['name' => 'account_name', 'label' => 'Account Name'],
            ['name' => 'account_number', 'label' => 'Account Number'],
            ['name' => 'username', 'label' => 'Username'],
            ['name' => 'email', 'label' => 'Email'],
            ['name' => 'password', 'label' => 'Password'],
            ['name' => 'url', 'label' => 'URL'],
            ['name' => 'notes', 'label' => 'Notes'],
        ];
    }
    public static function get_section_key() {
        return 'online_accounts';
    }
    public static function getFieldDefinitions() {
        return [
            'suitecrm_guid' => [ 'label' => 'SuiteCRM GUID', 'type' => 'text', 'required' => false, 'db_type' => 'VARCHAR(36)' ],
            'wp_record_id' => [ 'label' => 'WP Record ID', 'type' => 'number', 'required' => false, 'db_type' => 'BIGINT(20)' ],
            'client_id' => [ 'label' => 'Client ID', 'type' => 'number', 'required' => true, 'db_type' => 'BIGINT(20)' ],
            'account_name' => [ 'label' => 'Account Name', 'type' => 'text', 'required' => true, 'db_type' => 'VARCHAR(255)' ],
            'account_number' => [ 'label' => 'Account Number', 'type' => 'text', 'required' => false, 'db_type' => 'VARCHAR(100)' ],
            'username' => [ 'label' => 'Username', 'type' => 'text', 'required' => true, 'db_type' => 'VARCHAR(255)' ],
            'email' => [ 'label' => 'Email', 'type' => 'email', 'required' => false, 'db_type' => 'VARCHAR(255)' ],
            'password' => [ 'label' => 'Password', 'type' => 'text', 'required' => true, 'db_type' => 'VARCHAR(255)' ],
            'url' => [ 'label' => 'URL', 'type' => 'text', 'required' => false, 'db_type' => 'VARCHAR(255)' ],
            'notes' => [ 'label' => 'Notes', 'type' => 'textarea', 'required' => false, 'db_type' => 'TEXT' ],
        ];
    }
}
