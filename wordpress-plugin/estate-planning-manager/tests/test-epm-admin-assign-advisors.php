<?php
/**
 * Tests for admin screen: assign advisors to clients and invite
 */
class Test_EPM_Admin_Assign_Advisors extends EPM_Test_Case {
    public function test_assign_advisors_screen_renders() {
        require_once EPM_PLUGIN_DIR . 'admin/class-epm-assign-advisors.php';
        $admin = EPM_Assign_Advisors_Admin::instance();
        ob_start();
        $admin->render_page();
        $html = ob_get_clean();
        $this->assertStringContainsString('Assign Advisors', $html);
    }
}
