<?php
namespace EstatePlanningManager\Tables;

require_once __DIR__ . '/EPM_AbstractTable.php';
require_once __DIR__ . '/TableInterface.php';
use EstatePlanningManager\Models\PersonalModel;

if (!defined('ABSPATH')) exit;

class PersonalTable extends EPM_AbstractTable implements TableInterface {
    public function create($charset_collate) {
        $this->createGenericTable(
            $this->getTableName(),
            PersonalModel::class,
            $charset_collate,
            [
                'client_id BIGINT UNSIGNED NOT NULL',
                'suitecrm_guid VARCHAR(36) DEFAULT NULL',
            ]
        );
    }

    public function getTableName() {
        global $wpdb;
        return $wpdb->prefix . 'epm_personal';
    }
}
