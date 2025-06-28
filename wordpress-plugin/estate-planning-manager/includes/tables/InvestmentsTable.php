<?php
require_once __DIR__ . '/TableInterface.php';

class InvestmentsTable implements TableInterface {
    public function create($charset_collate) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_investments';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            client_id bigint(20) NOT NULL,
            suitecrm_guid varchar(36) DEFAULT NULL,
            wp_record_id bigint(20) DEFAULT NULL,
            investment_type varchar(100) DEFAULT NULL,
            financial_company varchar(255) DEFAULT NULL,
            account_type varchar(100) DEFAULT NULL,
            account_number varchar(255) DEFAULT NULL,
            address text DEFAULT NULL,
            phone varchar(50) DEFAULT NULL,
            email varchar(255) DEFAULT NULL,
            information_location text DEFAULT NULL,
            gift_or_inheritance varchar(10) DEFAULT NULL,
            collateral varchar(10) DEFAULT NULL,
            advisor varchar(255) DEFAULT NULL,
            advisor_email varchar(255) DEFAULT NULL,
            lender varchar(255) DEFAULT NULL,
            lender_address text DEFAULT NULL,
            lender_phone varchar(50) DEFAULT NULL,
            lender_email varchar(255) DEFAULT NULL,
            company_group varchar(255) DEFAULT NULL,
            auto_invest varchar(10) DEFAULT NULL,
            frequency varchar(50) DEFAULT NULL,
            account_paid_from varchar(255) DEFAULT NULL,
            beneficiary varchar(255) DEFAULT NULL,
            beneficiary_phone varchar(50) DEFAULT NULL,
            beneficiary_email varchar(255) DEFAULT NULL,
            created datetime DEFAULT CURRENT_TIMESTAMP,
            lastupdated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY client_id (client_id),
            KEY suitecrm_guid (suitecrm_guid),
            KEY wp_record_id (wp_record_id),
            KEY investment_type (investment_type),
            FOREIGN KEY (client_id) REFERENCES {$wpdb->prefix}epm_clients(id) ON DELETE CASCADE
        ) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    public function populate($charset_collate) {
        // No default data for user data tables
    }
}
