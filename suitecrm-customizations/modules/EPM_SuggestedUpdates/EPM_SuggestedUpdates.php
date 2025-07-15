<?php
/**
 * SuiteCRM Custom Module: EPM_SuggestedUpdates
 * For audit/sync tracking
 */
class EPM_SuggestedUpdates extends Basic {
    var $new_schema = true;
    var $module_dir = 'EPM_SuggestedUpdates';
    var $object_name = 'EPM_SuggestedUpdates';
    var $table_name = 'epm_suggested_updates';
    var $suitecrm_guid;
    var $wp_guid;
    var $client_id;
    var $section;
    var $field;
    var $old_value;
    var $new_value;
    var $notes;
    var $status;
    var $created_at;
    var $updated_at;
    var $source;
    var $source_record_id;
}
