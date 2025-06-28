<?php
// Minimal module file for EPM_RealEstate
class EPM_RealEstate extends Basic {
    var $new_schema = true;
    var $module_dir = 'EPM_RealEstate';
    var $object_name = 'EPM_RealEstate';
    var $table_name = 'epm_real_estate';
    var $wp_record_id;
    function EPM_RealEstate() {
        parent::Basic();
    }
}
