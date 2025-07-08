<?php
require_once __DIR__ . '/TableInterface.php';

class PersonTable extends EPM_AbstractTable implements TableInterface {
    public function create($charset_collate) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_persons';
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            client_id bigint(20) NOT NULL,
            full_name varchar(255) NOT NULL,
            email varchar(255) DEFAULT NULL,
            phone varchar(50) DEFAULT NULL,
            address text DEFAULT NULL,
            relationship varchar(100) DEFAULT NULL,
            notes text DEFAULT NULL,
            created datetime DEFAULT CURRENT_TIMESTAMP,
            lastupdated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY client_id (client_id)
        ) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    public function populate($charset_collate) {
        // No default data for person table
    }
    public static function get_person_options($client_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_persons';
        $array_a = defined('ARRAY_A') ? ARRAY_A : 'ARRAY_A';
        $results = $wpdb->get_results($wpdb->prepare("SELECT id, full_name FROM $table_name WHERE client_id = %d ORDER BY full_name", $client_id), $array_a);
        $options = array();
        foreach ($results as $row) {
            $id = is_array($row) ? $row['id'] : $row->id;
            $name = is_array($row) ? $row['full_name'] : $row->full_name;
            $options[$id] = $name;
        }
        return $options;
    }
}
