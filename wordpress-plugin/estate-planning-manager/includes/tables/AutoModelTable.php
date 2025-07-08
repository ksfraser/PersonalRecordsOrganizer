<?php
require_once __DIR__ . '/EPM_AbstractTable.php';
require_once __DIR__ . '/TableInterface.php';

class AutoModelTable extends \EstatePlanningManager\Tables\EPM_AbstractTable implements \EstatePlanningManager\Tables\TableInterface {
    public function create($charset_collate) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_auto_models';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            make varchar(100) NOT NULL,
            model varchar(100) NOT NULL,
            year int(4) NOT NULL,
            PRIMARY KEY (id),
            KEY make (make),
            KEY model (model),
            KEY year (year)
        ) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    public function populate($charset_collate) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_auto_models';
        // Prepopulate with makes/models/years (North America, last 50 years)
        $makes_models = include __DIR__ . '/data/auto_makes_models.php';
        $years = range(date('Y')-49, date('Y'));
        foreach ($makes_models as $make => $models) {
            foreach ($models as $model) {
                foreach ($years as $year) {
                    $wpdb->insert($table_name, [
                        'make' => $make,
                        'model' => $model,
                        'year' => $year
                    ]);
                }
            }
        }
    }
    public static function get_all_options() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_auto_models';
        $array_a = defined('ARRAY_A') ? ARRAY_A : 'ARRAY_A';
        $results = $wpdb->get_results("SELECT id, make, model, year FROM $table_name ORDER BY make, model, year", $array_a);
        $options = array();
        foreach ($results as $row) {
            $id = is_array($row) ? $row['id'] : $row->id;
            $label = (is_array($row) ? $row['make'] : $row->make) . ' ' . (is_array($row) ? $row['model'] : $row->model) . ' ' . (is_array($row) ? $row['year'] : $row->year);
            $options[$id] = $label;
        }
        return $options;
    }
}
