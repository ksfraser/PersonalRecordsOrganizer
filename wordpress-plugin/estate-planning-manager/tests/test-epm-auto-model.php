<?php
use PHPUnit\Framework\TestCase;

class Test_EPM_AutoModel extends TestCase {
    public function test_model_save_and_retrieve() {
        $model = new \EstatePlanningManager\Models\AutoModel();
        $data = [
            'client_id' => 1,
            'description' => 'Test vehicle',
            'vin' => 'VIN123456789',
            'year' => 2020,
            'make' => 'Toyota',
            'model' => 'Camry',
            'value' => 20000,
            'notes' => 'Vehicle notes',
        ];
        $id = $model->saveRecord($data);
        $this->assertNotEmpty($id, 'Model should return insert id');
        global $wpdb;
        $table = $wpdb->prefix . 'epm_auto_property';
        $row = $wpdb->get_row("SELECT * FROM $table WHERE id = $id", ARRAY_A);
        $this->assertEquals('Test vehicle', $row['description']);
        $this->assertEquals('Toyota', $row['make']);
        $this->assertEquals('Camry', $row['model']);
    }
}
