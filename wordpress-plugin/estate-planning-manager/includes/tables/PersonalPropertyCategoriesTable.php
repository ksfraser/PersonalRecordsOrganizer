<?php
require_once __DIR__ . '/TableInterface.php';

class PersonalPropertyCategoriesTable implements TableInterface {
    public function create_table($wpdb, $charset_collate) {
        $table_name = $wpdb->prefix . 'epm_personal_property_categories';
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            description VARCHAR(255) DEFAULT NULL
        ) $charset_collate;";
        $wpdb->query($sql);
    }
    public function populate_defaults($wpdb) {
        $table_name = $wpdb->prefix . 'epm_personal_property_categories';
        $defaults = [
            ['name' => 'Jewelry', 'description' => 'Jewelry and valuables'],
            ['name' => 'Art', 'description' => 'Artwork and collectibles'],
            ['name' => 'Vehicles', 'description' => 'Cars, boats, etc.'],
            ['name' => 'Electronics', 'description' => 'Personal electronics'],
            ['name' => 'Other', 'description' => 'Other personal property'],
        ];
        foreach ($defaults as $row) {
            $wpdb->insert($table_name, $row);
        }
    }
}
