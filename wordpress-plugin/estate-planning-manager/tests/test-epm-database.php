<?php
/**
 * Tests for EPM_Database class
 */

class Test_EPM_Database extends EPM_Test_Case {
    
    /**
     * Test database table creation
     */
    public function test_database_tables_created() {
        // Trigger table creation
        EPM_Database::instance()->create_tables();
        
        // Test core tables exist
        $this->assertTableExists('epm_clients');
        $this->assertTableExists('epm_basic_personal');
        $this->assertTableExists('epm_family_contacts');
        $this->assertTableExists('epm_key_contacts');
        $this->assertTableExists('epm_wills_poa');
        $this->assertTableExists('epm_funeral_organ');
        $this->assertTableExists('epm_taxes');
        $this->assertTableExists('epm_military_service');
        $this->assertTableExists('epm_employment');
        $this->assertTableExists('epm_volunteer');
        $this->assertTableExists('epm_bank_accounts');
        $this->assertTableExists('epm_investments');
        $this->assertTableExists('epm_real_estate');
        $this->assertTableExists('epm_personal_property');
        $this->assertTableExists('epm_digital_assets');
        $this->assertTableExists('epm_scheduled_payments');
        $this->assertTableExists('epm_debtors_creditors');
        $this->assertTableExists('epm_insurance');
        $this->assertTableExists('epm_sharing_permissions');
        $this->assertTableExists('epm_audit_log');
        $this->assertTableExists('epm_sync_log');
    }
    
    /**
     * Test client creation
     */
    public function test_create_client() {
        $client_id = EPM_Database::instance()->create_client($this->client_user_id, $this->advisor_user_id);
        
        $this->assertIsInt($client_id);
        $this->assertGreaterThan(0, $client_id);
        
        // Verify client record exists
        $this->assertRecordExists('epm_clients', array(
            'user_id' => $this->client_user_id,
            'advisor_id' => $this->advisor_user_id
        ));
    }
    
    /**
     * Test get client ID by user ID
     */
    public function test_get_client_id_by_user_id() {
        $client_id = EPM_Database::instance()->get_client_id_by_user_id($this->client_user_id);
        
        $this->assertEquals($this->test_client_id, $client_id);
    }
    
    /**
     * Test save client data - basic personal
     */
    public function test_save_basic_personal_data() {
        $data = EPM_Test_Factory::get_default_data('basic_personal');
        
        $result = EPM_Database::instance()->save_client_data($this->test_client_id, 'basic_personal', $data);
        
        $this->assertTrue($result);
        
        // Verify data was saved
        $saved_data = EPM_Database::instance()->get_client_data($this->test_client_id, 'basic_personal');
        
        $this->assertNotEmpty($saved_data);
        $this->assertEquals($data['full_legal_name'], $saved_data[0]->full_legal_name);
        $this->assertEquals($data['date_of_birth'], $saved_data[0]->date_of_birth);
    }
    
    /**
     * Test save client data - family contacts
     */
    public function test_save_family_contacts_data() {
        $data = EPM_Test_Factory::get_default_data('family_contacts');
        
        $result = EPM_Database::instance()->save_client_data($this->test_client_id, 'family_contacts', $data);
        
        $this->assertTrue($result);
        
        // Verify data was saved
        $saved_data = EPM_Database::instance()->get_client_data($this->test_client_id, 'family_contacts');
        
        $this->assertNotEmpty($saved_data);
        $this->assertEquals($data['name'], $saved_data[0]->name);
        $this->assertEquals($data['relationship'], $saved_data[0]->relationship);
    }
    
    /**
     * Test save client data - bank accounts
     */
    public function test_save_bank_accounts_data() {
        $data = EPM_Test_Factory::get_default_data('bank_accounts');
        
        $result = EPM_Database::instance()->save_client_data($this->test_client_id, 'bank_accounts', $data);
        
        $this->assertTrue($result);
        
        // Verify data was saved
        $saved_data = EPM_Database::instance()->get_client_data($this->test_client_id, 'bank_accounts');
        
        $this->assertNotEmpty($saved_data);
        $this->assertEquals($data['bank'], $saved_data[0]->bank);
        $this->assertEquals($data['account_type'], $saved_data[0]->account_type);
    }
    
    /**
     * Test save client data - investments
     */
    public function test_save_investments_data() {
        $data = EPM_Test_Factory::get_default_data('investments');
        
        $result = EPM_Database::instance()->save_client_data($this->test_client_id, 'investments', $data);
        
        $this->assertTrue($result);
        
        // Verify data was saved
        $saved_data = EPM_Database::instance()->get_client_data($this->test_client_id, 'investments');
        
        $this->assertNotEmpty($saved_data);
        $this->assertEquals($data['investment_type'], $saved_data[0]->investment_type);
        $this->assertEquals($data['financial_company'], $saved_data[0]->financial_company);
    }
    
    /**
     * Test update existing data
     */
    public function test_update_existing_data() {
        // First save some data
        $original_data = EPM_Test_Factory::get_default_data('basic_personal');
        EPM_Database::instance()->save_client_data($this->test_client_id, 'basic_personal', $original_data);
        
        // Update the data
        $updated_data = $original_data;
        $updated_data['full_legal_name'] = 'Updated Test Name';
        
        $result = EPM_Database::instance()->save_client_data($this->test_client_id, 'basic_personal', $updated_data);
        
        $this->assertTrue($result);
        
        // Verify data was updated
        $saved_data = EPM_Database::instance()->get_client_data($this->test_client_id, 'basic_personal');
        
        $this->assertEquals('Updated Test Name', $saved_data[0]->full_legal_name);
    }
    
    /**
     * Test delete client data
     */
    public function test_delete_client_data() {
        // First save some data
        $data = EPM_Test_Factory::get_default_data('family_contacts');
        EPM_Database::instance()->save_client_data($this->test_client_id, 'family_contacts', $data);
        
        // Get the record ID
        $saved_data = EPM_Database::instance()->get_client_data($this->test_client_id, 'family_contacts');
        $record_id = $saved_data[0]->id;
        
        // Delete the record
        $result = EPM_Database::instance()->delete_client_data($this->test_client_id, 'family_contacts', $record_id);
        
        $this->assertNotFalse($result);
        
        // Verify data was deleted
        $remaining_data = EPM_Database::instance()->get_client_data($this->test_client_id, 'family_contacts');
        
        $this->assertEmpty($remaining_data);
    }
    
    /**
     * Test get client sections with data
     */
    public function test_get_client_sections_with_data() {
        // Save data to multiple sections
        EPM_Test_Factory::create_client_data($this->test_client_id, 'basic_personal');
        EPM_Test_Factory::create_client_data($this->test_client_id, 'family_contacts');
        EPM_Test_Factory::create_client_data($this->test_client_id, 'bank_accounts');
        
        $sections = EPM_Database::instance()->get_client_sections_with_data($this->test_client_id);
        
        $this->assertContains('basic_personal', $sections);
        $this->assertContains('family_contacts', $sections);
        $this->assertContains('bank_accounts', $sections);
        $this->assertCount(3, $sections);
    }
    
    /**
     * Test get client completion percentage
     */
    public function test_get_client_completion_percentage() {
        // Initially should be 0%
        $percentage = EPM_Database::instance()->get_client_completion_percentage($this->test_client_id);
        $this->assertEquals(0, $percentage);
        
        // Add some data
        EPM_Test_Factory::create_client_data($this->test_client_id, 'basic_personal');
        EPM_Test_Factory::create_client_data($this->test_client_id, 'family_contacts');
        
        $percentage = EPM_Database::instance()->get_client_completion_percentage($this->test_client_id);
        
        // Should be approximately 11.76% (2 out of 17 sections)
        $this->assertGreaterThan(10, $percentage);
        $this->assertLessThan(15, $percentage);
    }
    
    /**
     * Test database version check
     */
    public function test_database_version_check() {
        // Delete version option to simulate fresh install
        delete_option('epm_db_version');
        
        // Trigger version check
        EPM_Database::instance()->check_database_version();
        
        // Verify version was set
        $version = get_option('epm_db_version');
        $this->assertNotEmpty($version);
        $this->assertEquals('1.0.0', $version);
    }
    
    /**
     * Test foreign key constraints
     */
    public function test_foreign_key_constraints() {
        // Save basic personal data
        $data = EPM_Test_Factory::get_default_data('basic_personal');
        $result = EPM_Database::instance()->save_client_data($this->test_client_id, 'basic_personal', $data);
        
        $this->assertTrue($result);
        
        // Verify the client_id foreign key relationship
        $saved_data = EPM_Database::instance()->get_client_data($this->test_client_id, 'basic_personal');
        $this->assertEquals($this->test_client_id, $saved_data[0]->client_id);
    }
    
    /**
     * Test data sanitization
     */
    public function test_data_sanitization() {
        $data = array(
            'full_legal_name' => '<script>alert("xss")</script>John Doe',
            'email' => 'invalid-email',
            'phone' => '123-456-7890'
        );
        
        $result = EPM_Database::instance()->save_client_data($this->test_client_id, 'basic_personal', $data);
        
        $this->assertTrue($result);
        
        // Verify data was sanitized (script tags should be removed)
        $saved_data = EPM_Database::instance()->get_client_data($this->test_client_id, 'basic_personal');
        $this->assertStringNotContainsString('<script>', $saved_data[0]->full_legal_name);
    }
}
