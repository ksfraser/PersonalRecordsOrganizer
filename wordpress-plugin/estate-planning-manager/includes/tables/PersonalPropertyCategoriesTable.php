<?php
require_once __DIR__ . '/EPM_AbstractTable.php';
require_once __DIR__ . '/TableInterface.php';

class PersonalPropertyCategoriesTable extends \EstatePlanningManager\Tables\EPM_AbstractTable implements \EstatePlanningManager\Tables\TableInterface {
    public function create($charset_collate) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_personal_property_categories';
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
        $table_name = $wpdb->prefix . 'epm_personal_property_categories';
        $defaults = [
            ['jewelry', 'Jewelry'],
            ['artwork', 'Artwork'],
            ['antiques', 'Antiques'],
            ['collectibles', 'Collectibles'],
            ['vehicles', 'Vehicles'],
            ['boats', 'Boats/Watercraft'],
            ['electronics', 'Electronics'],
            ['furniture', 'Furniture'],
            ['appliances', 'Appliances'],
            ['tools', 'Tools/Equipment'],
            ['books', 'Books/Library'],
            ['musical_instruments', 'Musical Instruments'],
            ['sports_equipment', 'Sports Equipment'],
            ['clothing', 'Clothing/Accessories'],
            ['household_items', 'Household Items'],
            ['business_assets', 'Business Assets'],
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
