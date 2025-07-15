<?php
/**
 * Test: PersonModel requirements and DB table creation
 *
 * @covers \EstatePlanningManager\Models\PersonModel
 */
use PHPUnit\Framework\TestCase;

class PersonModelRequirementsTest extends TestCase {
    public function testFieldDefinitionsArePresent() {
        $fields = \EstatePlanningManager\Models\PersonModel::getFieldDefinitions();
        $this->assertIsArray($fields);
        $this->assertNotEmpty($fields);
        $fieldNames = array_column($fields, 'name');
        $this->assertContains('full_name', $fieldNames);
        $this->assertContains('email', $fieldNames);
        $this->assertContains('phone', $fieldNames);
        $this->assertContains('address', $fieldNames);
    }

    public function testGetTableNameReturnsExpected() {
        $model = new \EstatePlanningManager\Models\PersonModel();
        $table = $model->getTableName();
        $this->assertStringContainsString('epm_persons', $table);
    }

    public function testNoCreateTableMethod() {
        $model = new \EstatePlanningManager\Models\PersonModel();
        $this->assertFalse(method_exists($model, 'createTable'), 'createTable should not exist in PersonModel');
    }
}
