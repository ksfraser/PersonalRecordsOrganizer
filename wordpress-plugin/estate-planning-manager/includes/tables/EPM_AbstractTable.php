<?php
namespace EstatePlanningManager\Tables;
// Abstract base class for EPM table classes
if (!defined('ABSPATH')) exit;

abstract class EPM_AbstractTable {
    /**
     * Create foreign key constraints for a table.
     * @param string $table_name
     * @param array $foreignKeys Array of foreign key definitions. Each item: [
     *   'column' => 'category_id',
     *   'ref_table' => 'wp_epm_insurance_category',
     *   'ref_column' => 'id',
     *   'constraint' => 'fk_category_id',
     *   'on_delete' => 'CASCADE',
     *   'on_update' => 'CASCADE'
     * ]
     */
    protected function createForeignKeys($table_name, $foreignKeys = []) {
        global $wpdb;
        foreach ($foreignKeys as $fk) {
            $constraint = isset($fk['constraint']) ? $fk['constraint'] : 'fk_' . $fk['column'];
            $onDelete = isset($fk['on_delete']) ? 'ON DELETE ' . $fk['on_delete'] : '';
            $onUpdate = isset($fk['on_update']) ? 'ON UPDATE ' . $fk['on_update'] : '';
            // Check if constraint already exists
            $exists = $wpdb->get_var($wpdb->prepare(
                "SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND CONSTRAINT_NAME = %s AND CONSTRAINT_TYPE = 'FOREIGN KEY'",
                DB_NAME,
                str_replace($wpdb->prefix, '', $table_name),
                $constraint
            ));
            if (!$exists) {
                $sql = sprintf(
                    'ALTER TABLE %s ADD CONSTRAINT %s FOREIGN KEY (%s) REFERENCES %s(%s) %s %s;',
                    $table_name,
                    $constraint,
                    $fk['column'],
                    $fk['ref_table'],
                    $fk['ref_column'],
                    $onDelete,
                    $onUpdate
                );
                $wpdb->query($sql);
            }
        }
    }
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
