<?php
$dictionary['EPM_SuggestedUpdates'] = array(
    'table' => 'epm_suggested_updates',
    'fields' => array(
        'name' => array(
            'name' => 'name',
            'vname' => 'LBL_NAME',
            'type' => 'varchar',
            'len' => '255',
            'required' => true,
        ),
        'update_type' => array(
            'name' => 'update_type',
            'vname' => 'LBL_UPDATE_TYPE',
            'type' => 'varchar',
            'len' => '100',
        ),
        'update_status' => array(
            'name' => 'update_status',
            'vname' => 'LBL_UPDATE_STATUS',
            'type' => 'varchar',
            'len' => '50',
        ),
        'related_guid' => array(
            'name' => 'related_guid',
            'vname' => 'LBL_RELATED_GUID',
            'type' => 'varchar',
            'len' => '36',
        ),
        'guid' => array(
            'name' => 'guid',
            'vname' => 'LBL_GUID',
            'type' => 'varchar',
            'len' => '36',
        ),
    ),
    'indices' => array(
        array('name' => 'epm_suggested_updates_pk', 'type' => 'primary', 'fields' => array('id')),
        array('name' => 'idx_guid', 'type' => 'index', 'fields' => array('guid')),
    ),
);
