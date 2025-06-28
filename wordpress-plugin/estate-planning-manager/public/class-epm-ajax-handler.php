<?php
/**
 * AJAX Handler Class
 * 
 * Handles AJAX requests for Estate Planning Manager
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class EPM_Ajax_Handler {
    
    /**
     * Instance of this class
     * @var EPM_Ajax_Handler
     */
    private static $instance = null;
    
    /**
     * Get instance (Singleton pattern)
     * @return EPM_Ajax_Handler
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
        // Constructor logic here
    }
    
    /**
     * Initialize AJAX handlers
     */
    public function init() {
        add_action('wp_ajax_epm_save_client_data', array($this, 'save_client_data'));
        add_action('wp_ajax_epm_load_client_data', array($this, 'load_client_data'));
        add_action('wp_ajax_nopriv_epm_save_client_data', array($this, 'save_client_data'));
        add_action('wp_ajax_nopriv_epm_load_client_data', array($this, 'load_client_data'));
        add_action('wp_ajax_epm_set_ui_mode', array($this, 'set_ui_mode'));
    }
    
    /**
     * Save client data via AJAX
     */
    public function save_client_data() {
        // Verify nonce
        if (!check_ajax_referer('epm_save_data', 'nonce', false)) {
            wp_send_json_error('Invalid security token');
        }

        if (!is_user_logged_in()) {
            wp_send_json_error('User not logged in');
        }

        $user_id = get_current_user_id();
        $section = sanitize_text_field($_POST['section']);
        parse_str($_POST['form_data'], $form_data);

        // Get form sections to validate fields
        $sections = EPM_Shortcodes::instance()->get_form_sections();
        if (!isset($sections[$section])) {
            wp_send_json_error('Invalid section');
        }

        $section_config = $sections[$section];
        $valid_fields = array_column($section_config['fields'], 'name');

        // Prepare data for DB
        $data = array();
        foreach ($form_data as $field => $value) {
            // Only process valid fields
            if (strpos($field, 'epm_') !== 0 || !in_array(substr($field, 4), $valid_fields)) {
                continue;
            }
            $field_name = substr($field, 4);
            // Sanitize value based on field type
            foreach ($section_config['fields'] as $field_config) {
                if ($field_config['name'] === $field_name) {
                    $sanitized_value = $this->sanitize_field_value($value, $field_config['type']);
                    $data[$field_name] = $sanitized_value;
                    break;
                }
            }
        }

        // Get client_id for this user
        $db = EPM_Database::instance();
        $client_id = $db->get_client_id_by_user_id($user_id);
        if (!$client_id) {
            // Create client record if not exists
            $client_id = $db->create_client($user_id);
        }

        // Save to custom table
        $result = $db->save_client_data($client_id, $section, $data);
        if ($result) {
            do_action('epm_data_updated', $user_id, $section, array_keys($data));
            wp_send_json_success('Data saved successfully');
        } else {
            wp_send_json_error('No fields were updated');
        }
    }

    /**
     * Sanitize field value based on type
     */
    private function sanitize_field_value($value, $type) {
        switch ($type) {
            case 'email':
                return sanitize_email($value);
            case 'textarea':
                return sanitize_textarea_field($value);
            case 'tel':
                return preg_replace('/[^0-9+\-() ]/', '', $value);
            case 'date':
                return sanitize_text_field($value); // Already in Y-m-d format from input
            case 'select':
                return sanitize_text_field($value);
            case 'text':
            default:
                return sanitize_text_field($value);
        }
    }
    
    /**
     * Load client data via AJAX
     */
    public function load_client_data() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'epm_ajax_nonce')) {
            wp_die('Security check failed');
        }
        
        wp_send_json_success(array('data' => array()));
    }

    /**
     * Handle AJAX for sharing sections with a user by email
     */
    public function send_share() {
        if (!check_ajax_referer('epm_save_data', 'nonce', false)) {
            wp_send_json_error('Invalid security token');
        }
        if (!is_user_logged_in()) {
            wp_send_json_error('User not logged in');
        }
        $user_id = get_current_user_id();
        parse_str($_POST['form_data'], $form_data);
        $email = sanitize_email($form_data['share_email']);
        $sections = isset($form_data['share_sections']) ? (array)$form_data['share_sections'] : array();
        $permission = sanitize_text_field($form_data['permission_level']);
        if (empty($email) || empty($sections)) {
            wp_send_json_error('Email and at least one section are required');
        }
        $existing_user = get_user_by('email', $email);
        global $wpdb;
        $table = $wpdb->prefix . 'epm_sharing_permissions';
        if ($existing_user) {
            // Grant access for each section
            foreach ($sections as $section) {
                $wpdb->replace($table, array(
                    'client_id' => $user_id,
                    'shared_with_user_id' => $existing_user->ID,
                    'section' => $section,
                    'permission_level' => $permission
                ));
            }
            wp_send_json_success('Access granted to existing user.');
        } else {
            // Generate invite token
            $token = wp_generate_password(32, false);
            $invite_data = array(
                'client_id' => $user_id,
                'email' => $email,
                'sections' => $sections,
                'permission_level' => $permission,
                'token' => $token,
                'created_at' => current_time('mysql')
            );
            $invites_table = $wpdb->prefix . 'epm_share_invites';
            $wpdb->insert($invites_table, array(
                'client_id' => $user_id,
                'email' => $email,
                'sections' => maybe_serialize($sections),
                'permission_level' => $permission,
                'token' => $token,
                'created_at' => current_time('mysql')
            ));
            // Send invite email
            $register_url = wp_registration_url();
            $invite_url = add_query_arg(array('epm_invite' => $token, 'email' => rawurlencode($email)), $register_url);
            wp_mail($email, 'You are invited to Estate Planning Manager',
                "You have been invited to access shared data. Register here: $invite_url");
            wp_send_json_success('Invite sent to email.');
        }
    }

    /**
     * AJAX: Set UI mode preference (tabs/twisties)
     */
    public function set_ui_mode() {
        if (!is_user_logged_in()) {
            wp_send_json_error('Not logged in');
        }
        $user_id = get_current_user_id();
        $ui_mode = isset($_POST['ui_mode']) && in_array($_POST['ui_mode'], ['tabs','twisties']) ? $_POST['ui_mode'] : 'tabs';
        $db = EPM_Database::instance();
        $db->set_user_preference($user_id, 'ui_mode', $ui_mode);
        wp_send_json_success('Preference saved');
    }
}
