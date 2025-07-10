<?php
namespace EstatePlanningManager\Models;

require_once __DIR__ . '/AbstractSectionModel.php';

if (!defined('ABSPATH')) exit;

class KeyContactsModel extends AbstractSectionModel {
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
