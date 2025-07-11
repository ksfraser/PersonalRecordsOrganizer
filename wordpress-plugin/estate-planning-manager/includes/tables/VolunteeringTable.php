<?php
require_once __DIR__ . '/EPM_AbstractTable.php';
require_once __DIR__ . '/TableInterface.php';
require_once __DIR__ . '/../../public/models/VolunteeringModel.php';

use EstatePlanningManager\Models\VolunteeringModel;

class VolunteeringTable extends \EstatePlanningManager\Tables\EPM_AbstractTable implements \EstatePlanningManager\Tables\TableInterface {
    public function getTableName() {
        global $wpdb;
        return $wpdb->prefix . 'epm_volunteering';
    }
    public function create($charset_collate) {
        $this->createGenericTable(
            $this->getTableName(),
            \EstatePlanningManager\Models\VolunteeringModel::class,
            $charset_collate,
            [],
            [
                'KEY client_id (client_id)'
            ]
        );
    }
    public function populate($charset_collate) {
        // No default data for user data tables
    }
}
