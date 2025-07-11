<?php
namespace EstatePlanningManager\Tables;

if (!defined('ABSPATH')) exit;

require_once __DIR__ . '/EPM_AbstractTable.php';
require_once __DIR__ . '/TableInterface.php';

class InsuranceTypeTable extends \EstatePlanningManager\Tables\EPM_AbstractTable implements \EstatePlanningManager\Tables\TableInterface {
    public static function getTableName() {
        global $wpdb;
        return $wpdb->prefix . 'epm_insurance_type';
    }
    public static function getCreateTableSql() {
        global $wpdb;
        $charset_collate = method_exists($wpdb, 'get_charset_collate') ? $wpdb->get_charset_collate() : '';
        return "CREATE TABLE " . self::getTableName() . " (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(100) NOT NULL,
            category_id BIGINT UNSIGNED NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";
    }
    public static function getPreFillSql() {
        $table = self::getTableName();
        return [
            // Life types
            "INSERT INTO $table (id, name, category_id) VALUES
                (1, 'Whole Life', 1),
                (2, 'Universal', 1),
                (3, 'Term', 1),
                (4, 'Critical Illness', 1),
                (5, 'House', 3),
                (6, 'Auto', 2)
            ON DUPLICATE KEY UPDATE name=VALUES(name), category_id=VALUES(category_id);"
        ];
    }
    public function create($charset_collate) {
        global $wpdb;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $sql = self::getCreateTableSql();
        dbDelta($sql);
        // Add foreign key constraint after table creation
        $this->createForeignKeys(
            self::getTableName(),
            [
                [
                    'column' => 'category_id',
                    'ref_table' => $GLOBALS['wpdb']->prefix . 'epm_insurance_category',
                    'ref_column' => 'id',
                    'constraint' => 'fk_category_id',
                    'on_delete' => 'CASCADE',
                    'on_update' => 'CASCADE'
                ]
            ]
        );
    }
    public function populate($charset_collate) {
        global $wpdb;
        foreach (self::getPreFillSql() as $sql) {
            $wpdb->query($sql);
        }
    }
}
