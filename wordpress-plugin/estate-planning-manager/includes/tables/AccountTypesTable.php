<?php
require_once __DIR__ . '/TableInterface.php';
require_once __DIR__ . '/../../public/models/AccountTypesModel.php';
use EstatePlanningManager\Models\AccountTypesModel;

class AccountTypesTable extends EPM_AbstractTable implements TableInterface {
    private function getSqlColumnsFromFieldDefinitions($fields) {
        $columns = [];
        foreach ($fields as $name => $def) {
            $dbType = isset($def['db_type']) ? $def['db_type'] : 'VARCHAR(255)';
            $columns[] = "$name $dbType";
        }
        return $columns;
    }
    public function create($charset_collate) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_account_types';
        $modelFields = AccountTypesModel::getFieldDefinitions();
        $modelColumns = $this->getSqlColumnsFromFieldDefinitions($modelFields);
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (\n"
            . "id bigint(20) NOT NULL AUTO_INCREMENT,\n"
            . implode(",\n", $modelColumns) . ",\n"
            . "created datetime DEFAULT CURRENT_TIMESTAMP,\n"
            . "lastupdated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,\n"
            . "PRIMARY KEY (id),\n"
            . "UNIQUE KEY value (value)\n"
            . ") $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    public function populate($charset_collate) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_account_types';
        $defaults = [
            ['chequing', 'Chequing'],
            ['savings', 'Savings'],
            ['investment', 'Investment'],
            ['credit_line', 'Line of Credit'],
            ['mortgage', 'Mortgage'],
            ['loan', 'Loan'],
            ['other', 'Other']
        ];
        $count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        if ($count > 0) return;
        $sort_order = 0;
        foreach ($defaults as $row) {
            $wpdb->insert($table_name, [
                'value' => $row[0],
                'label' => $row[1],
                'is_active' => 1,
                'sort_order' => $sort_order
            ], ['%s', '%s', '%d', '%d']);
            $sort_order += 10;
        }
    }
}
