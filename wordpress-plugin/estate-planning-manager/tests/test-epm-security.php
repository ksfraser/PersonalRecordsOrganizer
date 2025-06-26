<?php
/**
 * Tests for EPM_Security class
 */

class Test_EPM_Security extends EPM_Test_Case {
    
    /**
     * Test user can access own data
     */
    public function test_user_can_access_own_data() {
        wp_set_current_user($this->client_user_id);
        
        $can_access = EPM_Security::instance()->can_user_access_client_data($this->client_user_id, $this->test_client_id);
        
        $this->assertTrue($can_access);
    }
    
    /**
     * Test advisor can access client data
     */
    public function test_advisor_can_access_client_data() {
        wp_set_current_user($this->advisor_user_id);
        
        $can_access = EPM_Security::instance()->can_user_access_client_data($this->advisor_user_id, $this->test_client_id);
        
        $this->assertTrue($can_access);
    }
    
    /**
     * Test admin can access all data
     */
    public function test_admin_can_access_all_data() {
        wp_set_current_user($this->admin_user_id);
        
        $can_access = EPM_Security::instance()->can_user_access_client_data($this->admin_user_id, $this->test_client_id);
        
        $this->assertTrue($can_access);
    }
    
    /**
     * Test unauthorized user cannot access data
     */
    public function test_unauthorized_user_cannot_access_data() {
        // Create another user who shouldn't have access
        $unauthorized_user_id = $this->factory->user->create(array(
            'role' => 'estate_client',
            'user_login' => 'unauthorized_user',
            'user_email' => 'unauthorized@test.com'
        ));
        
        wp_set_current_user($unauthorized_user_id);
        
        $can_access = EPM_Security::instance()->can_user_access_client_data($unauthorized_user_id, $this->test_client_id);
        
        $this->assertFalse($can_access);
    }
    
    /**
     * Test sharing permissions
     */
    public function test_sharing_permissions() {
        // Create a user to share with
        $shared_user_id = $this->factory->user->create(array(
            'role' => 'financial_advisor',
            'user_login' => 'shared_advisor',
            'user_email' => 'shared@test.com'
        ));
        
        // Grant sharing permission
        EPM_Test_Factory::create_sharing_permission($this->test_client_id, $shared_user_id, 'basic_personal', 'view');
        
        wp_set_current_user($shared_user_id);
        
        $can_access = EPM_Security::instance()->can_user_access_client_section($shared_user_id, $this->test_client_id, 'basic_personal');
        
        $this->assertTrue($can_access);
    }
    
    /**
     * Test sharing permissions for specific section
     */
    public function test_sharing_permissions_specific_section() {
        // Create a user to share with
        $shared_user_id = $this->factory->user->create(array(
            'role' => 'financial_advisor',
            'user_login' => 'shared_advisor2',
            'user_email' => 'shared2@test.com'
        ));
        
        // Grant sharing permission for basic_personal only
        EPM_Test_Factory::create_sharing_permission($this->test_client_id, $shared_user_id, 'basic_personal', 'view');
        
        wp_set_current_user($shared_user_id);
        
        // Should have access to basic_personal
        $can_access_basic = EPM_Security::instance()->can_user_access_client_section($shared_user_id, $this->test_client_id, 'basic_personal');
        $this->assertTrue($can_access_basic);
        
        // Should NOT have access to family_contacts
        $can_access_family = EPM_Security::instance()->can_user_access_client_section($shared_user_id, $this->test_client_id, 'family_contacts');
        $this->assertFalse($can_access_family);
    }
    
    /**
     * Test edit permissions
     */
    public function test_edit_permissions() {
        // Create a user to share with
        $shared_user_id = $this->factory->user->create(array(
            'role' => 'financial_advisor',
            'user_login' => 'edit_advisor',
            'user_email' => 'edit@test.com'
        ));
        
        // Grant edit permission
        EPM_Test_Factory::create_sharing_permission($this->test_client_id, $shared_user_id, 'basic_personal', 'edit');
        
        wp_set_current_user($shared_user_id);
        
        $can_edit = EPM_Security::instance()->can_user_edit_client_section($shared_user_id, $this->test_client_id, 'basic_personal');
        
        $this->assertTrue($can_edit);
    }
    
    /**
     * Test view-only permissions cannot edit
     */
    public function test_view_only_cannot_edit() {
        // Create a user to share with
        $shared_user_id = $this->factory->user->create(array(
            'role' => 'financial_advisor',
            'user_login' => 'view_advisor',
            'user_email' => 'view@test.com'
        ));
        
        // Grant view-only permission
        EPM_Test_Factory::create_sharing_permission($this->test_client_id, $shared_user_id, 'basic_personal', 'view');
        
        wp_set_current_user($shared_user_id);
        
        $can_edit = EPM_Security::instance()->can_user_edit_client_section($shared_user_id, $this->test_client_id, 'basic_personal');
        
        $this->assertFalse($can_edit);
    }
    
    /**
     * Test data sanitization
     */
    public function test_data_sanitization() {
        $malicious_data = array(
            'name' => '<script>alert("xss")</script>John Doe',
            'email' => 'test@example.com<script>alert("xss")</script>',
            'phone' => '123-456-7890',
            'notes' => 'Some notes with <iframe src="javascript:alert(\'xss\')"></iframe>'
        );
        
        $sanitized = EPM_Security::instance()->sanitize_client_data($malicious_data);
        
        $this->assertStringNotContainsString('<script>', $sanitized['name']);
        $this->assertStringNotContainsString('<script>', $sanitized['email']);
        $this->assertStringNotContainsString('<iframe>', $sanitized['notes']);
        $this->assertEquals('123-456-7890', $sanitized['phone']); // Phone should be unchanged
    }
    
    /**
     * Test SQL injection prevention
     */
    public function test_sql_injection_prevention() {
        $malicious_input = "'; DROP TABLE wp_users; --";
        
        $sanitized = EPM_Security::instance()->sanitize_for_database($malicious_input);
        
        $this->assertStringNotContainsString('DROP TABLE', $sanitized);
        $this->assertStringNotContainsString(';', $sanitized);
        $this->assertStringNotContainsString('--', $sanitized);
    }
    
    /**
     * Test nonce verification
     */
    public function test_nonce_verification() {
        // Create a valid nonce
        $nonce = wp_create_nonce('epm_save_data');
        
        $is_valid = EPM_Security::instance()->verify_nonce($nonce, 'epm_save_data');
        
        $this->assertTrue($is_valid);
        
        // Test invalid nonce
        $invalid_nonce = 'invalid_nonce_value';
        $is_invalid = EPM_Security::instance()->verify_nonce($invalid_nonce, 'epm_save_data');
        
        $this->assertFalse($is_invalid);
    }
    
    /**
     * Test capability checks
     */
    public function test_capability_checks() {
        // Test client capabilities
        wp_set_current_user($this->client_user_id);
        
        $can_manage_own = EPM_Security::instance()->current_user_can('manage_own_estate_data');
        $this->assertTrue($can_manage_own);
        
        $can_manage_all = EPM_Security::instance()->current_user_can('manage_all_estate_data');
        $this->assertFalse($can_manage_all);
        
        // Test advisor capabilities
        wp_set_current_user($this->advisor_user_id);
        
        $can_manage_clients = EPM_Security::instance()->current_user_can('manage_client_estate_data');
        $this->assertTrue($can_manage_clients);
        
        // Test admin capabilities
        wp_set_current_user($this->admin_user_id);
        
        $can_manage_all = EPM_Security::instance()->current_user_can('manage_all_estate_data');
        $this->assertTrue($can_manage_all);
    }
    
    /**
     * Test rate limiting
     */
    public function test_rate_limiting() {
        $user_id = $this->client_user_id;
        $action = 'save_data';
        
        // First few attempts should be allowed
        for ($i = 0; $i < 5; $i++) {
            $is_allowed = EPM_Security::instance()->check_rate_limit($user_id, $action);
            $this->assertTrue($is_allowed);
        }
        
        // After rate limit is exceeded, should be blocked
        $is_blocked = EPM_Security::instance()->check_rate_limit($user_id, $action);
        $this->assertFalse($is_blocked);
    }
    
    /**
     * Test session security
     */
    public function test_session_security() {
        wp_set_current_user($this->client_user_id);
        
        // Test session validation
        $is_valid = EPM_Security::instance()->validate_user_session();
        $this->assertTrue($is_valid);
        
        // Test session regeneration
        $old_session_id = session_id();
        EPM_Security::instance()->regenerate_session();
        $new_session_id = session_id();
        
        $this->assertNotEquals($old_session_id, $new_session_id);
    }
    
    /**
     * Test file upload security
     */
    public function test_file_upload_security() {
        $allowed_file = array(
            'name' => 'document.pdf',
            'type' => 'application/pdf',
            'size' => 1024000, // 1MB
            'tmp_name' => '/tmp/phptest'
        );
        
        $is_allowed = EPM_Security::instance()->validate_file_upload($allowed_file);
        $this->assertTrue($is_allowed);
        
        $malicious_file = array(
            'name' => 'malicious.php',
            'type' => 'application/x-php',
            'size' => 1024,
            'tmp_name' => '/tmp/phptest2'
        );
        
        $is_blocked = EPM_Security::instance()->validate_file_upload($malicious_file);
        $this->assertFalse($is_blocked);
    }
    
    /**
     * Test encryption/decryption
     */
    public function test_encryption_decryption() {
        $sensitive_data = 'Social Insurance Number: 123-456-789';
        
        $encrypted = EPM_Security::instance()->encrypt_sensitive_data($sensitive_data);
        $this->assertNotEquals($sensitive_data, $encrypted);
        
        $decrypted = EPM_Security::instance()->decrypt_sensitive_data($encrypted);
        $this->assertEquals($sensitive_data, $decrypted);
    }
    
    /**
     * Test audit logging for security events
     */
    public function test_security_audit_logging() {
        wp_set_current_user($this->client_user_id);
        
        // Trigger a security event
        EPM_Security::instance()->log_security_event('unauthorized_access_attempt', array(
            'client_id' => $this->test_client_id,
            'section' => 'basic_personal'
        ));
        
        // Verify the event was logged
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_audit_log';
        
        $log_entry = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE user_id = %d AND action = %s ORDER BY created_at DESC LIMIT 1",
            $this->client_user_id,
            'unauthorized_access_attempt'
        ));
        
        $this->assertNotNull($log_entry);
        $this->assertEquals('unauthorized_access_attempt', $log_entry->action);
    }
}
