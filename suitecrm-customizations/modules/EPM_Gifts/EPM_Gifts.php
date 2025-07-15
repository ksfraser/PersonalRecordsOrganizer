<?php
/**
 * SuiteCRM Custom Module: EPM_Gifts
 * Maps to WordPress epm_gifts table
 * Links to Contact or Lead via GUID
 */
class EPM_Gifts extends Basic {
    var $new_schema = true;
    var $module_dir = 'EPM_Gifts';
    var $object_name = 'EPM_Gifts';
    var $table_name = 'epm_gifts';
    var $suitecrm_guid;
    var $wp_guid;
    var $contact_id;
    var $lead_id;
    var $gift_type;
    var $gift_value;
    var $date_given;
    // ... other fields ...
}
