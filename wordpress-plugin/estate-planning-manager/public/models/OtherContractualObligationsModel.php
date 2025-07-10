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
        return $this->table;
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
