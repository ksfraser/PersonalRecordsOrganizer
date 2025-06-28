<?php
/**
 * Tests for AJAX handlers: invites, shares, preferences
 * (Mocked basic success/failure)
 */
class Test_EPM_Ajax extends EPM_Test_Case {
    public function test_ajax_set_ui_mode() {
        $_POST['action'] = 'epm_set_ui_mode';
        $_POST['ui_mode'] = 'twisties';
        $_POST['nonce'] = wp_create_nonce('epm_set_ui_mode');
        $handler = EPM_Ajax_Handler::instance();
        ob_start();
        $handler->set_ui_mode();
        $json = ob_get_clean();
        $this->assertStringContainsString('success', $json);
    }
    public function test_ajax_send_invite_invalid_email() {
        $_POST['action'] = 'epm_send_invite';
        $_POST['section'] = 'personal';
        $_POST['form_data'] = 'invite_email=notanemail&permission_level=view';
        $_POST['nonce'] = wp_create_nonce('epm_send_invite');
        $handler = EPM_Ajax_Handler::instance();
        ob_start();
        $handler->send_invite();
        $json = ob_get_clean();
        $this->assertStringContainsString('error', $json);
    }
}
