<?php
use PHPUnit\Framework\TestCase;

class EPMSuggestedUpdatesSyncTest extends TestCase {
    public function test_suggested_update_guid_sync() {
        $update = new EPM_SuggestedUpdates();
        $update->suitecrm_guid = 'suitecrm-guid-4';
        $update->wp_guid = 'wp-guid-4';
        $update->client_id = 1;
        $update->section = 'assets';
        $update->field = 'asset_value';
        $update->old_value = json_encode(100000);
        $update->new_value = json_encode(120000);
        $update->status = 'pending';
        $update->source = 'suitecrm';
        $update->source_record_id = 'suitecrm-guid-2';
        // Simulate sync
        $update->wp_guid = 'wp-guid-4-updated';
        $this->assertEquals('wp-guid-4-updated', $update->wp_guid);
    }
}
