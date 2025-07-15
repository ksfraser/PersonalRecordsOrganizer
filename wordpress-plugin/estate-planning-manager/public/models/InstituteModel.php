<?php
namespace EstatePlanningManager\Models;

require_once __DIR__ . '/AbstractSectionModel.php';

class InstituteModel extends AbstractSectionModel {
    public static function getFieldDefinitions() {
        return [
            ['name' => 'name', 'label' => 'Name', 'type' => 'text', 'required' => true],
            ['name' => 'email', 'label' => 'Email', 'type' => 'email'],
            ['name' => 'phone', 'label' => 'Phone', 'type' => 'tel'],
            ['name' => 'address', 'label' => 'Address', 'type' => 'text'],
            ['name' => 'account_number', 'label' => 'Account Number', 'type' => 'text'],
            ['name' => 'branch', 'label' => 'Branch', 'type' => 'text'],
        ];
    }

    public function getTableName() {
        global $wpdb;
        return $wpdb->prefix . 'epm_institutes';
    }

    // Table creation is handled by OrganizationTable in includes/tables/
}
