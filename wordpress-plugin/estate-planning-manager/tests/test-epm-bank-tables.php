<?php
/**
 * Tests for EPM_BankNamesTable and EPM_BankLocationTypesTable
 */

class Test_EPM_Bank_Tables extends EPM_Test_Case {
    /**
     * Test bank names table exists
     */
    public function test_bank_names_table_exists() {
        $this->assertTableExists('epm_bank_names');
    }
    /**
     * Test bank location types table exists
     */
    public function test_bank_location_types_table_exists() {
        $this->assertTableExists('epm_bank_location_types');
    }
    /**
     * Test insert and retrieve bank name
     */
    public function test_insert_and_retrieve_bank_name() {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_bank_names';
        $wpdb->insert($table, [
            'name' => 'UnitTest Bank',
            'location' => 'Canada',
            'is_active' => 1,
            'sort_order' => 10
        ]);
        $row = $wpdb->get_row("SELECT * FROM $table WHERE name = 'UnitTest Bank'");
        $this->assertEquals('UnitTest Bank', $row->name);
        $this->assertEquals('Canada', $row->location);
    }
    /**
     * Test insert and retrieve bank location type
     */
    public function test_insert_and_retrieve_bank_location_type() {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_bank_location_types';
        $wpdb->insert($table, [
            'value' => 'TestRegion',
            'label' => 'Test Region',
            'is_active' => 1,
            'sort_order' => 99
        ]);
        $row = $wpdb->get_row("SELECT * FROM $table WHERE value = 'TestRegion'");
        $this->assertEquals('TestRegion', $row->value);
        $this->assertEquals('Test Region', $row->label);
    }
}
