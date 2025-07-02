<?php
namespace EstatePlanningManager\Models;

class RelationshipTypesModel {
    /**
     * Canonical field definitions for the relationship types selector table
     */
    public static function getFieldDefinitions() {
        return [
            'value' => [
                'label' => 'Value',
                'type' => 'text',
                'required' => true,
                'db_type' => 'VARCHAR(100) NOT NULL'
            ],
            'label' => [
                'label' => 'Label',
                'type' => 'text',
                'required' => true,
                'db_type' => 'VARCHAR(255) NOT NULL'
            ],
            'is_active' => [
                'label' => 'Is Active',
                'type' => 'checkbox',
                'required' => false,
                'db_type' => 'TINYINT(1) DEFAULT 1'
            ],
            'sort_order' => [
                'label' => 'Sort Order',
                'type' => 'number',
                'required' => false,
                'db_type' => 'INT(11) DEFAULT 0'
            ],
        ];
    }

    /**
     * Returns the default rows for the relationship types table as associative arrays.
     */
    public static function getDefaultRows() {
        return [
            ['value' => 'spouse', 'label' => 'Spouse', 'is_active' => 1],
            ['value' => 'child', 'label' => 'Child', 'is_active' => 1],
            ['value' => 'parent', 'label' => 'Parent', 'is_active' => 1],
            ['value' => 'sibling', 'label' => 'Sibling', 'is_active' => 1],
            ['value' => 'grandparent', 'label' => 'Grandparent', 'is_active' => 1],
            ['value' => 'grandchild', 'label' => 'Grandchild', 'is_active' => 1],
            ['value' => 'other_family', 'label' => 'Other Family', 'is_active' => 1],
            ['value' => 'friend', 'label' => 'Friend', 'is_active' => 1],
            ['value' => 'other', 'label' => 'Other', 'is_active' => 1],
        ];
    }
}
