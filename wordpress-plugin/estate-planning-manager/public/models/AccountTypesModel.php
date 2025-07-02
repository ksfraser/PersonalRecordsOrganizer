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
}
