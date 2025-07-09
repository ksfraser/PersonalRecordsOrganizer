<?php
use PHPUnit\Framework\TestCase;

class PhoneLineTypesTableTest extends TestCase {
    public function test_schema_has_value_and_label_columns() {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_phone_line_types';
        $columns = $wpdb->get_results("SHOW COLUMNS FROM $table", defined('ARRAY_A') ? ARRAY_A : 'ARRAY_A');
        $column_names = array_column($columns, 'Field');
        $this->assertContains('value', $column_names);
        $this->assertContains('label', $column_names);
        $this->assertContains('is_active', $column_names);
    }
    public function test_default_types_exist() {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_phone_line_types';
        $types = $wpdb->get_col("SELECT value FROM $table");
        $this->assertContains('home', $types);
        $this->assertContains('cell', $types);
        $this->assertContains('work', $types);
        $this->assertContains('fax', $types);
        $this->assertContains('business', $types);
        $this->assertContains('other', $types);
    }
}
