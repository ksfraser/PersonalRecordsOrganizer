<?php
// Minimal module file for EPM_DigitalAssets
class EPM_DigitalAssets extends Basic {
    var $new_schema = true;
    var $module_dir = 'EPM_DigitalAssets';
    var $object_name = 'EPM_DigitalAssets';
    var $table_name = 'epm_digital_assets';
    var $wp_record_id;
    function EPM_DigitalAssets() {
        parent::Basic();
    }
}
