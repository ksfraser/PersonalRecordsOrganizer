<?php
/**
 * Tests for sharing/invite and manage shares UI logic
 */

class Test_EPM_Shares extends EPM_Test_Case {
    /**
     * Test that manage shares screen lists shares for the user
     */
    public function test_manage_shares_lists_shares() {
        $shortcodes = EPM_Shortcodes::instance();
        $db = EPM_Database::instance();
        $client_id = $db->get_client_id_by_user_id($this->client_user_id);
        global $wpdb;
        $table = $wpdb->prefix . 'epm_share_invites';
        // Insert a fake share
        $wpdb->insert($table, [
            'client_id' => $client_id,
            'invitee_email' => 'shareduser@example.com',
            'invited_by_user_id' => $this->client_user_id,
            'sections' => json_encode(['personal','banking']),
            'permission_level' => 'view',
            'invite_token' => wp_generate_password(32, false),
            'status' => 'accepted',
            'created_at' => current_time('mysql'),
        ]);
        // Render manage shares
        ob_start();
        $shortcodes->render_manage_shares();
        $html = ob_get_clean();
        $this->assertStringContainsString('shareduser@example.com', $html);
        $this->assertStringContainsString('personal', $html);
        $this->assertStringContainsString('banking', $html);
    }

    /**
     * Test that shared with you screen lists shares for the invitee
     */
    public function test_shared_with_you_lists_shares() {
        $shortcodes = EPM_Shortcodes::instance();
        $db = EPM_Database::instance();
        $client_id = $db->get_client_id_by_user_id($this->client_user_id);
        global $wpdb;
        $table = $wpdb->prefix . 'epm_share_invites';
        // Insert a fake share for the advisor
        $wpdb->insert($table, [
            'client_id' => $client_id,
            'invitee_email' => 'advisor@test.com',
            'invited_by_user_id' => $this->client_user_id,
            'sections' => json_encode(['personal','banking']),
            'permission_level' => 'view',
            'invite_token' => wp_generate_password(32, false),
            'status' => 'accepted',
            'created_at' => current_time('mysql'),
        ]);
        // Set current user to advisor
        wp_set_current_user($this->advisor_user_id);
        ob_start();
        $shortcodes->render_shared_with_you();
        $html = ob_get_clean();
        $this->assertStringContainsString('personal', $html);
        $this->assertStringContainsString('banking', $html);
        $this->assertStringContainsString('advisor@test.com', $html);
    }
}
