<?php
$dictionary['EPM_Insurance'] = array(
    'table' => 'epm_insurance',
    'fields' => array(
        'wp_record_id' => array(
            'name' => 'wp_record_id',
            'vname' => 'LBL_WP_RECORD_ID',
            'type' => 'varchar',
            'len' => '20',
        ),
    ),
    'indices' => array(
        array('name' => 'idx_wp_record_id', 'type' => 'index', 'fields' => array('wp_record_id')),
    ),
);
