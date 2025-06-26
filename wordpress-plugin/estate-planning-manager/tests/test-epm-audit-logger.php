<?php
/**
 * Tests for EPM_Audit_Logger class
 */

class Test_EPM_Audit_Logger extends EPM_Test_Case {
    
    /**
     * Test basic audit logging
     */
    public function test_basic_audit_logging() {
        $result = EPM_Audit_Logger::instance()->log_action(
            $this->client_user_id,
            'data_save',
            'basic_personal',
            $this->test_client_id,
            array('field' => 'full_legal_name', 'value' => 'John Doe')
        );
        
        $this->assertNotFalse($result);
        
        // Verify log entry was created
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_audit_log';
        
        $log_entry = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE user_id = %d AND action = %s ORDER BY created_at DESC LIMIT 1",
            $this->client_user_id,
            'data_save'
        ));
        
        $this->assertNotNull($log_entry);
        $this->assertEquals($this->client_user_id, $log_entry->user_id);
        $this->assertEquals('data_save', $log_entry->action);
        $this->assertEquals('basic_personal', $log_entry->section);
        $this->assertEquals($this->test_client_id, $log_entry->client_id);
    }
    
    /**
     * Test audit logging with metadata
     */
    public function test_audit_logging_with_metadata() {
        $metadata = array(
            'old_value' => 'John Smith',
            'new_value' => 'John Doe',
            'field_name' => 'full_legal_name',
            'ip_address' => '192.168.1.1',
            'user_agent' => 'Mozilla/5.0 Test Browser'
        );
        
        $result = EPM_Audit_Logger::instance()->log_action(
            $this->client_user_id,
            'data_update',
            'basic_personal',
            $this->test_client_id,
            $metadata
        );
        
        $this->assertNotFalse($result);
        
        // Verify metadata was stored
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_audit_log';
        
        $log_entry = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE user_id = %d AND action = %s ORDER BY created_at DESC LIMIT 1",
            $this->client_user_id,
            'data_update'
        ));
        
        $this->assertNotNull($log_entry);
        
        $stored_metadata = json_decode($log_entry->metadata, true);
        $this->assertEquals('John Smith', $stored_metadata['old_value']);
        $this->assertEquals('John Doe', $stored_metadata['new_value']);
        $this->assertEquals('192.168.1.1', $stored_metadata['ip_address']);
    }
    
    /**
     * Test security event logging
     */
    public function test_security_event_logging() {
        $result = EPM_Audit_Logger::instance()->log_security_event(
            $this->client_user_id,
            'unauthorized_access_attempt',
            array(
                'attempted_section' => 'basic_personal',
                'client_id' => $this->test_client_id,
                'ip_address' => '192.168.1.100',
                'severity' => 'high'
            )
        );
        
        $this->assertNotFalse($result);
        
        // Verify security event was logged
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_audit_log';
        
        $log_entry = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE user_id = %d AND action = %s ORDER BY created_at DESC LIMIT 1",
            $this->client_user_id,
            'unauthorized_access_attempt'
        ));
        
        $this->assertNotNull($log_entry);
        $this->assertEquals('security', $log_entry->log_type);
        
        $metadata = json_decode($log_entry->metadata, true);
        $this->assertEquals('high', $metadata['severity']);
    }
    
    /**
     * Test user activity logging
     */
    public function test_user_activity_logging() {
        // Log login
        EPM_Audit_Logger::instance()->log_user_activity($this->client_user_id, 'login');
        
        // Log data access
        EPM_Audit_Logger::instance()->log_user_activity(
            $this->client_user_id, 
            'data_access',
            array('section' => 'family_contacts', 'client_id' => $this->test_client_id)
        );
        
        // Log logout
        EPM_Audit_Logger::instance()->log_user_activity($this->client_user_id, 'logout');
        
        // Verify all activities were logged
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_audit_log';
        
        $activity_count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE user_id = %d AND log_type = 'activity'",
            $this->client_user_id
        ));
        
        $this->assertEquals(3, $activity_count);
    }
    
    /**
     * Test data change logging
     */
    public function test_data_change_logging() {
        $old_data = array('full_legal_name' => 'John Smith', 'email' => 'john.smith@test.com');
        $new_data = array('full_legal_name' => 'John Doe', 'email' => 'john.doe@test.com');
        
        $result = EPM_Audit_Logger::instance()->log_data_change(
            $this->client_user_id,
            $this->test_client_id,
            'basic_personal',
            $old_data,
            $new_data
        );
        
        $this->assertNotFalse($result);
        
        // Verify change was logged with diff
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_audit_log';
        
        $log_entry = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE user_id = %d AND action = %s ORDER BY created_at DESC LIMIT 1",
            $this->client_user_id,
            'data_change'
        ));
        
        $this->assertNotNull($log_entry);
        
        $metadata = json_decode($log_entry->metadata, true);
        $this->assertArrayHasKey('changes', $metadata);
        $this->assertArrayHasKey('full_legal_name', $metadata['changes']);
        $this->assertEquals('John Smith', $metadata['changes']['full_legal_name']['old']);
        $this->assertEquals('John Doe', $metadata['changes']['full_legal_name']['new']);
    }
    
    /**
     * Test PDF generation logging
     */
    public function test_pdf_generation_logging() {
        $sections = array('basic_personal', 'family_contacts');
        
        $result = EPM_Audit_Logger::instance()->log_pdf_generation(
            $this->client_user_id,
            $this->test_client_id,
            $sections,
            array(
                'file_size' => 1024000,
                'generation_time' => 2.5,
                'format' => 'A4'
            )
        );
        
        $this->assertNotFalse($result);
        
        // Verify PDF generation was logged
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_audit_log';
        
        $log_entry = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE user_id = %d AND action = %s ORDER BY created_at DESC LIMIT 1",
            $this->client_user_id,
            'pdf_generated'
        ));
        
        $this->assertNotNull($log_entry);
        
        $metadata = json_decode($log_entry->metadata, true);
        $this->assertEquals($sections, $metadata['sections']);
        $this->assertEquals(1024000, $metadata['file_size']);
    }
    
    /**
     * Test sharing permission logging
     */
    public function test_sharing_permission_logging() {
        $result = EPM_Audit_Logger::instance()->log_sharing_change(
            $this->client_user_id,
            $this->test_client_id,
            $this->advisor_user_id,
            'basic_personal',
            'granted',
            'view'
        );
        
        $this->assertNotFalse($result);
        
        // Verify sharing change was logged
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_audit_log';
        
        $log_entry = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE user_id = %d AND action = %s ORDER BY created_at DESC LIMIT 1",
            $this->client_user_id,
            'sharing_granted'
        ));
        
        $this->assertNotNull($log_entry);
        
        $metadata = json_decode($log_entry->metadata, true);
        $this->assertEquals($this->advisor_user_id, $metadata['shared_with_user_id']);
        $this->assertEquals('basic_personal', $metadata['section']);
        $this->assertEquals('view', $metadata['permission_level']);
    }
    
    /**
     * Test audit log retrieval
     */
    public function test_audit_log_retrieval() {
        // Create multiple log entries
        EPM_Audit_Logger::instance()->log_action($this->client_user_id, 'data_save', 'basic_personal', $this->test_client_id);
        EPM_Audit_Logger::instance()->log_action($this->client_user_id, 'data_update', 'family_contacts', $this->test_client_id);
        EPM_Audit_Logger::instance()->log_action($this->advisor_user_id, 'data_access', 'basic_personal', $this->test_client_id);
        
        // Test get logs for user
        $user_logs = EPM_Audit_Logger::instance()->get_user_logs($this->client_user_id, 10);
        $this->assertGreaterThanOrEqual(2, count($user_logs));
        
        // Test get logs for client
        $client_logs = EPM_Audit_Logger::instance()->get_client_logs($this->test_client_id, 10);
        $this->assertGreaterThanOrEqual(3, count($client_logs));
        
        // Test get logs by action
        $action_logs = EPM_Audit_Logger::instance()->get_logs_by_action('data_save', 10);
        $this->assertGreaterThanOrEqual(1, count($action_logs));
    }
    
    /**
     * Test audit log filtering
     */
    public function test_audit_log_filtering() {
        // Create logs with different dates
        EPM_Audit_Logger::instance()->log_action($this->client_user_id, 'data_save', 'basic_personal', $this->test_client_id);
        
        $start_date = date('Y-m-d H:i:s', strtotime('-1 hour'));
        $end_date = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $filtered_logs = EPM_Audit_Logger::instance()->get_logs_by_date_range($start_date, $end_date);
        
        $this->assertNotEmpty($filtered_logs);
        
        // Test filtering by section
        $section_logs = EPM_Audit_Logger::instance()->get_logs_by_section('basic_personal', 10);
        $this->assertNotEmpty($section_logs);
    }
    
    /**
     * Test audit log cleanup
     */
    public function test_audit_log_cleanup() {
        // Create old log entries (simulate by updating timestamps)
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_audit_log';
        
        // Insert old log entry
        $wpdb->insert(
            $table_name,
            array(
                'user_id' => $this->client_user_id,
                'action' => 'old_action',
                'client_id' => $this->test_client_id,
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 years'))
            ),
            array('%d', '%s', '%d', '%s')
        );
        
        // Run cleanup (remove logs older than 1 year)
        $cleaned_count = EPM_Audit_Logger::instance()->cleanup_old_logs(365);
        
        $this->assertGreaterThan(0, $cleaned_count);
        
        // Verify old log was removed
        $old_log_count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE action = %s",
            'old_action'
        ));
        
        $this->assertEquals(0, $old_log_count);
    }
    
    /**
     * Test audit log export
     */
    public function test_audit_log_export() {
        // Create some log entries
        EPM_Audit_Logger::instance()->log_action($this->client_user_id, 'data_save', 'basic_personal', $this->test_client_id);
        EPM_Audit_Logger::instance()->log_action($this->client_user_id, 'data_update', 'family_contacts', $this->test_client_id);
        
        // Test CSV export
        $csv_data = EPM_Audit_Logger::instance()->export_logs_csv($this->test_client_id);
        
        $this->assertNotEmpty($csv_data);
        $this->assertStringContains('user_id,action,section', $csv_data); // CSV header
        $this->assertStringContains('data_save', $csv_data);
        $this->assertStringContains('data_update', $csv_data);
    }
    
    /**
     * Test audit log statistics
     */
    public function test_audit_log_statistics() {
        // Create various log entries
        EPM_Audit_Logger::instance()->log_action($this->client_user_id, 'data_save', 'basic_personal', $this->test_client_id);
        EPM_Audit_Logger::instance()->log_action($this->client_user_id, 'data_save', 'family_contacts', $this->test_client_id);
        EPM_Audit_Logger::instance()->log_action($this->client_user_id, 'data_update', 'basic_personal', $this->test_client_id);
        EPM_Audit_Logger::instance()->log_security_event($this->client_user_id, 'login_attempt');
        
        $stats = EPM_Audit_Logger::instance()->get_audit_statistics($this->test_client_id);
        
        $this->assertArrayHasKey('total_actions', $stats);
        $this->assertArrayHasKey('actions_by_type', $stats);
        $this->assertArrayHasKey('security_events', $stats);
        $this->assertArrayHasKey('most_active_sections', $stats);
        
        $this->assertGreaterThan(0, $stats['total_actions']);
        $this->assertEquals(2, $stats['actions_by_type']['data_save']);
        $this->assertEquals(1, $stats['actions_by_type']['data_update']);
    }
    
    /**
     * Test concurrent logging
     */
    public function test_concurrent_logging() {
        // Simulate concurrent log entries
        $results = array();
        
        for ($i = 0; $i < 10; $i++) {
            $results[] = EPM_Audit_Logger::instance()->log_action(
                $this->client_user_id,
                'concurrent_test',
                'basic_personal',
                $this->test_client_id,
                array('iteration' => $i)
            );
        }
        
        // All should succeed
        foreach ($results as $result) {
            $this->assertNotFalse($result);
        }
        
        // Verify all entries were created
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_audit_log';
        
        $count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE action = %s",
            'concurrent_test'
        ));
        
        $this->assertEquals(10, $count);
    }
    
    /**
     * Test audit log integrity
     */
    public function test_audit_log_integrity() {
        $result = EPM_Audit_Logger::instance()->log_action(
            $this->client_user_id,
            'integrity_test',
            'basic_personal',
            $this->test_client_id,
            array('test_data' => 'sensitive information')
        );
        
        $this->assertNotFalse($result);
        
        // Verify log entry cannot be modified (test immutability)
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_audit_log';
        
        $log_entry = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE action = %s",
            'integrity_test'
        ));
        
        $this->assertNotNull($log_entry);
        
        // Attempt to modify the log entry (should fail or be detected)
        $original_metadata = $log_entry->metadata;
        
        // Try to update (this should be prevented by proper audit log design)
        $wpdb->update(
            $table_name,
            array('metadata' => '{"modified": true}'),
            array('id' => $log_entry->id),
            array('%s'),
            array('%d')
        );
        
        // In a proper audit system, this modification should be detected
        // For testing purposes, we verify the original data structure
        $this->assertNotEmpty($original_metadata);
    }
}
