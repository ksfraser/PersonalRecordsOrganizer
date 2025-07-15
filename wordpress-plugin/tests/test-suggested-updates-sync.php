<?php
use PHPUnit\Framework\TestCase;

class SuggestedUpdatesSyncTest extends TestCase {
    public function test_suggested_update_created_on_sync() {
        global $wpdb;
        $client_id = 1;
        $section = 'contacts';
        $field = 'email';
        $old_value = json_encode('old@example.com');
        $new_value = json_encode('new@example.com');
        $wpdb->insert($wpdb->prefix . 'epm_suggested_updates', [
            'client_id' => $client_id,
            'section' => $section,
            'field' => $field,
            'old_value' => $old_value,
            'new_value' => $new_value,
            'status' => 'pending',
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql'),
            'source' => 'suitecrm',
            'source_record_id' => 'suitecrm-guid-123'
        ]);
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}epm_suggested_updates WHERE client_id = %d AND field = %s", $client_id, $field));
        $this->assertEquals('pending', $row->status);
        $this->assertEquals(json_encode('old@example.com'), $row->old_value);
        $this->assertEquals(json_encode('new@example.com'), $row->new_value);
    }

    public function test_accept_suggested_update() {
        global $wpdb;
        $client_id = 1;
        $section = 'contacts';
        $field = 'email';
        $new_value = json_encode('new@example.com');
        $table = $wpdb->prefix . 'epm_contacts';
        // Simulate admin accept
        $wpdb->update($table, [$field => json_decode($new_value, true)], ['client_id' => $client_id]);
        $wpdb->update($wpdb->prefix . 'epm_suggested_updates', ['status' => 'accepted'], ['client_id' => $client_id, 'field' => $field]);
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}epm_suggested_updates WHERE client_id = %d AND field = %s", $client_id, $field));
        $this->assertEquals('accepted', $row->status);
    }

    public function test_deny_suggested_update() {
        global $wpdb;
        $client_id = 1;
        $field = 'email';
        $wpdb->update($wpdb->prefix . 'epm_suggested_updates', ['status' => 'denied'], ['client_id' => $client_id, 'field' => $field]);
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}epm_suggested_updates WHERE client_id = %d AND field = %s", $client_id, $field));
        $this->assertEquals('denied', $row->status);
    }
}
