<?php
require_once __DIR__ . '/EPM_AbstractTable.php';
require_once __DIR__ . '/TableInterface.php';

class ContactAddressesTable extends \EstatePlanningManager\Tables\EPM_AbstractTable implements \EstatePlanningManager\Tables\TableInterface {
    public function create($charset_collate) {
        ob_start();
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_contact_addresses';
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (\n"
            . "id bigint(20) NOT NULL AUTO_INCREMENT,\n"
            . "contact_id bigint(20) NOT NULL,\n"
            . "suitecrm_guid varchar(36) DEFAULT NULL,\n"
            . "wp_record_id bigint(20) DEFAULT NULL,\n"
            . "type varchar(32) DEFAULT 'primary',\n"
            . "street varchar(255) DEFAULT NULL,\n"
            . "street2 varchar(255) DEFAULT NULL,\n"
            . "city varchar(100) DEFAULT NULL,\n"
            . "state varchar(100) DEFAULT NULL,\n"
            . "country varchar(100) DEFAULT NULL,\n"
            . "postalcode varchar(32) DEFAULT NULL,\n"
            . "created datetime DEFAULT CURRENT_TIMESTAMP,\n"
            . "lastupdated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,\n"
            . "PRIMARY KEY (id),\n"
            . "KEY contact_id (contact_id),\n"
            . "KEY suitecrm_guid (suitecrm_guid),\n"
            . "KEY wp_record_id (wp_record_id)\n"
            . ") $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        ob_end_clean();
    }
    public function populate($charset_collate) {
        // No default data for contact addresses
    }
}
