<?php
require_once __DIR__ . '/TableInterface.php';

class InsuranceTable implements TableInterface {
    public function create($charset_collate) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_insurance';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            client_id bigint(20) NOT NULL,
            suitecrm_guid varchar(36) DEFAULT NULL,
            wp_record_id bigint(20) DEFAULT NULL,
            insurance_category varchar(100) DEFAULT NULL,
            insurance_type varchar(100) DEFAULT NULL,
            advisor varchar(255) DEFAULT NULL,
            insurance_company varchar(255) DEFAULT NULL,
            policy_number varchar(255) DEFAULT NULL,
            address text DEFAULT NULL,
            phone varchar(50) DEFAULT NULL,
            email varchar(255) DEFAULT NULL,
            advisor_phone varchar(50) DEFAULT NULL,
            advisor_email varchar(255) DEFAULT NULL,
            policy_location text DEFAULT NULL,
            beneficiary varchar(255) DEFAULT NULL,
            beneficiary_phone varchar(50) DEFAULT NULL,
            beneficiary_email varchar(255) DEFAULT NULL,
            insured_person varchar(255) DEFAULT NULL,
            policy_owner varchar(255) DEFAULT NULL,
            owner_phone varchar(50) DEFAULT NULL,
            owner_email varchar(255) DEFAULT NULL,
            will_someone_become_owner varchar(10) DEFAULT NULL,
            company_association varchar(255) DEFAULT NULL,
            created datetime DEFAULT CURRENT_TIMESTAMP,
            lastupdated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY client_id (client_id),
            KEY suitecrm_guid (suitecrm_guid),
            KEY wp_record_id (wp_record_id),
            KEY insurance_category (insurance_category),
            KEY insurance_type (insurance_type),
            FOREIGN KEY (client_id) REFERENCES {$wpdb->prefix}epm_clients(id) ON DELETE CASCADE
        ) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    public function populate($charset_collate) {
        // No default data for user data tables
    }
}
