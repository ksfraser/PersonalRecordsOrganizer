<?php
require_once __DIR__ . '/TableInterface.php';

class DebtorsCreditorsTable implements TableInterface {
    public function create($charset_collate) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_debtors_creditors';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            client_id bigint(20) NOT NULL,
            suitecrm_guid varchar(36) DEFAULT NULL,
            wp_record_id bigint(20) DEFAULT NULL,
            relationship_type varchar(20) DEFAULT NULL,
            debt_type varchar(100) DEFAULT NULL,
            creditor_debtor varchar(255) DEFAULT NULL,
            contact varchar(255) DEFAULT NULL,
            address text DEFAULT NULL,
            phone varchar(50) DEFAULT NULL,
            email varchar(255) DEFAULT NULL,
            account_number varchar(255) DEFAULT NULL,
            amount decimal(10,2) DEFAULT NULL,
            date_of_loan date DEFAULT NULL,
            document_location text DEFAULT NULL,
            description text DEFAULT NULL,
            created datetime DEFAULT CURRENT_TIMESTAMP,
            lastupdated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY client_id (client_id),
            KEY suitecrm_guid (suitecrm_guid),
            KEY wp_record_id (wp_record_id),
            KEY relationship_type (relationship_type),
            --FOREIGN KEY (client_id) REFERENCES {$wpdb->prefix}epm_clients(id) ON DELETE CASCADE
        ) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    public function populate($charset_collate) {
        // No default data for user data tables
    }
}
