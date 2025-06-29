<?php
/**
 * Plugin Name: Estate Planning Manager
 * Plugin URI: https://github.com/your-repo/estate-planning-manager
 * Description: A comprehensive estate planning records management system with SuiteCRM integration for financial advisors.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://yourwebsite.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: estate-planning-manager
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.3
 * Network: false
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('EPM_VERSION', '1.0.0');
define('EPM_PLUGIN_FILE', __FILE__);
define('EPM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('EPM_PLUGIN_URL', plugin_dir_url(__FILE__));
define('EPM_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main Estate Planning Manager Class
 * 
 * Follows Single Responsibility Principle - handles plugin initialization only
 */
final class EstateplanningManager {
    
    /**
     * Plugin instance
     * @var EstateplanningManager
     */
    private static $instance = null;
    
    /**
     * Get plugin instance (Singleton pattern)
     * @return EstateplanningManager
     */
    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor - Initialize the plugin
     */
    private function __construct() {
        $this->init_hooks();
        $this->load_dependencies();
    }
    
    /**
     * Initialize WordPress hooks
     */
    private function init_hooks() {
        add_action('init', array($this, 'init'), 0);
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        
        // Activation and deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    /**
     * Load plugin dependencies
     */
    private function load_dependencies() {
        // Core classes - only load files that exist
        require_once EPM_PLUGIN_DIR . 'includes/class-epm-database.php';
        require_once EPM_PLUGIN_DIR . 'includes/class-epm-security.php';
        require_once EPM_PLUGIN_DIR . 'includes/class-epm-suitecrm-api.php';
        require_once EPM_PLUGIN_DIR . 'includes/class-epm-pdf-generator.php';
        require_once EPM_PLUGIN_DIR . 'includes/class-epm-audit-logger.php';
        require_once EPM_PLUGIN_DIR . 'includes/class-epm-field-definitions.php';
        
        // Admin classes
        if (is_admin()) {
            require_once EPM_PLUGIN_DIR . 'admin/class-epm-admin.php';
            require_once EPM_PLUGIN_DIR . 'admin/class-epm-admin-selectors.php';
            require_once EPM_PLUGIN_DIR . 'admin/class-epm-admin-suggested-updates.php';
            require_once EPM_PLUGIN_DIR . 'admin/class-epm-defaults-admin.php';
            require_once EPM_PLUGIN_DIR . 'admin/class-epm-assign-advisors.php';
        }
        
        // Public classes
        require_once EPM_PLUGIN_DIR . 'public/class-epm-frontend.php';
        require_once EPM_PLUGIN_DIR . 'public/class-epm-shortcodes.php';
        require_once EPM_PLUGIN_DIR . 'public/class-epm-ajax-handler.php';
    }
    
    /**
     * Initialize plugin functionality
     */
    public function init() {
        // Initialize database
        EPM_Database::instance()->init();
        
        // Initialize security
        EPM_Security::instance()->init();
        
        // Initialize admin interface
        if (is_admin()) {
            EPM_Admin::instance()->init();
            EPM_Admin_Selectors::instance()->init();
            EPM_Admin_Suggested_Updates::instance()->init();
            EPM_Defaults_Admin::instance()->init();
            EPM_Assign_Advisors_Admin::instance()->init();
        }
        
        // Initialize frontend
        EPM_Frontend::instance()->init();
        
        // Initialize shortcodes
        EPM_Shortcodes::instance()->init();
        
        // Initialize AJAX handlers
        EPM_Ajax_Handler::instance()->init();
        
        // Initialize SuiteCRM API
        EPM_SuiteCRM_API::instance()->init();
        
        do_action('epm_init');
    }
    
    /**
     * Load plugin textdomain for translations
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'estate-planning-manager',
            false,
            dirname(EPM_PLUGIN_BASENAME) . '/languages/'
        );
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Drop all EPM tables for a clean install
        require_once EPM_PLUGIN_DIR . 'includes/tables/TableFactory.php';
        if (class_exists('TableFactory')) {
            TableFactory::dropAllTables();
        }
        // Create database tables
        EPM_Database::instance()->create_tables();
        
        // Create user roles
        $this->create_user_roles();
        
        // Set default options
        $this->set_default_options();
        
        // Create main pages for shortcodes
        if (class_exists('EPM_Shortcodes')) {
            EPM_Shortcodes::create_pages_on_install();
        } else {
            require_once EPM_PLUGIN_DIR . 'public/class-epm-shortcodes.php';
            if (class_exists('EPM_Shortcodes')) {
                EPM_Shortcodes::create_pages_on_install();
            }
        }
        
        // Flush rewrite rules
        flush_rewrite_rules();
        
        do_action('epm_activated');
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
        
        do_action('epm_deactivated');
    }
    
    /**
     * Create custom user roles
     */
    private function create_user_roles() {
        // Estate Planning Client role
        add_role(
            'estate_client',
            __('Estate Planning Client', 'estate-planning-manager'),
            array(
                'read' => true,
                'epm_manage_own_data' => true,
                'epm_generate_pdf' => true,
                'epm_share_data' => true,
            )
        );
        
        // Financial Advisor role
        add_role(
            'financial_advisor',
            __('Financial Advisor', 'estate-planning-manager'),
            array(
                'read' => true,
                'epm_view_client_data' => true,
                'epm_manage_clients' => true,
                'epm_generate_reports' => true,
            )
        );
    }
    
    /**
     * Set default plugin options
     */
    private function set_default_options() {
        $default_options = array(
            'epm_version' => EPM_VERSION,
            'epm_suitecrm_enabled' => false,
            'epm_encryption_enabled' => true,
            'epm_audit_logging' => true,
            'epm_auto_sync' => true,
            'epm_pdf_templates' => array(
                'complete_estate_plan',
                'financial_summary',
                'emergency_contacts',
                'legal_documents'
            )
        );
        
        foreach ($default_options as $option => $value) {
            if (false === get_option($option)) {
                add_option($option, $value);
            }
        }
    }
}

/**
 * Initialize the plugin
 */
function epm_init() {
    return EstateplanningManager::instance();
}

// Start the plugin
epm_init();
