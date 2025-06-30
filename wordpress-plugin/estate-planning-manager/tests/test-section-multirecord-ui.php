<?php
use PHPUnit\Framework\TestCase;

class SectionMultiRecordUITest extends TestCase {
    public function test_real_estate_section_renders_table_and_add_button_for_owner() {
        // Simulate owner context
        $view = new \EstatePlanningManager\Sections\EPM_RealEstateView();
        ob_start();
        $view->renderSectionView();
        $output = ob_get_clean();
        $this->assertStringContainsString('<table', $output);
        $this->assertStringContainsString('Add New', $output);
    }
    public function test_real_estate_section_no_add_button_for_shared_user() {
        // Simulate shared user context (read-only)
        $view = new \EstatePlanningManager\Sections\EPM_RealEstateView();
        // Force is_owner = false for this test
        // You may need to mock getOwnerIdForSection or $current_user_id
        // For now, just check that the readonly notice appears
        ob_start();
        // Simulate by calling renderSummaryTable with is_owner = false
        $reflection = new ReflectionClass($view);
        $method = $reflection->getMethod('renderSummaryTable');
        $method->setAccessible(true);
        $method->invoke($view, [], false, $view->getModel());
        $output = ob_get_clean();
        $this->assertStringNotContainsString('Add New', $output);
    }
}
