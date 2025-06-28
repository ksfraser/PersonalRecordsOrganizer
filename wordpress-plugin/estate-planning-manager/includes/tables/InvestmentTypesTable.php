<?php
require_once __DIR__ . '/TableInterface.php';

class InvestmentTypesTable implements TableInterface {
    public function create_table($wpdb, $charset_collate) {
        $table_name = $wpdb->prefix . 'epm_investment_types';
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            description VARCHAR(255) DEFAULT NULL
        ) $charset_collate;";
        $wpdb->query($sql);
    }
    public function populate_defaults($wpdb) {
        $table_name = $wpdb->prefix . 'epm_investment_types';
        $defaults = [
            ['name' => 'Stocks', 'description' => 'Equity investments'],
            ['name' => 'Bonds', 'description' => 'Fixed income investments'],
            ['name' => 'Mutual Funds', 'description' => 'Pooled investments'],
            ['name' => 'Real Estate', 'description' => 'Property investments'],
            ['name' => 'Other', 'description' => 'Other investment types'],
        ];
        foreach ($defaults as $row) {
            $wpdb->insert($table_name, $row);
        }
    }
}
