<?php
require_once __DIR__ . '/TableInterface.php';

class RealEstateTable implements TableInterface {
    public function create($charset_collate) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_real_estate';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            client_id bigint(20) NOT NULL,
            suitecrm_guid varchar(36) DEFAULT NULL,
            wp_record_id bigint(20) DEFAULT NULL,
            property_type varchar(100) DEFAULT NULL,
            title_held_by varchar(255) DEFAULT NULL,
            address text DEFAULT NULL,
            has_mortgage varchar(10) DEFAULT NULL,
            mortgage_held_by varchar(255) DEFAULT NULL,
            lender_address text DEFAULT NULL,
            lender_phone varchar(50) DEFAULT NULL,
            lender_email varchar(255) DEFAULT NULL,
            mortgage_location text DEFAULT NULL,
            deed_location text DEFAULT NULL,
            property_insurance_docs text DEFAULT NULL,
            land_surveys text DEFAULT NULL,
            tax_receipts text DEFAULT NULL,
            leases text DEFAULT NULL,
            accounting_docs text DEFAULT NULL,
            lender_person_id bigint(20) DEFAULT NULL,
            created datetime DEFAULT CURRENT_TIMESTAMP,
            lastupdated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY client_id (client_id),
            KEY suitecrm_guid (suitecrm_guid),
            KEY wp_record_id (wp_record_id),
            KEY lender_person_id (lender_person_id),
            FOREIGN KEY (client_id) REFERENCES {$wpdb->prefix}epm_clients(id) ON DELETE CASCADE,
            FOREIGN KEY (lender_person_id) REFERENCES {$wpdb->prefix}epm_persons(id) ON DELETE SET NULL
        ) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    public function populate($charset_collate) {
        // No default data for user data tables
    }
}
