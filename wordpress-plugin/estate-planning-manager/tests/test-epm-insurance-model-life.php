<?php
use PHPUnit\Framework\TestCase;
require_once dirname(__DIR__) . '/estate-planning-manager/public/models/InsuranceModel.php';

class EPM_InsuranceModel_LifeInsurance_Test extends TestCase {
    public function test_validateData_life_type_valid() {
        if (!function_exists('epm_get_life_insurance_type_options')) {
            function epm_get_life_insurance_type_options() {
                return [
                    'whole_life' => 'Whole Life',
                    'universal' => 'Universal',
                    'term' => 'Term',
                    'critical_illness' => 'Critical Illness',
                ];
            }
        }
        $data = [
            'client_id' => 1,
            'property_type' => 'life',
            'insurance_type' => 'term',
            'insurance_category' => 'Life',
            'insurance_company' => 'Acme',
            'policy_number' => 'L123',
        ];
        list($is_valid, $errors, $sanitized) = \EstatePlanningManager\Models\InsuranceModel::validateData($data);
        $this->assertTrue($is_valid);
        $this->assertEmpty($errors);
        $this->assertEquals('life', $sanitized['property_type']);
        $this->assertEquals('term', $sanitized['insurance_type']);
    }

    public function test_validateData_life_type_invalid() {
        if (!function_exists('epm_get_life_insurance_type_options')) {
            function epm_get_life_insurance_type_options() {
                return [
                    'whole_life' => 'Whole Life',
                    'universal' => 'Universal',
                    'term' => 'Term',
                    'critical_illness' => 'Critical Illness',
                ];
            }
        }
        $data = [
            'client_id' => 1,
            'property_type' => 'life',
            'insurance_type' => 'not_a_type',
            'insurance_category' => 'Life',
            'insurance_company' => 'Acme',
            'policy_number' => 'L123',
        ];
        list($is_valid, $errors, $sanitized) = \EstatePlanningManager\Models\InsuranceModel::validateData($data);
        $this->assertFalse($is_valid);
        $this->assertNotEmpty($errors);
        $this->assertNull($sanitized['insurance_type']);
    }

    public function test_field_definitions_life_type() {
        $defs = \EstatePlanningManager\Models\InsuranceModel::getFieldDefinitions();
        $this->assertArrayHasKey('insurance_type', $defs);
        $this->assertEquals('select', $defs['insurance_type']['type']);
        $this->assertArrayHasKey('show_if', $defs['insurance_type']);
        $this->assertContains('life', $defs['insurance_type']['show_if']['property_type']);
    }
}
