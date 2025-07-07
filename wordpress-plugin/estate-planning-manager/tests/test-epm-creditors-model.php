<?php
use PHPUnit\Framework\TestCase;

class Test_EPM_CreditorsModel extends TestCase {
    public function test_model_save_and_retrieve() {
        $model = new \EstatePlanningManager\Models\CreditorsModel();
        $data = [
            'client_id' => 1,
            'person_org' => 'organization',
            'contact_id' => 3,
            'account_number' => '54321',
            'amount' => 2000.75,
            'scheduled_payment_id' => 2,
            'date_of_loan' => '2025-07-06',
            'description' => 'Test creditor',
        ];
        $id = $model->saveRecord($data);
        $this->assertNotEmpty($id, 'Model should return insert id');
        global $wpdb;
        $table = $wpdb->prefix . 'epm_creditors';
        $row = $wpdb->get_row("SELECT * FROM $table WHERE id = $id", ARRAY_A);
        $this->assertEquals('Test creditor', $row['description']);
        $this->assertEquals('54321', $row['account_number']);
        $this->assertEquals('organization', $row['person_org']);
    }
}
