<?php
use PHPUnit\Framework\TestCase;

class EPMLiabilitiesSyncTest extends TestCase {
    public function test_liability_sync_guid_update() {
        $liability = new EPM_Liabilities();
        $liability->suitecrm_guid = 'suitecrm-guid-3';
        $liability->wp_guid = 'wp-guid-3';
        $liability->contact_id = 'contact-guid-1';
        $liability->liability_type = 'Loan';
        $liability->liability_value = 20000;
        $liability->date_incurred = '2025-07-15';
        // Simulate lead conversion
        $liability->lead_id = 'lead-guid-1';
        // Conversion event
        $liability->contact_id = 'contact-guid-2';
        $this->assertEquals('contact-guid-2', $liability->contact_id);
    }
}
