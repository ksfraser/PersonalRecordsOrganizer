<?php
namespace EstatePlanningManager\Handlers;

class EPM_AddContactHandler {
    public static function handle() {
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(['message' => 'Unauthorized']);
        }
        $type = sanitize_text_field($_POST['contact_type'] ?? 'person');
        $section = sanitize_text_field($_POST['section'] ?? '');
        $name = '';
        if ($type === 'person') {
            $name = sanitize_text_field($_POST['person_name'] ?? '');
        } elseif ($type === 'organization') {
            $name = sanitize_text_field($_POST['org_name'] ?? '');
        }
        if (!$name) {
            wp_send_json_error(['message' => 'Name required.']);
        }
        global $wpdb;
        $table = $type === 'person' ? $wpdb->prefix . 'epm_persons' : $wpdb->prefix . 'epm_organizations';
        $data = $type === 'person' ? ['name' => $name] : ['organization_name' => $name];
        $result = $wpdb->insert($table, $data);
        $id = $wpdb->insert_id;
        if ($result && $id) {
            wp_send_json_success(['id' => $id, 'name' => $name]);
        } else {
            wp_send_json_error(['message' => 'Failed to add contact.']);
        }
    }
}

// Register AJAX handler outside the namespace
add_action('wp_ajax_epm_add_contact', ['EstatePlanningManager\\Handlers\\EPM_AddContactHandler', 'handle']);
