<?php
namespace EstatePlanningManager\Tables;

if (!defined('ABSPATH')) exit;

class InsuranceCategoryTable {
    public static function getTableName() {
        global $wpdb;
        return $wpdb->prefix . 'epm_insurance_category';
    }
    public static function getCreateTableSql() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        return "CREATE TABLE " . self::getTableName() . " (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(100) NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";
    }
    public static function getPreFillSql() {
        $table = self::getTableName();
        return [
            "INSERT INTO $table (id, name) VALUES (1, 'Life'), (2, 'Auto'), (3, 'House') ON DUPLICATE KEY UPDATE name=VALUES(name);"
        ];
    }
}
