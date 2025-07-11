<?php
// Ensure logger is always loaded
require_once __DIR__ . '/class-epm-logger.php';
/**
 * Database Management Class
 * 
 * Handles all database operations for Estate Planning Manager
 * Follows Single Responsibility Principle - only handles database operations
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/tables/TableFactory.php';

class EPM_Database {
    
    /**
     * Instance of this class
     * @var EPM_Database
     */
    private static $instance = null;
    
    /**
     * Database version
     * @var string
     */
    private $db_version = '1.0.0';
    
    /**
     * Get instance (Singleton pattern)
     * @return EPM_Database
     */
    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Initialize database operations
     */
    public function init() {
        add_action('init', array($this, 'check_database_version'));
    }
    
    /**
     * Check if database needs updating
     */
    public function check_database_version() {
        $installed_version = get_option('epm_db_version', '0.0.0');
        
        if (version_compare($installed_version, $this->db_version, '<')) {
            $this->create_tables();
            update_option('epm_db_version', $this->db_version);
        }
    }
    
    /**
     * Create all database tables
     */
    public function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        // Use new table classes for creation
        foreach (TableFactory::getTables() as $table) {
            $table->create($charset_collate);
            $table->populate($charset_collate);
        }
    }
    
    /**
     * Execute SQL statement
     */
    private function execute_sql($sql) {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Get client ID by user ID
     */
    public function get_client_id_by_user_id($user_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_clients';
        if (!class_exists('EstatePlanningManager\\Logger')) {
            require_once __DIR__ . '/class-epm-logger.php';
        }
        \EstatePlanningManager\Logger::debug("get_client_id_by_user_id called with user_id=" . var_export($user_id, true));
        $sql = $wpdb->prepare("SELECT id FROM $table_name WHERE user_id = %d", $user_id);
        \EstatePlanningManager\Logger::debug("SQL: $sql");
        $client_id = $wpdb->get_var($sql);
        \EstatePlanningManager\Logger::debug("Result client_id=" . var_export($client_id, true));
        return $client_id;
    }
    
    /**
     * Create new client record
     */
    public function create_client($user_id, $advisor_id = null) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_clients';
        
        $result = $wpdb->insert(
            $table_name,
            array(
                'user_id' => $user_id,
                'advisor_id' => $advisor_id,
                'status' => 'active'
            ),
            array('%d', '%d', '%s')
        );
        
        if ($result !== false) {
            return $wpdb->insert_id;
        }
        
        return false;
    }
    
    /**
     * Get client data by section
     */
    public function get_client_data($client_id, $section) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_' . $section;
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name WHERE client_id = %d",
            $client_id
        ));
    }
    
    /**
     * Save client data
     */
    public function save_client_data($client_id, $section, $data) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_' . $section;
        
        // Add client_id to data
        $data['client_id'] = $client_id;
        
        // Check if record exists
        $existing_record = $wpdb->get_row($wpdb->prepare(
            "SELECT id FROM $table_name WHERE client_id = %d LIMIT 1",
            $client_id
        ));
        
        if ($existing_record) {
            // Update existing record
            $data['lastupdated'] = current_time('mysql');
            $result = $wpdb->update(
                $table_name,
                $data,
                array('client_id' => $client_id)
            );
        } else {
            // Insert new record
            $data['created'] = current_time('mysql');
            $data['lastupdated'] = current_time('mysql');
            $result = $wpdb->insert($table_name, $data);
        }
        
        return $result !== false;
    }
    
    /**
     * Delete client data
     */
    public function delete_client_data($client_id, $section, $record_id = null) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_' . $section;
        
        if ($record_id) {
            // Delete specific record
            return $wpdb->delete(
                $table_name,
                array(
                    'id' => $record_id,
                    'client_id' => $client_id
                ),
                array('%d', '%d')
            );
        } else {
            // Delete all records for client in this section
            return $wpdb->delete(
                $table_name,
                array('client_id' => $client_id),
                array('%d')
            );
        }
    }
    
    /**
     * Get all sections with data for a client
     */
    public function get_client_sections_with_data($client_id) {
        $sections = array(
            'basic_personal', 'family_contacts', 'key_contacts', 'wills_poa',
            'funeral_organ', 'taxes', 'military_service', 'employment',
            'volunteer', 'bank_accounts', 'investments', 'real_estate',
            'personal_property', 'digital_assets', 'scheduled_payments',
            'debtors_creditors', 'insurance'
        );
        
        $sections_with_data = array();
        
        foreach ($sections as $section) {
            $data = $this->get_client_data($client_id, $section);
            if (!empty($data)) {
                $sections_with_data[] = $section;
            }
        }
        
        return $sections_with_data;
    }
    
    /**
     * Get completion percentage for client
     */
    public function get_client_completion_percentage($client_id) {
        $total_sections = 17; // Total number of sections
        $completed_sections = count($this->get_client_sections_with_data($client_id));
        
        return round(($completed_sections / $total_sections) * 100, 2);
    }
    
    /**
     * Get selector options from a selector table
     * @param string $selector_table (e.g. 'epm_account_types')
     * @return array key => label
     */
    public function get_selector_options($selector_table) {
        global $wpdb;
        $table = $wpdb->prefix . $selector_table;
        $results = $wpdb->get_results("SELECT value, label FROM $table WHERE is_active = 1 ORDER BY sort_order ASC, label ASC", ARRAY_A);
        $options = array();
        if ($results) {
            foreach ($results as $row) {
                $options[$row['value']] = $row['label'];
            }
        }
        return $options;
    }

    /**
     * Get a user preference
     */
    public function get_user_preference($user_id, $preference_name) {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_user_preferences';
        return $wpdb->get_var($wpdb->prepare(
            "SELECT preference_value FROM $table WHERE user_id = %d AND preference_name = %s",
            $user_id, $preference_name
        ));
    }

    /**
     * Set a user preference
     */
    public function set_user_preference($user_id, $preference_name, $preference_value) {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_user_preferences';
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table WHERE user_id = %d AND preference_name = %s",
            $user_id, $preference_name
        ));
        if ($exists) {
            return $wpdb->update(
                $table,
                array('preference_value' => $preference_value, 'lastupdated' => current_time('mysql')),
                array('user_id' => $user_id, 'preference_name' => $preference_name)
            );
        } else {
            return $wpdb->insert(
                $table,
                array(
                    'user_id' => $user_id,
                    'preference_name' => $preference_name,
                    'preference_value' => $preference_value,
                    'created' => current_time('mysql'),
                    'lastupdated' => current_time('mysql')
                )
            );
        }
    }

    /**
     * Get an advisor default value
     */
    public function get_advisor_default($advisor_user_id, $name) {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_defaults';
        return $wpdb->get_var($wpdb->prepare(
            "SELECT value FROM $table WHERE advisor_user_id = %d AND name = %s",
            $advisor_user_id, $name
        ));
    }

    /**
     * Set an advisor default value
     */
    public function set_advisor_default($advisor_user_id, $name, $value) {
        if (empty($advisor_user_id) || empty($name)) {
            return false;
        }
        global $wpdb;
        $table = $wpdb->prefix . 'epm_defaults';
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table WHERE advisor_user_id = %d AND name = %s",
            $advisor_user_id, $name
        ));
        if ($exists) {
            return $wpdb->update(
                $table,
                array('value' => $value, 'lastupdated' => current_time('mysql')),
                array('advisor_user_id' => $advisor_user_id, 'name' => $name)
            );
        } else {
            return $wpdb->insert(
                $table,
                array(
                    'advisor_user_id' => $advisor_user_id,
                    'name' => $name,
                    'value' => $value,
                    'created' => current_time('mysql'),
                    'lastupdated' => current_time('mysql')
                )
            );
        }
    }

    /**
     * Get user meta value (wrapper for get_user_meta, for testability and abstraction)
     * @param int $user_id
     * @param string $key
     * @return mixed
     */
    public function get_user_meta($user_id, $key) {
        // Use WordPress core get_user_meta, but allow for easier mocking/testing
        if (function_exists('get_user_meta')) {
            $value = get_user_meta($user_id, $key, true);
            return $value !== '' ? $value : null;
        }
        return null;
    }
}
