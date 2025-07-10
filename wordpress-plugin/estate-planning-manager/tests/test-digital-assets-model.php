<?php
/**
 * Unit test for DigitalAssetsModel
 *
 * @covers \EstatePlanningManager\Models\DigitalAssetsModel
 * @phpdoc
 * @uml DigitalAssetsModelTest --|> EPM_Test_Case
 */
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../public/models/DigitalAssetsModel.php';

class DigitalAssetsModelTest extends EPM_Test_Case {
    public function setUp(): void {
        parent::setUp();
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        \EstatePlanningManager\Models\DigitalAssetsModel::createTable($charset_collate);
    }

    public function testTableSchemaMatchesFieldDefinitions() {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_digital_assets';
        $fields = \EstatePlanningManager\Models\DigitalAssetsModel::getFieldDefinitions();
        $columns = $wpdb->get_results("SHOW COLUMNS FROM $table", ARRAY_A);
        $column_names = array_column($columns, 'Field');
        foreach (array_keys($fields) as $field) {
            $this->assertContains($field, $column_names, "Column $field missing in table");
        }
    }

    public function testInsertAndRetrieveDigitalAsset() {
        $model = new \EstatePlanningManager\Models\DigitalAssetsModel();
        $client_id = 1;
        $data = [
            'client_id' => $client_id,
            'company' => 'Test Company',
            'user_id' => 'testuser',
            'password' => 'secret',
            'account_number' => '12345',
            'url' => 'https://example.com',
            'email' => 'test@example.com',
            'phone' => '555-1234',
            'storage_type' => 'cloud',
            'notes' => 'Test notes',
        ];
        global $wpdb;
        $wpdb->insert($wpdb->prefix . 'epm_digital_assets', $data);
        $assets = $model->getAllRecordsForClient($client_id);
        $this->assertNotEmpty($assets);
        $this->assertEquals('Test Company', $assets[0]['company']);
        $this->assertEquals('cloud', $assets[0]['storage_type']);
    }
}
