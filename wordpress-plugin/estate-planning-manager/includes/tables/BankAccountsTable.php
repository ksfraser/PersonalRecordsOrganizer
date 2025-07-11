<?php
require_once __DIR__ . '/EPM_AbstractTable.php';
require_once __DIR__ . '/TableInterface.php';
require_once __DIR__ . '/../../public/models/BankingModel.php';
use EstatePlanningManager\Models\BankingModel;

class BankAccountsTable extends \EstatePlanningManager\Tables\EPM_AbstractTable implements \EstatePlanningManager\Tables\TableInterface {
    public function getTableName() {
        global $wpdb;
        return $wpdb->prefix . 'epm_bank_accounts';
    }
    public function create($charset_collate) {
        $this->createGenericTable(
            $this->getTableName(),
            \EstatePlanningManager\Models\BankingModel::class,
            $charset_collate,
            [
                'client_id BIGINT UNSIGNED NOT NULL',
                'suitecrm_guid VARCHAR(36) DEFAULT NULL',
                'wp_record_id BIGINT(20) DEFAULT NULL',
                'bank_location VARCHAR(32) DEFAULT NULL',
                'bank_name VARCHAR(255) DEFAULT NULL',
            ],
            [
                'KEY client_id (client_id)',
                'KEY suitecrm_guid (suitecrm_guid)',
                'KEY wp_record_id (wp_record_id)'
            ]
        );
    }
    public function populate($charset_collate) {
        // No default data for user data tables
    }
}
