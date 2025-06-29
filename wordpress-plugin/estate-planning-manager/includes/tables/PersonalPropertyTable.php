<?php
require_once __DIR__ . '/TableInterface.php';
require_once __DIR__ . '/AutoModelTable.php';

class PersonalPropertyTable implements TableInterface {
    public function create($charset_collate) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_personal_property';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            client_id bigint(20) NOT NULL,
            suitecrm_guid varchar(36) DEFAULT NULL,
            wp_record_id bigint(20) DEFAULT NULL,
            property_type varchar(100) DEFAULT NULL,
            item_type varchar(100) DEFAULT NULL,
            vehicle_model varchar(255) DEFAULT NULL,
            own_or_lease varchar(20) DEFAULT NULL,
            legal_document text DEFAULT NULL,
            registration_location text DEFAULT NULL,
            insurance_policy_location text DEFAULT NULL,
            bill_of_sale_location text DEFAULT NULL,
            location text DEFAULT NULL,
            safe_deposit_box_location varchar(255) DEFAULT NULL,
            box_access_names text DEFAULT NULL,
            keys_location text DEFAULT NULL,
            contents_list_location text DEFAULT NULL,
            owner_person_id bigint(20) DEFAULT NULL,
            auto_model_id bigint(20) DEFAULT NULL,
            created datetime DEFAULT CURRENT_TIMESTAMP,
            lastupdated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY client_id (client_id),
            KEY suitecrm_guid (suitecrm_guid),
            KEY wp_record_id (wp_record_id),
            KEY property_type (property_type),
            KEY owner_person_id (owner_person_id),
            KEY auto_model_id (auto_model_id),
            FOREIGN KEY (client_id) REFERENCES {$wpdb->prefix}epm_clients(id) ON DELETE CASCADE,
            FOREIGN KEY (owner_person_id) REFERENCES {$wpdb->prefix}epm_persons(id) ON DELETE SET NULL,
            FOREIGN KEY (auto_model_id) REFERENCES {$wpdb->prefix}epm_auto_models(id) ON DELETE SET NULL
        ) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    public function populate($charset_collate) {
        // No default data for user data tables
    }
}
