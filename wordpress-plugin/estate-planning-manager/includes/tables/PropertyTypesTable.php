<?php
require_once __DIR__ . '/TableInterface.php';

class PropertyTypesTable implements TableInterface {
    public function create_table($wpdb, $charset_collate) {
        $table_name = $wpdb->prefix . 'epm_property_types';
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            description VARCHAR(255) DEFAULT NULL
        ) $charset_collate;";
        $wpdb->query($sql);
    }
    public function populate_defaults($wpdb) {
        $table_name = $wpdb->prefix . 'epm_property_types';
        $defaults = [
            ['name' => 'Residential', 'description' => 'Homes, condos, apartments'],
            ['name' => 'Commercial', 'description' => 'Offices, retail, industrial'],
            ['name' => 'Land', 'description' => 'Vacant land, lots'],
            ['name' => 'Other', 'description' => 'Other property types'],
        ];
        foreach ($defaults as $row) {
            $wpdb->insert($table_name, $row);
        }
    }
}
