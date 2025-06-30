<?php
namespace EstatePlanningManager\Models;

class PersonalModel {
    public function getTableName() {
        global $wpdb;
        return $wpdb->prefix . 'epm_personal';
    }
    public function getAllRecordsForUser($user_id) {
        global $wpdb;
        $table = $this->getTableName();
        $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE user_id = %d", $user_id), ARRAY_A);
        return $results ? $results : [];
    }
    public function getOwnerIdForSection($section, $user_id) {
        return $user_id;
    }
    public function getSummaryFields() {
        return ['id', 'name'];
    }
}
