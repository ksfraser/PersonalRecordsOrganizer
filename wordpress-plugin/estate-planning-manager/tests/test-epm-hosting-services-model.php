<?php
use PHPUnit\Framework\TestCase;
use EstatePlanningManager\Models\HostingServicesModel;

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

class HostingServicesModelTest extends TestCase {
    public function testGetTableName() {
        $model = new HostingServicesModel();
        $this->assertStringContainsString('epm_hosting_services', $model->getTableName());
    }
    public function testGetFieldDefinitions() {
        $fields = HostingServicesModel::getFieldDefinitions();
        $this->assertArrayHasKey('service_name', $fields);
        $this->assertEquals('VARCHAR(255)', $fields['service_name']['db_type']);
    }
    public function testCreateTableMethodExists() {
        $this->assertTrue(method_exists(HostingServicesModel::class, 'createTable'));
    }
}
