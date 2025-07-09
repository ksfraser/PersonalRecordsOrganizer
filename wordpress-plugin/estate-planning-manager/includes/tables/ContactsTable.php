<?php
require_once __DIR__ . '/EPM_AbstractTable.php';
require_once __DIR__ . '/TableInterface.php';

class ContactsTable extends \EstatePlanningManager\Tables\EPM_AbstractTable implements \EstatePlanningManager\Tables\TableInterface {
    public function create($charset_collate) {
        ob_start();
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_contacts';
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (\n"
            . "id bigint(20) NOT NULL AUTO_INCREMENT,\n"
            . "client_id bigint(20) NOT NULL,\n"
            . "first_name varchar(100) NOT NULL,\n"
            . "last_name varchar(100) NOT NULL,\n"
            . "relationship_type_id bigint(20) DEFAULT NULL,\n"
            . "is_advisor tinyint(1) DEFAULT 0,\n"
            . "created datetime DEFAULT CURRENT_TIMESTAMP,\n"
            . "lastupdated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,\n"
            . "PRIMARY KEY (id),\n"
            . "KEY client_id (client_id)\n"
            . ") $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        ob_end_clean();
    }
    public function populate($charset_collate) {
        // No default data for user data tables
    }
}
