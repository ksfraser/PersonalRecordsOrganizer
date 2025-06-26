<?php
/**
 * Test factory for creating test data
 */

class EPM_Test_Factory {
    
    /**
     * Create test client data
     */
    public static function create_client_data($client_id, $section, $overrides = array()) {
        $default_data = self::get_default_data($section);
        $data = array_merge($default_data, $overrides);
        
        return EPM_Database::instance()->save_client_data($client_id, $section, $data);
    }
    
    /**
     * Get default test data for each section
     */
    public static function get_default_data($section) {
        $data = array();
        
        switch ($section) {
            case 'basic_personal':
                $data = array(
                    'full_legal_name' => 'John Test Doe',
                    'date_of_birth' => '1980-05-15',
                    'place_of_birth' => 'Toronto, ON',
                    'birth_certificate_location' => 'Safe deposit box',
                    'sin' => '123456789',
                    'sin_card_location' => 'Home safe',
                    'citizenship_countries' => 'Canada',
                    'citizenship_papers_location' => 'Filing cabinet',
                    'passports_location' => 'Home safe',
                    'drivers_license_location' => 'Wallet',
                    'marriage_certificate_location' => 'Safe deposit box'
                );
                break;
                
            case 'family_contacts':
                $data = array(
                    'name' => 'Jane Test Doe',
                    'relationship' => 'Spouse',
                    'address' => '123 Test Street, Toronto, ON M1M 1M1',
                    'phone' => '416-555-0123',
                    'email' => 'jane.doe@test.com'
                );
                break;
                
            case 'key_contacts':
                $data = array(
                    'contact_type' => 'Lawyer',
                    'name' => 'Test Law Firm',
                    'relationship' => 'Estate Lawyer',
                    'address' => '456 Legal Ave, Toronto, ON M2M 2M2',
                    'phone' => '416-555-0456',
                    'email' => 'lawyer@testlaw.com'
                );
                break;
                
            case 'wills_poa':
                $data = array(
                    'document_type' => 'Will',
                    'has_document' => 'Yes',
                    'document_date' => '2023-01-15',
                    'original_location' => 'Lawyer office',
                    'copies_location' => 'Home safe',
                    'document_type_detail' => 'Last Will and Testament',
                    'legal_representative' => 'Test Law Firm',
                    'representative_email' => 'lawyer@testlaw.com',
                    'representative_phone' => '416-555-0456'
                );
                break;
                
            case 'bank_accounts':
                $data = array(
                    'bank' => 'Test Bank of Canada',
                    'account_type' => 'Checking',
                    'account_number' => '1234567890',
                    'branch' => 'Main Branch',
                    'address' => '789 Bank St, Toronto, ON M3M 3M3',
                    'phone' => '416-555-0789',
                    'email' => 'service@testbank.com'
                );
                break;
                
            case 'investments':
                $data = array(
                    'investment_type' => 'RRSP',
                    'financial_company' => 'Test Investment Corp',
                    'account_type' => 'Retirement Savings',
                    'account_number' => 'RRSP123456',
                    'address' => '321 Investment Blvd, Toronto, ON M4M 4M4',
                    'phone' => '416-555-0321',
                    'email' => 'service@testinvest.com',
                    'advisor' => 'John Investment Advisor',
                    'advisor_email' => 'john@testinvest.com'
                );
                break;
                
            case 'real_estate':
                $data = array(
                    'property_type' => 'Primary Residence',
                    'title_held_by' => 'Joint Tenancy',
                    'address' => '123 Test Street, Toronto, ON M1M 1M1',
                    'has_mortgage' => 'Yes',
                    'mortgage_held_by' => 'Test Mortgage Corp',
                    'lender_address' => '654 Mortgage Ave, Toronto, ON M5M 5M5',
                    'lender_phone' => '416-555-0654',
                    'lender_email' => 'service@testmortgage.com'
                );
                break;
                
            case 'insurance':
                $data = array(
                    'insurance_category' => 'Life Insurance',
                    'insurance_type' => 'Term Life',
                    'advisor' => 'Test Insurance Advisor',
                    'insurance_company' => 'Test Life Insurance Co',
                    'policy_number' => 'LIFE123456',
                    'address' => '987 Insurance Way, Toronto, ON M6M 6M6',
                    'phone' => '416-555-0987',
                    'email' => 'service@testlife.com',
                    'beneficiary' => 'Jane Test Doe',
                    'beneficiary_phone' => '416-555-0123',
                    'beneficiary_email' => 'jane.doe@test.com'
                );
                break;
        }
        
        return $data;
    }
    
    /**
     * Create multiple test records
     */
    public static function create_multiple_records($client_id, $section, $count = 3) {
        $records = array();
        
        for ($i = 1; $i <= $count; $i++) {
            $data = self::get_default_data($section);
            
            // Modify data to make it unique
            foreach ($data as $key => $value) {
                if (is_string($value) && strpos($value, 'Test') !== false) {
                    $data[$key] = str_replace('Test', "Test$i", $value);
                }
            }
            
            $result = EPM_Database::instance()->save_client_data($client_id, $section, $data);
            if ($result) {
                $records[] = $data;
            }
        }
        
        return $records;
    }
    
    /**
     * Create test sharing permissions
     */
    public static function create_sharing_permission($client_id, $shared_with_user_id, $section, $permission_level = 'view') {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_sharing_permissions';
        
        return $wpdb->insert(
            $table_name,
            array(
                'client_id' => $client_id,
                'shared_with_user_id' => $shared_with_user_id,
                'section' => $section,
                'permission_level' => $permission_level
            ),
            array('%d', '%d', '%s', '%s')
        );
    }
    
    /**
     * Create test audit log entry
     */
    public static function create_audit_log($user_id, $client_id, $action, $section = null) {
        return EPM_Audit_Logger::instance()->log_action(
            $user_id,
            $action,
            $section,
            null,
            array('test_data' => 'test_value')
        );
    }
}
