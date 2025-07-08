<?php
namespace EstatePlanningManager\Tables;

if (!defined('ABSPATH')) exit;

require_once __DIR__ . '/EPM_AbstractTable.php';
require_once __DIR__ . '/TableInterface.php';

class InsuranceTypeTable extends EPM_AbstractTable implements TableInterface {
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
            FOREIGN KEY (category_id) REFERENCES {$wpdb->prefix}epm_insurance_category(id) ON DELETE CASCADE,
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
    }
    public function populate($charset_collate) {
        global $wpdb;
        foreach (self::getPreFillSql() as $sql) {
            $wpdb->query($sql);
        }
    }
}
