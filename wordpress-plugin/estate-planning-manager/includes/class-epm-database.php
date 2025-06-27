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
        $this->create_suggested_updates_table($charset_collate);
        $this->create_audit_log_table($charset_collate);
        $this->create_sync_log_table($charset_collate);
        $this->create_selector_tables($charset_collate);
        $this->create_share_invites_table($charset_collate); // Add invites table
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
            relationship_other varchar(255) DEFAULT NULL,
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
            account_type_other varchar(255) DEFAULT NULL,
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
     * Create suggested updates table
     */
    private function create_suggested_updates_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_suggested_updates';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            client_id bigint(20) NOT NULL,
            section varchar(100) NOT NULL,
            record_id bigint(20) DEFAULT NULL,
            field_name varchar(100) NOT NULL,
            current_value longtext DEFAULT NULL,
            suggested_value longtext DEFAULT NULL,
            source varchar(50) DEFAULT 'suitecrm',
            source_record_id varchar(36) DEFAULT NULL,
            status varchar(20) DEFAULT 'pending',
            suggested_by_user_id bigint(20) DEFAULT NULL,
            reviewed_by_user_id bigint(20) DEFAULT NULL,
            review_notes text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            reviewed_at datetime DEFAULT NULL,
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
     * Create selector tables for dropdown options
     */
    private function create_selector_tables($charset_collate) {
        $this->create_relationship_types_table($charset_collate);
        $this->create_account_types_table($charset_collate);
        $this->create_contact_types_table($charset_collate);
        $this->create_insurance_categories_table($charset_collate);
        $this->create_insurance_types_table($charset_collate);
        $this->create_property_types_table($charset_collate);
        $this->create_investment_types_table($charset_collate);
        $this->create_payment_types_table($charset_collate);
        $this->create_debt_types_table($charset_collate);
        $this->create_employment_status_table($charset_collate);
        $this->create_document_types_table($charset_collate);
        $this->create_digital_asset_types_table($charset_collate);
        $this->create_personal_property_categories_table($charset_collate);
        $this->populate_default_selectors();
    }
    
    /**
     * Create relationship types table
     */
    private function create_relationship_types_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_relationship_types';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            value varchar(100) NOT NULL,
            label varchar(255) NOT NULL,
            is_active tinyint(1) DEFAULT 1,
            sort_order int(11) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY value (value)
        ) $charset_collate;";
        
        $this->execute_sql($sql);
    }
    
    /**
     * Create account types table
     */
    private function create_account_types_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_account_types';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            value varchar(100) NOT NULL,
            label varchar(255) NOT NULL,
            is_active tinyint(1) DEFAULT 1,
            sort_order int(11) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY value (value)
        ) $charset_collate;";
        
        $this->execute_sql($sql);
    }
    
    /**
     * Create contact types table
     */
    private function create_contact_types_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_contact_types';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            value varchar(100) NOT NULL,
            label varchar(255) NOT NULL,
            is_active tinyint(1) DEFAULT 1,
            sort_order int(11) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY value (value)
        ) $charset_collate;";
        
        $this->execute_sql($sql);
    }
    
    /**
     * Create insurance categories table
     */
    private function create_insurance_categories_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_insurance_categories';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            value varchar(100) NOT NULL,
            label varchar(255) NOT NULL,
            is_active tinyint(1) DEFAULT 1,
            sort_order int(11) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY value (value)
        ) $charset_collate;";
        
        $this->execute_sql($sql);
    }
    
    /**
     * Create insurance types table
     */
    private function create_insurance_types_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_insurance_types';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            value varchar(100) NOT NULL,
            label varchar(255) NOT NULL,
            category varchar(100) DEFAULT NULL,
            is_active tinyint(1) DEFAULT 1,
            sort_order int(11) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY value (value),
            KEY category (category)
        ) $charset_collate;";
        
        $this->execute_sql($sql);
    }
    
    /**
     * Create property types table
     */
    private function create_property_types_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_property_types';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            value varchar(100) NOT NULL,
            label varchar(255) NOT NULL,
            is_active tinyint(1) DEFAULT 1,
            sort_order int(11) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY value (value)
        ) $charset_collate;";
        
        $this->execute_sql($sql);
    }
    
    /**
     * Create investment types table
     */
    private function create_investment_types_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_investment_types';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            value varchar(100) NOT NULL,
            label varchar(255) NOT NULL,
            is_active tinyint(1) DEFAULT 1,
            sort_order int(11) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY value (value)
        ) $charset_collate;";
        
        $this->execute_sql($sql);
    }
    
    /**
     * Create payment types table
     */
    private function create_payment_types_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_payment_types';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            value varchar(100) NOT NULL,
            label varchar(255) NOT NULL,
            is_active tinyint(1) DEFAULT 1,
            sort_order int(11) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY value (value)
        ) $charset_collate;";
        
        $this->execute_sql($sql);
    }
    
    /**
     * Create debt types table
     */
    private function create_debt_types_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_debt_types';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            value varchar(100) NOT NULL,
            label varchar(255) NOT NULL,
            is_active tinyint(1) DEFAULT 1,
            sort_order int(11) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY value (value)
        ) $charset_collate;";
        
        $this->execute_sql($sql);
    }
    
    /**
     * Create employment status table
     */
    private function create_employment_status_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_employment_status';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            value varchar(100) NOT NULL,
            label varchar(255) NOT NULL,
            is_active tinyint(1) DEFAULT 1,
            sort_order int(11) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY value (value)
        ) $charset_collate;";
        
        $this->execute_sql($sql);
    }
    
    /**
     * Create document types table
     */
    private function create_document_types_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_document_types';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            value varchar(100) NOT NULL,
            label varchar(255) NOT NULL,
            is_active tinyint(1) DEFAULT 1,
            sort_order int(11) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY value (value)
        ) $charset_collate;";
        
        $this->execute_sql($sql);
    }
    
    /**
     * Create digital asset types table
     */
    private function create_digital_asset_types_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_digital_asset_types';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            value varchar(100) NOT NULL,
            label varchar(255) NOT NULL,
            is_active tinyint(1) DEFAULT 1,
            sort_order int(11) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY value (value)
        ) $charset_collate;";
        
        $this->execute_sql($sql);
    }
    
    /**
     * Create personal property categories table
     */
    private function create_personal_property_categories_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_personal_property_categories';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            value varchar(100) NOT NULL,
            label varchar(255) NOT NULL,
            is_active tinyint(1) DEFAULT 1,
            sort_order int(11) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY value (value)
        ) $charset_collate;";
        
        $this->execute_sql($sql);
    }
    
    /**
     * Create share invites table
     */
    private function create_share_invites_table($charset_collate) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_share_invites';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            client_id bigint(20) NOT NULL,
            invitee_email varchar(255) NOT NULL,
            invited_by_user_id bigint(20) NOT NULL,
            sections text NOT NULL,
            permission_level varchar(20) DEFAULT 'view',
            invite_token varchar(64) NOT NULL,
            status varchar(20) DEFAULT 'pending',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            accepted_at datetime DEFAULT NULL,
            PRIMARY KEY (id),
            KEY client_id (client_id),
            KEY invitee_email (invitee_email),
            KEY invite_token (invite_token),
            FOREIGN KEY (client_id) REFERENCES {$wpdb->prefix}epm_clients(id) ON DELETE CASCADE
        ) $charset_collate;";
        $this->execute_sql($sql);
    }

    /**
     * Populate default selector values
     */
    private function populate_default_selectors() {
        global $wpdb;
        
        // Relationship types
        $relationship_types = array(
            array('spouse', 'Spouse'),
            array('child', 'Child'),
            array('parent', 'Parent'),
            array('sibling', 'Sibling'),
            array('grandparent', 'Grandparent'),
            array('grandchild', 'Grandchild'),
            array('other_family', 'Other Family'),
            array('friend', 'Friend'),
            array('other', 'Other')
        );
        
        $this->populate_selector_table('epm_relationship_types', $relationship_types);
        
        // Account types
        $account_types = array(
            array('chequing', 'Chequing'),
            array('savings', 'Savings'),
            array('investment', 'Investment'),
            array('credit_line', 'Line of Credit'),
            array('mortgage', 'Mortgage'),
            array('loan', 'Loan'),
            array('other', 'Other')
        );
        
        $this->populate_selector_table('epm_account_types', $account_types);
        
        // Contact types
        $contact_types = array(
            array('lawyer', 'Lawyer'),
            array('accountant', 'Accountant'),
            array('financial_advisor', 'Financial Advisor'),
            array('doctor', 'Doctor'),
            array('dentist', 'Dentist'),
            array('insurance_agent', 'Insurance Agent'),
            array('real_estate_agent', 'Real Estate Agent'),
            array('other', 'Other')
        );
        
        $this->populate_selector_table('epm_contact_types', $contact_types);
        
        // Insurance categories
        $insurance_categories = array(
            array('life', 'Life Insurance'),
            array('health', 'Health Insurance'),
            array('auto', 'Auto Insurance'),
            array('property', 'Property Insurance'),
            array('disability', 'Disability Insurance'),
            array('other', 'Other')
        );
        
        $this->populate_selector_table('epm_insurance_categories', $insurance_categories);
        
        // Property types
        $property_types = array(
            array('primary_residence', 'Primary Residence'),
            array('rental_property', 'Rental Property'),
            array('commercial_property', 'Commercial Property'),
            array('vacant_land', 'Vacant Land'),
            array('cottage', 'Cottage/Vacation Home'),
            array('other', 'Other')
        );
        
        $this->populate_selector_table('epm_property_types', $property_types);
        
        // Investment types
        $investment_types = array(
            array('stocks', 'Stocks'),
            array('bonds', 'Bonds'),
            array('mutual_funds', 'Mutual Funds'),
            array('etfs', 'Exchange-Traded Funds (ETFs)'),
            array('gics', 'Guaranteed Investment Certificates (GICs)'),
            array('term_deposits', 'Term Deposits'),
            array('cryptocurrency', 'Cryptocurrency'),
            array('reits', 'Real Estate Investment Trusts (REITs)'),
            array('pension_plans', 'Pension Plans'),
            array('annuities', 'Annuities'),
            array('life_insurance', 'Life Insurance Policies'),
            array('segregated_funds', 'Segregated Funds'),
            array('options', 'Options'),
            array('futures', 'Futures'),
            array('commodities', 'Commodities'),
            array('foreign_currency', 'Foreign Currency'),
            array('rrsp', 'RRSP'),
            array('tfsa', 'TFSA'),
            array('rrif', 'RRIF'),
            array('resp', 'RESP'),
            array('other', 'Other')
        );
        
        $this->populate_selector_table('epm_investment_types', $investment_types);
        
        // Payment types
        $payment_types = array(
            array('mortgage', 'Mortgage'),
            array('rent', 'Rent'),
            array('utilities', 'Utilities'),
            array('insurance', 'Insurance'),
            array('loan_payment', 'Loan Payment'),
            array('subscription', 'Subscription'),
            array('other', 'Other')
        );
        
        $this->populate_selector_table('epm_payment_types', $payment_types);
        
        // Debt types
        $debt_types = array(
            array('mortgage', 'Mortgage'),
            array('personal_loan', 'Personal Loan'),
            array('credit_card', 'Credit Card'),
            array('line_of_credit', 'Line of Credit'),
            array('student_loan', 'Student Loan'),
            array('business_loan', 'Business Loan'),
            array('other', 'Other')
        );
        
        $this->populate_selector_table('epm_debt_types', $debt_types);
        
        // Employment status types
        $employment_status_types = array(
            array('full_time_employee', 'Full-time Employee'),
            array('part_time_employee', 'Part-time Employee'),
            array('self_employed', 'Self-employed'),
            array('contractor', 'Contractor/Freelancer'),
            array('retired', 'Retired'),
            array('unemployed', 'Unemployed'),
            array('student', 'Student'),
            array('homemaker', 'Homemaker'),
            array('on_leave', 'On Leave'),
            array('seasonal', 'Seasonal Worker'),
            array('volunteer', 'Volunteer'),
            array('other', 'Other')
        );
        
        $this->populate_selector_table('epm_employment_status', $employment_status_types);
        
        // Document types
        $document_types = array(
            array('will', 'Will'),
            array('poa_financial', 'Power of Attorney (Financial)'),
            array('poa_personal_care', 'Power of Attorney (Personal Care)'),
            array('living_will', 'Living Will/Advance Directive'),
            array('trust_document', 'Trust Document'),
            array('beneficiary_designation', 'Beneficiary Designation'),
            array('funeral_instructions', 'Funeral Instructions'),
            array('organ_donation', 'Organ Donation Consent'),
            array('guardianship', 'Guardianship Document'),
            array('business_succession', 'Business Succession Plan'),
            array('prenuptial_agreement', 'Prenuptial Agreement'),
            array('separation_agreement', 'Separation Agreement'),
            array('other', 'Other')
        );
        
        $this->populate_selector_table('epm_document_types', $document_types);
        
        // Digital asset types
        $digital_asset_types = array(
            array('email_accounts', 'Email Accounts'),
            array('social_media', 'Social Media Accounts'),
            array('cloud_storage', 'Cloud Storage'),
            array('cryptocurrency', 'Cryptocurrency Wallets'),
            array('online_banking', 'Online Banking'),
            array('investment_accounts', 'Online Investment Accounts'),
            array('domain_names', 'Domain Names'),
            array('websites', 'Websites/Blogs'),
            array('digital_photos', 'Digital Photos/Videos'),
            array('software_licenses', 'Software Licenses'),
            array('gaming_accounts', 'Gaming Accounts'),
            array('subscription_services', 'Subscription Services'),
            array('loyalty_programs', 'Loyalty Programs'),
            array('other', 'Other')
        );
        
        $this->populate_selector_table('epm_digital_asset_types', $digital_asset_types);
        
        // Personal property categories
        $personal_property_categories = array(
            array('jewelry', 'Jewelry'),
            array('artwork', 'Artwork'),
            array('antiques', 'Antiques'),
            array('collectibles', 'Collectibles'),
            array('vehicles', 'Vehicles'),
            array('boats', 'Boats/Watercraft'),
            array('electronics', 'Electronics'),
            array('furniture', 'Furniture'),
            array('appliances', 'Appliances'),
            array('tools', 'Tools/Equipment'),
            array('books', 'Books/Library'),
            array('musical_instruments', 'Musical Instruments'),
            array('sports_equipment', 'Sports Equipment'),
            array('clothing', 'Clothing/Accessories'),
            array('household_items', 'Household Items'),
            array('business_assets', 'Business Assets'),
            array('other', 'Other')
        );
        
        $this->populate_selector_table('epm_personal_property_categories', $personal_property_categories);
    }
    
    /**
     * Populate a selector table with default values
     */
    private function populate_selector_table($table_name, $values) {
        global $wpdb;
        
        $full_table_name = $wpdb->prefix . $table_name;
        
        // Check if table already has data
        $count = $wpdb->get_var("SELECT COUNT(*) FROM $full_table_name");
        
        if ($count > 0) {
            return; // Table already populated
        }
        
        $sort_order = 0;
        foreach ($values as $value_data) {
            $wpdb->insert(
                $full_table_name,
                array(
                    'value' => $value_data[0],
                    'label' => $value_data[1],
                    'is_active' => 1,
                    'sort_order' => $sort_order++
                ),
                array('%s', '%s', '%d', '%d')
            );
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
}
