<?php
namespace EstatePlanningManager\Models;

require_once __DIR__ . '/AbstractSectionModel.php';

if (!defined('ABSPATH')) exit;

class HostingServicesModel extends AbstractSectionModel {
    public function getTableName() {
        global $wpdb;
        return $wpdb->prefix . 'epm_hosting_services';
    }
    public function getAllRecordsForClient($client_id) {
        global $wpdb;
        $table = $this->getTableName();
        $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE client_id = %d", $client_id), ARRAY_A);
        return $results ? $results : [];
    }
    public function getSummaryFields() {
        return ['id', 'service_name', 'account_id'];
    }
    public function getFormFields($client_id = null) {
        return [
            ['name' => 'service_name', 'label' => 'Service Name'],
            ['name' => 'account_id', 'label' => 'Account ID'],
            ['name' => 'username', 'label' => 'Username'],
            ['name' => 'email', 'label' => 'Email'],
            ['name' => 'password', 'label' => 'Password'],
            ['name' => 'url', 'label' => 'URL'],
            ['name' => 'notes', 'label' => 'Notes'],
        ];
    }
    public static function get_section_key() {
        return 'hosting_services';
    }
    public static function getFieldDefinitions() {
        return [
            'suitecrm_guid' => [ 'label' => 'SuiteCRM GUID', 'type' => 'text', 'required' => false, 'db_type' => 'VARCHAR(36)' ],
            'wp_record_id' => [ 'label' => 'WP Record ID', 'type' => 'number', 'required' => false, 'db_type' => 'BIGINT(20)' ],
            'client_id' => [ 'label' => 'Client ID', 'type' => 'number', 'required' => true, 'db_type' => 'BIGINT(20)' ],
            'service_name' => [ 'label' => 'Service Name', 'type' => 'text', 'required' => true, 'db_type' => 'VARCHAR(255)' ],
            'account_id' => [ 'label' => 'Account ID', 'type' => 'text', 'required' => true, 'db_type' => 'VARCHAR(255)' ],
            'username' => [ 'label' => 'Username', 'type' => 'text', 'required' => true, 'db_type' => 'VARCHAR(255)' ],
            'email' => [ 'label' => 'Email', 'type' => 'email', 'required' => false, 'db_type' => 'VARCHAR(255)' ],
            'password' => [ 'label' => 'Password', 'type' => 'text', 'required' => true, 'db_type' => 'VARCHAR(255)' ],
            'url' => [ 'label' => 'URL', 'type' => 'text', 'required' => false, 'db_type' => 'VARCHAR(255)' ],
            'notes' => [ 'label' => 'Notes', 'type' => 'textarea', 'required' => false, 'db_type' => 'TEXT' ],
        ];
    }
    /**
     * Create the hosting services table if it does not exist
     *
     * @param string $charset_collate
     * @phpdoc
     * @uml
     * class HostingServicesModel {
     *   +static createTable($charset_collate)
     * }
     */
    public static function createTable($charset_collate) {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_hosting_services';
        $sql = "CREATE TABLE IF NOT EXISTS $table (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            suitecrm_guid VARCHAR(36) DEFAULT NULL,
            wp_record_id BIGINT(20) DEFAULT NULL,
            client_id BIGINT(20) NOT NULL,
            service_name VARCHAR(255) NOT NULL,
            account_id VARCHAR(255) NOT NULL,
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
}
