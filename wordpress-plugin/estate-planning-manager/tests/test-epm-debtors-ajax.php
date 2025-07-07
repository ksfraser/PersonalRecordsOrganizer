<?php
use PHPUnit\Framework\TestCase;

class Test_EPM_DebtorsAjax extends TestCase {
    public function test_add_contact_ajax() {
        $_POST = [
            'contact_type' => 'person',
            'person_name' => 'Ajax Debtor',
            'section' => 'debtors',
        ];
        ob_start();
        \EstatePlanningManager\Handlers\EPM_AddContactHandler::handle();
        $output = ob_get_clean();
        $this->assertStringContainsString('Ajax Debtor', $output);
        $this->assertStringContainsString('success', $output);
    }
}
