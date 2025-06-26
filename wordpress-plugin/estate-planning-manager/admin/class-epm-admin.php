<?php
/**
 * Admin Interface Class
 * 
 * Handles admin interface for Estate Planning Manager
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class EPM_Admin {
    
    /**
     * Instance of this class
     * @var EPM_Admin
     */
    private static $instance = null;
    
    /**
     * Get instance (Singleton pattern)
     * @return EPM_Admin
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
     * Initialize admin functionality
     */
    public function init() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            __('Estate Planning Manager', 'estate-planning-manager'),
            __('Estate Planning', 'estate-planning-manager'),
            'manage_options',
            'estate-planning-manager',
            array($this, 'admin_page'),
            'dashicons-clipboard',
            30
        );
    }
    
    /**
     * Admin page callback
     */
    public function admin_page() {
        echo '<div class="wrap">';
        echo '<h1>' . __('Estate Planning Manager', 'estate-planning-manager') . '</h1>';
        echo '<p>' . __('Estate Planning Manager is active and ready to use.', 'estate-planning-manager') . '</p>';
        echo '</div>';
    }
    
    /**
     * Enqueue admin scripts
     */
    public function enqueue_admin_scripts($hook) {
        if (strpos($hook, 'estate-planning-manager') !== false) {
            wp_enqueue_style('epm-admin-style', EPM_PLUGIN_URL . 'assets/css/admin.css', array(), EPM_VERSION);
            wp_enqueue_script('epm-admin-script', EPM_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), EPM_VERSION, true);
        }
    }
}
