<?php
namespace EstatePlanningManager\Models;

require_once __DIR__ . '/AbstractSectionModel.php';

if (!defined('ABSPATH')) exit;

class DigitalAssetsModel extends AbstractSectionModel {
    public function getTableName() {
        global $wpdb;
        return $wpdb->prefix . 'epm_digital_assets';
    }
    public static function get_section_key() {
        return 'digital_assets';
    }
    public static function getFieldDefinitions() {
        return [
            'suitecrm_guid' => [ 'label' => 'SuiteCRM GUID', 'type' => 'text', 'required' => false, 'db_type' => 'VARCHAR(36)' ],
            'wp_record_id' => [ 'label' => 'WP Record ID', 'type' => 'number', 'required' => false, 'db_type' => 'BIGINT(20)' ],
            'client_id' => [ 'label' => 'Client ID', 'type' => 'number', 'required' => true, 'db_type' => 'BIGINT(20)' ],
            'asset_name' => [ 'label' => 'Asset Name', 'type' => 'text', 'required' => true, 'db_type' => 'VARCHAR(255)' ],
            'asset_type' => [ 'label' => 'Asset Type', 'type' => 'text', 'required' => true, 'db_type' => 'VARCHAR(100)' ],
            'account_username' => [ 'label' => 'Account/Username', 'type' => 'text', 'required' => false, 'db_type' => 'VARCHAR(100)' ],
            'url' => [ 'label' => 'URL', 'type' => 'text', 'required' => false, 'db_type' => 'VARCHAR(255)' ],
            'notes' => [ 'label' => 'Notes', 'type' => 'textarea', 'required' => false, 'db_type' => 'TEXT' ],
        ];
    }
}
