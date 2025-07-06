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

    /**
     * Get year options for dropdowns, using DOB if available, or 80 years ago.
     */
    protected function getYearOptions($client_id) {
        global $wpdb;
        $current_year = (int)date('Y');
        $earliest_year = $current_year - 80;
        // Try to get DOB from personal info
        $dob = null;
        $personal_table = $wpdb->prefix . 'epm_personal_property';
        if (method_exists($wpdb, 'get_row') && method_exists($wpdb, 'prepare')) {
            $row = $wpdb->get_row($wpdb->prepare("SELECT dob FROM $personal_table WHERE client_id = %d", $client_id), ARRAY_A);
            if ($row && !empty($row['dob'])) {
                $dob = $row['dob'];
                $dob_year = (int)date('Y', strtotime($dob));
                $age_10_year = $dob_year + 10;
                if ($age_10_year < $current_year) {
                    $earliest_year = $age_10_year;
                }
            }
        }
        $years = [];
        for ($y = $current_year; $y >= $earliest_year; $y--) {
            $years[$y] = $y;
        }
        return $years;
    }
}
