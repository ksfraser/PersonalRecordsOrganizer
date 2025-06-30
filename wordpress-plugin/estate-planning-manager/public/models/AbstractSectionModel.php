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
}
