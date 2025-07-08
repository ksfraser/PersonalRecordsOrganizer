<?php
require_once __DIR__ . '/TableInterface.php';
require_once __DIR__ . '/../../public/models/CharitableGiftsModel.php';
use EstatePlanningManager\Models\CharitableGiftsModel;

class CharitableGiftsTable extends EPM_AbstractTable implements TableInterface {
    private function getSqlColumnsFromFieldDefinitions($fields) {
        $columns = [];
        foreach ($fields as $name => $def) {
            $dbType = isset($def['db_type']) ? $def['db_type'] : 'VARCHAR(255)';
            $columns[] = "$name $dbType DEFAULT NULL";
        }
        return $columns;
    }
    public function create($charset_collate) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_charitable_gifts';
        $modelFields = CharitableGiftsModel::getFieldDefinitions();
        $modelColumns = $this->getSqlColumnsFromFieldDefinitions($modelFields);
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (\n"
            . "id bigint(20) NOT NULL AUTO_INCREMENT,\n"
            . implode(",\n", $modelColumns) . ",\n"
            . "created datetime DEFAULT CURRENT_TIMESTAMP,\n"
            . "lastupdated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,\n"
            . "PRIMARY KEY (id),\n"
            . "KEY client_id (client_id)\n"
            . ") $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    public function populate($charset_collate) {
        // No default data for user data tables
    }
}
