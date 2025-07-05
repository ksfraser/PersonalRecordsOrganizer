<?php
require_once __DIR__ . '/TableInterface.php';

class ContactsTable implements TableInterface {
    public function create($charset_collate) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_contacts';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            full_name varchar(255) NOT NULL,
            relationship varchar(100) DEFAULT NULL,
            email varchar(100) DEFAULT NULL,
            phone varchar(50) DEFAULT NULL,
            address varchar(255) DEFAULT NULL,
            client_id bigint(20) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY client_id (client_id)
        ) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    public function populate($charset_collate) {
        // No default data for user data tables
    }
}
