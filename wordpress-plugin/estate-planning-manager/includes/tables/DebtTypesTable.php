<?php
require_once __DIR__ . '/TableInterface.php';

class DebtTypesTable implements TableInterface {
    public function create_table($wpdb, $charset_collate) {
        $table_name = $wpdb->prefix . 'epm_debt_types';
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            description VARCHAR(255) DEFAULT NULL
        ) $charset_collate;";
        $wpdb->query($sql);
    }
    public function populate_defaults($wpdb) {
        $table_name = $wpdb->prefix . 'epm_debt_types';
        $defaults = [
            ['name' => 'Mortgage', 'description' => 'Home loans'],
            ['name' => 'Credit Card', 'description' => 'Credit card debt'],
            ['name' => 'Auto Loan', 'description' => 'Vehicle loans'],
            ['name' => 'Student Loan', 'description' => 'Education loans'],
            ['name' => 'Personal Loan', 'description' => 'Other personal loans'],
            ['name' => 'Other', 'description' => 'Other debt types'],
        ];
        foreach ($defaults as $row) {
            $wpdb->insert($table_name, $row);
        }
    }
}
