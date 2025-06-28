<?php
require_once __DIR__ . '/TableInterface.php';

class PaymentTypesTable implements TableInterface {
    public function create_table($wpdb, $charset_collate) {
        $table_name = $wpdb->prefix . 'epm_payment_types';
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            description VARCHAR(255) DEFAULT NULL
        ) $charset_collate;";
        $wpdb->query($sql);
    }
    public function populate_defaults($wpdb) {
        $table_name = $wpdb->prefix . 'epm_payment_types';
        $defaults = [
            ['name' => 'Cash', 'description' => 'Physical currency'],
            ['name' => 'Check', 'description' => 'Paper check'],
            ['name' => 'Credit Card', 'description' => 'Credit card payment'],
            ['name' => 'Bank Transfer', 'description' => 'Electronic transfer'],
            ['name' => 'Other', 'description' => 'Other payment types'],
        ];
        foreach ($defaults as $row) {
            $wpdb->insert($table_name, $row);
        }
    }
}
