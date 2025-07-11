<?php
namespace EstatePlanningManager\Tables;
// Abstract base class for EPM table classes
if (!defined('ABSPATH')) exit;

abstract class EPM_AbstractTable {
    /**
     * Generic table creation utility for EPM tables.
     * @param string $table_name
     * @param string $modelClass (must have getFieldDefinitions())
     * @param string $charset_collate
     * @param array $extraColumns (optional extra columns before model fields)
     * @param array $extraKeys (optional extra keys after PRIMARY KEY)
     */
    protected function createGenericTable($table_name, $modelClass, $charset_collate, $extraColumns = [], $extraKeys = []) {
        global $wpdb;
        $fields = $modelClass::getFieldDefinitions();
        $columns = array_merge([
            'id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT',
        ], $extraColumns);
        foreach ($fields as $name => $def) {
            $columns[] = "$name {$def['db_type']}" . (empty($def['required']) ? '' : ' NOT NULL');
        }
        $columns[] = 'created_at DATETIME DEFAULT CURRENT_TIMESTAMP';
        $columns[] = 'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP';
        $columns[] = 'PRIMARY KEY  (id)';
        foreach ($extraKeys as $key) {
            $columns[] = $key;
        }
        $sql = "CREATE TABLE $table_name (\n    " . implode(",\n    ", $columns) . "\n) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Create the table. Override in child if needed.
     */
    public function create($charset_collate) {
        // Default: do nothing
    }
    /**
     * Populate the table with default data. Override in child if needed.
     */
    public function populate($charset_collate) {
        // Default: do nothing
    }
}
