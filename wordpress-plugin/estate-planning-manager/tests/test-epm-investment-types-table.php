<?php
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../estate-planning-manager.php';
require_once __DIR__ . '/../includes/tables/InvestmentTypesTable.php';

class InvestmentTypesTableTest extends TestCase {
    protected $wpdb;
    protected $table;
    protected $table_name;
    protected $charset_collate = '';

    protected function setUp(): void {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table = new InvestmentTypesTable();
        $this->table_name = $this->wpdb->prefix . 'epm_investment_types';
        $this->wpdb->query("DROP TABLE IF EXISTS {$this->table_name}");
    }

    public function test_create_table() {
        $this->table->create_table($this->wpdb, $this->charset_collate);
        $result = $this->wpdb->get_results("SHOW TABLES LIKE '{$this->table_name}'");
        $this->assertNotEmpty($result, 'Table was not created.');
    }

    public function test_populate_defaults() {
        $this->table->create_table($this->wpdb, $this->charset_collate);
        $this->table->populate_defaults($this->wpdb);
        $rows = $this->wpdb->get_results("SELECT * FROM {$this->table_name}");
        $this->assertGreaterThanOrEqual(1, count($rows), 'Defaults not inserted.');
    }
}
