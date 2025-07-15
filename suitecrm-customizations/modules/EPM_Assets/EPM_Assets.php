<?php
/**
 * SuiteCRM Custom Module: EPM_Assets
 * Maps to WordPress epm_assets table
 * Links to Contact or Lead via GUID
 */
class EPM_Assets extends Basic {
    var $new_schema = true;
    var $module_dir = 'EPM_Assets';
    var $object_name = 'EPM_Assets';
    var $table_name = 'epm_assets';
    var $suitecrm_guid;
    var $wp_guid;
    var $contact_id;
    var $lead_id;
    var $asset_type;
    var $asset_value;
    var $date_acquired;
    // ... other fields ...
}
