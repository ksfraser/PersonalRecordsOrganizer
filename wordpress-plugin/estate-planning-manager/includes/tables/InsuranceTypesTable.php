<?php
require_once __DIR__ . '/TableInterface.php';

class InsuranceTypesTable implements TableInterface {
    public function create($charset_collate) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_insurance_types';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            value varchar(100) NOT NULL,
            label varchar(255) NOT NULL,
            category varchar(100) DEFAULT NULL,
            is_active tinyint(1) DEFAULT 1,
            sort_order int(11) DEFAULT 0,
            created datetime DEFAULT CURRENT_TIMESTAMP,
            lastupdated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY value (value),
            KEY category (category)
        ) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    public function populate($charset_collate) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_insurance_types';
        $defaults = [
            ['life', 'Life', 'life'],
            ['health', 'Health', 'health'],
            ['auto', 'Auto', 'auto'],
            ['property', 'Property', 'property'],
            ['disability', 'Disability', 'disability'],
            ['other', 'Other', null]
        ];
        $count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        if ($count > 0) return;
        $sort_order = 0;
        foreach ($defaults as $row) {
            $wpdb->insert($table_name, [
                'value' => $row[0],
                'label' => $row[1],
                'category' => $row[2],
                'is_active' => 1,
                'sort_order' => $sort_order++
            ], ['%s', '%s', '%s', '%d', '%d']);
        }
    }
}
