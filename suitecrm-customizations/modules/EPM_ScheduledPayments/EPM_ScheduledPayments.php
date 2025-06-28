<?php
// Minimal module file for EPM_ScheduledPayments
class EPM_ScheduledPayments extends Basic {
    var $new_schema = true;
    var $module_dir = 'EPM_ScheduledPayments';
    var $object_name = 'EPM_ScheduledPayments';
    var $table_name = 'epm_scheduled_payments';
    var $wp_record_id;
    function EPM_ScheduledPayments() {
        parent::Basic();
    }
}
