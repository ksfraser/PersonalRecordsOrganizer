<?php
namespace EstatePlanningManager\Models;

require_once __DIR__ . '/AbstractSectionModel.php';

if (!defined('ABSPATH')) exit;

class CharitableGiftsModel extends AbstractSectionModel {
    public function getTableName() {
        global $wpdb;
        return $wpdb->prefix . 'epm_charitable_gifts';
    }
    public function getAllRecordsForClient($client_id) {
        global $wpdb;
        $table = $this->getTableName();
        $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE client_id = %d", $client_id), ARRAY_A);
        return $results ? $results : [];
    }
    public function getSummaryFields() {
        return ['id', 'charity', 'amount', 'frequency', 'in_will'];
    }
    public function getFormFields() {
        return [
            ['name' => 'charity', 'label' => 'Charity'],
            ['name' => 'address', 'label' => 'Address'],
            ['name' => 'phone', 'label' => 'Phone'],
            ['name' => 'email', 'label' => 'Email'],
            ['name' => 'information_location', 'label' => 'Information Location'],
            ['name' => 'amount', 'label' => 'Amount'],
            ['name' => 'frequency', 'label' => 'Frequency', 'type' => 'select', 'options' => self::getFrequencyOptions()],
            ['name' => 'in_will', 'label' => 'In Will', 'type' => 'select', 'options' => ['No' => 'No', 'Yes' => 'Yes']],
        ];
    }
    public static function getFrequencyOptions() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_frequency_types';
        $results = $wpdb->get_results("SELECT value, label FROM $table_name WHERE is_active = 1 ORDER BY sort_order ASC, label ASC", ARRAY_A);
        $options = [];
        if ($results) {
            foreach ($results as $row) {
                $options[$row['value']] = $row['label'];
            }
        }
        return $options;
    }
    public static function get_section_key() {
        return 'charitable_gifts';
    }
    public static function getFieldDefinitions() {
        return [
            'suitecrm_guid' => [ 'label' => 'SuiteCRM GUID', 'type' => 'text', 'required' => false, 'db_type' => 'VARCHAR(36)' ],
            'wp_record_id' => [ 'label' => 'WP Record ID', 'type' => 'number', 'required' => false, 'db_type' => 'BIGINT(20)' ],
            'client_id' => [ 'label' => 'Client ID', 'type' => 'number', 'required' => true, 'db_type' => 'BIGINT(20)' ],
            'charity' => [ 'label' => 'Charity', 'type' => 'text', 'required' => true, 'db_type' => 'VARCHAR(255)' ],
            'address' => [ 'label' => 'Address', 'type' => 'text', 'required' => false, 'db_type' => 'VARCHAR(255)' ],
            'phone' => [ 'label' => 'Phone', 'type' => 'text', 'required' => false, 'db_type' => 'VARCHAR(50)' ],
            'email' => [ 'label' => 'Email', 'type' => 'email', 'required' => false, 'db_type' => 'VARCHAR(100)' ],
            'information_location' => [ 'label' => 'Information Location', 'type' => 'text', 'required' => false, 'db_type' => 'VARCHAR(255)' ],
            'amount' => [ 'label' => 'Amount', 'type' => 'text', 'required' => false, 'db_type' => 'VARCHAR(50)' ],
            'frequency' => [ 'label' => 'Frequency', 'type' => 'select', 'required' => false, 'db_type' => 'VARCHAR(32)' ],
            'in_will' => [ 'label' => 'In Will', 'type' => 'select', 'required' => false, 'db_type' => 'TINYINT(1)' ],
        ];
    }
}
