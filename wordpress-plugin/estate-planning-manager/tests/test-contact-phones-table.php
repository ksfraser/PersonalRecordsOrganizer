<?php
use PHPUnit\Framework\TestCase;

class ContactPhonesTableTest extends TestCase {
    public function test_schema_has_type_id_column() {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_contact_phones';
        $columns = $wpdb->get_results("SHOW COLUMNS FROM $table", ARRAY_A);
        $column_names = array_column($columns, 'Field');
        $this->assertContains('type_id', $column_names, 'type_id column missing in epm_contact_phones');
    }
    public function test_insert_and_retrieve_phone_with_type_id() {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_contact_phones';
        $data = [
            'contact_id' => 1,
            'phone' => '555-1234',
            'type_id' => 2,
            'is_primary' => 1,
            'created' => current_time('mysql'),
            'lastupdated' => current_time('mysql')
        ];
        $wpdb->insert($table, $data);
        $id = $wpdb->insert_id;
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id), ARRAY_A);
        $this->assertEquals('555-1234', $row['phone']);
        $this->assertEquals(2, $row['type_id']);
        $this->assertEquals(1, $row['is_primary']);
    }
}
