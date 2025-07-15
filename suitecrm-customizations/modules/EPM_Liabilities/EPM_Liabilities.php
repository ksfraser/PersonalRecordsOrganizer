<?php
/**
 * SuiteCRM Custom Module: EPM_Liabilities
 * Maps to WordPress epm_liabilities table
 * Links to Contact or Lead via GUID
 */
class EPM_Liabilities extends Basic {
    var $new_schema = true;
    var $module_dir = 'EPM_Liabilities';
    var $object_name = 'EPM_Liabilities';
    var $table_name = 'epm_liabilities';
    var $suitecrm_guid;
    var $wp_guid;
    var $contact_id;
    var $lead_id;
    var $liability_type;
    var $liability_value;
    var $date_incurred;
    // ... other fields ...
}
