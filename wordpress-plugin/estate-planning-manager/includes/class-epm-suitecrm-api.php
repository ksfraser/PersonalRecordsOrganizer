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
    
    /**
     * Pull updates from SuiteCRM and create suggested updates
     */
    public function pull_suitecrm_updates($client_id) {
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
        
        // Get contact data from SuiteCRM
        $contact_response = $this->make_request('module/' . $suitecrm_contact_id);
        
        if (is_wp_error($contact_response)) {
            return $contact_response;
        }
        
        if (isset($contact_response['data']['attributes'])) {
            $this->process_contact_updates($client_id, $contact_response['data']['attributes']);
        }
        
        // Get related records (bank accounts, investments, etc.)
        $this->pull_related_records($client_id, $suitecrm_contact_id);
        
        return true;
    }
    
    /**
     * Process contact updates and create suggested updates
     */
    private function process_contact_updates($client_id, $suitecrm_data) {
        global $wpdb;
        
        // Get current WordPress data
        $wp_data = EPM_Database::instance()->get_client_data($client_id, 'basic_personal');
        $current_data = !empty($wp_data) ? $wp_data[0] : new stdClass();
        
        // Map SuiteCRM fields to WordPress fields
        $field_mapping = array(
            'first_name' => 'first_name',
            'last_name' => 'last_name',
            'email1' => 'email',
            'phone_work' => 'phone',
            'birthdate' => 'date_of_birth'
        );
        
        foreach ($field_mapping as $suitecrm_field => $wp_field) {
            if (isset($suitecrm_data[$suitecrm_field])) {
                $suggested_value = $suitecrm_data[$suitecrm_field];
                $current_value = isset($current_data->$wp_field) ? $current_data->$wp_field : '';
                
                // Only create suggestion if values are different
                if ($suggested_value !== $current_value) {
                    $this->create_suggested_update(
                        $client_id,
                        'basic_personal',
                        null,
                        $wp_field,
                        $current_value,
                        $suggested_value,
                        'suitecrm',
                        $suitecrm_data['id'] ?? null
                    );
                }
            }
        }
    }
    
    /**
     * Pull related records from SuiteCRM
     */
    private function pull_related_records($client_id, $suitecrm_contact_id) {
        // Pull bank accounts
        $this->pull_bank_accounts($client_id, $suitecrm_contact_id);
        
        // Pull investments
        $this->pull_investments($client_id, $suitecrm_contact_id);
        
        // Pull real estate
        $this->pull_real_estate($client_id, $suitecrm_contact_id);
        
        // Pull insurance
        $this->pull_insurance($client_id, $suitecrm_contact_id);
    }
    
    /**
     * Pull bank accounts from SuiteCRM
     */
    private function pull_bank_accounts($client_id, $suitecrm_contact_id) {
        $response = $this->make_request('module/EPM_BankAccounts?filter[contact_id]=' . $suitecrm_contact_id);
        
        if (is_wp_error($response) || !isset($response['data'])) {
            return;
        }
        
        foreach ($response['data'] as $bank_account) {
            $this->process_bank_account_update($client_id, $bank_account['attributes']);
        }
    }
    
    /**
     * Process bank account updates
     */
    private function process_bank_account_update($client_id, $suitecrm_data) {
        // Get current WordPress bank accounts
        $wp_accounts = EPM_Database::instance()->get_client_data($client_id, 'bank_accounts');
        
        // Try to match by bank name and account type
        $matched_account = null;
        foreach ($wp_accounts as $account) {
            if ($account->bank === $suitecrm_data['bank_name'] && 
                $account->account_type === $suitecrm_data['account_type']) {
                $matched_account = $account;
                break;
            }
        }
        
        if ($matched_account) {
            // Compare and create suggestions for differences
            $field_mapping = array(
                'bank_name' => 'bank',
                'account_type' => 'account_type',
                'branch' => 'branch'
            );
            
            foreach ($field_mapping as $suitecrm_field => $wp_field) {
                if (isset($suitecrm_data[$suitecrm_field])) {
                    $suggested_value = $suitecrm_data[$suitecrm_field];
                    $current_value = $matched_account->$wp_field ?? '';
                    
                    if ($suggested_value !== $current_value) {
                        $this->create_suggested_update(
                            $client_id,
                            'bank_accounts',
                            $matched_account->id,
                            $wp_field,
                            $current_value,
                            $suggested_value,
                            'suitecrm',
                            $suitecrm_data['id'] ?? null
                        );
                    }
                }
            }
        } else {
            // New bank account from SuiteCRM - suggest adding it
            $this->create_suggested_update(
                $client_id,
                'bank_accounts',
                null,
                'new_record',
                '',
                json_encode($suitecrm_data),
                'suitecrm',
                $suitecrm_data['id'] ?? null
            );
        }
    }
    
    /**
     * Pull investments from SuiteCRM
     */
    private function pull_investments($client_id, $suitecrm_contact_id) {
        $response = $this->make_request('module/EPM_Investments?filter[contact_id]=' . $suitecrm_contact_id);
        
        if (is_wp_error($response) || !isset($response['data'])) {
            return;
        }
        
        foreach ($response['data'] as $investment) {
            $this->process_investment_update($client_id, $investment['attributes']);
        }
    }
    
    /**
     * Process investment updates
     */
    private function process_investment_update($client_id, $suitecrm_data) {
        // Similar logic to bank accounts but for investments
        $wp_investments = EPM_Database::instance()->get_client_data($client_id, 'investments');
        
        $matched_investment = null;
        foreach ($wp_investments as $investment) {
            if ($investment->financial_company === $suitecrm_data['financial_company'] && 
                $investment->investment_type === $suitecrm_data['investment_type']) {
                $matched_investment = $investment;
                break;
            }
        }
        
        if ($matched_investment) {
            $field_mapping = array(
                'investment_type' => 'investment_type',
                'financial_company' => 'financial_company',
                'account_type' => 'account_type',
                'advisor' => 'advisor'
            );
            
            foreach ($field_mapping as $suitecrm_field => $wp_field) {
                if (isset($suitecrm_data[$suitecrm_field])) {
                    $suggested_value = $suitecrm_data[$suitecrm_field];
                    $current_value = $matched_investment->$wp_field ?? '';
                    
                    if ($suggested_value !== $current_value) {
                        $this->create_suggested_update(
                            $client_id,
                            'investments',
                            $matched_investment->id,
                            $wp_field,
                            $current_value,
                            $suggested_value,
                            'suitecrm',
                            $suitecrm_data['id'] ?? null
                        );
                    }
                }
            }
        } else {
            $this->create_suggested_update(
                $client_id,
                'investments',
                null,
                'new_record',
                '',
                json_encode($suitecrm_data),
                'suitecrm',
                $suitecrm_data['id'] ?? null
            );
        }
    }
    
    /**
     * Pull real estate from SuiteCRM
     */
    private function pull_real_estate($client_id, $suitecrm_contact_id) {
        $response = $this->make_request('module/EPM_RealEstate?filter[contact_id]=' . $suitecrm_contact_id);
        
        if (is_wp_error($response) || !isset($response['data'])) {
            return;
        }
        
        foreach ($response['data'] as $property) {
            $this->process_real_estate_update($client_id, $property['attributes']);
        }
    }
    
    /**
     * Process real estate updates
     */
    private function process_real_estate_update($client_id, $suitecrm_data) {
        $wp_properties = EPM_Database::instance()->get_client_data($client_id, 'real_estate');
        
        $matched_property = null;
        foreach ($wp_properties as $property) {
            if ($property->address === $suitecrm_data['address']) {
                $matched_property = $property;
                break;
            }
        }
        
        if ($matched_property) {
            $field_mapping = array(
                'property_type' => 'property_type',
                'address' => 'address',
                'title_held_by' => 'title_held_by',
                'has_mortgage' => 'has_mortgage',
                'mortgage_held_by' => 'mortgage_held_by'
            );
            
            foreach ($field_mapping as $suitecrm_field => $wp_field) {
                if (isset($suitecrm_data[$suitecrm_field])) {
                    $suggested_value = $suitecrm_data[$suitecrm_field];
                    $current_value = $matched_property->$wp_field ?? '';
                    
                    if ($suggested_value !== $current_value) {
                        $this->create_suggested_update(
                            $client_id,
                            'real_estate',
                            $matched_property->id,
                            $wp_field,
                            $current_value,
                            $suggested_value,
                            'suitecrm',
                            $suitecrm_data['id'] ?? null
                        );
                    }
                }
            }
        } else {
            $this->create_suggested_update(
                $client_id,
                'real_estate',
                null,
                'new_record',
                '',
                json_encode($suitecrm_data),
                'suitecrm',
                $suitecrm_data['id'] ?? null
            );
        }
    }
    
    /**
     * Pull insurance from SuiteCRM
     */
    private function pull_insurance($client_id, $suitecrm_contact_id) {
        $response = $this->make_request('module/EPM_Insurance?filter[contact_id]=' . $suitecrm_contact_id);
        
        if (is_wp_error($response) || !isset($response['data'])) {
            return;
        }
        
        foreach ($response['data'] as $insurance) {
            $this->process_insurance_update($client_id, $insurance['attributes']);
        }
    }
    
    /**
     * Process insurance updates
     */
    private function process_insurance_update($client_id, $suitecrm_data) {
        $wp_insurance = EPM_Database::instance()->get_client_data($client_id, 'insurance');
        
        $matched_insurance = null;
        foreach ($wp_insurance as $insurance) {
            if ($insurance->insurance_company === $suitecrm_data['insurance_company'] && 
                $insurance->insurance_type === $suitecrm_data['insurance_type']) {
                $matched_insurance = $insurance;
                break;
            }
        }
        
        if ($matched_insurance) {
            $field_mapping = array(
                'insurance_category' => 'insurance_category',
                'insurance_type' => 'insurance_type',
                'insurance_company' => 'insurance_company',
                'advisor' => 'advisor',
                'beneficiary' => 'beneficiary'
            );
            
            foreach ($field_mapping as $suitecrm_field => $wp_field) {
                if (isset($suitecrm_data[$suitecrm_field])) {
                    $suggested_value = $suitecrm_data[$suitecrm_field];
                    $current_value = $matched_insurance->$wp_field ?? '';
                    
                    if ($suggested_value !== $current_value) {
                        $this->create_suggested_update(
                            $client_id,
                            'insurance',
                            $matched_insurance->id,
                            $wp_field,
                            $current_value,
                            $suggested_value,
                            'suitecrm',
                            $suitecrm_data['id'] ?? null
                        );
                    }
                }
            }
        } else {
            $this->create_suggested_update(
                $client_id,
                'insurance',
                null,
                'new_record',
                '',
                json_encode($suitecrm_data),
                'suitecrm',
                $suitecrm_data['id'] ?? null
            );
        }
    }
    
    /**
     * Create a suggested update record
     */
    private function create_suggested_update($client_id, $section, $record_id, $field_name, $current_value, $suggested_value, $source = 'suitecrm', $source_record_id = null) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_suggested_updates';
        
        // Check if suggestion already exists
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table_name WHERE client_id = %d AND section = %s AND record_id = %s AND field_name = %s AND status = 'pending'",
            $client_id,
            $section,
            $record_id,
            $field_name
        ));
        
        if ($existing) {
            // Update existing suggestion
            $wpdb->update(
                $table_name,
                array(
                    'current_value' => $current_value,
                    'suggested_value' => $suggested_value,
                    'source_record_id' => $source_record_id
                ),
                array('id' => $existing),
                array('%s', '%s', '%s'),
                array('%d')
            );
        } else {
            // Create new suggestion
            $wpdb->insert(
                $table_name,
                array(
                    'client_id' => $client_id,
                    'section' => $section,
                    'record_id' => $record_id,
                    'field_name' => $field_name,
                    'current_value' => $current_value,
                    'suggested_value' => $suggested_value,
                    'source' => $source,
                    'source_record_id' => $source_record_id,
                    'status' => 'pending'
                ),
                array('%d', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s')
            );
        }
    }
    
    /**
     * Schedule regular sync from SuiteCRM
     */
    public function schedule_sync_from_suitecrm() {
        if (!wp_next_scheduled('epm_pull_suitecrm_updates')) {
            wp_schedule_event(time(), 'hourly', 'epm_pull_suitecrm_updates');
        }
    }
    
    /**
     * Pull updates for all clients
     */
    public function pull_all_client_updates() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_clients';
        
        $clients = $wpdb->get_results(
            "SELECT id FROM $table_name WHERE suitecrm_contact_id IS NOT NULL AND status = 'active'"
        );
        
        foreach ($clients as $client) {
            $this->pull_suitecrm_updates($client->id);
        }
    }
    
    /**
     * Search for a contact or lead by email in SuiteCRM
     */
    public function find_contact_or_lead_by_email($email) {
        // Search Contacts
        $contacts = $this->make_request('module/Contacts?filter[email1]=' . urlencode($email));
        if (!is_wp_error($contacts) && !empty($contacts['data'])) {
            return array('type' => 'Contacts', 'id' => $contacts['data'][0]['id']);
        }
        // Search Leads
        $leads = $this->make_request('module/Leads?filter[email1]=' . urlencode($email));
        if (!is_wp_error($leads) && !empty($leads['data'])) {
            return array('type' => 'Leads', 'id' => $leads['data'][0]['id']);
        }
        return false;
    }

    /**
     * Create a new lead in SuiteCRM
     */
    public function create_lead($email, $first_name = '', $last_name = '', $phone = '') {
        $lead_data = array(
            'type' => 'Leads',
            'attributes' => array(
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email1' => $email,
                'phone_work' => $phone,
                'lead_source' => 'WordPress Estate Planning Manager'
            )
        );
        $response = $this->make_request('module', 'POST', array('data' => $lead_data));
        if (!is_wp_error($response) && isset($response['data']['id'])) {
            return array('type' => 'Leads', 'id' => $response['data']['id']);
        }
        return false;
    }

    /**
     * Create or get SuiteCRM contact/lead for invited user
     */
    public function sync_invited_user($invitee_email, $first_name = '', $last_name = '', $phone = '') {
        $found = $this->find_contact_or_lead_by_email($invitee_email);
        if ($found) {
            return $found;
        }
        // Not found, create as lead
        return $this->create_lead($invitee_email, $first_name, $last_name, $phone);
    }

    /**
     * Create or update a custom module record with wp_record_id
     */
    public function sync_custom_module_record($module, $data, $suitecrm_guid = null, $wp_record_id = null, $contact_id = null) {
        $attributes = $data;
        if ($wp_record_id) {
            $attributes['wp_record_id'] = $wp_record_id;
        }
        if ($contact_id) {
            $attributes['contact_id'] = $contact_id;
        }
        $payload = array(
            'type' => $module,
            'attributes' => $attributes
        );
        if ($suitecrm_guid) {
            // Update
            $payload['id'] = $suitecrm_guid;
            $response = $this->make_request('module/' . $suitecrm_guid, 'PATCH', array('data' => $payload));
        } else {
            // Create
            $response = $this->make_request('module', 'POST', array('data' => $payload));
        }
        return $response;
    }

    /**
     * Fetch SuiteCRM GUID for a custom module record by wp_record_id
     */
    public function get_suitecrm_guid_by_wp_record_id($module, $wp_record_id) {
        $response = $this->make_request('module/' . $module . '?filter[wp_record_id]=' . urlencode($wp_record_id));
        if (!is_wp_error($response) && !empty($response['data'])) {
            return $response['data'][0]['id'];
        }
        return false;
    }

    /**
     * Handle SuiteCRM sync for any section (contacts, accounts, etc.)
     * @param array $sync_data
     */
    public function handle_suitecrm_sync($sync_data) {
        global $wpdb;
        $section = $sync_data['module'] ?? '';
        $source_record_id = $sync_data['data']['id'] ?? '';
        $wp_guid = $sync_data['data']['wp_guid'] ?? '';
        $client_id = $this->find_client_id_by_guid($source_record_id, $wp_guid);
        if (!$client_id) {
            // Optionally log or create new client
            return;
        }
        // Get current data for section
        $current_data = $this->get_client_section_data($client_id, $section);
        // Compare each field
        foreach ($sync_data['data'] as $field => $new_value) {
            if ($field === 'id' || $field === 'wp_guid') continue;
            $old_value = isset($current_data[$field]) ? $current_data[$field] : '';
            if ($new_value !== $old_value) {
                // Store both values as JSON for flexible display
                $wpdb->insert(
                    $wpdb->prefix . 'epm_suggested_updates',
                    array(
                        'client_id' => $client_id,
                        'section' => $section,
                        'field' => $field,
                        'old_value' => json_encode($old_value),
                        'new_value' => json_encode($new_value),
                        'notes' => '',
                        'status' => 'pending',
                        'created_at' => current_time('mysql'),
                        'updated_at' => current_time('mysql')
                    ),
                    array('%d','%s','%s','%s','%s','%s','%s','%s','%s')
                );
            }
        }
    }

    /**
     * Find client by SuiteCRM GUID or WordPress GUID
     */
    private function find_client_id_by_guid($suitecrm_guid, $wp_guid) {
        global $wpdb;
        $client_id = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$wpdb->prefix}epm_clients WHERE suitecrm_guid = %s",
            $suitecrm_guid
        ));
        if ($client_id) return $client_id;
        if ($wp_guid) {
            $client_id = $wpdb->get_var($wpdb->prepare(
                "SELECT id FROM {$wpdb->prefix}epm_clients WHERE wp_guid = %s",
                $wp_guid
            ));
            if ($client_id) return $client_id;
        }
        return null;
    }

    /**
     * Get current section data for client
     */
    private function get_client_section_data($client_id, $section) {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_' . $section;
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE client_id = %d", $client_id), ARRAY_A);
        return $row ? $row : array();
    }
}
