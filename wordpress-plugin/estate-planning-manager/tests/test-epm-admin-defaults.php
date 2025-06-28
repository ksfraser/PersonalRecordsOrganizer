<?php
/**
 * Tests for admin screen: advisor defaults
 */
class Test_EPM_Admin_Defaults extends EPM_Test_Case {
    public function test_admin_defaults_screen_renders() {
        require_once EPM_PLUGIN_DIR . 'admin/class-epm-defaults-admin.php';
        $admin = EPM_Defaults_Admin::instance();
        ob_start();
        $admin->render_page();
        $html = ob_get_clean();
        $this->assertStringContainsString('Advisor Defaults', $html);
    }
}
