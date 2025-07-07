<?php
use PHPUnit\Framework\TestCase;

class Test_EPM_CreditorsUI extends TestCase {
    public function test_section_view_renders_form() {
        ob_start();
        \EstatePlanningManager\Sections\EPM_CreditorsView::render([
            'account_number' => '54321',
            'amount' => 2000.75,
            'description' => 'Creditor UI test',
        ]);
        $output = ob_get_clean();
        $this->assertStringContainsString('Creditors', $output);
        $this->assertStringContainsString('Add Contact', $output);
        $this->assertStringContainsString('account_number', $output);
        $this->assertStringContainsString('Creditor UI test', $output);
    }
}
