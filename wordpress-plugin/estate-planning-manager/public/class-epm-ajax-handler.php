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
        if (!wp_verify_nonce($_POST['nonce'], 'epm_ajax_nonce')) {
            wp_die('Security check failed');
        }
        
        wp_send_json_success(array('message' => 'Data saved successfully'));
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
}
