<?php
namespace EstatePlanningManager\Models;

require_once __DIR__ . '/AbstractSectionModel.php';

if (!defined('ABSPATH')) exit;

class SocialMediaAccountsModel extends AbstractSectionModel {
    /**
     * Create the social media accounts table if it does not exist
     */
    public static function createTable($charset_collate) {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_social_media_accounts';
        $sql = "CREATE TABLE IF NOT EXISTS $table (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            client_id BIGINT UNSIGNED NOT NULL,
            suitecrm_guid VARCHAR(36) DEFAULT NULL,
            wp_record_id BIGINT UNSIGNED DEFAULT NULL,
            type VARCHAR(100) DEFAULT NULL,
            platform VARCHAR(100) NOT NULL,
            username VARCHAR(255) NOT NULL,
            email VARCHAR(255) DEFAULT NULL,
            password VARCHAR(255) NOT NULL,
            profile_url VARCHAR(255) DEFAULT NULL,
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
        return $wpdb->prefix . 'epm_social_media_accounts';
    }
    public function getAllRecordsForClient($client_id) {
        global $wpdb;
        $table = $this->getTableName();
        $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE client_id = %d", $client_id), ARRAY_A);
        return $results ? $results : [];
    }
    public function getSummaryFields() {
        return ['id', 'platform', 'username'];
    }
    protected static function getPlatformTypeOptions() {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_social_media_platform_types';
        $results = $wpdb->get_results("SELECT value, label FROM $table WHERE is_active = 1 ORDER BY sort_order ASC, label ASC", ARRAY_A);
        $options = [];
        if ($results) {
            foreach ($results as $row) {
                $options[$row['value']] = $row['label'];
            }
        }
        return $options;
    }
    public function getFormFields($client_id = null) {
        return [
            ['name' => 'type', 'label' => 'Type', 'type' => 'select', 'options' => self::getPlatformTypeOptions()],
            ['name' => 'platform', 'label' => 'Platform'],
            ['name' => 'username', 'label' => 'Username'],
            ['name' => 'email', 'label' => 'Email'],
            ['name' => 'password', 'label' => 'Password'],
            ['name' => 'profile_url', 'label' => 'Profile URL'],
            ['name' => 'notes', 'label' => 'Notes'],
        ];
    }
    public static function get_section_key() {
        return 'social_media_accounts';
    }
    public static function getFieldDefinitions() {
        return [
            'suitecrm_guid' => [ 'label' => 'SuiteCRM GUID', 'type' => 'text', 'required' => false, 'db_type' => 'VARCHAR(36)' ],
            'wp_record_id' => [ 'label' => 'WP Record ID', 'type' => 'number', 'required' => false, 'db_type' => 'BIGINT(20)' ],
            'client_id' => [ 'label' => 'Client ID', 'type' => 'number', 'required' => true, 'db_type' => 'BIGINT(20)' ],
            'type' => [ 'label' => 'Type', 'type' => 'select', 'required' => false, 'db_type' => 'VARCHAR(100)' ],
            'platform' => [ 'label' => 'Platform', 'type' => 'text', 'required' => true, 'db_type' => 'VARCHAR(100)' ],
            'username' => [ 'label' => 'Username', 'type' => 'text', 'required' => true, 'db_type' => 'VARCHAR(255)' ],
            'email' => [ 'label' => 'Email', 'type' => 'email', 'required' => false, 'db_type' => 'VARCHAR(255)' ],
            'password' => [ 'label' => 'Password', 'type' => 'text', 'required' => true, 'db_type' => 'VARCHAR(255)' ],
            'profile_url' => [ 'label' => 'Profile URL', 'type' => 'text', 'required' => false, 'db_type' => 'VARCHAR(255)' ],
            'notes' => [ 'label' => 'Notes', 'type' => 'textarea', 'required' => false, 'db_type' => 'TEXT' ],
        ];
    }
}
