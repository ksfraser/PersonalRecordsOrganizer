<?php
require_once __DIR__ . '/EPM_AbstractTable.php';
require_once __DIR__ . '/TableInterface.php';

class ClientsTable extends \EstatePlanningManager\Tables\EPM_AbstractTable implements \EstatePlanningManager\Tables\TableInterface {
    public function create($charset_collate) {
        ob_start();
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_clients';
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (\n"
            . "id bigint(20) NOT NULL AUTO_INCREMENT,\n"
            . "user_id bigint(20) DEFAULT NULL,\n"
            . "created datetime DEFAULT CURRENT_TIMESTAMP,\n"
            . "lastupdated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,\n"
            . "PRIMARY KEY (id),\n"
            . "KEY user_id (user_id)\n"
            . ") $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        ob_end_clean();
    }
    public function populate($charset_collate) {
        // No default data for clients table
    }
}
