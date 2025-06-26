<?php
/**
 * Security Management Class
 * 
 * Handles all security operations for Estate Planning Manager
 * Follows Single Responsibility Principle - only handles security operations
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class EPM_Security {
    
    /**
     * Instance of this class
     * @var EPM_Security
     */
    private static $instance = null;
    
    /**
     * Encryption key
     * @var string
     */
    private $encryption_key;
    
    /**
     * Get instance (Singleton pattern)
     * @return EPM_Security
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
        $this->encryption_key = $this->get_encryption_key();
    }
    
    /**
     * Initialize security operations
     */
    public function init() {
        add_action('init', array($this, 'setup_security_headers'));
        add_filter('wp_authenticate_user', array($this, 'check_user_permissions'), 10, 2);
        add_action('wp_login', array($this, 'log_user_login'), 10, 2);
        add_action('wp_logout', array($this, 'log_user_logout'));
    }
    
    /**
     * Setup security headers
     */
    public function setup_security_headers() {
        if (!headers_sent()) {
            header('X-Content-Type-Options: nosniff');
            header('X-Frame-Options: SAMEORIGIN');
            header('X-XSS-Protection: 1; mode=block');
        }
    }
    
    /**
     * Check user permissions for estate planning access
     */
    public function check_user_permissions($user, $password) {
        if (is_wp_error($user)) {
            return $user;
        }
        
        // Additional security checks can be added here
        return $user;
    }
    
    /**
     * Log user login
     */
    public function log_user_login($user_login, $user) {
        if (in_array('estate_client', $user->roles) || in_array('financial_advisor', $user->roles)) {
            EPM_Audit_Logger::instance()->log_action(
                $user->ID,
                'user_login',
                'authentication',
                null,
                array('user_login' => $user_login)
            );
        }
    }
    
    /**
     * Log user logout
     */
    public function log_user_logout() {
        $user = wp_get_current_user();
        if ($user && (in_array('estate_client', $user->roles) || in_array('financial_advisor', $user->roles))) {
            EPM_Audit_Logger::instance()->log_action(
                $user->ID,
                'user_logout',
                'authentication'
            );
        }
    }
    
    /**
     * Get or generate encryption key
     */
    private function get_encryption_key() {
        $key = get_option('epm_encryption_key');
        
        if (!$key) {
            $key = wp_generate_password(64, true, true);
            update_option('epm_encryption_key', $key);
        }
        
        return $key;
    }
    
    /**
     * Encrypt sensitive data
     */
    public function encrypt_data($data) {
        if (!get_option('epm_encryption_enabled', true)) {
            return $data;
        }
        
        if (empty($data)) {
            return $data;
        }
        
        $method = 'AES-256-CBC';
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($method));
        $encrypted = openssl_encrypt($data, $method, $this->encryption_key, 0, $iv);
        
        return base64_encode($encrypted . '::' . $iv);
    }
    
    /**
     * Decrypt sensitive data
     */
    public function decrypt_data($encrypted_data) {
        if (!get_option('epm_encryption_enabled', true)) {
            return $encrypted_data;
        }
        
        if (empty($encrypted_data)) {
            return $encrypted_data;
        }
        
        $data = base64_decode($encrypted_data);
        $parts = explode('::', $data, 2);
        
        if (count($parts) !== 2) {
            return $encrypted_data; // Return original if not encrypted
        }
        
        list($encrypted, $iv) = $parts;
        $method = 'AES-256-CBC';
        
        return openssl_decrypt($encrypted, $method, $this->encryption_key, 0, $iv);
    }
    
    /**
     * Hash sensitive data (one-way)
     */
    public function hash_data($data) {
        return wp_hash($data);
    }
    
    /**
     * Verify nonce for AJAX requests
     */
    public function verify_nonce($nonce, $action) {
        return wp_verify_nonce($nonce, $action);
    }
    
    /**
     * Check if user can access client data
     */
    public function can_user_access_client_data($user_id, $client_id, $section = null) {
        $user = get_user_by('ID', $user_id);
        
        if (!$user) {
            return false;
        }
        
        // Get client record
        $client_user_id = EPM_Database::instance()->get_client_id_by_user_id($client_id);
        
        // Owner can always access their own data
        if ($user_id == $client_user_id) {
            return true;
        }
        
        // Financial advisors can access their assigned clients
        if (in_array('financial_advisor', $user->roles)) {
            return $this->is_advisor_assigned_to_client($user_id, $client_id);
        }
        
        // Check sharing permissions
        if ($section) {
            return $this->has_sharing_permission($user_id, $client_id, $section);
        }
        
        return false;
    }
    
    /**
     * Check if advisor is assigned to client
     */
    private function is_advisor_assigned_to_client($advisor_user_id, $client_id) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_clients';
        
        $result = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE id = %d AND advisor_id = %d",
            $client_id,
            $advisor_user_id
        ));
        
        return $result > 0;
    }
    
    /**
     * Check sharing permissions
     */
    private function has_sharing_permission($user_id, $client_id, $section) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_sharing_permissions';
        
        $result = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name 
             WHERE client_id = %d 
             AND shared_with_user_id = %d 
             AND section = %s 
             AND (expires_at IS NULL OR expires_at > NOW())",
            $client_id,
            $user_id,
            $section
        ));
        
        return $result > 0;
    }
    
    /**
     * Sanitize input data
     */
    public function sanitize_input($data, $type = 'text') {
        switch ($type) {
            case 'email':
                return sanitize_email($data);
            case 'url':
                return esc_url_raw($data);
            case 'textarea':
                return sanitize_textarea_field($data);
            case 'html':
                return wp_kses_post($data);
            case 'int':
                return intval($data);
            case 'float':
                return floatval($data);
            case 'date':
                return sanitize_text_field($data); // Additional date validation can be added
            default:
                return sanitize_text_field($data);
        }
    }
    
    /**
     * Validate required fields
     */
    public function validate_required_fields($data, $required_fields) {
        $errors = array();
        
        foreach ($required_fields as $field) {
            if (empty($data[$field])) {
                $errors[] = sprintf(__('Field %s is required.', 'estate-planning-manager'), $field);
            }
        }
        
        return $errors;
    }
    
    /**
     * Rate limiting for sensitive operations
     */
    public function check_rate_limit($user_id, $action, $limit = 10, $window = 3600) {
        $transient_key = 'epm_rate_limit_' . $user_id . '_' . $action;
        $attempts = get_transient($transient_key);
        
        if ($attempts === false) {
            set_transient($transient_key, 1, $window);
            return true;
        }
        
        if ($attempts >= $limit) {
            return false;
        }
        
        set_transient($transient_key, $attempts + 1, $window);
        return true;
    }
    
    /**
     * Generate secure token
     */
    public function generate_secure_token($length = 32) {
        return bin2hex(random_bytes($length / 2));
    }
    
    /**
     * Validate file upload
     */
    public function validate_file_upload($file) {
        $allowed_types = array(
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png'
        );
        
        $file_type = wp_check_filetype($file['name']);
        
        if (!in_array($file_type['type'], $allowed_types)) {
            return new WP_Error('invalid_file_type', __('Invalid file type.', 'estate-planning-manager'));
        }
        
        // Check file size (5MB limit)
        if ($file['size'] > 5 * 1024 * 1024) {
            return new WP_Error('file_too_large', __('File size exceeds 5MB limit.', 'estate-planning-manager'));
        }
        
        return true;
    }
    
    /**
     * Log security event
     */
    public function log_security_event($event_type, $description, $user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        
        EPM_Audit_Logger::instance()->log_action(
            $user_id,
            $event_type,
            'security',
            null,
            array('description' => $description)
        );
    }
}
