<?php
/**
 * Frontend Class
 * 
 * Handles frontend functionality for Estate Planning Manager
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class EPM_Frontend {
    
    /**
     * Instance of this class
     * @var EPM_Frontend
     */
    private static $instance = null;
    
    /**
     * Get instance (Singleton pattern)
     * @return EPM_Frontend
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
     * Initialize frontend functionality
     */
    public function init() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }
    
    /**
     * Enqueue frontend scripts
     */
    public function enqueue_scripts() {
        wp_enqueue_style('epm-frontend-style', EPM_PLUGIN_URL . 'assets/css/frontend.css', array(), EPM_VERSION);
        wp_enqueue_script('epm-frontend-script', EPM_PLUGIN_URL . 'assets/js/frontend.js', array('jquery'), EPM_VERSION, true);
    }
}
