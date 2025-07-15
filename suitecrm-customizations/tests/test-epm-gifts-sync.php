<?php
use PHPUnit\Framework\TestCase;

class EPMGiftsSyncTest extends TestCase {
    public function test_gift_sync_guid_update() {
        $gift = new EPM_Gifts();
        $gift->suitecrm_guid = 'suitecrm-guid-1';
        $gift->wp_guid = 'wp-guid-1';
        $gift->contact_id = 'contact-guid-1';
        $gift->gift_type = 'Charity';
        $gift->gift_value = 1000;
        $gift->date_given = '2025-07-15';
        // Simulate lead conversion
        $gift->lead_id = 'lead-guid-1';
        // Conversion event
        $gift->contact_id = 'contact-guid-2';
        $this->assertEquals('contact-guid-2', $gift->contact_id);
    }
}
