<?php

require_once __DIR__ . '/EPM_AbstractTable.php';
require_once __DIR__ . '/TableInterface.php';

class OtherContractualObligationsTable extends \EstatePlanningManager\Tables\EPM_AbstractTable implements \EstatePlanningManager\Tables\TableInterface {
    public function create($charset_collate) {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_other_contractual_obligations';
        $sql = "CREATE TABLE $table (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            client_id BIGINT UNSIGNED NOT NULL,
            suitecrm_guid VARCHAR(64) DEFAULT NULL,
            wp_record_id BIGINT UNSIGNED DEFAULT NULL,
            description TEXT,
            location_of_documents VARCHAR(255),
            notes TEXT,
            created DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public function populate($charset_collate) {
        // No default data for this table
    }
}
