<?php
namespace EstatePlanningManager\Handlers;

class EPM_AddAdvisorHandler {
    public static function handle() {
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(['message' => 'Unauthorized']);
        }
        global $wpdb;
        $email = sanitize_email($_POST['email'] ?? '');
        $phone = preg_replace('/\D/', '', $_POST['phone'] ?? '');
        $name = sanitize_text_field($_POST['name'] ?? '');
        if (!$email && !$phone) {
            wp_send_json_error(['message' => 'Email or phone required.']);
        }
        // Try to find contact by email or phone
        $contact_id = null;
        if ($email) {
            $contact_id = $wpdb->get_var($wpdb->prepare("SELECT c.id FROM {$wpdb->prefix}epm_contacts c JOIN {$wpdb->prefix}epm_contact_emails e ON c.id = e.contact_id WHERE e.email = %s", $email));
        }
        if (!$contact_id && $phone) {
            $contact_id = $wpdb->get_var($wpdb->prepare("SELECT c.id FROM {$wpdb->prefix}epm_contacts c JOIN {$wpdb->prefix}epm_contact_phones p ON c.id = p.contact_id WHERE p.phone_number = %s", $phone));
        }
        if ($contact_id) {
            $wpdb->update($wpdb->prefix . 'epm_contacts', ['is_advisor' => 1], ['id' => $contact_id]);
        } else {
            $wpdb->insert($wpdb->prefix . 'epm_contacts', ['name' => $name, 'is_advisor' => 1]);
            $contact_id = $wpdb->insert_id;
            if ($email) {
                $wpdb->insert($wpdb->prefix . 'epm_contact_emails', ['contact_id' => $contact_id, 'email' => $email, 'is_primary' => 1]);
            }
            if ($phone) {
                $wpdb->insert($wpdb->prefix . 'epm_contact_phones', ['contact_id' => $contact_id, 'phone_number' => $phone, 'type' => 'Other', 'is_primary' => 1]);
            }
        }
        if ($contact_id) {
            wp_send_json_success(['id' => $contact_id, 'name' => $name ?: $email ?: $phone]);
        } else {
            wp_send_json_error(['message' => 'Failed to add advisor.']);
        }
    }
}

// Register AJAX handler outside the namespace
add_action('wp_ajax_epm_add_advisor', ['EstatePlanningManager\\Handlers\\EPM_AddAdvisorHandler', 'handle']);
