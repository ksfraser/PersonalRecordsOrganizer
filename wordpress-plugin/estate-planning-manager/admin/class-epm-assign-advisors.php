<?php
// Admin screen for assigning advisors to clients and inviting new clients by email
if (!defined('ABSPATH')) exit;

class EPM_Assign_Advisors_Admin {
    /**
     * Singleton instance
     * @var self|null
     */
    private static $instance = null;

    /**
     * Get the singleton instance
     * @return self
     */
    public static function instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function init() {
        add_action('admin_menu', [__CLASS__, 'add_menu']);
        add_action('wp_ajax_epm_invite_client', [__CLASS__, 'ajax_invite_client']);
    }

    public static function add_menu() {
        add_menu_page(
            'Assign Advisors',
            'Assign Advisors',
            'edit_users',
            'epm-assign-advisors',
            [__CLASS__, 'render_page'],
            'dashicons-admin-users',
            81
        );
    }

    public static function render_page() {
        if (!current_user_can('edit_users')) {
            wp_die('You do not have permission to access this page.');
        }
        $advisors = get_users(['role' => 'financial_advisor']);
        $clients = get_users(['role__not_in' => ['administrator', 'financial_advisor']]);
        $db = EPM_Database::instance();
        echo '<div class="wrap"><h1>Assign Advisors to Clients</h1>';
        echo '<form method="post">';
        wp_nonce_field('epm_assign_advisors', 'epm_assign_advisors_nonce');
        echo '<table class="widefat"><thead><tr><th>Client</th><th>Current Advisor</th><th>Assign Advisor</th></tr></thead><tbody>';
        foreach ($clients as $client) {
            $client_id = $db->get_client_id_by_user_id($client->ID);
            $advisor_id = null;
            if ($client_id) {
                global $wpdb;
                $advisor_id = $wpdb->get_var($wpdb->prepare("SELECT advisor_id FROM {$wpdb->prefix}epm_clients WHERE id = %d", $client_id));
            }
            echo '<tr>';
            echo '<td>' . esc_html($client->display_name) . ' (' . esc_html($client->user_email) . ')</td>';
            echo '<td>';
            if ($advisor_id) {
                $advisor = get_user_by('ID', $advisor_id);
                echo esc_html($advisor ? $advisor->display_name : 'Unknown');
            } else {
                echo '<em>None</em>';
            }
            echo '</td>';
            echo '<td><select name="advisor_for_' . esc_attr($client->ID) . '"><option value="">-- None --</option>';
            foreach ($advisors as $advisor) {
                echo '<option value="' . esc_attr($advisor->ID) . '"' . ($advisor_id == $advisor->ID ? ' selected' : '') . '>' . esc_html($advisor->display_name) . '</option>';
            }
            echo '</select></td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
        echo '<p><input type="submit" class="button button-primary" value="Save Assignments"></p>';
        echo '</form>';
        // Invite new client
        echo '<h2>Invite New Client</h2>';
        echo '<form id="epm-invite-client-form">';
        echo '<label>Email: <input type="email" name="invite_email" required></label> ';
        echo '<label>Assign Advisor: <select name="advisor_id">';
        foreach ($advisors as $advisor) {
            echo '<option value="' . esc_attr($advisor->ID) . '">' . esc_html($advisor->display_name) . '</option>';
        }
        echo '</select></label> ';
        echo '<button type="submit" class="button">Send Invite</button>';
        echo '<span class="epm-invite-status" style="margin-left:10px;"></span>';
        echo '</form>';
        echo '<script>jQuery(function($){$("#epm-invite-client-form").on("submit",function(e){e.preventDefault();var form=$(this);var email=form.find("[name="invite_email"]").val();var advisor=form.find("[name="advisor_id"]").val();form.find(".epm-invite-status").text("Sending...");$.post(ajaxurl,{action:"epm_invite_client",invite_email:email,advisor_id:advisor,nonce:"' . wp_create_nonce('epm_invite_client') . '"},function(resp){if(resp.success){form.find(".epm-invite-status").text("Invite sent!").css("color","green");}else{form.find(".epm-invite-status").text("Error: "+resp.data).css("color","red");}});});});</script>';
        echo '</div>';
        // Save assignments
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && check_admin_referer('epm_assign_advisors', 'epm_assign_advisors_nonce')) {
            foreach ($clients as $client) {
                $field = 'advisor_for_' . $client->ID;
                if (isset($_POST[$field])) {
                    $advisor_id = intval($_POST[$field]);
                    $client_id = $db->get_client_id_by_user_id($client->ID);
                    if ($client_id) {
                        global $wpdb;
                        $wpdb->update($wpdb->prefix . 'epm_clients', ['advisor_id' => $advisor_id ?: null], ['id' => $client_id]);
                        // --- Ensure advisor is in epm_contacts ---
                        $advisor_user = get_user_by('ID', $advisor_id);
                        if ($advisor_user) {
                            $contact_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$wpdb->prefix}epm_contacts WHERE user_id = %d", $advisor_id));
                            if (!$contact_id) {
                                // Add advisor to contacts
                                \EstatePlanningManager\Models\PeopleModel::addPerson([
                                    'full_name' => $advisor_user->display_name,
                                    'email' => $advisor_user->user_email,
                                    'phone' => get_user_meta($advisor_id, 'phone', true),
                                    'user_id' => $advisor_id
                                ]);
                            }
                        }
                    }
                }
            }
            echo '<div class="updated"><p>Assignments saved.</p></div>';
        }
    }

    public static function ajax_invite_client() {
        check_ajax_referer('epm_invite_client', 'nonce');
        $email = sanitize_email($_POST['invite_email']);
        $advisor_id = intval($_POST['advisor_id']);
        if (!is_email($email) || !$advisor_id) {
            wp_send_json_error('Invalid input.');
        }
        // Check if user exists
        if (email_exists($email)) {
            wp_send_json_error('User already exists.');
        }
        // Create a pending invite (could be a row in epm_share_invites or a new table)
        global $wpdb;
        $table = $wpdb->prefix . 'epm_share_invites';
        $wpdb->insert($table, [
            'client_id' => null,
            'invitee_email' => $email,
            'sections' => json_encode([]),
            'permission_level' => 'view',
            'status' => 'pending',
            'created' => current_time('mysql'),
            'lastupdated' => current_time('mysql'),
            'advisor_id' => $advisor_id,
            'user_role' => 'estate_planning_client' // Set the desired WP role
        ]);
        // TODO: Send email invite (implement as needed)
        wp_send_json_success();
    }
}

EPM_Assign_Advisors_Admin::init();
