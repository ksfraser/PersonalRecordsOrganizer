<?php
namespace EstatePlanningManager\Models;

require_once __DIR__ . '/AbstractSectionModel.php';

if (!defined('ABSPATH')) exit;

class SafetyDepositBoxModel extends AbstractSectionModel {
    /**
     * Create the safety deposit box table if it does not exist
     */
    public static function createTable($charset_collate) {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_safety_deposit_box';
        $sql = "CREATE TABLE IF NOT EXISTS $table (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            client_id BIGINT UNSIGNED NOT NULL,
            suitecrm_guid VARCHAR(36) DEFAULT NULL,
            wp_record_id BIGINT UNSIGNED DEFAULT NULL,
            box_location VARCHAR(255) NOT NULL,
            box_access_names TEXT DEFAULT NULL,
            keys_location TEXT DEFAULT NULL,
            contents_list_location TEXT DEFAULT NULL,
            created DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            KEY idx_client (client_id)
        ) ENGINE=InnoDB $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    public function getTableName() {
        global $wpdb;
        return $wpdb->prefix . 'epm_safety_deposit_box';
    }
    public function getAllRecordsForClient($client_id) {
        global $wpdb;
        $table = $this->getTableName();
        $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE client_id = %d", $client_id), \ARRAY_A);
        return $results ? $results : [];
    }
    public function getOwnerIdForSection($section, $client_id) {
        return $client_id;
    }
    public function getSummaryFields() {
        return ['id', 'box_location'];
    }
    public function getFormFields() {
        return [
            ['name' => 'box_location', 'label' => 'Box Location'],
            ['name' => 'box_access_names', 'label' => 'Box Access Names'],
            ['name' => 'keys_location', 'label' => 'Keys Location'],
            ['name' => 'contents_list_location', 'label' => 'Contents List Location'],
        ];
    }
    public static function get_section_key() {
        return 'safety_deposit_box';
    }
    public static function getFieldDefinitions() {
        return [
            'suitecrm_guid' => [
                'label' => 'SuiteCRM GUID',
                'type' => 'text',
                'required' => false,
                'db_type' => 'VARCHAR(36)'
            ],
            'wp_record_id' => [
                'label' => 'WP Record ID',
                'type' => 'number',
                'required' => false,
                'db_type' => 'BIGINT(20)'
            ],
            'client_id' => [
                'label' => 'Client ID',
                'type' => 'number',
                'required' => true,
                'db_type' => 'BIGINT(20)'
            ],
            'box_location' => [
                'label' => 'Box Location',
                'type' => 'text',
                'required' => true,
                'db_type' => 'VARCHAR(255)'
            ],
            'box_access_names' => [
                'label' => 'Box Access Names',
                'type' => 'textarea',
                'required' => false,
                'db_type' => 'TEXT'
            ],
            'keys_location' => [
                'label' => 'Keys Location',
                'type' => 'textarea',
                'required' => false,
                'db_type' => 'TEXT'
            ],
            'contents_list_location' => [
                'label' => 'Contents List Location',
                'type' => 'textarea',
                'required' => false,
                'db_type' => 'TEXT'
            ],
        ];
    }
}
