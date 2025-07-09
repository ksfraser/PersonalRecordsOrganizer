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

require_once __DIR__ . '/class-epm-admin-suggested-updates.php';
require_once __DIR__ . '/class-epm-admin-selectors.php';
require_once __DIR__ . '/class-epm-admin-insurance-category.php';
require_once __DIR__ . '/class-epm-admin-insurance-type.php';
require_once __DIR__ . '/class-epm-admin-log-viewer.php';
require_once __DIR__ . '/class-epm-admin-phone-line-types.php';
require_once __DIR__ . '/class-epm-admin-contact-phones.php';

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
        add_action('wp_ajax_epm_save_contact_phone', function() {
            include_once EPM_PLUGIN_DIR . 'admin/ajax-save-contact-phone.php';
            exit;
        });
        add_action('admin_enqueue_scripts', function($hook) {
            if (strpos($hook, 'epm-contact-phones') !== false) {
                wp_enqueue_script('epm-admin-contact-phones', EPM_PLUGIN_URL . 'assets/js/admin-contact-phones.js', array('jquery'), null, true);
            }
        });
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
        // Add all EPM-related screens as submenus
        add_submenu_page('estate-planning-manager', __('Suggested Updates', 'estate-planning-manager'), __('Suggested Updates', 'estate-planning-manager'), 'manage_options', 'epm-suggested-updates', array(EPM_Admin_Suggested_Updates::instance(), 'render_admin_page'));
        add_submenu_page('estate-planning-manager', __('Selectors', 'estate-planning-manager'), __('Selectors', 'estate-planning-manager'), 'manage_options', 'epm-selectors', array(EPM_Admin_Selectors::instance(), 'render_admin_page'));
        add_submenu_page('estate-planning-manager', __('Insurance Categories', 'estate-planning-manager'), __('Insurance Categories', 'estate-planning-manager'), 'manage_options', 'epm-insurance-categories', ['\EstatePlanningManager\Admin\EPM_Admin_Insurance_Category', 'render']);
        add_submenu_page('estate-planning-manager', __('Insurance Types', 'estate-planning-manager'), __('Insurance Types', 'estate-planning-manager'), 'manage_options', 'epm-insurance-types', ['\EstatePlanningManager\Admin\EPM_Admin_Insurance_Type', 'render']);
        add_submenu_page('estate-planning-manager', __('Log Viewer', 'estate-planning-manager'), __('Log Viewer', 'estate-planning-manager'), 'manage_options', 'epm-log-viewer', ['EPM_Admin_Log_Viewer', 'render_page']);
        add_submenu_page(
            'estate-planning-manager',
            'Phone Line Types',
            'Phone Line Types',
            'manage_options',
            'epm-phone-line-types',
            ['EPM_Admin_Phone_Line_Types', 'render_page']
        );
        add_submenu_page(
            'estate-planning-manager',
            'Contact Phones',
            'Contact Phones',
            'manage_options',
            'epm-contact-phones',
            ['EPM_Admin_Contact_Phones', 'render_page']
        );
        // Add other EPM-related screens here as needed
    }
    
    /**
     * Admin page callback
     */
    public function admin_page() {
        echo '<div class="wrap">';
        echo '<h1>' . __('Estate Planning Manager', 'estate-planning-manager') . '</h1>';
        echo '<p>' . __('Estate Planning Manager is active and ready to use.', 'estate-planning-manager') . '</p>';
        
        // Show log level indicator and View Log link for admins
        $log_level = class_exists('EPM_Logger') ? \EPM_Logger::get_log_level() : 1;
        $log_label = ['1'=>'Error','2'=>'Warning','3'=>'Info','4'=>'Debug'];
        echo '<div style="float:right;margin-top:-40px;">'
            . '<span style="background:#222;color:#fff;padding:2px 8px;border-radius:3px;font-size:12px;">Log Level: ' . esc_html($log_label[$log_level]) . '</span> '
            . '<a href="' . admin_url('options-general.php?page=epm-log-viewer') . '" target="_blank" style="color:#0073aa;margin-left:10px;">View Log</a>'
            . '<a href="' . admin_url('options-general.php#epm_log_level') . '" style="color:#0073aa;margin-left:10px;">Change Log Level</a>'
            . '</div>';
        
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
