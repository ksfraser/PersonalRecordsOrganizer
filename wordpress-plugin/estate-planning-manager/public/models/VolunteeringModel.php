<?php
namespace EstatePlanningManager\Models;

require_once __DIR__ . '/AbstractSectionModel.php';

if (!defined('ABSPATH')) exit;

class VolunteeringModel extends AbstractSectionModel {
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
        $year_options = $this->getYearOptions($client_id);
        return [
            ['name' => 'organization_name', 'label' => 'Organization Name'],
            ['name' => 'start_year', 'label' => 'Start Year', 'type' => 'select', 'options' => $year_options],
            ['name' => 'end_year', 'label' => 'End Year', 'type' => 'select', 'options' => $year_options],
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
