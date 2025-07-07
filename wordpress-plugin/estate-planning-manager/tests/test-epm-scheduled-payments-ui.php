<?php
use PHPUnit\Framework\TestCase;

class Test_EPM_ScheduledPaymentsUI extends TestCase {
    public function test_section_view_renders_form() {
        ob_start();
        \EstatePlanningManager\Sections\EPM_ScheduledPaymentsView::render([
            'account_number' => 'SP-001',
            'amount' => 500.00,
            'description' => 'Scheduled Payment UI test',
        ]);
        $output = ob_get_clean();
        $this->assertStringContainsString('Scheduled Payments', $output);
        $this->assertStringContainsString('Add', $output);
        $this->assertStringContainsString('account_number', $output);
        $this->assertStringContainsString('Scheduled Payment UI test', $output);
    }
}
