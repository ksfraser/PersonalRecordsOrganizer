<?php
namespace EstatePlanningManager\Models;

class InstituteModel {
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
}
