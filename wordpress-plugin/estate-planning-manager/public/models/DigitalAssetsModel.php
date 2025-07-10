<?php
namespace EstatePlanningManager\Models;

require_once __DIR__ . '/AbstractSectionModel.php';

if (!defined('ABSPATH')) exit;

class DigitalAssetsModel extends AbstractSectionModel {
    /**
     * Create the digital assets table if it does not exist
     */
    public static function createTable($charset_collate) {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_digital_assets';
        $sql = "CREATE TABLE IF NOT EXISTS $table (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            client_id BIGINT UNSIGNED NOT NULL,
            company VARCHAR(255) NOT NULL,
            user_id VARCHAR(255) DEFAULT NULL,
            password VARCHAR(255) DEFAULT NULL,
            account_number VARCHAR(255) DEFAULT NULL,
            url VARCHAR(255) DEFAULT NULL,
            email VARCHAR(255) DEFAULT NULL,
            phone VARCHAR(50) DEFAULT NULL,
            storage_type VARCHAR(50) DEFAULT NULL,
            notes TEXT DEFAULT NULL,
            created DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            KEY idx_client (client_id)
        ) ENGINE=InnoDB $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public static function getFieldDefinitions() {
        return [
            'company' => [ 'label' => 'Company', 'type' => 'text', 'required' => true, 'db_type' => 'VARCHAR(255)' ],
            'user_id' => [ 'label' => 'User ID', 'type' => 'text', 'required' => false, 'db_type' => 'VARCHAR(255)' ],
            'password' => [ 'label' => 'Password', 'type' => 'password', 'required' => false, 'db_type' => 'VARCHAR(255)' ],
            'account_number' => [ 'label' => 'Account #', 'type' => 'text', 'required' => false, 'db_type' => 'VARCHAR(255)' ],
            'url' => [ 'label' => 'URL', 'type' => 'url', 'required' => false, 'db_type' => 'VARCHAR(255)' ],
            'email' => [ 'label' => 'Email', 'type' => 'email', 'required' => false, 'db_type' => 'VARCHAR(255)' ],
            'phone' => [ 'label' => 'Phone', 'type' => 'text', 'required' => false, 'db_type' => 'VARCHAR(50)' ],
            'storage_type' => [
                'label' => 'Storage Type',
                'type' => 'select',
                'options' => [
                    'cloud' => 'Cloud',
                    'local' => 'Local',
                    'external' => 'External Drive',
                    'other' => 'Other',
                ],
                'required' => false,
                'db_type' => 'VARCHAR(50)'
            ],
            'notes' => [ 'label' => 'Notes', 'type' => 'textarea', 'required' => false, 'db_type' => 'TEXT' ],
        ];
    }

    public function getTableName() {
        global $wpdb;
        return $wpdb->prefix . 'epm_digital_assets';
    }

    public function getAllRecordsForClient($client_id) {
        global $wpdb;
        $table = $this->getTableName();
        $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE client_id = %d", $client_id), ARRAY_A);
        return $results ? $results : [];
    }

    public function getSummaryFields() {
        return ['company', 'user_id', 'account_number', 'url'];
    }

    public function getFormFields($client_id = null) {
        return [
            ['name' => 'company', 'label' => 'Company', 'type' => 'text'],
            ['name' => 'user_id', 'label' => 'User ID', 'type' => 'text'],
            ['name' => 'password', 'label' => 'Password', 'type' => 'password'],
            ['name' => 'account_number', 'label' => 'Account #', 'type' => 'text'],
            ['name' => 'url', 'label' => 'URL', 'type' => 'url'],
            ['name' => 'email', 'label' => 'Email', 'type' => 'email'],
            ['name' => 'phone', 'label' => 'Phone', 'type' => 'text'],
            [
                'name' => 'storage_type',
                'label' => 'Storage Type',
                'type' => 'select',
                'options' => [
                    'cloud' => 'Cloud',
                    'local' => 'Local',
                    'external' => 'External Drive',
                    'other' => 'Other',
                ]
            ],
            ['name' => 'notes', 'label' => 'Notes', 'type' => 'textarea'],
        ];
    }
}
