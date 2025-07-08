<?php
require_once __DIR__ . '/TableInterface.php';
require_once __DIR__ . '/AutoModelTable.php';

class AutoPropertyTable extends EPM_AbstractTable implements TableInterface {
    public function create($charset_collate) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_auto_property';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            client_id bigint(20) NOT NULL,
            suitecrm_guid varchar(36) DEFAULT NULL,
            wp_record_id bigint(20) DEFAULT NULL,
            auto_model_id bigint(20) DEFAULT NULL,
            type varchar(100) NOT NULL,
            vehicle varchar(255) NOT NULL,
            model varchar(100) DEFAULT NULL,
            own_or_lease varchar(100) DEFAULT NULL,
            legal_document_location varchar(255) DEFAULT NULL,
            registration_location varchar(255) DEFAULT NULL,
            insurance_policy_location varchar(255) DEFAULT NULL,
            bill_of_sale_location varchar(255) DEFAULT NULL,
            owner_person_id bigint(20) DEFAULT NULL,
            created datetime DEFAULT CURRENT_TIMESTAMP,
            lastupdated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY client_id (client_id),
            KEY suitecrm_guid (suitecrm_guid),
            KEY wp_record_id (wp_record_id),
            KEY auto_model_id (auto_model_id),
            KEY owner_person_id (owner_person_id)
        ) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    public function populate($charset_collate) {
        // No default data for user data tables
    }
}
