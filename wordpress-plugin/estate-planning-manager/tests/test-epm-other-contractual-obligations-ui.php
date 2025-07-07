<?php
use PHPUnit\Framework\TestCase;

class Test_EPM_OtherContractualObligationsUI extends TestCase {
    public function test_section_view_renders_form() {
        ob_start();
        \EstatePlanningManager\Sections\EPM_OtherContractualObligationsView::render([
            'description' => 'UI test',
            'location_of_documents' => 'Drawer',
            'notes' => 'UI notes',
        ]);
        $output = ob_get_clean();
        $this->assertStringContainsString('Other Contractual Obligations', $output);
        $this->assertStringContainsString('UI test', $output);
        $this->assertStringContainsString('Drawer', $output);
        $this->assertStringContainsString('UI notes', $output);
        $this->assertStringContainsString('form', $output);
        $this->assertStringContainsString('description', $output);
        $this->assertStringContainsString('location_of_documents', $output);
        $this->assertStringContainsString('notes', $output);
    }
}
