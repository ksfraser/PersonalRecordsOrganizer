<?php
// Minimal module file for EPM_DebtorsCreditors
class EPM_DebtorsCreditors extends Basic {
    var $new_schema = true;
    var $module_dir = 'EPM_DebtorsCreditors';
    var $object_name = 'EPM_DebtorsCreditors';
    var $table_name = 'epm_debtors_creditors';
    var $wp_record_id;
    function EPM_DebtorsCreditors() {
        parent::Basic();
    }
}
