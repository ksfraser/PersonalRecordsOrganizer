<?php
// Table class for epm_defaults
if (!defined('ABSPATH')) exit;

require_once __DIR__ . '/EPM_AbstractTable.php';
require_once __DIR__ . '/TableInterface.php';

class DefaultsTable extends \EstatePlanningManager\Tables\EPM_AbstractTable implements \EstatePlanningManager\Tables\TableInterface {
    public static $table_name = 'epm_defaults';

    public static function get_full_table_name() {
        global $wpdb;
        return $wpdb->prefix . self::$table_name;
    }

    public static function create_table() {
        global $wpdb;
        $table_name = self::get_full_table_name();
        $charset_collate = $wpdb->get_charset_collate();
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $sql = "CREATE TABLE $table_name (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id BIGINT UNSIGNED NOT NULL,
            meta_key VARCHAR(100) NOT NULL,
            meta_value TEXT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY user_key (user_id, meta_key)
        ) $charset_collate;";
        dbDelta($sql);
    }
}
