<?php
namespace EstatePlanningManager\Models;

class OtherContractualObligationsModel extends AbstractSectionModel {
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
