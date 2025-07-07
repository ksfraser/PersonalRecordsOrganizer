<?php
use PHPUnit\Framework\TestCase;

class Test_EPM_PersonalPropertyModel extends TestCase {
    public function test_model_save_and_retrieve() {
        $model = new \EstatePlanningManager\Models\PersonalPropertyModel();
        $data = [
            'client_id' => 1,
            'description' => 'Test property',
            'category_id' => 2,
            'value' => 10000,
            'location' => 'Home',
            'notes' => 'Some notes',
        ];
        $id = $model->saveRecord($data);
        $this->assertNotEmpty($id, 'Model should return insert id');
        global $wpdb;
        $table = $wpdb->prefix . 'epm_personal_property';
        $row = $wpdb->get_row("SELECT * FROM $table WHERE id = $id", ARRAY_A);
        $this->assertEquals('Test property', $row['description']);
        $this->assertEquals('Home', $row['location']);
        $this->assertEquals(10000, $row['value']);
    }
}
