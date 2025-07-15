<?php
/**
 * Test: InstituteModel requirements and DB table creation
 *
 * @covers \EstatePlanningManager\Models\InstituteModel
 */
use PHPUnit\Framework\TestCase;

class InstituteModelRequirementsTest extends TestCase {
    public function testFieldDefinitionsArePresent() {
        $fields = \EstatePlanningManager\Models\InstituteModel::getFieldDefinitions();
        $this->assertIsArray($fields);
        $this->assertNotEmpty($fields);
        $fieldNames = array_column($fields, 'name');
        $this->assertContains('name', $fieldNames);
        $this->assertContains('email', $fieldNames);
        $this->assertContains('phone', $fieldNames);
        $this->assertContains('address', $fieldNames);
        $this->assertContains('account_number', $fieldNames);
        $this->assertContains('branch', $fieldNames);
    }

    public function testGetTableNameReturnsExpected() {
        $model = new \EstatePlanningManager\Models\InstituteModel();
        $table = $model->getTableName();
        $this->assertStringContainsString('epm_institutes', $table);
    }

    public function testNoCreateTableMethod() {
        $model = new \EstatePlanningManager\Models\InstituteModel();
        $this->assertFalse(method_exists($model, 'createTable'), 'createTable should not exist in InstituteModel');
    }
}
