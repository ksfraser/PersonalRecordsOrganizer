<?php
/**
 * Table for storing known banks and credit unions by region.
 */
class BankNamesTable implements TableInterface {
    public function create($charset_collate) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_bank_names';
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            region varchar(32) NOT NULL,
            name varchar(255) NOT NULL,
            is_active tinyint(1) DEFAULT 1,
            sort_order int(11) DEFAULT 0,
            created datetime DEFAULT CURRENT_TIMESTAMP,
            lastupdated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY region (region),
            KEY name (name)
        ) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    public function populate($charset_collate) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_bank_names';
        $defaults = [
            // Canada
            ['canada', 'Royal Bank of Canada (RBC)'],
            ['canada', 'Toronto-Dominion Bank (TD)'],
            ['canada', 'Scotiabank'],
            ['canada', 'Bank of Montreal (BMO)'],
            ['canada', 'Canadian Imperial Bank of Commerce (CIBC)'],
            ['canada', 'National Bank of Canada'],
            ['canada', 'Desjardins Group'],
            ['canada', 'ATB Financial'],
            ['canada', 'Vancity Credit Union'],
            ['canada', 'Meridian Credit Union'],
            ['canada', 'Other'],
            // USA
            ['usa', 'JPMorgan Chase'],
            ['usa', 'Bank of America'],
            ['usa', 'Wells Fargo'],
            ['usa', 'Citibank'],
            ['usa', 'U.S. Bank'],
            ['usa', 'PNC Bank'],
            ['usa', 'Truist Bank'],
            ['usa', 'Capital One'],
            ['usa', 'Ally Bank'],
            ['usa', 'Navy Federal Credit Union'],
            ['usa', 'Other'],
            // Europe
            ['europe', 'HSBC'],
            ['europe', 'Barclays'],
            ['europe', 'Deutsche Bank'],
            ['europe', 'BNP Paribas'],
            ['europe', 'Santander'],
            ['europe', 'UBS'],
            ['europe', 'Credit Suisse'],
            ['europe', 'ING'],
            ['europe', 'UniCredit'],
            ['europe', 'Other'],
        ];
        $count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        if ($count > 0) return;
        $sort_orders = ['canada'=>0, 'usa'=>0, 'europe'=>0];
        foreach ($defaults as $row) {
            $region = $row[0];
            $wpdb->insert($table_name, [
                'region' => $region,
                'name' => $row[1],
                'is_active' => 1,
                'sort_order' => $sort_orders[$region]
            ], ['%s', '%s', '%d', '%d']);
            $sort_orders[$region] += 10;
        }
    }
}
