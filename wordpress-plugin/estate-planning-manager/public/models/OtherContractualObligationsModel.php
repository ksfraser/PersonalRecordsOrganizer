<?php
namespace EstatePlanningManager\Models;

class OtherContractualObligationsModel extends AbstractSectionModel {
    /**
     * Return field definitions for the Other Contractual Obligations section (for use in forms and shortcodes)
     */
    public static function getFieldDefinitions() {
        return [
            'obligation_type' => [
                'label' => 'Obligation Type',
                'type' => 'text',
            ],
            'counterparty' => [
                'label' => 'Counterparty',
                'type' => 'text',
            ],
            'amount' => [
                'label' => 'Amount',
                'type' => 'text',
            ],
            'start_date' => [
                'label' => 'Start Date',
                'type' => 'date',
            ],
            'end_date' => [
                'label' => 'End Date',
                'type' => 'date',
            ],
            'document_location' => [
                'label' => 'Document Location',
                'type' => 'text',
            ],
            'description' => [
                'label' => 'Description',
                'type' => 'textarea',
            ],
        ];
    }
    protected $table = 'epm_other_contractual_obligations';
    protected $fields = [
        'id',
        'client_id',
        'suitecrm_guid',
        'wp_record_id',
        'description',
        'location_of_documents',
        'notes',
        'created',
        'updated',
    ];
    public function getTableName() {
        global $wpdb;
        return $wpdb->prefix . 'epm_other_contractual_obligations';
    }
    /**
     * Create the other contractual obligations table if it does not exist
     *
     * @param string $charset_collate
     * @phpdoc
     * @uml
     * class OtherContractualObligationsModel {
     *   +static createTable($charset_collate)
     * }
     */
    public static function createTable($charset_collate) {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_other_contractual_obligations';
        $sql = "CREATE TABLE IF NOT EXISTS $table (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            client_id BIGINT(20) NOT NULL,
            obligation_type VARCHAR(255) DEFAULT NULL,
            counterparty VARCHAR(255) DEFAULT NULL,
            amount VARCHAR(100) DEFAULT NULL,
            start_date DATE DEFAULT NULL,
            end_date DATE DEFAULT NULL,
            document_location VARCHAR(255) DEFAULT NULL,
            description TEXT DEFAULT NULL,
            created DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            KEY idx_client (client_id)
        ) ENGINE=InnoDB $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    public function saveRecord($data) {
        // Remove id to allow auto-increment
        if (isset($data['id'])) unset($data['id']);
        global $wpdb;
        $table = $wpdb->prefix . $this->getTableName();
        $data['created'] = current_time('mysql');
        $wpdb->insert($table, $data);
        return $wpdb->insert_id;
    }
}
