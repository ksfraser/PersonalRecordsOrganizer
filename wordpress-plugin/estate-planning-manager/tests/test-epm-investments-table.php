<?php
use PHPUnit\Framework\TestCase;
require_once dirname(__DIR__) . '/estate-planning-manager/includes/tables/InvestmentsTable.php';

class InvestmentsTableTest extends TestCase {
    public function setUp(): void {
        // Setup: mock $wpdb and dependencies if needed
        global $wpdb;
        $wpdb = $this->getMockBuilder(stdClass::class)->setMethods(['prepare', 'get_results', 'insert', 'update'])->getMock();
        $this->table = new InvestmentsTable();
    }

    public function testLenderTypePersonClearsOrgId() {
        $data = [
            'lender_type' => 'person',
            'lender_person_id' => 5,
            'lender_org_id' => 7
        ];
        $client_id = 1;
        $section = 'investments';
        $result = $this->table->save_client_data($client_id, $section, $data);
        $this->assertTrue($data['lender_org_id'] === null || $data['lender_org_id'] === '' || !isset($data['lender_org_id']));
    }

    public function testLenderTypeOrganizationClearsPersonId() {
        $data = [
            'lender_type' => 'organization',
            'lender_person_id' => 5,
            'lender_org_id' => 7
        ];
        $client_id = 1;
        $section = 'investments';
        $result = $this->table->save_client_data($client_id, $section, $data);
        $this->assertTrue($data['lender_person_id'] === null || $data['lender_person_id'] === '' || !isset($data['lender_person_id']));
    }

    public function testLenderTypeUnsetClearsBoth() {
        $data = [
            'lender_type' => '',
            'lender_person_id' => 5,
            'lender_org_id' => 7
        ];
        $client_id = 1;
        $section = 'investments';
        $result = $this->table->save_client_data($client_id, $section, $data);
        $this->assertTrue((!isset($data['lender_person_id']) || $data['lender_person_id'] === null || $data['lender_person_id'] === '') && (!isset($data['lender_org_id']) || $data['lender_org_id'] === null || $data['lender_org_id'] === ''));
    }
}
