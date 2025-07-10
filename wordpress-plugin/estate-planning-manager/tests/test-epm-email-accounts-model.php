<?php
use PHPUnit\Framework\TestCase;
use EstatePlanningManager\Models\EmailAccountsModel;

if (!class_exists('wpdb')) {
    // Mock wpdb for lint/test
    class wpdb {
        public $prefix = 'wp_';
        public function get_results($query, $output = null) { throw new Exception('wpdb::get_results called in test'); }
        public function prepare($query, ...$args) { return $query; }
        public function get_charset_collate() { return 'utf8mb4_unicode_ci'; }
    }
}
if (!function_exists('dbDelta')) {
    function dbDelta($sql) { throw new Exception('dbDelta called in test'); }
}
if (!defined('ABSPATH')) define('ABSPATH', '/');

class EmailAccountsModelTest extends TestCase {
    public function testGetTableName() {
        $model = new EmailAccountsModel();
        $this->assertStringContainsString('epm_email_accounts', $model->getTableName());
    }
    public function testGetFieldDefinitions() {
        $fields = EmailAccountsModel::getFieldDefinitions();
        $this->assertArrayHasKey('email_address', $fields);
        $this->assertEquals('VARCHAR(255)', $fields['email_address']['db_type']);
    }
    public function testCreateTableMethodExists() {
        $this->assertTrue(method_exists(EmailAccountsModel::class, 'createTable'));
    }
}
