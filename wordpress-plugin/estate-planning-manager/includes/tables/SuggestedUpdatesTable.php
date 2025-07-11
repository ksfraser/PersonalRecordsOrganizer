<?php
require_once __DIR__ . '/EPM_AbstractTable.php';
require_once __DIR__ . '/TableInterface.php';


require_once __DIR__ . '/../../public/models/SuggestedUpdatesModel.php';
use EstatePlanningManager\Models\SuggestedUpdatesModel;

class SuggestedUpdatesTable extends \EstatePlanningManager\Tables\EPM_AbstractTable implements \EstatePlanningManager\Tables\TableInterface {
    /**
     * Create the suggested updates table using the generic method and model.
     * @param string $charset_collate
     */
    public function create($charset_collate) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_suggested_updates';
        $extraColumns = [
            'id' => 'BIGINT(20) NOT NULL AUTO_INCREMENT',
            'created_at' => 'DATETIME DEFAULT CURRENT_TIMESTAMP',
            'updated_at' => 'DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
        ];
        $extraKeys = [
            'PRIMARY KEY (id)',
            'KEY client_id (client_id)',
            'KEY status (status)'
        ];
        $this->createGenericTable($table_name, SuggestedUpdatesModel::class, $charset_collate, $extraColumns, $extraKeys);
    }

    public function populate($charset_collate) {
        // No default data for suggested updates
    }
}
