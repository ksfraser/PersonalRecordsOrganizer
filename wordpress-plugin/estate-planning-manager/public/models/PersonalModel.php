<?php
namespace EstatePlanningManager\Models;

require_once __DIR__ . '/AbstractSectionModel.php';

class PersonalModel extends AbstractSectionModel {
    public function getTableName() {
        global $wpdb;
        return $wpdb->prefix . 'epm_personal_property';
    }
    public function getOwnerIdForSection($section, $client_id) {
        return $client_id;
    }
    public function getSummaryFields() {
        return ['id', 'name'];
    }
    public function getFormFields() {
        return [
            ['name' => 'name', 'label' => 'Full Name'],
            ['name' => 'dob', 'label' => 'Date of Birth'],
            ['name' => 'email', 'label' => 'Email'],
            ['name' => 'phone', 'label' => 'Phone'],
            // Add more fields as needed
        ];
    }
}
