<?php
require_once __DIR__ . '/TableInterface.php';
require_once dirname(__DIR__) . '/EPM_PhoneValidator.php';
require_once dirname(__DIR__) . '/EPM_EmailValidator.php';

class InvestmentsTable implements TableInterface {
    public function create($charset_collate) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_investments';
        $wpdb->query("DROP TABLE IF EXISTS $table_name");
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            client_id bigint(20) NOT NULL,
            suitecrm_guid varchar(36) DEFAULT NULL,
            wp_record_id bigint(20) DEFAULT NULL,
            investment_type varchar(100) DEFAULT NULL,
            financial_company varchar(255) DEFAULT NULL,
            account_type varchar(100) DEFAULT NULL,
            account_number varchar(255) DEFAULT NULL,
            address text DEFAULT NULL,
            information_location text DEFAULT NULL,
            gift_or_inheritance varchar(10) DEFAULT NULL,
            collateral varchar(10) DEFAULT NULL,
            company_group varchar(255) DEFAULT NULL,
            auto_invest varchar(10) DEFAULT NULL,
            frequency varchar(50) DEFAULT NULL,
            account_paid_from varchar(255) DEFAULT NULL,
            beneficiary_person_id bigint(20) DEFAULT NULL,
            advisor_person_id bigint(20) DEFAULT NULL,
            lender_type ENUM('person','organization') DEFAULT NULL,
            lender_person_id bigint(20) DEFAULT NULL,
            lender_org_id bigint(20) DEFAULT NULL,
            created datetime DEFAULT CURRENT_TIMESTAMP,
            lastupdated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY client_id (client_id),
            KEY suitecrm_guid (suitecrm_guid),
            KEY wp_record_id (wp_record_id),
            KEY investment_type (investment_type),
            KEY beneficiary_person_id (beneficiary_person_id),
            KEY advisor_person_id (advisor_person_id),
            KEY lender_person_id (lender_person_id),
            KEY lender_org_id (lender_org_id),
            FOREIGN KEY (client_id) REFERENCES {$wpdb->prefix}epm_clients(id) ON DELETE CASCADE,
            FOREIGN KEY (beneficiary_person_id) REFERENCES {$wpdb->prefix}epm_persons(id) ON DELETE SET NULL,
            FOREIGN KEY (advisor_person_id) REFERENCES {$wpdb->prefix}epm_persons(id) ON DELETE SET NULL,
            FOREIGN KEY (lender_person_id) REFERENCES {$wpdb->prefix}epm_persons(id) ON DELETE SET NULL,
            FOREIGN KEY (lender_org_id) REFERENCES {$wpdb->prefix}epm_organizations(id) ON DELETE SET NULL
        ) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    public function populate($charset_collate) {
        // No default data for user data tables
    }
    public function save_client_data($client_id, $section, $data, $checkEmailDns = false) {
        // Validate email and phone fields (server-side)
        $email_fields = ['email', 'advisor_email', 'lender_email', 'beneficiary_email'];
        $phone_fields = ['phone', 'advisor_phone', 'lender_phone', 'beneficiary_phone'];
        foreach ($email_fields as $field) {
            if (!empty($data[$field]) && !EPM_EmailValidator::validateWithDns($data[$field], $checkEmailDns)) {
                return false;
            }
        }
        foreach ($phone_fields as $field) {
            if (!empty($data[$field]) && !EPM_PhoneValidator::validate($data[$field])) {
                return false;
            }
        }
        // Normalize lender fields
        if (isset($data['lender_type'])) {
            if ($data['lender_type'] === 'person') {
                $data['lender_org_id'] = null;
            } elseif ($data['lender_type'] === 'organization') {
                $data['lender_person_id'] = null;
            } else {
                $data['lender_person_id'] = null;
                $data['lender_org_id'] = null;
            }
        }
        // ...existing code...
    }
}
