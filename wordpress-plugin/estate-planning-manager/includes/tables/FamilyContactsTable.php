<?php
require_once __DIR__ . '/TableInterface.php';

class FamilyContactsTable implements TableInterface {
    public function create($charset_collate) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_family_contacts';
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            client_id bigint(20) NOT NULL,
            name varchar(255) NOT NULL,
            relationship varchar(100) DEFAULT NULL,
            phone varchar(50) DEFAULT NULL,
            email varchar(100) DEFAULT NULL,
            created datetime DEFAULT CURRENT_TIMESTAMP,
            lastupdated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY client_id (client_id)
        ) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    public function populate($charset_collate) {
        // No default data for family contacts
    }
}
