<?php
require_once __DIR__ . '/EPM_AbstractTable.php';
require_once __DIR__ . '/TableInterface.php';

class PhoneLineTypesTable extends \EstatePlanningManager\Tables\EPM_AbstractTable implements \EstatePlanningManager\Tables\TableInterface {
    public function create($charset_collate) {
        ob_start();
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_phone_line_types';
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (\n"
            . "id bigint(20) NOT NULL AUTO_INCREMENT,\n"
            . "value varchar(32) NOT NULL,\n"
            . "label varchar(100) NOT NULL,\n"
            . "is_active tinyint(1) DEFAULT 1,\n"
            . "sort_order int(11) DEFAULT 0,\n"
            . "created datetime DEFAULT CURRENT_TIMESTAMP,\n"
            . "lastupdated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,\n"
            . "PRIMARY KEY (id),\n"
            . "UNIQUE KEY value (value)\n"
            . ") $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        ob_end_clean();
    }
    public function populate($charset_collate) {
        ob_start();
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_phone_line_types';
        $defaults = [
            ['home', 'Home'],
            ['cell', 'Cell'],
            ['work', 'Work'],
            ['fax', 'Fax'],
            ['business', 'Business'],
            ['other', 'Other']
        ];
        $count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        if ($count > 0) {
            ob_end_clean();
            return;
        }
        $sort_order = 0;
        foreach ($defaults as $row) {
            $wpdb->insert($table_name, [
                'value' => $row[0],
                'label' => $row[1],
                'sort_order' => $sort_order++
            ]);
        }
        ob_end_clean();
    }
}
