<?php
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
        
        // Create tables for each section
        $this->create_clients_table($charset_collate);
        $this->create_basic_personal_table($charset_collate);
        $this->create_family_contacts_table($charset_collate);
        $this->create_key_contacts_table($charset_collate);
        $this->create_wills_poa_table($charset_collate);
        $this->create_funeral_organ_table($charset_collate);
        $this->create_taxes_table($charset_collate);
        $this->create_military_service_table($charset_collate);
        $this->create_employment_table($charset_collate);
        $this->create_volunteer_table($charset_collate);
        $this->create_bank_accounts_table($charset_collate);
        $this->create_investments_table($charset_collate);
        $this->create_real_estate_table($charset_collate);
        $this->create_personal_property_table($charset_collate);
        $this->create_digital_assets_table($charset_collate);
        $this->create_scheduled_payments_table($charset_collate);
        $this->create_debtors_creditors_table($charset_collate);
        $this->create_insurance_table($charset_collate);
        $this->create_sharing_permissions_table($charset_collate);
        $this->create_audit_log_table($charset_collate);
        $this->create_sync_log_table($charset_collate);
    }
    
    /**
     * Create clients table
     */
    private function create_clients_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_clients';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            advisor_id bigint(20) DEFAULT NULL,
            suitecrm_contact_id varchar(36) DEFAULT NULL,
            status varchar(20) DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY user_id (user_id),
            KEY advisor_id (advisor_id),
            KEY suitecrm_contact_id (suitecrm_contact_id)
        ) $charset_collate;";
        
        $this->execute_sql($sql);
    }
    
    /**
     * Create basic personal information table
     */
    private function create_basic_personal_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_basic_personal';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            client_id bigint(20) NOT NULL,
            full_legal_name varchar(255) DEFAULT NULL,
            date_of_birth date DEFAULT NULL,
            place_of_birth varchar(255) DEFAULT NULL,
            birth_certificate_location text DEFAULT NULL,
            sin varchar(255) DEFAULT NULL,
            sin_card_location text DEFAULT NULL,
            citizenship_countries text DEFAULT NULL,
            citizenship_papers_location text DEFAULT NULL,
            passports_location text DEFAULT NULL,
            drivers_license_location text DEFAULT NULL,
            marriage_certificate_location text DEFAULT NULL,
            divorce_papers_location text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY client_id (client_id),
            FOREIGN KEY (client_id) REFERENCES {$wpdb->prefix}epm_clients(id) ON DELETE CASCADE
        ) $charset_collate;";
        
        $this->execute_sql($sql);
    }
    
    /**
     * Create family contacts table
     */
    private function create_family_contacts_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_family_contacts';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            client_id bigint(20) NOT NULL,
            name varchar(255) DEFAULT NULL,
            relationship varchar(100) DEFAULT NULL,
            address text DEFAULT NULL,
            phone varchar(50) DEFAULT NULL,
            email varchar(255) DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY client_id (client_id),
            FOREIGN KEY (client_id) REFERENCES {$wpdb->prefix}epm_clients(id) ON DELETE CASCADE
        ) $charset_collate;";
        
        $this->execute_sql($sql);
    }
    
    /**
     * Create key contacts table
     */
    private function create_key_contacts_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_key_contacts';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            client_id bigint(20) NOT NULL,
            contact_type varchar(100) DEFAULT NULL,
            name varchar(255) DEFAULT NULL,
            relationship varchar(100) DEFAULT NULL,
            address text DEFAULT NULL,
            phone varchar(50) DEFAULT NULL,
            email varchar(255) DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY client_id (client_id),
            KEY contact_type (contact_type),
            FOREIGN KEY (client_id) REFERENCES {$wpdb->prefix}epm_clients(id) ON DELETE CASCADE
        ) $charset_collate;";
        
        $this->execute_sql($sql);
    }
    
    /**
     * Create wills and POA table
     */
    private function create_wills_poa_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_wills_poa';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            client_id bigint(20) NOT NULL,
            document_type varchar(100) DEFAULT NULL,
            has_document varchar(10) DEFAULT NULL,
            document_date date DEFAULT NULL,
            original_location text DEFAULT NULL,
            copies_location text DEFAULT NULL,
            document_type_detail varchar(100) DEFAULT NULL,
            legal_representative varchar(255) DEFAULT NULL,
            representative_email varchar(255) DEFAULT NULL,
            representative_phone varchar(50) DEFAULT NULL,
            representative_address text DEFAULT NULL,
            law_firm varchar(255) DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY client_id (client_id),
            KEY document_type (document_type),
            FOREIGN KEY (client_id) REFERENCES {$wpdb->prefix}epm_clients(id) ON DELETE CASCADE
        ) $charset_collate;";
        
        $this->execute_sql($sql);
    }
    
    /**
     * Create funeral and organ donation table
     */
    private function create_funeral_organ_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_funeral_organ';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            client_id bigint(20) NOT NULL,
            funeral_arrangements_made varchar(10) DEFAULT NULL,
            funeral_home varchar(255) DEFAULT NULL,
            funeral_home_address text DEFAULT NULL,
            funeral_home_phone varchar(50) DEFAULT NULL,
            funeral_home_email varchar(255) DEFAULT NULL,
            burial_instructions varchar(10) DEFAULT NULL,
            instructions_location text DEFAULT NULL,
            document_location text DEFAULT NULL,
            cemetery_plot_owned varchar(10) DEFAULT NULL,
            cemetery_location varchar(255) DEFAULT NULL,
            ongoing_care_provided varchar(10) DEFAULT NULL,
            plot_deed_location text DEFAULT NULL,
            organ_donation varchar(10) DEFAULT NULL,
            organ_donation_explanation text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY client_id (client_id),
            FOREIGN KEY (client_id) REFERENCES {$wpdb->prefix}epm_clients(id) ON DELETE CASCADE
        ) $charset_collate;";
        
        $this->execute_sql($sql);
    }
    
    /**
     * Create taxes table
     */
    private function create_taxes_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_taxes';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            client_id bigint(20) NOT NULL,
            file_taxes_yourself varchar(10) DEFAULT NULL,
            tax_advisor varchar(255) DEFAULT NULL,
            advisor_address text DEFAULT NULL,
            advisor_phone varchar(50) DEFAULT NULL,
            advisor_email varchar(255) DEFAULT NULL,
            tax_info_location text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY client_id (client_id),
            FOREIGN KEY (client_id) REFERENCES {$wpdb->prefix}epm_clients(id) ON DELETE CASCADE
        ) $charset_collate;";
        
        $this->execute_sql($sql);
    }
    
    /**
     * Create military service table
     */
    private function create_military_service_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_military_service';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            client_id bigint(20) NOT NULL,
            has_served varchar(10) DEFAULT NULL,
            country_served varchar(100) DEFAULT NULL,
            veteran_number varchar(100) DEFAULT NULL,
            discharge_papers_location text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY client_id (client_id),
            FOREIGN KEY (client_id) REFERENCES {$wpdb->prefix}epm_clients(id) ON DELETE CASCADE
        ) $charset_collate;";
        
        $this->execute_sql($sql);
    }
    
    /**
     * Create employment table
     */
    private function create_employment_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_employment';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            client_id bigint(20) NOT NULL,
            employer varchar(255) DEFAULT NULL,
            start_year year DEFAULT NULL,
            end_year year DEFAULT NULL,
            address text DEFAULT NULL,
            phone varchar(50) DEFAULT NULL,
            email varchar(255) DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY client_id (client_id),
            FOREIGN KEY (client_id) REFERENCES {$wpdb->prefix}epm_clients(id) ON DELETE CASCADE
        ) $charset_collate;";
        
        $this->execute_sql($sql);
    }
    
    /**
     * Create volunteer table
     */
    private function create_volunteer_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_volunteer';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            client_id bigint(20) NOT NULL,
            organization varchar(255) DEFAULT NULL,
            start_year year DEFAULT NULL,
            end_year year DEFAULT NULL,
            address text DEFAULT NULL,
            phone varchar(50) DEFAULT NULL,
            email varchar(255) DEFAULT NULL,
            is_charitable_gift tinyint(1) DEFAULT 0,
            information_location text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY client_id (client_id),
            FOREIGN KEY (client_id) REFERENCES {$wpdb->prefix}epm_clients(id) ON DELETE CASCADE
        ) $charset_collate;";
        
        $this->execute_sql($sql);
    }
    
    /**
     * Create bank accounts table
     */
    private function create_bank_accounts_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_bank_accounts';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            client_id bigint(20) NOT NULL,
            bank varchar(255) DEFAULT NULL,
            account_type varchar(100) DEFAULT NULL,
            account_number varchar(255) DEFAULT NULL,
            branch varchar(255) DEFAULT NULL,
            address text DEFAULT NULL,
            phone varchar(50) DEFAULT NULL,
            email varchar(255) DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY client_id (client_id),
            FOREIGN KEY (client_id) REFERENCES {$wpdb->prefix}epm_clients(id) ON DELETE CASCADE
        ) $charset_collate;";
        
        $this->execute_sql($sql);
    }
    
    /**
     * Create investments table
     */
    private function create_investments_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_investments';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            client_id bigint(20) NOT NULL,
            investment_type varchar(100) DEFAULT NULL,
            financial_company varchar(255) DEFAULT NULL,
            account_type varchar(100) DEFAULT NULL,
            account_number varchar(255) DEFAULT NULL,
            address text DEFAULT NULL,
            phone varchar(50) DEFAULT NULL,
            email varchar(255) DEFAULT NULL,
            information_location text DEFAULT NULL,
            gift_or_inheritance varchar(10) DEFAULT NULL,
            collateral varchar(10) DEFAULT NULL,
            advisor varchar(255) DEFAULT NULL,
            advisor_email varchar(255) DEFAULT NULL,
            lender varchar(255) DEFAULT NULL,
            lender_address text DEFAULT NULL,
            lender_phone varchar(50) DEFAULT NULL,
            lender_email varchar(255) DEFAULT NULL,
            company_group varchar(255) DEFAULT NULL,
            auto_invest varchar(10) DEFAULT NULL,
            frequency varchar(50) DEFAULT NULL,
            account_paid_from varchar(255) DEFAULT NULL,
            beneficiary varchar(255) DEFAULT NULL,
            beneficiary_phone varchar(50) DEFAULT NULL,
            beneficiary_email varchar(255) DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY client_id (client_id),
            KEY investment_type (investment_type),
            FOREIGN KEY (client_id) REFERENCES {$wpdb->prefix}epm_clients(id) ON DELETE CASCADE
        ) $charset_collate;";
        
        $this->execute_sql($sql);
    }
    
    /**
     * Create real estate table
     */
    private function create_real_estate_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_real_estate';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            client_id bigint(20) NOT NULL,
            property_type varchar(100) DEFAULT NULL,
            title_held_by varchar(255) DEFAULT NULL,
            address text DEFAULT NULL,
            has_mortgage varchar(10) DEFAULT NULL,
            mortgage_held_by varchar(255) DEFAULT NULL,
            lender_address text DEFAULT NULL,
            lender_phone varchar(50) DEFAULT NULL,
            lender_email varchar(255) DEFAULT NULL,
            mortgage_location text DEFAULT NULL,
            deed_location text DEFAULT NULL,
            property_insurance_docs text DEFAULT NULL,
            land_surveys text DEFAULT NULL,
            tax_receipts text DEFAULT NULL,
            leases text DEFAULT NULL,
            accounting_docs text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY client_id (client_id),
            FOREIGN KEY (client_id) REFERENCES {$wpdb->prefix}epm_clients(id) ON DELETE CASCADE
        ) $charset_collate;";
        
        $this->execute_sql($sql);
    }
    
    /**
     * Create personal property table
     */
    private function create_personal_property_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_personal_property';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            client_id bigint(20) NOT NULL,
            property_type varchar(100) DEFAULT NULL,
            item_type varchar(100) DEFAULT NULL,
            vehicle_model varchar(255) DEFAULT NULL,
            own_or_lease varchar(20) DEFAULT NULL,
            legal_document text DEFAULT NULL,
            registration_location text DEFAULT NULL,
            insurance_policy_location text DEFAULT NULL,
            bill_of_sale_location text DEFAULT NULL,
            location text DEFAULT NULL,
            safe_deposit_box_location varchar(255) DEFAULT NULL,
            box_access_names text DEFAULT NULL,
            keys_location text DEFAULT NULL,
            contents_list_location text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY client_id (client_id),
            KEY property_type (property_type),
            FOREIGN KEY (client_id) REFERENCES {$wpdb->prefix}epm_clients(id) ON DELETE CASCADE
        ) $charset_collate;";
        
        $this->execute_sql($sql);
    }
    
    /**
     * Create digital assets table
     */
    private function create_digital_assets_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_digital_assets';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            client_id bigint(20) NOT NULL,
            asset_type varchar(100) DEFAULT NULL,
            service_company varchar(255) DEFAULT NULL,
            manager_type varchar(100) DEFAULT NULL,
            location text DEFAULT NULL,
            url varchar(500) DEFAULT NULL,
            username varchar(255) DEFAULT NULL,
            password_reference text DEFAULT NULL,
            account_number varchar(255) DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY client_id (client_id),
            KEY asset_type (asset_type),
            FOREIGN KEY (client_id) REFERENCES {$wpdb->prefix}epm_clients(id) ON DELETE CASCADE
        ) $charset_collate;";
        
        $this->execute_sql($sql);
    }
    
    /**
     * Create scheduled payments table
     */
    private function create_scheduled_payments_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_scheduled_payments';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            client_id bigint(20) NOT NULL,
            payment_type varchar(100) DEFAULT NULL,
            paid_to varchar(255) DEFAULT NULL,
            is_automatic varchar(10) DEFAULT NULL,
            address text DEFAULT NULL,
            phone varchar(50) DEFAULT NULL,
            email varchar(255) DEFAULT NULL,
            account_number varchar(255) DEFAULT NULL,
            amount decimal(10,2) DEFAULT NULL,
            due_date varchar(100) DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY client_id (client_id),
            KEY payment_type (payment_type),
            FOREIGN KEY (client_id) REFERENCES {$wpdb->prefix}epm_clients(id) ON DELETE CASCADE
        ) $charset_collate;";
        
        $this->execute_sql($sql);
    }
    
    /**
     * Create debtors and creditors table
     */
    private function create_debtors_creditors_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_debtors_creditors';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            client_id bigint(20) NOT NULL,
            relationship_type varchar(20) DEFAULT NULL,
            debt_type varchar(100) DEFAULT NULL,
            creditor_debtor varchar(255) DEFAULT NULL,
            contact varchar(255) DEFAULT NULL,
            address text DEFAULT NULL,
            phone varchar(50) DEFAULT NULL,
            email varchar(255) DEFAULT NULL,
            account_number varchar(255) DEFAULT NULL,
            amount decimal(10,2) DEFAULT NULL,
            date_of_loan date DEFAULT NULL,
            document_location text DEFAULT NULL,
            description text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY client_id (client_id),
            KEY relationship_type (relationship_type),
            FOREIGN KEY (client_id) REFERENCES {$wpdb->prefix}epm_clients(id) ON DELETE CASCADE
        ) $charset_collate;";
        
        $this->execute_sql($sql);
    }
    
    /**
     * Create insurance table
     */
    private function create_insurance_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_insurance';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            client_id bigint(20) NOT NULL,
            insurance_category varchar(100) DEFAULT NULL,
            insurance_type varchar(100) DEFAULT NULL,
            advisor varchar(255) DEFAULT NULL,
            insurance_company varchar(255) DEFAULT NULL,
            policy_number varchar(255) DEFAULT NULL,
            address text DEFAULT NULL,
            phone varchar(50) DEFAULT NULL,
            email varchar(255) DEFAULT NULL,
            advisor_phone varchar(50) DEFAULT NULL,
            advisor_email varchar(255) DEFAULT NULL,
            policy_location text DEFAULT NULL,
            beneficiary varchar(255) DEFAULT NULL,
            beneficiary_phone varchar(50) DEFAULT NULL,
            beneficiary_email varchar(255) DEFAULT NULL,
            insured_person varchar(255) DEFAULT NULL,
            policy_owner varchar(255) DEFAULT NULL,
            owner_phone varchar(50) DEFAULT NULL,
            owner_email varchar(255) DEFAULT NULL,
            will_someone_become_owner varchar(10) DEFAULT NULL,
            company_association varchar(255) DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY client_id (client_id),
            KEY insurance_category (insurance_category),
            KEY insurance_type (insurance_type),
            FOREIGN KEY (client_id) REFERENCES {$wpdb->prefix}epm_clients(id) ON DELETE CASCADE
        ) $charset_collate;";
        
        $this->execute_sql($sql);
    }
    
    /**
     * Create sharing permissions table
     */
    private function create_sharing_permissions_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_sharing_permissions';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            client_id bigint(20) NOT NULL,
            shared_with_user_id bigint(20) NOT NULL,
            section varchar(100) NOT NULL,
            permission_level varchar(20) DEFAULT 'view',
            expires_at datetime DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY unique_permission (client_id, shared_with_user_id, section),
            KEY client_id (client_id),
            KEY shared_with_user_id (shared_with_user_id),
            FOREIGN KEY (client_id) REFERENCES {$wpdb->prefix}epm_clients(id) ON DELETE CASCADE
        ) $charset_collate;";
        
        $this->execute_sql($sql);
    }
    
    /**
     * Create audit log table
     */
    private function create_audit_log_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_audit_log';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            client_id bigint(20) DEFAULT NULL,
            action varchar(100) NOT NULL,
            section varchar(100) DEFAULT NULL,
            record_id bigint(20) DEFAULT NULL,
            old_values longtext DEFAULT NULL,
            new_values longtext DEFAULT NULL,
            ip_address varchar(45) DEFAULT NULL,
            user_agent text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY client_id (client_id),
            KEY action (action),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        $this->execute_sql($sql);
    }
    
    /**
     * Create sync log table
     */
    private function create_sync_log_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_sync_log';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            client_id bigint(20) NOT NULL,
            section varchar(100) NOT NULL,
            sync_direction varchar(20) NOT NULL,
            status varchar(20) NOT NULL,
            error_message text DEFAULT NULL,
            suitecrm_record_id varchar(36) DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY client_id (client_id),
            KEY section (section),
            KEY status (status),
            KEY created_at (created_at),
            FOREIGN KEY (client_id) REFERENCES {$wpdb->prefix}epm_clients(id) ON DELETE CASCADE
        ) $charset_collate;";
        
        $this->execute_sql($sql);
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
        
        return $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table_name WHERE user_id = %d",
            $user_id
        ));
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
            $data['updated_at'] = current_time('mysql');
            $result = $wpdb->update(
                $table_name,
                $data,
                array('client_id' => $client_id)
            );
        } else {
            // Insert new record
            $data['created_at'] = current_time('mysql');
            $data['updated_at'] = current_time('mysql');
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
}
