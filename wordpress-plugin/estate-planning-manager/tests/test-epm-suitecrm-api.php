<?php
/**
 * Tests for EPM_SuiteCRM_API class
 */

class Test_EPM_SuiteCRM_API extends EPM_Test_Case {
    
    /**
     * Mock API instance for testing
     */
    private $mock_api;
    
    /**
     * Set up test environment
     */
    public function setUp(): void {
        parent::setUp();
        
        // Mock the API connection for testing
        $this->mock_api = $this->getMockBuilder('EPM_SuiteCRM_API')
            ->setMethods(['make_api_request'])
            ->getMock();
    }
    
    /**
     * Test API connection
     */
    public function test_api_connection() {
        // Mock successful connection response
        $this->mock_api->method('make_api_request')
            ->willReturn(array(
                'success' => true,
                'data' => array('version' => '8.0.0')
            ));
        
        $connection_result = $this->mock_api->test_connection();
        
        $this->assertTrue($connection_result);
    }
    
    /**
     * Test API authentication
     */
    public function test_api_authentication() {
        // Mock successful authentication response
        $this->mock_api->method('make_api_request')
            ->willReturn(array(
                'success' => true,
                'data' => array(
                    'access_token' => 'test_token_123',
                    'expires_in' => 3600
                )
            ));
        
        $auth_result = $this->mock_api->authenticate('test_user', 'test_password');
        
        $this->assertNotFalse($auth_result);
        $this->assertArrayHasKey('access_token', $auth_result);
    }
    
    /**
     * Test create contact in SuiteCRM
     */
    public function test_create_contact() {
        // Create test client data
        EPM_Test_Factory::create_client_data($this->test_client_id, 'basic_personal');
        
        // Mock successful contact creation response
        $this->mock_api->method('make_api_request')
            ->willReturn(array(
                'success' => true,
                'data' => array(
                    'id' => 'contact_123',
                    'attributes' => array(
                        'first_name' => 'John',
                        'last_name' => 'Doe'
                    )
                )
            ));
        
        $contact_id = $this->mock_api->create_contact($this->test_client_id);
        
        $this->assertNotFalse($contact_id);
        $this->assertEquals('contact_123', $contact_id);
    }
    
    /**
     * Test update contact in SuiteCRM
     */
    public function test_update_contact() {
        // Create test client data
        EPM_Test_Factory::create_client_data($this->test_client_id, 'basic_personal');
        
        // Mock successful contact update response
        $this->mock_api->method('make_api_request')
            ->willReturn(array(
                'success' => true,
                'data' => array(
                    'id' => 'contact_123',
                    'attributes' => array(
                        'first_name' => 'John',
                        'last_name' => 'Doe Updated'
                    )
                )
            ));
        
        $result = $this->mock_api->update_contact('contact_123', $this->test_client_id);
        
        $this->assertTrue($result);
    }
    
    /**
     * Test sync client data to SuiteCRM
     */
    public function test_sync_client_data() {
        // Create comprehensive test data
        EPM_Test_Factory::create_client_data($this->test_client_id, 'basic_personal');
        EPM_Test_Factory::create_client_data($this->test_client_id, 'family_contacts');
        EPM_Test_Factory::create_client_data($this->test_client_id, 'bank_accounts');
        
        // Mock successful sync response
        $this->mock_api->method('make_api_request')
            ->willReturn(array(
                'success' => true,
                'data' => array(
                    'contact_id' => 'contact_123',
                    'synced_sections' => array('basic_personal', 'family_contacts', 'bank_accounts')
                )
            ));
        
        $sync_result = $this->mock_api->sync_client_data($this->test_client_id);
        
        $this->assertNotFalse($sync_result);
        $this->assertArrayHasKey('contact_id', $sync_result);
        $this->assertArrayHasKey('synced_sections', $sync_result);
    }
    
    /**
     * Test create opportunity in SuiteCRM
     */
    public function test_create_opportunity() {
        $opportunity_data = array(
            'name' => 'Estate Planning - John Doe',
            'amount' => 5000.00,
            'sales_stage' => 'Prospecting',
            'date_closed' => date('Y-m-d', strtotime('+30 days')),
            'assigned_user_id' => 'advisor_123'
        );
        
        // Mock successful opportunity creation response
        $this->mock_api->method('make_api_request')
            ->willReturn(array(
                'success' => true,
                'data' => array(
                    'id' => 'opportunity_123',
                    'attributes' => $opportunity_data
                )
            ));
        
        $opportunity_id = $this->mock_api->create_opportunity($this->test_client_id, $opportunity_data);
        
        $this->assertNotFalse($opportunity_id);
        $this->assertEquals('opportunity_123', $opportunity_id);
    }
    
    /**
     * Test create case in SuiteCRM
     */
    public function test_create_case() {
        $case_data = array(
            'name' => 'Estate Planning Consultation',
            'description' => 'Initial consultation for estate planning services',
            'status' => 'New',
            'priority' => 'Medium',
            'type' => 'Estate Planning'
        );
        
        // Mock successful case creation response
        $this->mock_api->method('make_api_request')
            ->willReturn(array(
                'success' => true,
                'data' => array(
                    'id' => 'case_123',
                    'attributes' => $case_data
                )
            ));
        
        $case_id = $this->mock_api->create_case($this->test_client_id, $case_data);
        
        $this->assertNotFalse($case_id);
        $this->assertEquals('case_123', $case_id);
    }
    
    /**
     * Test data mapping for SuiteCRM
     */
    public function test_data_mapping() {
        $client_data = array(
            'full_legal_name' => 'John Test Doe',
            'date_of_birth' => '1980-05-15',
            'email' => 'john.doe@test.com',
            'phone' => '416-555-0123'
        );
        
        $mapped_data = EPM_SuiteCRM_API::instance()->map_client_data_to_crm($client_data);
        
        $this->assertArrayHasKey('first_name', $mapped_data);
        $this->assertArrayHasKey('last_name', $mapped_data);
        $this->assertArrayHasKey('email1', $mapped_data);
        $this->assertArrayHasKey('phone_work', $mapped_data);
        $this->assertArrayHasKey('birthdate', $mapped_data);
        
        $this->assertEquals('John Test', $mapped_data['first_name']);
        $this->assertEquals('Doe', $mapped_data['last_name']);
        $this->assertEquals('john.doe@test.com', $mapped_data['email1']);
        $this->assertEquals('416-555-0123', $mapped_data['phone_work']);
        $this->assertEquals('1980-05-15', $mapped_data['birthdate']);
    }
    
    /**
     * Test error handling
     */
    public function test_error_handling() {
        // Mock API error response
        $this->mock_api->method('make_api_request')
            ->willReturn(array(
                'success' => false,
                'error' => 'Authentication failed',
                'error_code' => 401
            ));
        
        $result = $this->mock_api->create_contact($this->test_client_id);
        
        $this->assertFalse($result);
        
        // Check that error was logged
        $last_error = $this->mock_api->get_last_error();
        $this->assertNotEmpty($last_error);
        $this->assertStringContains('Authentication failed', $last_error);
    }
    
    /**
     * Test rate limiting
     */
    public function test_rate_limiting() {
        // Mock rate limit exceeded response
        $this->mock_api->method('make_api_request')
            ->willReturn(array(
                'success' => false,
                'error' => 'Rate limit exceeded',
                'error_code' => 429,
                'retry_after' => 60
            ));
        
        $result = $this->mock_api->create_contact($this->test_client_id);
        
        $this->assertFalse($result);
        
        // Verify rate limiting is handled
        $rate_limit_info = $this->mock_api->get_rate_limit_info();
        $this->assertArrayHasKey('retry_after', $rate_limit_info);
        $this->assertEquals(60, $rate_limit_info['retry_after']);
    }
    
    /**
     * Test batch operations
     */
    public function test_batch_operations() {
        // Create multiple test clients
        $client_ids = array();
        for ($i = 0; $i < 3; $i++) {
            $user_id = $this->factory->user->create(array(
                'role' => 'estate_client',
                'user_login' => "batch_client_$i",
                'user_email' => "batch$i@test.com"
            ));
            
            $client_id = EPM_Database::instance()->create_client($user_id, $this->advisor_user_id);
            EPM_Test_Factory::create_client_data($client_id, 'basic_personal');
            $client_ids[] = $client_id;
        }
        
        // Mock successful batch response
        $this->mock_api->method('make_api_request')
            ->willReturn(array(
                'success' => true,
                'data' => array(
                    'processed' => 3,
                    'results' => array(
                        array('id' => 'contact_1', 'status' => 'created'),
                        array('id' => 'contact_2', 'status' => 'created'),
                        array('id' => 'contact_3', 'status' => 'created')
                    )
                )
            ));
        
        $batch_result = $this->mock_api->batch_sync_clients($client_ids);
        
        $this->assertNotFalse($batch_result);
        $this->assertEquals(3, $batch_result['processed']);
    }
    
    /**
     * Test webhook handling
     */
    public function test_webhook_handling() {
        $webhook_data = array(
            'event' => 'contact.updated',
            'data' => array(
                'id' => 'contact_123',
                'attributes' => array(
                    'first_name' => 'John Updated',
                    'last_name' => 'Doe',
                    'email1' => 'john.updated@test.com'
                )
            )
        );
        
        $result = EPM_SuiteCRM_API::instance()->handle_webhook($webhook_data);
        
        $this->assertTrue($result);
        
        // Verify webhook was logged
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_sync_log';
        
        $log_entry = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE sync_type = %s ORDER BY created_at DESC LIMIT 1",
            'webhook'
        ));
        
        $this->assertNotNull($log_entry);
        $this->assertEquals('contact.updated', json_decode($log_entry->sync_data, true)['event']);
    }
    
    /**
     * Test sync logging
     */
    public function test_sync_logging() {
        $sync_data = array(
            'client_id' => $this->test_client_id,
            'crm_contact_id' => 'contact_123',
            'sections_synced' => array('basic_personal', 'family_contacts'),
            'sync_direction' => 'to_crm'
        );
        
        $result = EPM_SuiteCRM_API::instance()->log_sync_operation('contact_sync', $sync_data);
        
        $this->assertNotFalse($result);
        
        // Verify sync was logged
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_sync_log';
        
        $log_entry = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE sync_type = %s ORDER BY created_at DESC LIMIT 1",
            'contact_sync'
        ));
        
        $this->assertNotNull($log_entry);
        
        $logged_data = json_decode($log_entry->sync_data, true);
        $this->assertEquals($this->test_client_id, $logged_data['client_id']);
        $this->assertEquals('contact_123', $logged_data['crm_contact_id']);
    }
    
    /**
     * Test field mapping configuration
     */
    public function test_field_mapping_configuration() {
        $custom_mapping = array(
            'full_legal_name' => 'name',
            'email' => 'email_address',
            'phone' => 'phone_number',
            'date_of_birth' => 'birth_date'
        );
        
        EPM_SuiteCRM_API::instance()->set_field_mapping($custom_mapping);
        
        $client_data = array(
            'full_legal_name' => 'John Doe',
            'email' => 'john@test.com',
            'phone' => '555-0123',
            'date_of_birth' => '1980-01-01'
        );
        
        $mapped_data = EPM_SuiteCRM_API::instance()->map_client_data_to_crm($client_data);
        
        $this->assertArrayHasKey('name', $mapped_data);
        $this->assertArrayHasKey('email_address', $mapped_data);
        $this->assertArrayHasKey('phone_number', $mapped_data);
        $this->assertArrayHasKey('birth_date', $mapped_data);
        
        $this->assertEquals('John Doe', $mapped_data['name']);
        $this->assertEquals('john@test.com', $mapped_data['email_address']);
    }
    
    /**
     * Test sync conflict resolution
     */
    public function test_sync_conflict_resolution() {
        // Simulate data conflict
        $local_data = array(
            'full_legal_name' => 'John Doe Local',
            'email' => 'john.local@test.com',
            'updated_at' => '2023-01-15 10:00:00'
        );
        
        $crm_data = array(
            'name' => 'John Doe CRM',
            'email1' => 'john.crm@test.com',
            'date_modified' => '2023-01-15 11:00:00'
        );
        
        $resolved_data = EPM_SuiteCRM_API::instance()->resolve_sync_conflict($local_data, $crm_data);
        
        // CRM data should win (newer timestamp)
        $this->assertEquals('John Doe CRM', $resolved_data['full_legal_name']);
        $this->assertEquals('john.crm@test.com', $resolved_data['email']);
    }
    
    /**
     * Test API configuration validation
     */
    public function test_api_configuration_validation() {
        $valid_config = array(
            'api_url' => 'https://crm.example.com/api/v8',
            'client_id' => 'test_client_id',
            'client_secret' => 'test_client_secret',
            'username' => 'test_user',
            'password' => 'test_password'
        );
        
        $is_valid = EPM_SuiteCRM_API::instance()->validate_configuration($valid_config);
        $this->assertTrue($is_valid);
        
        $invalid_config = array(
            'api_url' => 'invalid_url',
            'client_id' => '',
            'client_secret' => 'test_secret'
            // Missing username and password
        );
        
        $is_invalid = EPM_SuiteCRM_API::instance()->validate_configuration($invalid_config);
        $this->assertFalse($is_invalid);
    }
    
    /**
     * Test connection timeout handling
     */
    public function test_connection_timeout() {
        // Mock timeout response
        $this->mock_api->method('make_api_request')
            ->willThrowException(new Exception('Connection timeout'));
        
        $result = $this->mock_api->test_connection();
        
        $this->assertFalse($result);
        
        $last_error = $this->mock_api->get_last_error();
        $this->assertStringContains('timeout', strtolower($last_error));
    }
    
    /**
     * Test data sanitization for CRM
     */
    public function test_data_sanitization_for_crm() {
        $unsanitized_data = array(
            'full_legal_name' => '<script>alert("xss")</script>John Doe',
            'email' => 'john@test.com<script>',
            'phone' => '555-0123',
            'notes' => 'Some notes with <iframe>malicious content</iframe>'
        );
        
        $sanitized_data = EPM_SuiteCRM_API::instance()->sanitize_data_for_crm($unsanitized_data);
        
        $this->assertStringNotContainsString('<script>', $sanitized_data['full_legal_name']);
        $this->assertStringNotContainsString('<script>', $sanitized_data['email']);
        $this->assertStringNotContainsString('<iframe>', $sanitized_data['notes']);
        $this->assertEquals('555-0123', $sanitized_data['phone']); // Phone should be unchanged
    }
}
