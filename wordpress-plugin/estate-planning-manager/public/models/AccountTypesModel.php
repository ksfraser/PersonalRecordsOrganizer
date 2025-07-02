<?php
namespace EstatePlanningManager\Models;
class AccountTypesModel {
    public static function getFieldDefinitions() {
        return [
            'value' => [
                'label' => 'Value', 'type' => 'text', 'required' => true, 'db_type' => 'VARCHAR(100) NOT NULL'],
            'label' => [
                'label' => 'Label', 'type' => 'text', 'required' => true, 'db_type' => 'VARCHAR(255) NOT NULL'],
            'is_active' => [
                'label' => 'Is Active', 'type' => 'checkbox', 'required' => false, 'db_type' => 'TINYINT(1) DEFAULT 1'],
            'sort_order' => [
                'label' => 'Sort Order', 'type' => 'number', 'required' => false, 'db_type' => 'INT(11) DEFAULT 0'],
        ];
    }
    public static function getDefaultRows() {
        return [
            ['value' => 'checking', 'label' => 'Checking', 'is_active' => 1],
            ['value' => 'savings', 'label' => 'Savings', 'is_active' => 1],
            ['value' => 'money_market', 'label' => 'Money Market', 'is_active' => 1],
            ['value' => 'cd', 'label' => 'Certificate of Deposit', 'is_active' => 1],
            ['value' => 'brokerage', 'label' => 'Brokerage', 'is_active' => 1],
            ['value' => 'retirement', 'label' => 'Retirement', 'is_active' => 1],
            ['value' => 'other', 'label' => 'Other', 'is_active' => 1],
        ];
    }
}
