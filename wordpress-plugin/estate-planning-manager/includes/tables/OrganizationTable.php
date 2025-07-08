<?php
require_once __DIR__ . '/EPM_AbstractTable.php';
require_once __DIR__ . '/TableInterface.php';

class OrganizationTable extends EPM_AbstractTable implements TableInterface {
    public function create($charset_collate) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_organizations';
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            client_id bigint(20) NOT NULL,
            name varchar(255) NOT NULL,
            address text DEFAULT NULL,
            phone varchar(50) DEFAULT NULL,
            email varchar(255) DEFAULT NULL,
            account_number varchar(255) DEFAULT NULL,
            branch varchar(255) DEFAULT NULL,
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
        // No default data for organization table
    }

    /**
     * Get organization options for a client (for dropdowns)
     * @param int $client_id
     * @return array id => name
     */
    public static function get_org_options($client_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_organizations';
        // Use fallback if ARRAY_A is not defined
        $array_a = defined('ARRAY_A') ? ARRAY_A : 'ARRAY_A';
        $results = $wpdb->get_results($wpdb->prepare("SELECT id, name FROM $table WHERE client_id = %d ORDER BY name", $client_id), $array_a);
        $options = array();
        foreach ($results as $row) {
            // Support both array and object result
            $id = is_array($row) ? $row['id'] : $row->id;
            $name = is_array($row) ? $row['name'] : $row->name;
            $options[$id] = $name;
        }
        return $options;
    }
}
