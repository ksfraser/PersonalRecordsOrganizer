<?php
/**
 * Test that model, view, and DB schema field definitions are consistent for all EPM sections.
 */
use PHPUnit\Framework\TestCase;

class Test_EPM_FieldDefinitionsConsistency extends TestCase {
    protected $sections = [
        'personal' => [
            'model' => '\EstatePlanningManager\Models\PersonalModel',
            'table' => 'epm_basic_personal',
        ],
        'banking' => [
            'model' => '\EstatePlanningManager\Models\BankingModel',
            'table' => 'epm_bank_accounts',
        ],
        'investments' => [
            'model' => '\EstatePlanningManager\Models\InvestmentsModel',
            'table' => 'epm_investments',
        ],
        'insurance' => [
            'model' => '\EstatePlanningManager\Models\InsuranceModel',
            'table' => 'epm_insurance',
        ],
        'real_estate' => [
            'model' => '\EstatePlanningManager\Models\RealEstateModel',
            'table' => 'epm_real_estate',
        ],
        'scheduled_payments' => [
            'model' => '\EstatePlanningManager\Models\ScheduledPaymentsModel',
            'table' => 'epm_scheduled_payments',
        ],
        'auto_property' => [
            'model' => '\EstatePlanningManager\Models\AutoModel',
            'table' => 'epm_auto_property',
        ],
        'personal_property' => [
            'model' => '\EstatePlanningManager\Models\PersonalPropertyModel',
            'table' => 'epm_personal_property',
        ],
        'emergency_contacts' => [
            'model' => '\EstatePlanningManager\Models\EmergencyContactsModel',
            'table' => 'epm_emergency_contacts',
        ],
    ];

    public function test_field_definitions_consistency() {
        global $wpdb;
        $shortcodes = \EPM_Shortcodes::instance();
        $sections = $shortcodes->get_form_sections();
        foreach ($this->sections as $key => $meta) {
            $modelClass = $meta['model'];
            if (!class_exists($modelClass)) require_once __DIR__ . '/../public/models/' . basename(str_replace('\\', '/', $modelClass)) . '.php';
            $modelFields = $modelClass::getFieldDefinitions();
            $viewFields = array_column($sections[$key]['fields'], 'name');
            $table = $wpdb->prefix . $meta['table'];
            $columns = $wpdb->get_col("SHOW COLUMNS FROM $table");
            // All model fields must be present in view and DB
            foreach (array_keys($modelFields) as $field) {
                $this->assertContains($field, $viewFields, "$key: Field '$field' missing in view");
                $this->assertContains($field, $columns, "$key: Field '$field' missing in DB schema");
            }
            // All view fields must be present in model
            foreach ($viewFields as $field) {
                $this->assertArrayHasKey($field, $modelFields, "$key: View field '$field' missing in model");
            }
        }
    }
}
