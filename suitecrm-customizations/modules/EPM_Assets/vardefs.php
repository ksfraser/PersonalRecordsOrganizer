<?php
$dictionary['EPM_Assets'] = array(
    'table' => 'epm_assets',
    'fields' => array(
        'name' => array(
            'name' => 'name',
            'vname' => 'LBL_NAME',
            'type' => 'varchar',
            'len' => '255',
            'required' => true,
        ),
        'asset_type' => array(
            'name' => 'asset_type',
            'vname' => 'LBL_ASSET_TYPE',
            'type' => 'varchar',
            'len' => '100',
        ),
        'asset_value' => array(
            'name' => 'asset_value',
            'vname' => 'LBL_ASSET_VALUE',
            'type' => 'currency',
        ),
        'date_acquired' => array(
            'name' => 'date_acquired',
            'vname' => 'LBL_DATE_ACQUIRED',
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
        array('name' => 'epm_assets_pk', 'type' => 'primary', 'fields' => array('id')),
        array('name' => 'idx_guid', 'type' => 'index', 'fields' => array('guid')),
    ),
);
