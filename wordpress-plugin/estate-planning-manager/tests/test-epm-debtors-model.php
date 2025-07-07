<?php
use PHPUnit\Framework\TestCase;

class Test_EPM_DebtorsModel extends TestCase {
    public function test_model_save_and_retrieve() {
        $model = new \EstatePlanningManager\Models\DebtorsModel();
        $data = [
            'client_id' => 1,
            'person_org' => 'person',
            'contact_id' => 2,
            'account_number' => '12345',
            'amount' => 1000.50,
            'scheduled_payment_id' => 1,
            'date_of_loan' => '2025-07-06',
            'description' => 'Test debtor',
        ];
        $id = $model->saveRecord($data);
        $this->assertNotEmpty($id, 'Model should return insert id');
        global $wpdb;
        $table = $wpdb->prefix . 'epm_debtors';
        $row = $wpdb->get_row("SELECT * FROM $table WHERE id = $id", ARRAY_A);
        $this->assertEquals('Test debtor', $row['description']);
        $this->assertEquals('12345', $row['account_number']);
        $this->assertEquals('person', $row['person_org']);
    }
}
