<?php
use PHPUnit\Framework\TestCase;
require_once dirname(__DIR__) . '/estate-planning-manager/public/models/InsuranceModel.php';

class EPM_InsuranceModel_GroupInsurance_Test extends TestCase {
    public function test_validateData_group_insurance_false() {
        $data = [
            'client_id' => 1,
            'insurance_category' => 'Life',
            'insurance_type' => 'Term',
            'insurance_company' => 'Acme',
            'policy_number' => '123',
            'is_group_insurance' => false,
            'group_insurance_sponsor_org_id' => 99 // should be ignored
        ];
        list($is_valid, $errors, $sanitized) = \EstatePlanningManager\Models\InsuranceModel::validateData($data);
        $this->assertTrue($is_valid);
        $this->assertEmpty($errors);
        $this->assertEquals(0, $sanitized['is_group_insurance']);
        $this->assertNull($sanitized['group_insurance_sponsor_org_id']);
    }

    public function test_validateData_group_insurance_true_with_sponsor() {
        $data = [
            'client_id' => 1,
            'insurance_category' => 'Life',
            'insurance_type' => 'Term',
            'insurance_company' => 'Acme',
            'policy_number' => '123',
            'is_group_insurance' => true,
            'group_insurance_sponsor_org_id' => 42
        ];
        list($is_valid, $errors, $sanitized) = \EstatePlanningManager\Models\InsuranceModel::validateData($data);
        $this->assertTrue($is_valid);
        $this->assertEmpty($errors);
        $this->assertEquals(1, $sanitized['is_group_insurance']);
        $this->assertEquals(42, $sanitized['group_insurance_sponsor_org_id']);
    }

    public function test_validateData_group_insurance_true_without_sponsor() {
        $data = [
            'client_id' => 1,
            'insurance_category' => 'Life',
            'insurance_type' => 'Term',
            'insurance_company' => 'Acme',
            'policy_number' => '123',
            'is_group_insurance' => true
            // sponsor missing
        ];
        list($is_valid, $errors, $sanitized) = \EstatePlanningManager\Models\InsuranceModel::validateData($data);
        $this->assertTrue($is_valid); // sponsor is optional
        $this->assertEmpty($errors);
        $this->assertEquals(1, $sanitized['is_group_insurance']);
        $this->assertNull($sanitized['group_insurance_sponsor_org_id']);
    }

    public function test_field_definitions_include_group_fields() {
        $defs = \EstatePlanningManager\Models\InsuranceModel::getFieldDefinitions();
        $this->assertArrayHasKey('is_group_insurance', $defs);
        $this->assertArrayHasKey('group_insurance_sponsor_org_id', $defs);
        $this->assertEquals('checkbox', $defs['is_group_insurance']['type']);
        $this->assertEquals('select', $defs['group_insurance_sponsor_org_id']['type']);
    }
}
