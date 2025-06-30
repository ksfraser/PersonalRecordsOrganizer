<?php
// tests/test-section-sharing.php
use PHPUnit\Framework\TestCase;

class TestSectionSharing extends TestCase {
    public function setUp(): void {
        global $wpdb;
        // Setup: insert test data into epm_section_shares and users table
        $wpdb->query("DELETE FROM {$wpdb->prefix}epm_section_shares");
        $wpdb->query("DELETE FROM {$wpdb->users} WHERE user_email LIKE 'testuser%' OR user_email LIKE 'shareduser%'");
        $wpdb->insert($wpdb->users, [
            'user_login' => 'testuser1',
            'user_pass' => wp_hash_password('pass'),
            'user_email' => 'testuser1@example.com',
            'display_name' => 'Test User 1'
        ]);
        $wpdb->insert($wpdb->users, [
            'user_login' => 'shareduser1',
            'user_pass' => wp_hash_password('pass'),
            'user_email' => 'shareduser1@example.com',
            'display_name' => 'Shared User 1'
        ]);
        $this->owner_id = $wpdb->get_var("SELECT ID FROM {$wpdb->users} WHERE user_login = 'testuser1'");
        $this->shared_id = $wpdb->get_var("SELECT ID FROM {$wpdb->users} WHERE user_login = 'shareduser1'");
        $wpdb->insert($wpdb->prefix . 'epm_section_shares', [
            'owner_id' => $this->owner_id,
            'shared_with_id' => $this->shared_id,
            'section_key' => 'personal'
        ]);
    }
    public function tearDown(): void {
        global $wpdb;
        $wpdb->query("DELETE FROM {$wpdb->prefix}epm_section_shares");
        $wpdb->query("DELETE FROM {$wpdb->users} WHERE user_email LIKE 'testuser%' OR user_email LIKE 'shareduser%'");
    }
    public function test_get_users_who_shared_with() {
        $users = \EPM_Shortcodes::get_users_who_shared_with($this->shared_id);
        $this->assertNotEmpty($users);
        $this->assertEquals('Test User 1', $users[0]['display_name']);
    }
    public function test_get_sections_shared_with_user() {
        $sections = \EPM_Shortcodes::get_sections_shared_with_user($this->owner_id, $this->shared_id);
        $this->assertContains('personal', $sections);
    }
}
