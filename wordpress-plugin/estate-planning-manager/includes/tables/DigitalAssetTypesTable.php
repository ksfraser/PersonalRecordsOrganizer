<?php
require_once __DIR__ . '/EPM_AbstractTable.php';
require_once __DIR__ . '/TableInterface.php';

class DigitalAssetTypesTable extends \EstatePlanningManager\Tables\EPM_AbstractTable implements TableInterface {
    public function create($charset_collate) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_digital_asset_types';
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            value varchar(100) NOT NULL,
            label varchar(255) NOT NULL,
            is_active tinyint(1) DEFAULT 1,
            sort_order int(11) DEFAULT 0,
            created datetime DEFAULT CURRENT_TIMESTAMP,
            lastupdated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY value (value)
        ) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    public function populate($charset_collate) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_digital_asset_types';
        $defaults = [
            ['email_accounts', 'Email Accounts'],
            ['social_media', 'Social Media Accounts'],
            ['cloud_storage', 'Cloud Storage'],
            ['cryptocurrency', 'Cryptocurrency Wallets'],
            ['online_banking', 'Online Banking'],
            ['investment_accounts', 'Online Investment Accounts'],
            ['domain_names', 'Domain Names'],
            ['websites', 'Websites/Blogs'],
            ['digital_photos', 'Digital Photos/Videos'],
            ['software_licenses', 'Software Licenses'],
            ['gaming_accounts', 'Gaming Accounts'],
            ['subscription_services', 'Subscription Services'],
            ['loyalty_programs', 'Loyalty Programs'],
            ['other', 'Other']
        ];
        $count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        if ($count > 0) return;
        $sort_order = 0;
        foreach ($defaults as $row) {
            $wpdb->insert($table_name, [
                'value' => $row[0],
                'label' => $row[1],
                'is_active' => 1,
                'sort_order' => $sort_order++
            ], ['%s', '%s', '%d', '%d']);
        }
    }
}
