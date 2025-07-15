<?php
$dictionary['EPM_Liabilities'] = array(
    'table' => 'epm_liabilities',
    'fields' => array(
        'name' => array(
            'name' => 'name',
            'vname' => 'LBL_NAME',
            'type' => 'varchar',
            'len' => '255',
            'required' => true,
        ),
        'liability_type' => array(
            'name' => 'liability_type',
            'vname' => 'LBL_LIABILITY_TYPE',
            'type' => 'varchar',
            'len' => '100',
        ),
        'liability_value' => array(
            'name' => 'liability_value',
            'vname' => 'LBL_LIABILITY_VALUE',
            'type' => 'currency',
        ),
        'date_incurred' => array(
            'name' => 'date_incurred',
            'vname' => 'LBL_DATE_INCURRED',
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
        array('name' => 'epm_liabilities_pk', 'type' => 'primary', 'fields' => array('id')),
        array('name' => 'idx_guid', 'type' => 'index', 'fields' => array('guid')),
    ),
);
