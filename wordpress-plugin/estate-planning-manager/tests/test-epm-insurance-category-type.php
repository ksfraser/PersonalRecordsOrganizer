<?php
use PHPUnit\Framework\TestCase;

class EPM_InsuranceCategoryType_Test extends TestCase {
    public function test_category_options_prefilled() {
        if (!function_exists('epm_get_insurance_category_options')) {
            require_once dirname(__DIR__) . '/estate-planning-manager/public/includes/epm-insurance-selectors.php';
        }
        $options = epm_get_insurance_category_options();
        $this->assertArrayHasKey(1, $options); // Life
        $this->assertArrayHasKey(2, $options); // Auto
        $this->assertArrayHasKey(3, $options); // House
    }
    public function test_type_options_prefilled() {
        if (!function_exists('epm_get_insurance_type_options')) {
            require_once dirname(__DIR__) . '/estate-planning-manager/public/includes/epm-insurance-selectors.php';
        }
        $lifeTypes = epm_get_insurance_type_options(1);
        $autoTypes = epm_get_insurance_type_options(2);
        $houseTypes = epm_get_insurance_type_options(3);
        $this->assertContains('Whole Life', $lifeTypes);
        $this->assertContains('Auto', $autoTypes);
        $this->assertContains('House', $houseTypes);
    }
}
