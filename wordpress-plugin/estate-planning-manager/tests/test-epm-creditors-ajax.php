<?php
use PHPUnit\Framework\TestCase;

class Test_EPM_CreditorsAjax extends TestCase {
    public function test_add_contact_ajax() {
        $_POST = [
            'contact_type' => 'organization',
            'org_name' => 'Ajax Creditor',
            'section' => 'creditors',
        ];
        ob_start();
        \EstatePlanningManager\Handlers\EPM_AddContactHandler::handle();
        $output = ob_get_clean();
        $this->assertStringContainsString('Ajax Creditor', $output);
        $this->assertStringContainsString('success', $output);
    }
}
