<?php
require_once __DIR__ . '/TableInterface.php';

class DigitalAssetTypesTable implements TableInterface {
    public function create_table($wpdb, $charset_collate) {
        $table_name = $wpdb->prefix . 'epm_digital_asset_types';
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            description VARCHAR(255) DEFAULT NULL
        ) $charset_collate;";
        $wpdb->query($sql);
    }
    public function populate_defaults($wpdb) {
        $table_name = $wpdb->prefix . 'epm_digital_asset_types';
        $defaults = [
            ['name' => 'Email', 'description' => 'Email accounts'],
            ['name' => 'Social Media', 'description' => 'Social media accounts'],
            ['name' => 'Cryptocurrency', 'description' => 'Digital currencies'],
            ['name' => 'Cloud Storage', 'description' => 'Online storage accounts'],
            ['name' => 'Other', 'description' => 'Other digital assets'],
        ];
        foreach ($defaults as $row) {
            $wpdb->insert($table_name, $row);
        }
    }
}
