<?php
use PHPUnit\Framework\TestCase;

class KeyContactsTableTest extends TestCase {
    public function test_table_created() {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_key_contacts';
        (new KeyContactsTable())->create($wpdb->get_charset_collate());
        $exists = $wpdb->get_var($wpdb->prepare(
            "SHOW TABLES LIKE %s", $table
        ));
        $this->assertEquals($table, $exists);
    }
}
