<?php
namespace EstatePlanningManager\Models;

if (!defined('ABSPATH')) exit;

/**
 * Model for suggested updates table.
 * @phpdoc
 * @uml
 * class SuggestedUpdatesModel extends AbstractSectionModel {
 *   +static getFieldDefinitions()
 * }
 */
class SuggestedUpdatesModel {
    /**
     * Return field definitions for suggested updates table.
     * @return array
     */
    public static function getFieldDefinitions() {
        return [
            'client_id' => [
                'label' => 'Client',
                'type' => 'number',
                'required' => true,
                'db_type' => 'BIGINT(20) NOT NULL',
            ],
            'field' => [
                'label' => 'Field',
                'type' => 'text',
                'required' => false,
                'db_type' => 'VARCHAR(100) DEFAULT NULL',
            ],
            'old_value' => [
                'label' => 'Old Value',
                'type' => 'text',
                'required' => false,
                'db_type' => 'TEXT DEFAULT NULL',
            ],
            'new_value' => [
                'label' => 'New Value',
                'type' => 'text',
                'required' => false,
                'db_type' => 'TEXT DEFAULT NULL',
            ],
            'notes' => [
                'label' => 'Notes',
                'type' => 'text',
                'required' => false,
                'db_type' => 'TEXT DEFAULT NULL',
            ],
            'status' => [
                'label' => 'Status',
                'type' => 'text',
                'required' => false,
                'db_type' => "VARCHAR(50) DEFAULT 'pending'",
            ],
            'section' => [
                'label' => 'Section',
                'type' => 'text',
                'required' => false,
                'db_type' => 'VARCHAR(100) DEFAULT NULL',
            ],
        ];
    }
}
