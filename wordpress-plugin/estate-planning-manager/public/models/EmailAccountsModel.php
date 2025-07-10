<?php
namespace EstatePlanningManager\Models;

require_once __DIR__ . '/AbstractSectionModel.php';

if (!defined('ABSPATH')) exit;

class EmailAccountsModel extends AbstractSectionModel {
    public function getTableName() {
        global $wpdb;
        return $wpdb->prefix . 'epm_email_accounts';
    }
    public function getAllRecordsForClient($client_id) {
        global $wpdb;
        $table = $this->getTableName();
        $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE client_id = %d", $client_id), ARRAY_A);
        return $results ? $results : [];
    }
    public function getSummaryFields() {
        return ['id', 'email_address', 'provider'];
    }
    public function getFormFields($client_id = null) {
        return [
            ['name' => 'email_address', 'label' => 'Email Address'],
            ['name' => 'username', 'label' => 'Username'],
            ['name' => 'email', 'label' => 'Email'],
            ['name' => 'password', 'label' => 'Password'],
            ['name' => 'provider', 'label' => 'Provider'],
            ['name' => 'recovery_email', 'label' => 'Recovery Email'],
            ['name' => 'notes', 'label' => 'Notes'],
        ];
    }
    public static function get_section_key() {
        return 'email_accounts';
    }
    public static function getFieldDefinitions() {
        return [
            'suitecrm_guid' => [ 'label' => 'SuiteCRM GUID', 'type' => 'text', 'required' => false, 'db_type' => 'VARCHAR(36)' ],
            'wp_record_id' => [ 'label' => 'WP Record ID', 'type' => 'number', 'required' => false, 'db_type' => 'BIGINT(20)' ],
            'client_id' => [ 'label' => 'Client ID', 'type' => 'number', 'required' => true, 'db_type' => 'BIGINT(20)' ],
            'email_address' => [ 'label' => 'Email Address', 'type' => 'email', 'required' => true, 'db_type' => 'VARCHAR(255)' ],
            'username' => [ 'label' => 'Username', 'type' => 'text', 'required' => true, 'db_type' => 'VARCHAR(255)' ],
            'email' => [ 'label' => 'Email', 'type' => 'email', 'required' => false, 'db_type' => 'VARCHAR(255)' ],
            'password' => [ 'label' => 'Password', 'type' => 'text', 'required' => true, 'db_type' => 'VARCHAR(255)' ],
            'provider' => [ 'label' => 'Provider', 'type' => 'text', 'required' => false, 'db_type' => 'VARCHAR(100)' ],
            'recovery_email' => [ 'label' => 'Recovery Email', 'type' => 'email', 'required' => false, 'db_type' => 'VARCHAR(255)' ],
            'notes' => [ 'label' => 'Notes', 'type' => 'textarea', 'required' => false, 'db_type' => 'TEXT' ],
        ];
    }
    /**
     * Create the email accounts table if it does not exist
     *
     * @param string $charset_collate
     * @phpdoc
     * @uml
     * class EmailAccountsModel {
     *   +static createTable($charset_collate)
     * }
     */
    public static function createTable($charset_collate) {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_email_accounts';
        $sql = "CREATE TABLE IF NOT EXISTS $table (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            suitecrm_guid VARCHAR(36) DEFAULT NULL,
            wp_record_id BIGINT(20) DEFAULT NULL,
            client_id BIGINT(20) NOT NULL,
            email_address VARCHAR(255) NOT NULL,
            username VARCHAR(255) NOT NULL,
            email VARCHAR(255) DEFAULT NULL,
            password VARCHAR(255) NOT NULL,
            provider VARCHAR(100) DEFAULT NULL,
            recovery_email VARCHAR(255) DEFAULT NULL,
            notes TEXT DEFAULT NULL,
            created DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            KEY idx_client (client_id)
        ) ENGINE=InnoDB $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}
