<?php
require_once __DIR__ . '/EPM_AbstractTable.php';
require_once __DIR__ . '/TableInterface.php';

class ContactTypesTable extends \EstatePlanningManager\Tables\EPM_AbstractTable implements \EstatePlanningManager\Tables\TableInterface {
    public function create($charset_collate) {
        ob_start();
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_contact_types';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            value varchar(100) NOT NULL,
            label varchar(255) NOT NULL,
            is_active tinyint(1) DEFAULT 1,
            sort_order int(11) DEFAULT 0,
            created datetime DEFAULT CURRENT_TIMESTAMP,
            lastupdated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY value (value)
        ) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        ob_end_clean();
    }
    public function populate($charset_collate) {
        ob_start();
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_contact_types';
        $defaults = [
            ['lawyer', 'Lawyer'],
            ['accountant', 'Accountant'],
            ['financial_advisor', 'Financial Advisor'],
            ['doctor', 'Doctor'],
            ['dentist', 'Dentist'],
            ['insurance_agent', 'Insurance Agent'],
            ['real_estate_agent', 'Real Estate Agent'],
            ['other', 'Other']
        ];
        $count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        if ($count > 0) {
            ob_end_clean();
            return;
        }
        $sort_order = 0;
        foreach ($defaults as $row) {
            $wpdb->insert($table_name, [
                'value' => $row[0],
                'label' => $row[1],
                'is_active' => 1,
                'sort_order' => $sort_order
            ], ['%s', '%s', '%d', '%d']);
            $sort_order += 10;
        }
        ob_end_clean();
    }
}
