<?php
/**
 * PaymentTypeModel
 * Model for Scheduled Payment Types
 * 
 * @package EstatePlanningManager\Models
 * @phpdoc [UML] class PaymentTypeModel extends BaseModel
 */
namespace EstatePlanningManager\Models;

class PaymentTypeModel extends BaseModel {
    /**
     * Get the table name for this model
     * @return string
     */
    public static function getTableName() {
        return 'epm_payment_types';
    }

    /**
     * Get field definitions for the Payment Type section
     * @return array
     */
    public static function getFieldDefinitions() {
        return [
            'value' => [
                'type' => 'text',
                'label' => 'Value',
                'required' => true,
            ],
            'label' => [
                'type' => 'text',
                'label' => 'Label',
                'required' => true,
            ],
        ];
    }
}
