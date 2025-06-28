<?php
interface TableInterface {
    public function create_table($wpdb, $charset_collate);
    public function populate_defaults($wpdb);
}
