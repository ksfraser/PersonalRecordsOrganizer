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

// GLOBAL POST debug: log every POST before WordPress loads
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log('EPM DEBUG: GLOBAL POST handler reached. URI=' . $_SERVER['REQUEST_URI']);
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
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        
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
     * Enqueue frontend scripts for modal AJAX
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            'epm-section-modal',
            EPM_PLUGIN_URL . 'assets/js/epm-section-modal.js',
            array('jquery'),
            EPM_VERSION,
            true
        );
        wp_localize_script('epm-section-modal', 'epmSectionModal', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('epm_save_data'),
        ));
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

// === EPM Modal POST Handler (shared for all sections) ===
add_action('init', function() {
    error_log('[EPM] DEBUG: POST handler triggered. $_POST=' . print_r($_POST, true));
    $epm_section_models = [
        'personal' => '\EstatePlanningManager\Models\PersonalModel',
        'banking' => '\EstatePlanningManager\Models\BankingModel',
        'investments' => '\EstatePlanningManager\Models\InvestmentsModel',
        'real_estate' => '\EstatePlanningManager\Models\RealEstateModel',
        'insurance' => '\EstatePlanningManager\Models\InsuranceModel',
        'scheduled_payments' => '\EstatePlanningManager\Models\ScheduledPaymentsModel',
        'personal_property' => '\EstatePlanningManager\Models\PersonalPropertyModel',
        'auto_property' => '\EstatePlanningManager\Models\AutoModel',
        'emergency_contacts' => '\EstatePlanningManager\Models\EmergencyContactsModel',
    ];
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['section'], $_POST['nonce'])) {
        $section = sanitize_text_field($_POST['section']);
        $nonce = sanitize_text_field($_POST['nonce']);
        // Log every section/action call
        error_log('[EPM] Section POST: ' . $section);
        // Optionally validate nonce here
        if (isset($epm_section_models[$section])) {
            $model_class = $epm_section_models[$section];
            if (class_exists($model_class)) {
                $model = new $model_class();
                $data = $_POST;
                unset($data['section'], $data['nonce']);
                if (is_user_logged_in()) {
                    $current_user_id = get_current_user_id();
                    $data['client_id'] = $current_user_id;
                }
                $result = $model->saveRecord($data);
                if ($result) {
                    // === SuiteCRM sync stub ===
                    // TODO: Implement SuiteCRM sync for $section and $result
                    // =========================
                    wp_redirect($_SERVER['REQUEST_URI']);
                    exit;
                } else {
                    error_log('[EPM] Save failed for section: ' . $section);
                    add_action('admin_notices', function() {
                        echo '<div class="notice notice-error"><p>Failed to save record.</p></div>';
                    });
                }
            } else {
                error_log('[EPM] Model class does not exist for section: ' . $section . ' (Class: ' . $model_class . ')');
            }
        } else {
            error_log('[EPM] Invalid section: ' . $section . ' (Available: ' . implode(', ', array_keys($epm_section_models)) . ')');
            add_action('admin_notices', function() use ($section, $epm_section_models) {
                echo '<div class="notice notice-error"><p>Invalid section: ' . esc_html($section) . '. Available: ' . esc_html(implode(', ', array_keys($epm_section_models))) . '</p></div>';
            });
        }
    }
});


// === Register Handler Classes for Log Level and Log Viewer ===
require_once EPM_PLUGIN_DIR . 'includes/handlers/EPM_LogLevelHandler.php';
require_once EPM_PLUGIN_DIR . 'includes/handlers/EPM_LogViewerHandler.php';
EPM_LogLevelHandler::register();
EPM_LogViewerHandler::register();

// === EPM Modal POST Handlers for admin-post.php ===
require_once EPM_PLUGIN_DIR . 'includes/handlers/EPM_AddPersonHandler.php';
require_once EPM_PLUGIN_DIR . 'includes/handlers/EPM_AddInstituteHandler.php';
require_once EPM_PLUGIN_DIR . 'includes/handlers/EPM_SaveSectionHandler.php';

add_action('admin_post_epm_add_person', [EPM_AddPersonHandler::class, 'handle']);
add_action('admin_post_epm_add_institute', [EPM_AddInstituteHandler::class, 'handle']);
add_action('admin_post_epm_save_section', [EPM_SaveSectionHandler::class, 'handle']);

// Register EPM_AdminInitHandler and EPM_InitHandler for admin_init and init actions
require_once EPM_PLUGIN_DIR . 'includes/handlers/EPM_AdminInitHandler.php';
require_once EPM_PLUGIN_DIR . 'includes/handlers/EPM_InitHandler.php';

add_action('admin_init', [EPM_AdminInitHandler::class, 'handle']);
add_action('init', [EPM_InitHandler::class, 'handle']);

// Register EPM_AdminMenuHandler for admin_menu
require_once EPM_PLUGIN_DIR . 'includes/handlers/EPM_AdminMenuHandler.php';
add_action('admin_menu', [EPM_AdminMenuHandler::class, 'handle']);

// Initialize the plugin
function epm_init() {
    return EstateplanningManager::instance();
}

// Start the plugin
epm_init();

// Ensure EPM log file exists and is writable on plugin init
add_action('init', function() {
    $upload_dir = wp_upload_dir();
    $log_file = $upload_dir['basedir'] . '/epm-log.txt';
    if (!file_exists($log_file)) {
        // Try to create the file
        @file_put_contents($log_file, "EPM Log initialized on " . date('Y-m-d H:i:s') . "\n");
        @chmod($log_file, 0664);
    }
});
