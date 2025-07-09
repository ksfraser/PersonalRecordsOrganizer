<?php
namespace EstatePlanningManager\Tables;

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
            advisor_user_id BIGINT UNSIGNED NOT NULL,
            name VARCHAR(100) NOT NULL,
            value TEXT NULL,
            created DATETIME DEFAULT CURRENT_TIMESTAMP,
            lastupdated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY user_key (advisor_user_id, name)
        ) $charset_collate;";
        dbDelta($sql);
    }

    public function create($charset_collate) {
        self::create_table();
    }
}
