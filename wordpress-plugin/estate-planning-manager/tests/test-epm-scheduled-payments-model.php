<?php
use PHPUnit\Framework\TestCase;

class Test_EPM_ScheduledPaymentsModel extends TestCase {
    public function test_model_save_and_retrieve() {
        $model = new \EstatePlanningManager\Models\ScheduledPaymentsModel();
        $data = [
            'client_id' => 1,
            'account_number' => 'SP-001',
            'amount' => 500.00,
            'payment_type_id' => 1,
            'payee_id' => 2,
            'schedule' => 'monthly',
            'description' => 'Scheduled payment test',
        ];
        $id = $model->saveRecord($data);
        $this->assertNotEmpty($id, 'Model should return insert id');
        global $wpdb;
        $table = $wpdb->prefix . 'epm_scheduled_payments';
        $row = $wpdb->get_row("SELECT * FROM $table WHERE id = $id", ARRAY_A);
        $this->assertEquals('Scheduled payment test', $row['description']);
        $this->assertEquals('SP-001', $row['account_number']);
        $this->assertEquals(500.00, $row['amount']);
    }
}
