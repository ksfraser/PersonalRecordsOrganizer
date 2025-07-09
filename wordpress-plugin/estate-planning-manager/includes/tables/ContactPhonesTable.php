<?php
require_once __DIR__ . '/EPM_AbstractTable.php';
require_once __DIR__ . '/TableInterface.php';

class ContactPhonesTable extends \EstatePlanningManager\Tables\EPM_AbstractTable implements \EstatePlanningManager\Tables\TableInterface {
    public function create($charset_collate) {
        ob_start();
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_contact_phones';
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (\n"
            . "id bigint(20) NOT NULL AUTO_INCREMENT,\n"
            . "contact_id bigint(20) NOT NULL,\n"
            . "phone varchar(50) NOT NULL,\n"
            . "type_id bigint(20) DEFAULT NULL,\n"
            . "is_primary tinyint(1) DEFAULT 0,\n"
            . "created datetime DEFAULT CURRENT_TIMESTAMP,\n"
            . "lastupdated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,\n"
            . "PRIMARY KEY (id),\n"
            . "KEY contact_id (contact_id),\n"
            . "KEY type_id (type_id)\n"
            . ") $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        ob_end_clean();
    }
}
