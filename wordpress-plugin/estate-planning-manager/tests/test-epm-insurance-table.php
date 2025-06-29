<?php
/**
 * Tests for InsuranceTable CRUD and schema
 */

class Test_InsuranceTable extends EPM_Test_Case {
    protected $client_id;
    protected $table;
    protected $table_name;

    protected function setUp(): void {
        parent::setUp();
        $this->table = new InsuranceTable();
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'epm_insurance';
        // Ensure table exists
        $this->table->create($wpdb->get_charset_collate());
        // Create a test client
        $this->client_id = EPM_Database::instance()->create_client($this->client_user_id, $this->advisor_user_id);
    }

    public function test_schema_has_cross_system_fields() {
        global $wpdb;
        $columns = $wpdb->get_results("SHOW COLUMNS FROM {$this->table_name}", ARRAY_A);
        $fields = array_column($columns, 'Field');
        $this->assertContains('suitecrm_guid', $fields);
        $this->assertContains('wp_record_id', $fields);
    }

    public function test_crud_operations() {
        global $wpdb;
        // Create
        $data = array(
            'client_id' => $this->client_id,
            'suitecrm_guid' => 'abc-123',
            'wp_record_id' => 42,
            'insurance_category' => 'Life',
            'insurance_type' => 'Whole Life',
            'advisor' => 'Jane Advisor',
            'insurance_company' => 'Acme Insurance',
            'policy_number' => 'POL123',
            'beneficiary' => 'John Doe',
            'insured_person' => 'John Doe',
            'policy_owner' => 'Jane Owner',
        );
        $result = $wpdb->insert($this->table_name, $data);
        $this->assertNotFalse($result);
        $insert_id = $wpdb->insert_id;

        // Read
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->table_name} WHERE id = %d", $insert_id));
        $this->assertEquals('Life', $row->insurance_category);
        $this->assertEquals('abc-123', $row->suitecrm_guid);
        $this->assertEquals(42, $row->wp_record_id);

        // Update
        $wpdb->update($this->table_name, ['insurance_category' => 'Health'], ['id' => $insert_id]);
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->table_name} WHERE id = %d", $insert_id));
        $this->assertEquals('Health', $row->insurance_category);

        // Delete
        $wpdb->delete($this->table_name, ['id' => $insert_id]);
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->table_name} WHERE id = %d", $insert_id));
        $this->assertNull($row);
    }

    public function test_crud_operations_with_person_fks() {
        global $wpdb;
        // Create test persons
        $beneficiary_id = EPM_Test_Factory::create_person($this->client_id, ['full_name' => 'John Beneficiary', 'email' => 'john.beneficiary@example.com']);
        $advisor_id = EPM_Test_Factory::create_person($this->client_id, ['full_name' => 'Jane Advisor', 'email' => 'jane.advisor@example.com']);
        $owner_id = EPM_Test_Factory::create_person($this->client_id, ['full_name' => 'Jane Owner', 'email' => 'jane.owner@example.com']);
        // Create insurance record using person FKs
        $data = array(
            'client_id' => $this->client_id,
            'suitecrm_guid' => 'abc-123',
            'wp_record_id' => 42,
            'insurance_category' => 'Life',
            'insurance_type' => 'Whole Life',
            'insurance_company' => 'Acme Insurance',
            'policy_number' => 'POL123',
            'address' => '123 Main St',
            'beneficiary_person_id' => $beneficiary_id,
            'advisor_person_id' => $advisor_id,
            'owner_person_id' => $owner_id
        );
        $result = $wpdb->insert($this->table_name, $data);
        $this->assertNotFalse($result);
        $insert_id = $wpdb->insert_id;
        // Read
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->table_name} WHERE id = %d", $insert_id));
        $this->assertEquals('Life', $row->insurance_category);
        $this->assertEquals($beneficiary_id, $row->beneficiary_person_id);
        $this->assertEquals($advisor_id, $row->advisor_person_id);
        $this->assertEquals($owner_id, $row->owner_person_id);
        // Update
        $wpdb->update($this->table_name, ['insurance_category' => 'Health'], ['id' => $insert_id]);
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->table_name} WHERE id = %d", $insert_id));
        $this->assertEquals('Health', $row->insurance_category);
        // Delete
        $wpdb->delete($this->table_name, ['id' => $insert_id]);
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->table_name} WHERE id = %d", $insert_id));
        $this->assertNull($row);
    }
}
