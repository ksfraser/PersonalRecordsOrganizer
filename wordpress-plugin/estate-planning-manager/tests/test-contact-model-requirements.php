<?php
/**
 * Test: ContactModel requirements and DB table creation
 *
 * @covers \EstatePlanningManager\Models\ContactModel
 */
use PHPUnit\Framework\TestCase;

class ContactModelRequirementsTest extends TestCase {
    public function testFieldDefinitionsArePresent() {
        $fields = \EstatePlanningManager\Models\ContactModel::getFieldDefinitions();
        $this->assertIsArray($fields);
        $this->assertNotEmpty($fields);
        $fieldNames = array_column($fields, 'name');
        $this->assertContains('full_name', $fieldNames);
        $this->assertContains('email', $fieldNames);
        $this->assertContains('phone', $fieldNames);
        $this->assertContains('address', $fieldNames);
    }

    public function testGetTableNameReturnsExpected() {
        $model = new \EstatePlanningManager\Models\ContactModel();
        $table = $model->getTableName();
        $this->assertStringContainsString('epm_contacts', $table);
    }

    public function testNoCreateTableMethod() {
        $model = new \EstatePlanningManager\Models\ContactModel();
        $this->assertFalse(method_exists($model, 'createTable'), 'createTable should not exist in ContactModel');
    }
}
