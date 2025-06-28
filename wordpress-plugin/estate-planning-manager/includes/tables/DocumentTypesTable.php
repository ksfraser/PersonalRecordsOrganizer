<?php
require_once __DIR__ . '/TableInterface.php';

class DocumentTypesTable implements TableInterface {
    public function create_table($wpdb, $charset_collate) {
        $table_name = $wpdb->prefix . 'epm_document_types';
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            description VARCHAR(255) DEFAULT NULL
        ) $charset_collate;";
        $wpdb->query($sql);
    }
    public function populate_defaults($wpdb) {
        $table_name = $wpdb->prefix . 'epm_document_types';
        $defaults = [
            ['name' => 'Will', 'description' => 'Last will and testament'],
            ['name' => 'Trust', 'description' => 'Trust documents'],
            ['name' => 'Power of Attorney', 'description' => 'POA documents'],
            ['name' => 'Insurance Policy', 'description' => 'Insurance documents'],
            ['name' => 'Deed', 'description' => 'Property deeds'],
            ['name' => 'Other', 'description' => 'Other document types'],
        ];
        foreach ($defaults as $row) {
            $wpdb->insert($table_name, $row);
        }
    }
}
