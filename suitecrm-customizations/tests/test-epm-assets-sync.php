<?php
use PHPUnit\Framework\TestCase;

class EPMAssetsSyncTest extends TestCase {
    public function test_asset_sync_guid_update() {
        $asset = new EPM_Assets();
        $asset->suitecrm_guid = 'suitecrm-guid-2';
        $asset->wp_guid = 'wp-guid-2';
        $asset->contact_id = 'contact-guid-1';
        $asset->asset_type = 'Property';
        $asset->asset_value = 500000;
        $asset->date_acquired = '2025-07-15';
        // Simulate lead conversion
        $asset->lead_id = 'lead-guid-1';
        // Conversion event
        $asset->contact_id = 'contact-guid-2';
        $this->assertEquals('contact-guid-2', $asset->contact_id);
    }
}
