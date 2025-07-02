<?php
namespace EstatePlanningManager\Models;

abstract class AbstractSectionModel {
    abstract public function getTableName();

    public function getAllRecordsForClient($client_id) {
        global $wpdb;
        $table = $this->getTableName();
        if (!method_exists($wpdb, 'get_results') || !method_exists($wpdb, 'prepare')) {
            return [];
        }
        $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE client_id = %d", $client_id), 'ARRAY_A');
        return $results ? $results : [];
    }

    /**
     * Generic saveRecord for all section models (insert only, update if needed)
     */
    public function saveRecord($data) {
        global $wpdb;
        $table = $this->getTableName();
        if (!isset($data['client_id']) || !is_numeric($data['client_id'])) {
            return false;
        }
        // Remove fields not in table definition
        $fields = method_exists($this, 'getFieldDefinitions') ? array_keys($this->getFieldDefinitions()) : array_keys($data);
        $insert = [];
        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $insert[$field] = $data[$field];
            }
        }
        $insert['client_id'] = $data['client_id'];
        // Insert row
        $result = $wpdb->insert($table, $insert);
        return $result !== false;
    }
}
