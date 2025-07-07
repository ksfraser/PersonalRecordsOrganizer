<?php
use PHPUnit\Framework\TestCase;

class Test_EPM_OtherContractualObligationsTable extends TestCase {
    public function test_table_creation() {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_other_contractual_obligations';
        $tableClass = new OtherContractualObligationsTable();
        $tableClass->create();
        $result = $wpdb->get_results("SHOW TABLES LIKE '$table'");
        $this->assertNotEmpty($result, 'Table should be created');
    }

    public function test_insert_and_retrieve() {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_other_contractual_obligations';
        $data = [
            'client_id' => 1,
            'suitecrm_guid' => 'test-guid',
            'wp_record_id' => 123,
            'description' => 'Test contract',
            'location_of_documents' => 'Safe',
            'notes' => 'Some notes',
        ];
        $wpdb->insert($table, $data);
        $id = $wpdb->insert_id;
        $row = $wpdb->get_row("SELECT * FROM $table WHERE id = $id", ARRAY_A);
        $this->assertEquals('Test contract', $row['description']);
        $this->assertEquals('Safe', $row['location_of_documents']);
        $this->assertEquals('Some notes', $row['notes']);
    }
}
