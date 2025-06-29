<?php
require_once __DIR__ . '/TableInterface.php';

class PersonXrefTable implements TableInterface {
    public function create($charset_collate) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_person_xref';
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            client_id bigint(20) NOT NULL,
            person_id bigint(20) NOT NULL,
            section varchar(100) NOT NULL, -- e.g. 'insurance', 'bank_accounts', etc.
            record_id bigint(20) DEFAULT NULL, -- id in the section table
            role varchar(50) NOT NULL, -- e.g. 'advisor', 'beneficiary', 'owner', etc.
            created datetime DEFAULT CURRENT_TIMESTAMP,
            lastupdated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY client_id (client_id),
            KEY person_id (person_id),
            KEY section (section),
            KEY record_id (record_id),
            FOREIGN KEY (client_id) REFERENCES {$wpdb->prefix}epm_clients(id) ON DELETE CASCADE,
            FOREIGN KEY (person_id) REFERENCES {$wpdb->prefix}epm_persons(id) ON DELETE CASCADE
        ) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    public function populate($charset_collate) {
        // No default data for xref table
    }
}
