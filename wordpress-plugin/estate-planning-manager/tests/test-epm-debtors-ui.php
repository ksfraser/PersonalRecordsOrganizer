<?php
use PHPUnit\Framework\TestCase;

class Test_EPM_DebtorsUI extends TestCase {
    public function test_section_view_renders_form() {
        ob_start();
        \EstatePlanningManager\Sections\EPM_DebtorsView::render([
            'account_number' => '12345',
            'amount' => 1000.50,
            'description' => 'Debtor UI test',
        ]);
        $output = ob_get_clean();
        $this->assertStringContainsString('Debtors', $output);
        $this->assertStringContainsString('Add Contact', $output);
        $this->assertStringContainsString('account_number', $output);
        $this->assertStringContainsString('Debtor UI test', $output);
    }
}
