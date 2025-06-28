<?php
require_once __DIR__ . '/TableInterface.php';

class EmploymentStatusTable implements TableInterface {
    public function create_table($wpdb, $charset_collate) {
        $table_name = $wpdb->prefix . 'epm_employment_status';
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            description VARCHAR(255) DEFAULT NULL
        ) $charset_collate;";
        $wpdb->query($sql);
    }
    public function populate_defaults($wpdb) {
        $table_name = $wpdb->prefix . 'epm_employment_status';
        $defaults = [
            ['name' => 'Employed', 'description' => 'Currently employed'],
            ['name' => 'Unemployed', 'description' => 'Not employed'],
            ['name' => 'Retired', 'description' => 'Retired from work'],
            ['name' => 'Student', 'description' => 'Currently a student'],
            ['name' => 'Other', 'description' => 'Other status'],
        ];
        foreach ($defaults as $row) {
            $wpdb->insert($table_name, $row);
        }
    }
}
