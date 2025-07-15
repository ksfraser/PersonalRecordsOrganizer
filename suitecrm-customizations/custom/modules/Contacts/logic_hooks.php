<?php
/**
 * Logic hook registration for SuiteCRM to WordPress sync
 * Place in custom/modules/Contacts/logic_hooks.php
 */
$hook_array['after_save'][] = array(
    1,
    'Sync Contact to WordPress',
    'custom/modules/Contacts/SuiteCRMToWordPressSync.php',
    'SuiteCRMToWordPressSync',
    'syncToWordPress'
);
