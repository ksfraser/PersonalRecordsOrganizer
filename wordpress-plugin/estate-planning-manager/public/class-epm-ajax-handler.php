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

        // Save each field to user meta
        $updated_fields = array();
        foreach ($form_data as $field => $value) {
            // Skip non-epm fields and invalid fields
            if (strpos($field, 'epm_') !== 0 || !in_array(substr($field, 4), $valid_fields)) {
                continue;
            }

            // Sanitize value based on field type
            foreach ($section_config['fields'] as $field_config) {
                if ('epm_' . $field_config['name'] === $field) {
                    $sanitized_value = $this->sanitize_field_value($value, $field_config['type']);
                    break;
                }
            }

            // Update user meta
            if (update_user_meta($user_id, $field, $sanitized_value)) {
                $updated_fields[] = $field;
            }
        }

        if (!empty($updated_fields)) {
            // Log the update
            do_action('epm_data_updated', $user_id, $section, $updated_fields);
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
}
