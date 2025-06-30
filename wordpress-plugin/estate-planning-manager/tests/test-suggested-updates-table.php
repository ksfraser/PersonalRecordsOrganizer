<?php
use PHPUnit\Framework\TestCase;

class SuggestedUpdatesTableTest extends TestCase {
    public function test_table_created() {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_suggested_updates';
        (new SuggestedUpdatesTable())->create($wpdb->get_charset_collate());
        $exists = $wpdb->get_var($wpdb->prepare(
            "SHOW TABLES LIKE %s", $table
        ));
        $this->assertEquals($table, $exists);
    }
}
