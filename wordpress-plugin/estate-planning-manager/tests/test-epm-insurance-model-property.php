<?php
use PHPUnit\Framework\TestCase;
require_once dirname(__DIR__) . '/estate-planning-manager/public/models/InsuranceModel.php';

class EPM_InsuranceModel_PropertyInsurance_Test extends TestCase {
    public function test_validateData_property_auto() {
        $data = [
            'client_id' => 1,
            'property_type' => 'auto',
            'property_id' => 5,
            'insurance_category' => 'Auto',
            'insurance_type' => 'Comprehensive',
            'insurance_company' => 'Acme',
            'policy_number' => 'A123',
        ];
        list($is_valid, $errors, $sanitized) = \EstatePlanningManager\Models\InsuranceModel::validateData($data);
        $this->assertTrue($is_valid);
        $this->assertEmpty($errors);
        $this->assertEquals('auto', $sanitized['property_type']);
        $this->assertEquals(5, $sanitized['property_id']);
    }

    public function test_validateData_property_house() {
        $data = [
            'client_id' => 1,
            'property_type' => 'house',
            'property_id' => 7,
            'insurance_category' => 'Home',
            'insurance_type' => 'Fire',
            'insurance_company' => 'HomeSafe',
            'policy_number' => 'H456',
        ];
        list($is_valid, $errors, $sanitized) = \EstatePlanningManager\Models\InsuranceModel::validateData($data);
        $this->assertTrue($is_valid);
        $this->assertEmpty($errors);
        $this->assertEquals('house', $sanitized['property_type']);
        $this->assertEquals(7, $sanitized['property_id']);
    }

    public function test_validateData_property_none() {
        $data = [
            'client_id' => 1,
            'property_type' => '',
            'insurance_category' => 'Other',
            'insurance_type' => 'Other',
            'insurance_company' => 'Other',
            'policy_number' => 'O789',
        ];
        list($is_valid, $errors, $sanitized) = \EstatePlanningManager\Models\InsuranceModel::validateData($data);
        $this->assertTrue($is_valid);
        $this->assertEmpty($errors);
        $this->assertNull($sanitized['property_id']);
    }

    public function test_field_definitions_include_property_fields() {
        $defs = \EstatePlanningManager\Models\InsuranceModel::getFieldDefinitions();
        $this->assertArrayHasKey('property_type', $defs);
        $this->assertArrayHasKey('property_id', $defs);
        $this->assertEquals('select', $defs['property_type']['type']);
        $this->assertEquals('select', $defs['property_id']['type']);
    }
}
