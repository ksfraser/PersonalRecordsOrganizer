<?php
namespace EstatePlanningManager\Models;

class PeopleModel {
    private static $advisors = [
        ['id' => 1, 'full_name' => 'John Doe', 'email' => 'john@example.com', 'phone' => '555-1234'],
        ['id' => 2, 'full_name' => 'Jane Smith', 'email' => 'jane@example.com', 'phone' => '555-5678'],
        ['id' => 3, 'full_name' => 'Alice Johnson', 'email' => 'alice@example.com', 'phone' => '555-9999'],
    ];
    private static $next_id = 4;

    /**
     * Return an array of people for dropdowns: [ ['id' => 1, 'full_name' => 'John Doe'], ... ]
     * In production, this should query the people/persons table.
     */
    public static function getAllForDropdown() {
        global $wpdb;
        $table = isset($wpdb->prefix) ? $wpdb->prefix . 'epm_contacts' : 'epm_contacts';
        if (isset($wpdb) && $wpdb->get_var("SHOW TABLES LIKE '$table'") == $table) {
            $results = $wpdb->get_results("SELECT id, full_name FROM $table ORDER BY full_name ASC", ARRAY_A);
            return $results ? $results : [];
        }
        // Fallback to static demo data if table doesn't exist
        return array_map(function($a) {
            return ['id' => $a['id'], 'full_name' => $a['full_name']];
        }, self::$advisors);
    }

    /**
     * Return an associative array of people for dropdowns: [id => full_name, ...]
     */
    public static function getDropdownOptions() {
        $options = [];
        foreach (self::getAllForDropdown() as $person) {
            $options[$person['id']] = $person['full_name'];
        }
        return $options;
    }

    public static function getDefaultAdvisor() {
        // In production, this should query the DB for the user's default advisor
        return self::$advisors[0]; // Always return John Doe as default for now
    }

    public static function addAdvisor($full_name, $email, $phone) {
        $id = self::$next_id++;
        $advisor = [
            'id' => $id,
            'full_name' => $full_name,
            'email' => $email,
            'phone' => $phone
        ];
        self::$advisors[] = $advisor;
        return $advisor;
    }

    public static function addPerson($data) {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_contacts';
        $wpdb->insert($table, [
            'full_name' => $data['full_name'] ?? '',
            'email' => $data['email'] ?? '',
            'phone' => $data['phone'] ?? '',
            'address' => $data['address'] ?? '',
            'user_id' => $data['user_id'] ?? 0,
            'created_at' => current_time('mysql'),
        ]);
        return $wpdb->insert_id;
    }
    public static function addInstitute($data) {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_institutes';
        $wpdb->insert($table, [
            'name' => $data['name'] ?? '',
            'email' => $data['email'] ?? '',
            'phone' => $data['phone'] ?? '',
            'address' => $data['address'] ?? '',
            'account_number' => $data['account_number'] ?? '',
            'branch' => $data['branch'] ?? '',
            'user_id' => $data['user_id'] ?? 0,
            'created_at' => current_time('mysql'),
        ]);
        return $wpdb->insert_id;
    }
    public static function addContactEmail($data) {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_contact_emails';
        $wpdb->insert($table, [
            'contact_id' => $data['contact_id'] ?? 0,
            'email' => $data['email'] ?? '',
            'is_primary' => !empty($data['is_primary']) ? 1 : 0,
            'created' => current_time('mysql'),
            'lastupdated' => current_time('mysql'),
        ]);
        return $wpdb->insert_id;
    }
}
