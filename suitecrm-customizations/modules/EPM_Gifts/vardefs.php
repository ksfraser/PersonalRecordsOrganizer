<?php
$dictionary['EPM_Gifts'] = array(
    'table' => 'epm_gifts',
    'fields' => array(
        'name' => array(
            'name' => 'name',
            'vname' => 'LBL_NAME',
            'type' => 'varchar',
            'len' => '255',
            'required' => true,
        ),
        'gift_type' => array(
            'name' => 'gift_type',
            'vname' => 'LBL_GIFT_TYPE',
            'type' => 'varchar',
            'len' => '100',
        ),
        'gift_value' => array(
            'name' => 'gift_value',
            'vname' => 'LBL_GIFT_VALUE',
            'type' => 'currency',
        ),
        'date_given' => array(
            'name' => 'date_given',
            'vname' => 'LBL_DATE_GIVEN',
            'type' => 'date',
        ),
        'guid' => array(
            'name' => 'guid',
            'vname' => 'LBL_GUID',
            'type' => 'varchar',
            'len' => '36',
        ),
    ),
    'indices' => array(
        array('name' => 'epm_gifts_pk', 'type' => 'primary', 'fields' => array('id')),
        array('name' => 'idx_guid', 'type' => 'index', 'fields' => array('guid')),
    ),
);
