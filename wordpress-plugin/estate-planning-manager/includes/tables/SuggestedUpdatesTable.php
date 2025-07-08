<?php
require_once __DIR__ . '/TableInterface.php';

class SuggestedUpdatesTable extends EPM_AbstractTable implements TableInterface {
    public function create($charset_collate) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_suggested_updates';
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            client_id bigint(20) NOT NULL,
            field varchar(100) DEFAULT NULL,
            old_value text DEFAULT NULL,
            new_value text DEFAULT NULL,
            notes text DEFAULT NULL,
            status varchar(50) DEFAULT 'pending',
            section varchar(100) DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY client_id (client_id),
            KEY status (status)
        ) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    public function populate($charset_collate) {
        // No default data for suggested updates
    }
}
