<?php
/**
 * Tests for ScheduledPaymentsTable CRUD and schema (with organization FKs)
 */

class Test_ScheduledPaymentsTable extends EPM_Test_Case {
    protected $client_id;
    protected $table;
    protected $table_name;

    protected function setUp(): void {
        parent::setUp();
        $this->table = new ScheduledPaymentsTable();
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'epm_scheduled_payments';
        // Ensure table exists
        $this->table->create($wpdb->get_charset_collate());
        // Create a test client
        $this->client_id = EPM_Database::instance()->create_client($this->client_user_id, $this->advisor_user_id);
    }

    public function test_schema_has_org_fk() {
        global $wpdb;
        $columns = $wpdb->get_results("SHOW COLUMNS FROM {$this->table_name}", ARRAY_A);
        $fields = array_column($columns, 'Field');
        $this->assertContains('paid_to_org_id', $fields);
    }

    public function test_crud_operations_with_org_fk() {
        global $wpdb;
        // Create test organization
        $org_id = EPM_Test_Factory::create_organization($this->client_id, ['name' => 'Acme Utility', 'email' => 'billing@acme.com']);
        // Create scheduled payment record using org FK
        $data = array(
            'client_id' => $this->client_id,
            'suitecrm_guid' => 'sched-123',
            'wp_record_id' => 99,
            'payment_type' => 'Utility',
            'paid_to_org_id' => $org_id,
            'is_automatic' => 'Yes',
            'amount' => 123.45,
            'due_date' => '2025-07-01'
        );
        $result = $wpdb->insert($this->table_name, $data);
        $this->assertNotFalse($result);
        $insert_id = $wpdb->insert_id;
        // Read
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->table_name} WHERE id = %d", $insert_id));
        $this->assertEquals('Utility', $row->payment_type);
        $this->assertEquals($org_id, $row->paid_to_org_id);
        // Update
        $wpdb->update($this->table_name, ['payment_type' => 'Subscription'], ['id' => $insert_id]);
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->table_name} WHERE id = %d", $insert_id));
        $this->assertEquals('Subscription', $row->payment_type);
        // Delete
        $wpdb->delete($this->table_name, ['id' => $insert_id]);
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->table_name} WHERE id = %d", $insert_id));
        $this->assertNull($row);
    }
}
