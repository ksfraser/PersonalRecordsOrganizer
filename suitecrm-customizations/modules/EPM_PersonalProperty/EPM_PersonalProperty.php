<?php
// Minimal module file for EPM_PersonalProperty
class EPM_PersonalProperty extends Basic {
    var $new_schema = true;
    var $module_dir = 'EPM_PersonalProperty';
    var $object_name = 'EPM_PersonalProperty';
    var $table_name = 'epm_personal_property';
    var $wp_record_id;
    function EPM_PersonalProperty() {
        parent::Basic();
    }
}
