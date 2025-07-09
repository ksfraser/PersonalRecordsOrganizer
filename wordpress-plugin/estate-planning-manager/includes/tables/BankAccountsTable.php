<?php
require_once __DIR__ . '/EPM_AbstractTable.php';
require_once __DIR__ . '/TableInterface.php';
require_once __DIR__ . '/../../public/models/BankingModel.php';
use EstatePlanningManager\Models\BankingModel;

class BankAccountsTable extends \EstatePlanningManager\Tables\EPM_AbstractTable implements \EstatePlanningManager\Tables\TableInterface {
    private function getSqlColumnsFromFieldDefinitions($fields) {
        $columns = [];
        foreach ($fields as $name => $def) {
            $dbType = isset($def['db_type']) ? $def['db_type'] : 'VARCHAR(255)';
            $columns[] = "$name $dbType DEFAULT NULL";
        }
        return $columns;
    }
    public function create($charset_collate) {
        ob_start(); // Prevent accidental output
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_bank_accounts';
        $modelFields = BankingModel::getFieldDefinitions();
        $modelColumns = $this->getSqlColumnsFromFieldDefinitions($modelFields);
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (\n"
            . "id bigint(20) NOT NULL AUTO_INCREMENT,\n"
            . "client_id bigint(20) NOT NULL,\n"
            . "suitecrm_guid varchar(36) DEFAULT NULL,\n"
            . "wp_record_id bigint(20) DEFAULT NULL,\n"
            . "bank_location VARCHAR(32) DEFAULT NULL,\n"
            . "bank_name VARCHAR(255) DEFAULT NULL,\n"
            . implode(",\n", $modelColumns) . ",\n"
            . "created datetime DEFAULT CURRENT_TIMESTAMP,\n"
            . "lastupdated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,\n"
            . "PRIMARY KEY (id),\n"
            . "KEY client_id (client_id),\n"
            . "KEY suitecrm_guid (suitecrm_guid),\n"
            . "KEY wp_record_id (wp_record_id)\n"
            . ") $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        // Always log the SQL for debugging
        error_log('[EPM] BankAccountsTable SQL: ' . $sql);
        $result = dbDelta($sql);
        error_log('[EPM] BankAccountsTable dbDelta result: ' . print_r($result, true));
        // Check if table exists after creation attempt
        if ($wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $table_name)) != $table_name) {
            error_log('[EPM] BankAccountsTable ERROR: Table not created: ' . $table_name);
        }
        ob_end_clean(); // Discard any output
    }
    public function populate($charset_collate) {
        // No default data for user data tables
    }
}
