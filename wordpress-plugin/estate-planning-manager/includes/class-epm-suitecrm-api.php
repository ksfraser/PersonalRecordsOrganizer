<?php
/**
 * SuiteCRM API Integration Class
 * 
 * Handles all SuiteCRM API operations for Estate Planning Manager
 * Follows Single Responsibility Principle - only handles SuiteCRM API operations
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class EPM_SuiteCRM_API {
    
    /**
     * Instance of this class
     * @var EPM_SuiteCRM_API
     */
    private static $instance = null;
    
    /**
     * SuiteCRM base URL
     * @var string
     */
    private $base_url;
    
    /**
     * API credentials
     * @var array
     */
    private $credentials;
    
    /**
     * Access token
     * @var string
     */
    private $access_token;
    
    /**
     * Get instance (Singleton pattern)
     * @return EPM_SuiteCRM_API
     */
    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->base_url = get_option('epm_suitecrm_url', '');
        $this->credentials = array(
            'username' => get_option('epm_suitecrm_username', ''),
            'password' => get_option('epm_suitecrm_password', '')
        );
    }
    
    /**
     * Initialize SuiteCRM API operations
     */
    public function init() {
        add_action('epm_sync_client_data', array($this, 'sync_client_data'), 10, 3);
        add_action('epm_create_suitecrm_contact', array($this, 'create_contact'), 10, 2);
    }
    
    /**
     * Test API connection
     */
    public function test_connection() {
        if (empty($this->base_url) || empty($this->credentials['username'])) {
            return new WP_Error('missing_credentials', __('SuiteCRM credentials not configured.', 'estate-planning-manager'));
        }
        
        $token = $this->get_access_token();
        
        if (is_wp_error($token)) {
            return $token;
        }
        
        return true;
    }
    
    /**
     * Get access token
     */
    private function get_access_token() {
        if ($this->access_token) {
            return $this->access_token;
        }
        
        $url = trailingslashit($this->base_url) . 'Api/access_token';
        
        $body = array(
            'grant_type' => 'password',
            'client_id' => 'suitecrm_client',
            'client_secret' => '',
            'username' => $this->credentials['username'],
            'password' => $this->credentials['password'],
            'scope' => ''
        );
        
        $response = wp_remote_post($url, array(
            'body' => $body,
            'timeout' => 30,
            'headers' => array(
                'Content-Type' => 'application/x-www-form-urlencoded'
            )
        ));
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (isset($data['access_token'])) {
            $this->access_token = $data['access_token'];
            return $this->access_token;
        }
        
        return new WP_Error('auth_failed', __('Failed to authenticate with SuiteCRM.', 'estate-planning-manager'));
    }
    
    /**
     * Make API request
     */
    private function make_request($endpoint, $method = 'GET', $data = null) {
        $token = $this->get_access_token();
        
        if (is_wp_error($token)) {
            return $token;
        }
        
        $url = trailingslashit($this->base_url) . 'Api/V8/' . ltrim($endpoint, '/');
        
        $args = array(
            'method' => $method,
            'timeout' => 30,
            'headers' => array(
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json'
            )
        );
        
        if ($data && in_array($method, array('POST', 'PUT', 'PATCH'))) {
            $args['body'] = json_encode($data);
        }
        
        $response = wp_remote_request($url, $args);
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $body = wp_remote_retrieve_body($response);
        $decoded = json_decode($body, true);
        
        $status_code = wp_remote_retrieve_response_code($response);
        
        if ($status_code >= 400) {
            return new WP_Error('api_error', 
                isset($decoded['error']['message']) ? $decoded['error']['message'] : 'API request failed'
            );
        }
        
        return $decoded;
    }
    
    /**
     * Create contact in SuiteCRM
     */
    public function create_contact($client_id, $client_data) {
        if (!get_option('epm_suitecrm_enabled', false)) {
            return false;
        }
        
        $contact_data = array(
            'type' => 'Contacts',
            'attributes' => array(
                'first_name' => $client_data['first_name'] ?? '',
                'last_name' => $client_data['last_name'] ?? '',
                'email1' => $client_data['email'] ?? '',
                'phone_work' => $client_data['phone'] ?? '',
                'description' => 'Estate Planning Client',
                'lead_source' => 'WordPress Estate Planning Manager'
            )
        );
        
        $response = $this->make_request('module', 'POST', array('data' => $contact_data));
        
        if (is_wp_error($response)) {
            $this->log_sync_error($client_id, 'contact_creation', $response->get_error_message());
            return $response;
        }
        
        if (isset($response['data']['id'])) {
            // Update client record with SuiteCRM contact ID
            global $wpdb;
            $table_name = $wpdb->prefix . 'epm_clients';
            
            $wpdb->update(
                $table_name,
                array('suitecrm_contact_id' => $response['data']['id']),
                array('id' => $client_id),
                array('%s'),
                array('%d')
            );
            
            $this->log_sync_success($client_id, 'contact_creation', $response['data']['id']);
            
            return $response['data']['id'];
        }
        
        return false;
    }
    
    /**
     * Update contact in SuiteCRM
     */
    public function update_contact($suitecrm_contact_id, $client_data) {
        if (!get_option('epm_suitecrm_enabled', false)) {
            return false;
        }
        
        $contact_data = array(
            'type' => 'Contacts',
            'id' => $suitecrm_contact_id,
            'attributes' => array(
                'first_name' => $client_data['first_name'] ?? '',
                'last_name' => $client_data['last_name'] ?? '',
                'email1' => $client_data['email'] ?? '',
                'phone_work' => $client_data['phone'] ?? ''
            )
        );
        
        $response = $this->make_request('module/' . $suitecrm_contact_id, 'PATCH', array('data' => $contact_data));
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        return isset($response['data']['id']);
    }
    
    /**
     * Sync client data to SuiteCRM
     */
    public function sync_client_data($client_id, $section, $data) {
        if (!get_option('epm_suitecrm_enabled', false)) {
            return false;
        }
        
        // Get client's SuiteCRM contact ID
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_clients';
        
        $suitecrm_contact_id = $wpdb->get_var($wpdb->prepare(
            "SELECT suitecrm_contact_id FROM $table_name WHERE id = %d",
            $client_id
        ));
        
        if (!$suitecrm_contact_id) {
            return new WP_Error('no_contact_id', __('No SuiteCRM contact ID found for client.', 'estate-planning-manager'));
        }
        
        // Map section data to SuiteCRM modules
        $sync_result = false;
        
        switch ($section) {
            case 'basic_personal':
                $sync_result = $this->sync_basic_personal($suitecrm_contact_id, $data);
                break;
            case 'bank_accounts':
                $sync_result = $this->sync_bank_accounts($suitecrm_contact_id, $data);
                break;
            case 'investments':
                $sync_result = $this->sync_investments($suitecrm_contact_id, $data);
                break;
            case 'real_estate':
                $sync_result = $this->sync_real_estate($suitecrm_contact_id, $data);
                break;
            case 'insurance':
                $sync_result = $this->sync_insurance($suitecrm_contact_id, $data);
                break;
            default:
                // For other sections, create notes
                $sync_result = $this->create_note($suitecrm_contact_id, $section, $data);
                break;
        }
        
        if (is_wp_error($sync_result)) {
            $this->log_sync_error($client_id, $section, $sync_result->get_error_message());
        } else {
            $this->log_sync_success($client_id, $section, $suitecrm_contact_id);
        }
        
        return $sync_result;
    }
    
    /**
     * Sync basic personal information
     */
    private function sync_basic_personal($suitecrm_contact_id, $data) {
        $contact_data = array(
            'type' => 'Contacts',
            'id' => $suitecrm_contact_id,
            'attributes' => array()
        );
        
        if (isset($data['full_legal_name'])) {
            $name_parts = explode(' ', $data['full_legal_name'], 2);
            $contact_data['attributes']['first_name'] = $name_parts[0];
            $contact_data['attributes']['last_name'] = isset($name_parts[1]) ? $name_parts[1] : '';
        }
        
        if (isset($data['date_of_birth'])) {
            $contact_data['attributes']['birthdate'] = $data['date_of_birth'];
        }
        
        return $this->make_request('module/' . $suitecrm_contact_id, 'PATCH', array('data' => $contact_data));
    }
    
    /**
     * Sync bank accounts
     */
    private function sync_bank_accounts($suitecrm_contact_id, $data) {
        // Create custom module record for bank accounts
        $account_data = array(
            'type' => 'EPM_BankAccounts',
            'attributes' => array(
                'name' => ($data['bank'] ?? '') . ' - ' . ($data['account_type'] ?? ''),
                'bank_name' => $data['bank'] ?? '',
                'account_type' => $data['account_type'] ?? '',
                'account_number' => EPM_Security::instance()->encrypt_data($data['account_number'] ?? ''),
                'branch' => $data['branch'] ?? '',
                'contact_id' => $suitecrm_contact_id
            )
        );
        
        return $this->make_request('module', 'POST', array('data' => $account_data));
    }
    
    /**
     * Sync investments
     */
    private function sync_investments($suitecrm_contact_id, $data) {
        $investment_data = array(
            'type' => 'EPM_Investments',
            'attributes' => array(
                'name' => ($data['financial_company'] ?? '') . ' - ' . ($data['account_type'] ?? ''),
                'investment_type' => $data['investment_type'] ?? '',
                'financial_company' => $data['financial_company'] ?? '',
                'account_type' => $data['account_type'] ?? '',
                'account_number' => EPM_Security::instance()->encrypt_data($data['account_number'] ?? ''),
                'advisor' => $data['advisor'] ?? '',
                'contact_id' => $suitecrm_contact_id
            )
        );
        
        return $this->make_request('module', 'POST', array('data' => $investment_data));
    }
    
    /**
     * Sync real estate
     */
    private function sync_real_estate($suitecrm_contact_id, $data) {
        $property_data = array(
            'type' => 'EPM_RealEstate',
            'attributes' => array(
                'name' => ($data['property_type'] ?? '') . ' - ' . ($data['address'] ?? ''),
                'property_type' => $data['property_type'] ?? '',
                'address' => $data['address'] ?? '',
                'title_held_by' => $data['title_held_by'] ?? '',
                'has_mortgage' => $data['has_mortgage'] ?? '',
                'mortgage_held_by' => $data['mortgage_held_by'] ?? '',
                'contact_id' => $suitecrm_contact_id
            )
        );
        
        return $this->make_request('module', 'POST', array('data' => $property_data));
    }
    
    /**
     * Sync insurance
     */
    private function sync_insurance($suitecrm_contact_id, $data) {
        $insurance_data = array(
            'type' => 'EPM_Insurance',
            'attributes' => array(
                'name' => ($data['insurance_type'] ?? '') . ' - ' . ($data['insurance_company'] ?? ''),
                'insurance_category' => $data['insurance_category'] ?? '',
                'insurance_type' => $data['insurance_type'] ?? '',
                'insurance_company' => $data['insurance_company'] ?? '',
                'policy_number' => EPM_Security::instance()->encrypt_data($data['policy_number'] ?? ''),
                'advisor' => $data['advisor'] ?? '',
                'beneficiary' => $data['beneficiary'] ?? '',
                'contact_id' => $suitecrm_contact_id
            )
        );
        
        return $this->make_request('module', 'POST', array('data' => $insurance_data));
    }
    
    /**
     * Create note for other sections
     */
    private function create_note($suitecrm_contact_id, $section, $data) {
        $note_content = $this->format_data_for_note($section, $data);
        
        $note_data = array(
            'type' => 'Notes',
            'attributes' => array(
                'name' => ucwords(str_replace('_', ' ', $section)) . ' Information',
                'description' => $note_content,
                'parent_type' => 'Contacts',
                'parent_id' => $suitecrm_contact_id
            )
        );
        
        return $this->make_request('module', 'POST', array('data' => $note_data));
    }
    
    /**
     * Format data for note
     */
    private function format_data_for_note($section, $data) {
        $content = "Estate Planning - " . ucwords(str_replace('_', ' ', $section)) . "\n\n";
        
        foreach ($data as $key => $value) {
            if (!empty($value)) {
                $label = ucwords(str_replace('_', ' ', $key));
                $content .= $label . ": " . $value . "\n";
            }
        }
        
        return $content;
    }
    
    /**
     * Log sync success
     */
    private function log_sync_success($client_id, $section, $suitecrm_record_id) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_sync_log';
        
        $wpdb->insert(
            $table_name,
            array(
                'client_id' => $client_id,
                'section' => $section,
                'sync_direction' => 'wp_to_suitecrm',
                'status' => 'success',
                'suitecrm_record_id' => $suitecrm_record_id
            ),
            array('%d', '%s', '%s', '%s', '%s')
        );
    }
    
    /**
     * Log sync error
     */
    private function log_sync_error($client_id, $section, $error_message) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_sync_log';
        
        $wpdb->insert(
            $table_name,
            array(
                'client_id' => $client_id,
                'section' => $section,
                'sync_direction' => 'wp_to_suitecrm',
                'status' => 'error',
                'error_message' => $error_message
            ),
            array('%d', '%s', '%s', '%s', '%s')
        );
    }
    
    /**
     * Get sync status for client
     */
    public function get_sync_status($client_id) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_sync_log';
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name WHERE client_id = %d ORDER BY created_at DESC LIMIT 10",
            $client_id
        ));
    }
    
    /**
     * Trigger sync for client data
     */
    public function trigger_sync($client_id, $section, $data) {
        if (get_option('epm_auto_sync', true)) {
            wp_schedule_single_event(time(), 'epm_sync_client_data', array($client_id, $section, $data));
        }
    }
}
