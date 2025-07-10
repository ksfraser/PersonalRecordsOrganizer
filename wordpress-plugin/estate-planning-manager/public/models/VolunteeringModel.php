<?php
namespace EstatePlanningManager\Models;

require_once __DIR__ . '/AbstractSectionModel.php';

if (!defined('ABSPATH')) exit;

class VolunteeringModel extends AbstractSectionModel {
    /**
     * Create the volunteering table if it does not exist
     */
    public static function createTable($charset_collate) {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_volunteering';
        $sql = "CREATE TABLE IF NOT EXISTS $table (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            client_id BIGINT UNSIGNED NOT NULL,
            suitecrm_guid VARCHAR(36) DEFAULT NULL,
            wp_record_id BIGINT UNSIGNED DEFAULT NULL,
            organization_name VARCHAR(255) NOT NULL,
            start_year INT(4) NOT NULL,
            end_year INT(4) DEFAULT NULL,
            address VARCHAR(255) DEFAULT NULL,
            phone VARCHAR(50) DEFAULT NULL,
            email VARCHAR(100) DEFAULT NULL,
            created DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            KEY idx_client (client_id)
        ) ENGINE=InnoDB $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    public function getTableName() {
        global $wpdb;
        return $wpdb->prefix . 'epm_volunteering';
    }
    public function getAllRecordsForClient($client_id) {
        global $wpdb;
        $table = $this->getTableName();
        $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE client_id = %d", $client_id), ARRAY_A);
        return $results ? $results : [];
    }
    public function getSummaryFields() {
        return ['id', 'organization_name', 'start_year', 'end_year'];
    }

    public function getFormFields($client_id = null) {
        $current_year = (int)date('Y');
        $years = [];
        for ($y = $current_year; $y >= $current_year - 80; $y--) {
            $years[$y] = $y;
        }
        return [
            ['name' => 'organization_name', 'label' => 'Organization Name'],
            ['name' => 'start_year', 'label' => 'Start Year', 'type' => 'select', 'options' => $years],
            ['name' => 'end_year', 'label' => 'End Year', 'type' => 'select', 'options' => $years],
            ['name' => 'address', 'label' => 'Address'],
            ['name' => 'phone', 'label' => 'Phone'],
            ['name' => 'email', 'label' => 'Email'],
        ];
    }
    public static function get_section_key() {
        return 'volunteering';
    }
    public static function getFieldDefinitions() {
        return [
            'suitecrm_guid' => [ 'label' => 'SuiteCRM GUID', 'type' => 'text', 'required' => false, 'db_type' => 'VARCHAR(36)' ],
            'wp_record_id' => [ 'label' => 'WP Record ID', 'type' => 'number', 'required' => false, 'db_type' => 'BIGINT(20)' ],
            'client_id' => [ 'label' => 'Client ID', 'type' => 'number', 'required' => true, 'db_type' => 'BIGINT(20)' ],
            'organization_name' => [ 'label' => 'Organization Name', 'type' => 'text', 'required' => true, 'db_type' => 'VARCHAR(255)' ],
            'start_year' => [ 'label' => 'Start Year', 'type' => 'number', 'required' => true, 'db_type' => 'INT(4)' ],
            'end_year' => [ 'label' => 'End Year', 'type' => 'number', 'required' => false, 'db_type' => 'INT(4)' ],
            'address' => [ 'label' => 'Address', 'type' => 'text', 'required' => false, 'db_type' => 'VARCHAR(255)' ],
            'phone' => [ 'label' => 'Phone', 'type' => 'text', 'required' => false, 'db_type' => 'VARCHAR(50)' ],
            'email' => [ 'label' => 'Email', 'type' => 'email', 'required' => false, 'db_type' => 'VARCHAR(100)' ],
        ];
    }
}
