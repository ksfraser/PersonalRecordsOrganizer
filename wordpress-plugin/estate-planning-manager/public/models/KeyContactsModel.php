<?php
namespace EstatePlanningManager\Models;

require_once __DIR__ . '/AbstractSectionModel.php';

if (!defined('ABSPATH')) exit;

class KeyContactsModel extends AbstractSectionModel {
    /**
     * Create the key contacts table if it does not exist
     */
    public static function createTable($charset_collate) {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_key_contacts';
        $sql = "CREATE TABLE IF NOT EXISTS $table (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            client_id BIGINT UNSIGNED NOT NULL,
            suitecrm_guid VARCHAR(36) DEFAULT NULL,
            wp_record_id BIGINT UNSIGNED DEFAULT NULL,
            full_name VARCHAR(255) NOT NULL,
            relationship VARCHAR(100) NOT NULL,
            phone VARCHAR(50) DEFAULT NULL,
            email VARCHAR(100) DEFAULT NULL,
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
        return $wpdb->prefix . 'epm_key_contacts';
    }
    public static function get_section_key() {
        return 'key_contacts';
    }
    public static function getFieldDefinitions() {
        return [
            'suitecrm_guid' => [ 'label' => 'SuiteCRM GUID', 'type' => 'text', 'required' => false, 'db_type' => 'VARCHAR(36)' ],
            'wp_record_id' => [ 'label' => 'WP Record ID', 'type' => 'number', 'required' => false, 'db_type' => 'BIGINT(20)' ],
            'client_id' => [ 'label' => 'Client ID', 'type' => 'number', 'required' => true, 'db_type' => 'BIGINT(20)' ],
            'full_name' => [ 'label' => 'Full Name', 'type' => 'text', 'required' => true, 'db_type' => 'VARCHAR(255)' ],
            'relationship' => [ 'label' => 'Relationship', 'type' => 'text', 'required' => true, 'db_type' => 'VARCHAR(100)' ],
            'phone' => [ 'label' => 'Phone', 'type' => 'text', 'required' => false, 'db_type' => 'VARCHAR(50)' ],
            'email' => [ 'label' => 'Email', 'type' => 'email', 'required' => false, 'db_type' => 'VARCHAR(100)' ],
            'notes' => [ 'label' => 'Notes', 'type' => 'textarea', 'required' => false, 'db_type' => 'TEXT' ],
        ];
    }
}
