<?php
require_once __DIR__ . '/EPM_AbstractTable.php';
require_once __DIR__ . '/TableInterface.php';
require_once __DIR__ . '/../../public/models/PasswordManagementModel.php';
use EstatePlanningManager\Models\PasswordManagementModel;

class PasswordManagementTable extends \EstatePlanningManager\Tables\EPM_AbstractTable implements \EstatePlanningManager\Tables\TableInterface {
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
        $table_name = $wpdb->prefix . 'epm_password_management';
        $modelFields = PasswordManagementModel::getFieldDefinitions();
        $modelColumns = $this->getSqlColumnsFromFieldDefinitions($modelFields);
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (\n"
            . "id bigint(20) NOT NULL AUTO_INCREMENT,\n"
            . "client_id bigint(20) NOT NULL,\n"
            . "suitecrm_guid varchar(36) DEFAULT NULL,\n"
            . "wp_record_id bigint(20) DEFAULT NULL,\n"
            . implode(",\n", $modelColumns) . ",\n"
            . "created datetime DEFAULT CURRENT_TIMESTAMP,\n"
            . "lastupdated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,\n"
            . "PRIMARY KEY (id),\n"
            . "KEY client_id (client_id),\n"
            . "KEY suitecrm_guid (suitecrm_guid),\n"
            . "KEY wp_record_id (wp_record_id)\n"
            . ") $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}
