<?php
namespace EstatePlanningManager\Models;

require_once __DIR__ . '/AbstractSectionModel.php';

class PersonModel extends AbstractSectionModel {
    public static function getFieldDefinitions() {
        return [
            ['name' => 'full_name', 'label' => 'Name', 'type' => 'text', 'required' => true],
            ['name' => 'email', 'label' => 'Email', 'type' => 'email'],
            ['name' => 'phone', 'label' => 'Phone', 'type' => 'tel'],
            ['name' => 'address', 'label' => 'Address', 'type' => 'text'],
        ];
    }

    public function getTableName() {
        global $wpdb;
        return $wpdb->prefix . 'epm_persons';
    }

    // Table creation is handled by PersonTable in includes/tables/
}
