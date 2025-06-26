<?php
/**
 * Base test case for Estate Planning Manager tests
 */

class EPM_Test_Case extends WP_UnitTestCase {
    
    /**
     * Test user IDs
     */
    protected $client_user_id;
    protected $advisor_user_id;
    protected $admin_user_id;
    
    /**
     * Test client ID
     */
    protected $test_client_id;
    
    /**
     * Set up test environment
     */
    public function setUp(): void {
        parent::setUp();
        
        // Create test users
        $this->client_user_id = $this->factory->user->create(array(
            'role' => 'estate_client',
            'user_login' => 'test_client',
            'user_email' => 'client@test.com'
        ));
        
        $this->advisor_user_id = $this->factory->user->create(array(
            'role' => 'financial_advisor',
            'user_login' => 'test_advisor',
            'user_email' => 'advisor@test.com'
        ));
        
        $this->admin_user_id = $this->factory->user->create(array(
            'role' => 'administrator',
            'user_login' => 'test_admin',
            'user_email' => 'admin@test.com'
        ));
        
        // Create test client record
        $this->test_client_id = EPM_Database::instance()->create_client($this->client_user_id, $this->advisor_user_id);
        
        // Set current user
        wp_set_current_user($this->client_user_id);
    }
    
    /**
     * Clean up after tests
     */
    public function tearDown(): void {
        // Clean up test data
        $this->clean_test_data();
        
        parent::tearDown();
    }
    
    /**
     * Clean up test data
     */
    protected function clean_test_data() {
        global $wpdb;
        
        // Delete test client data
        if ($this->test_client_id) {
            $tables = array(
                'epm_clients', 'epm_basic_personal', 'epm_family_contacts',
                'epm_key_contacts', 'epm_wills_poa', 'epm_funeral_organ',
                'epm_taxes', 'epm_military_service', 'epm_employment',
                'epm_volunteer', 'epm_bank_accounts', 'epm_investments',
                'epm_real_estate', 'epm_personal_property', 'epm_digital_assets',
                'epm_scheduled_payments', 'epm_debtors_creditors', 'epm_insurance',
                'epm_sharing_permissions', 'epm_audit_log', 'epm_sync_log'
            );
            
            foreach ($tables as $table) {
                $table_name = $wpdb->prefix . $table;
                $wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE client_id = %d", $this->test_client_id));
            }
        }
    }
    
    /**
     * Create test client data
     */
    protected function create_test_client_data($section, $data = array()) {
        $default_data = $this->get_default_test_data($section);
        $test_data = array_merge($default_data, $data);
        
        return EPM_Database::instance()->save_client_data($this->test_client_id, $section, $test_data);
    }
    
    /**
     * Get default test data for sections
     */
    protected function get_default_test_data($section) {
        $data = array();
        
        switch ($section) {
            case 'basic_personal':
                $data = array(
                    'full_legal_name' => 'John Doe Test',
                    'date_of_birth' => '1980-01-01',
                    'place_of_birth' => 'Test City',
                    'sin' => '123456789'
                );
                break;
                
            case 'family_contacts':
                $data = array(
                    'name' => 'Jane Doe',
                    'relationship' => 'Spouse',
                    'phone' => '555-0123',
                    'email' => 'jane@test.com'
                );
                break;
                
            case 'bank_accounts':
                $data = array(
                    'bank' => 'Test Bank',
                    'account_type' => 'Checking',
                    'account_number' => '1234567890',
                    'branch' => 'Main Branch'
                );
                break;
                
            case 'investments':
                $data = array(
                    'investment_type' => 'RRSP',
                    'financial_company' => 'Test Investment Co',
                    'account_number' => 'INV123456',
                    'advisor' => 'Test Advisor'
                );
                break;
        }
        
        return $data;
    }
    
    /**
     * Assert database table exists
     */
    protected function assertTableExists($table_name) {
        global $wpdb;
        
        $full_table_name = $wpdb->prefix . $table_name;
        $result = $wpdb->get_var("SHOW TABLES LIKE '$full_table_name'");
        
        $this->assertEquals($full_table_name, $result, "Table $table_name should exist");
    }
    
    /**
     * Assert record exists in database
     */
    protected function assertRecordExists($table_name, $conditions) {
        global $wpdb;
        
        $full_table_name = $wpdb->prefix . $table_name;
        
        $where_clause = array();
        $values = array();
        
        foreach ($conditions as $column => $value) {
            $where_clause[] = "$column = %s";
            $values[] = $value;
        }
        
        $where_sql = implode(' AND ', $where_clause);
        $query = "SELECT COUNT(*) FROM $full_table_name WHERE $where_sql";
        
        $count = $wpdb->get_var($wpdb->prepare($query, $values));
        
        $this->assertGreaterThan(0, $count, "Record should exist in $table_name");
    }
    
    /**
     * Mock WordPress functions for testing
     */
    protected function mock_wp_functions() {
        // Mock functions that might not be available in test environment
        if (!function_exists('wp_upload_dir')) {
            function wp_upload_dir() {
                return array(
                    'basedir' => '/tmp/wp-uploads',
                    'baseurl' => 'http://test.com/wp-uploads'
                );
            }
        }
        
        if (!function_exists('wp_mkdir_p')) {
            function wp_mkdir_p($target) {
                return mkdir($target, 0755, true);
            }
        }
    }
}
