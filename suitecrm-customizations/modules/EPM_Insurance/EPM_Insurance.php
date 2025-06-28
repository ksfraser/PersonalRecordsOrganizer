<?php
// Minimal module file for EPM_Insurance
class EPM_Insurance extends Basic {
    var $new_schema = true;
    var $module_dir = 'EPM_Insurance';
    var $object_name = 'EPM_Insurance';
    var $table_name = 'epm_insurance';
    var $wp_record_id;
    function EPM_Insurance() {
        parent::Basic();
    }
}
