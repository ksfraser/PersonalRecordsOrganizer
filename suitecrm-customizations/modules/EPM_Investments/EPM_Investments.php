<?php
// Minimal module file for EPM_Investments
class EPM_Investments extends Basic {
    var $new_schema = true;
    var $module_dir = 'EPM_Investments';
    var $object_name = 'EPM_Investments';
    var $table_name = 'epm_investments';
    var $wp_record_id;
    function EPM_Investments() {
        parent::Basic();
    }
}
