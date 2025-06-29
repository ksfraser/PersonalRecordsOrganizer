<?php
require_once __DIR__ . '/TableInterface.php';
require_once dirname(__DIR__) . '/EPM_PhoneValidator.php';
require_once dirname(__DIR__) . '/EPM_EmailValidator.php';

class InsuranceTable implements TableInterface {
    public function create($charset_collate) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_insurance';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            client_id bigint(20) NOT NULL,
            suitecrm_guid varchar(36) DEFAULT NULL,
            wp_record_id bigint(20) DEFAULT NULL,
            insurance_category varchar(100) DEFAULT NULL,
            insurance_type varchar(100) DEFAULT NULL,
            insurance_company varchar(255) DEFAULT NULL,
            policy_number varchar(255) DEFAULT NULL,
            address text DEFAULT NULL,
            policy_location text DEFAULT NULL,
            insured_person varchar(255) DEFAULT NULL,
            will_someone_become_owner varchar(10) DEFAULT NULL,
            company_association varchar(255) DEFAULT NULL,
            beneficiary_person_id bigint(20) DEFAULT NULL,
            advisor_person_id bigint(20) DEFAULT NULL,
            owner_person_id bigint(20) DEFAULT NULL,
            created datetime DEFAULT CURRENT_TIMESTAMP,
            lastupdated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY client_id (client_id),
            KEY suitecrm_guid (suitecrm_guid),
            KEY wp_record_id (wp_record_id),
            KEY insurance_category (insurance_category),
            KEY insurance_type (insurance_type),
            KEY beneficiary_person_id (beneficiary_person_id),
            KEY advisor_person_id (advisor_person_id),
            KEY owner_person_id (owner_person_id),
            --FOREIGN KEY (client_id) REFERENCES {$wpdb->prefix}epm_clients(id) ON DELETE CASCADE,
            --FOREIGN KEY (beneficiary_person_id) REFERENCES {$wpdb->prefix}epm_persons(id) ON DELETE SET NULL,
            --FOREIGN KEY (advisor_person_id) REFERENCES {$wpdb->prefix}epm_persons(id) ON DELETE SET NULL,
            --FOREIGN KEY (owner_person_id) REFERENCES {$wpdb->prefix}epm_persons(id) ON DELETE SET NULL
        ) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    public function populate($charset_collate) {
        // No default data for user data tables
    }
    public function save_client_data($client_id, $section, $data, $checkEmailDns = false) {
        // Validate email and phone fields (server-side)
        $email_fields = ['email', 'advisor_email', 'beneficiary_email', 'owner_email'];
        $phone_fields = ['phone', 'advisor_phone', 'beneficiary_phone', 'owner_phone'];
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
    }
}
