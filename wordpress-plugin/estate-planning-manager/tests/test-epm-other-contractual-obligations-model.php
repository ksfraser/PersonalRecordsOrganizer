<?php
use PHPUnit\Framework\TestCase;

class Test_EPM_OtherContractualObligationsModel extends TestCase {
    public function test_model_save_and_retrieve() {
        $model = new \EstatePlanningManager\Models\OtherContractualObligationsModel();
        $data = [
            'client_id' => 1,
            'suitecrm_guid' => 'guid-123',
            'wp_record_id' => 456,
            'description' => 'Model test contract',
            'location_of_documents' => 'Vault',
            'notes' => 'Model notes',
        ];
        $id = $model->saveRecord($data);
        $this->assertNotEmpty($id, 'Model should return insert id');
        global $wpdb;
        $table = $wpdb->prefix . 'epm_other_contractual_obligations';
        $row = $wpdb->get_row("SELECT * FROM $table WHERE id = $id", ARRAY_A);
        $this->assertEquals('Model test contract', $row['description']);
        $this->assertEquals('Vault', $row['location_of_documents']);
        $this->assertEquals('Model notes', $row['notes']);
    }
}
