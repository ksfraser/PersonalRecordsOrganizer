<?php
use PHPUnit\Framework\TestCase;

class ContactTypesTableTest extends TestCase {
    public function test_schema_has_is_active_and_sort_order() {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_contact_types';
        $columns = $wpdb->get_results("SHOW COLUMNS FROM $table", defined('ARRAY_A') ? ARRAY_A : 'ARRAY_A');
        $column_names = array_column($columns, 'Field');
        $this->assertContains('is_active', $column_names);
        $this->assertContains('sort_order', $column_names);
    }
    public function test_default_types_are_active() {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_contact_types';
        $active = $wpdb->get_var("SELECT COUNT(*) FROM $table WHERE is_active = 1");
        $total = $wpdb->get_var("SELECT COUNT(*) FROM $table");
        $this->assertEquals($total, $active, 'All default contact types should be active');
    }
}
