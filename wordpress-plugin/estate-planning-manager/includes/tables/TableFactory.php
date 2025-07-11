<?php
// Always require the abstract table base
require_once __DIR__ . '/EPM_AbstractTable.php';

class TableFactory {
    public static function getTables() {
        // Dynamically require Table classes managed by ModelMap
        require_once dirname(__DIR__, 2) . '/public/model-map.php';
        $sectionTableMap = \EstatePlanningManager\ModelMap::getSectionModelMap();
        $typesTableMap = \EstatePlanningManager\ModelMap::getTypesTableMap();

        // Section tables
        foreach ($sectionTableMap as $section => $model_class) {
            $table_class_name = str_replace('Model', 'Table', substr($model_class, strrpos($model_class, '\\') + 1));
            $table_file = __DIR__ . "/{$table_class_name}.php";
            if (file_exists($table_file)) {
                require_once $table_file;
            }
        }
        // Types/admin tables
        foreach ($typesTableMap as $typesClass) {
            $table_file = __DIR__ . "/{$typesClass}.php";
            if (file_exists($table_file)) {
                require_once $table_file;
            }
        }
        // Manually require Table classes not managed by ModelMap
        $manualTables = array(
            'PersonTable',
            'PersonXrefTable',
            'ClientsTable',
            'UserPreferencesTable',
            'SuggestedUpdatesTable',
            'EmergencyContactsTable',
            'ContactsTable',
            'DefaultsTable'
        );
        foreach ($manualTables as $manualClass) {
            $table_file = __DIR__ . "/{$manualClass}.php";
            if (file_exists($table_file)) {
                require_once $table_file;
            }
        }

        $tables = array();
        // Instantiate section tables
        foreach ($sectionTableMap as $section => $model_class) {
            $table_class_name = str_replace('Model', 'Table', substr($model_class, strrpos($model_class, '\\') + 1));
            $fqcn = "EstatePlanningManager\\Tables\\$table_class_name";
            if (class_exists($fqcn)) {
                $tables[] = new $fqcn();
            } elseif (class_exists($table_class_name)) {
                $tables[] = new $table_class_name();
            }
        }
        // Add any additional tables not covered by the model map
        $extraTables = array(
            new ClientsTable(),
            new UserPreferencesTable(),
            new PersonTable(),
            new PersonXrefTable(),
            new SuggestedUpdatesTable(),
            new EmergencyContactsTable(),
            new ContactsTable(),
            new DefaultsTable()
        );
        // Add all admin/fk tables
        foreach ($typesTableMap as $typesClass) {
            $fqcn = "EstatePlanningManager\\Tables\\$typesClass";
            if (class_exists($fqcn)) {
                $extraTables[] = new $fqcn();
            } elseif (class_exists($typesClass)) {
                $extraTables[] = new $typesClass();
            }
        }
        // Only add if not already present
        $added = array();
        foreach ($tables as $t) {
            $added[get_class($t)] = true;
        }
        foreach ($extraTables as $t) {
            if (!isset($added[get_class($t)])) {
                $tables[] = $t;
            }
        }
        return $tables;
    }

    public static function dropAllTables() {
        global $wpdb;
        $tables = array(
            'epm_investments',
            'epm_bank_accounts',
            'epm_real_estate',
            'epm_personal_property',
            'epm_digital_assets',
            'epm_debtors_creditors',
            'epm_person_xref',
            'epm_insurance',
            'epm_persons',
            'epm_organizations',
            'epm_clients'
        );
        foreach ($tables as $table) {
            $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}$table");
        }
    }
}