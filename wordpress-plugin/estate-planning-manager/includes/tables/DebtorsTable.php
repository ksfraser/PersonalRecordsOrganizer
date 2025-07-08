<?php
require_once __DIR__ . '/TableInterface.php';
class DebtorsTable extends EPM_AbstractTable implements TableInterface {
    public function create($charset_collate) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_debtors';
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            client_id bigint(20) NOT NULL,
            suitecrm_guid varchar(36) DEFAULT NULL,
            wp_record_id bigint(20) DEFAULT NULL,
            person_org varchar(20) DEFAULT NULL,
            contact_id bigint(20) DEFAULT NULL,
            scheduled_payment_id bigint(20) DEFAULT NULL,
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
            KEY person_org (person_org),
            KEY contact_id (contact_id),
            KEY scheduled_payment_id (scheduled_payment_id)
        ) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}
