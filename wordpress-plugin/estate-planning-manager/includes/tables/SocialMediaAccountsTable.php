<?php
require_once __DIR__ . '/EPM_AbstractTable.php';
require_once __DIR__ . '/TableInterface.php';
require_once __DIR__ . '/../../public/models/SocialMediaAccountsModel.php';
use EstatePlanningManager\Models\SocialMediaAccountsModel;

class SocialMediaAccountsTable extends \EstatePlanningManager\Tables\EPM_AbstractTable implements \EstatePlanningManager\Tables\TableInterface {
    public function getTableName() {
        global $wpdb;
        return $wpdb->prefix . 'epm_social_media_accounts';
    }
    public function create($charset_collate) {
        $this->createGenericTable(
            $this->getTableName(),
            \EstatePlanningManager\Models\SocialMediaAccountsModel::class,
            $charset_collate,
            [
                'client_id BIGINT UNSIGNED NOT NULL',
                'suitecrm_guid VARCHAR(36) DEFAULT NULL',
                'wp_record_id BIGINT(20) DEFAULT NULL',
            ],
            [
                'KEY client_id (client_id)',
                'KEY suitecrm_guid (suitecrm_guid)',
                'KEY wp_record_id (wp_record_id)'
            ]
        );
    }
}
