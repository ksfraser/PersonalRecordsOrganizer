<?php
/**
 * Modular UI tests for Estate Planning Manager
 */

use EstatePlanningManager\Models\PersonalModel;
use EstatePlanningManager\Models\BankingModel;
use EstatePlanningManager\Models\InvestmentsModel;
use EstatePlanningManager\Models\RealEstateModel;
use EstatePlanningManager\Models\InsuranceModel;
use EstatePlanningManager\Models\ScheduledPaymentsModel;
use EstatePlanningManager\Models\PersonalPropertyModel;
use EstatePlanningManager\Models\AutoModel;
use EstatePlanningManager\Models\EmergencyContactsModel;

class Test_EPM_Modular_UI extends EPM_Test_Case {
    public function test_personal_model_form_fields() {
        $model = new PersonalModel();
        $fields = $model->getFormFields();
        $field_names = array_column($fields, 'name');
        $this->assertTrue(in_array('name', $field_names));
        $this->assertTrue(in_array('dob', $field_names));
    }
    public function test_banking_model_form_fields() {
        $model = new BankingModel();
        $fields = $model->getFormFields();
        $field_names = array_column($fields, 'name');
        $this->assertTrue(in_array('bank', $field_names));
    }
    public function test_investments_model_form_fields() {
        $model = new InvestmentsModel();
        $fields = $model->getFormFields();
        $field_names = array_column($fields, 'name');
        $this->assertTrue(in_array('investment_type', $field_names));
    }
    public function test_real_estate_model_form_fields() {
        $model = new RealEstateModel();
        $fields = $model->getFormFields();
        $field_names = array_column($fields, 'name');
        $this->assertTrue(in_array('property_type', $field_names));
    }
    public function test_insurance_model_form_fields() {
        $model = new InsuranceModel();
        $fields = $model->getFormFields();
        $field_names = array_column($fields, 'name');
        $this->assertTrue(in_array('insurance_category', $field_names));
    }
    public function test_scheduled_payments_model_form_fields() {
        $model = new ScheduledPaymentsModel();
        $fields = $model->getFormFields();
        $field_names = array_column($fields, 'name');
        $this->assertTrue(in_array('payment_type', $field_names));
    }
    public function test_personal_property_model_form_fields() {
        $model = new PersonalPropertyModel();
        $fields = $model->getFormFields();
        $field_names = array_column($fields, 'name');
        $this->assertTrue(in_array('property_type', $field_names));
    }
    public function test_auto_model_form_fields() {
        $model = new AutoModel();
        $fields = $model->getFormFields();
        $field_names = array_column($fields, 'name');
        $expected = [
            'type',
            'vehicle',
            'model',
            'own_or_lease',
            'legal_document_location',
            'registration_location',
            'insurance_policy_location',
            'bill_of_sale_location',
            'owner_person_id',
        ];
        foreach ($expected as $field) {
            $this->assertTrue(in_array($field, $field_names), "AutoModel missing field: $field");
        }
    }
    public function test_emergency_contacts_model_form_fields() {
        $model = new EmergencyContactsModel();
        $fields = $model->getFormFields();
        $field_names = array_column($fields, 'name');
        $this->assertTrue(in_array('contact_name', $field_names));
    }
}
