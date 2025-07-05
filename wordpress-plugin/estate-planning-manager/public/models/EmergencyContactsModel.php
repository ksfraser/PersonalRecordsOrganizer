<?php
namespace EstatePlanningManager\Models;

require_once __DIR__ . '/AbstractSectionModel.php';

if (!defined('ABSPATH')) exit;

class EmergencyContactsModel extends AbstractSectionModel {
    public function getTableName() {
        global $wpdb;
        return $wpdb->prefix . 'epm_contacts';
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
        return ['id', 'contact_name'];
    }
    public function getFormFields() {
        return [
            ['name' => 'contact_name', 'label' => 'Contact Name'],
            ['name' => 'relationship', 'label' => 'Relationship'],
            ['name' => 'phone', 'label' => 'Phone'],
            ['name' => 'email', 'label' => 'Email'],
        ];
    }
    public static function get_section_key() {
        return 'emergency_contacts';
    }
    public static function getFieldDefinitions() {
        return [
            'name' => [
                'label' => 'Contact Name',
                'type' => 'text',
                'required' => true,
                'db_type' => 'VARCHAR(255)'
            ],
            'relationship' => [
                'label' => 'Relationship',
                'type' => 'text',
                'required' => false,
                'db_type' => 'VARCHAR(100)'
            ],
            'phone' => [
                'label' => 'Phone',
                'type' => 'tel',
                'required' => false,
                'db_type' => 'VARCHAR(50)'
            ],
            'email' => [
                'label' => 'Email',
                'type' => 'email',
                'required' => false,
                'db_type' => 'VARCHAR(100)'
            ],
            'address' => [
                'label' => 'Address',
                'type' => 'text',
                'required' => false,
                'db_type' => 'VARCHAR(255)'
            ],
            'notes' => [
                'label' => 'Notes',
                'type' => 'textarea',
                'required' => false,
                'db_type' => 'TEXT'
            ],
        ];
    }
}
