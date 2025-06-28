<?php
/**
 * Tests for user preferences (UI mode: tabs/twisties)
 */
class Test_EPM_Preferences extends EPM_Test_Case {
    public function test_set_and_get_user_preference() {
        $db = EPM_Database::instance();
        $user_id = $this->client_user_id;
        $db->set_user_preference($user_id, 'ui_mode', 'twisties');
        $pref = $db->get_user_preference($user_id, 'ui_mode');
        $this->assertEquals('twisties', $pref);
    }
    public function test_ui_mode_affects_rendering() {
        $db = EPM_Database::instance();
        $shortcodes = EPM_Shortcodes::instance();
        $user_id = $this->client_user_id;
        $db->set_user_preference($user_id, 'ui_mode', 'twisties');
        wp_set_current_user($user_id);
        ob_start();
        $shortcodes->client_data_shortcode(['section'=>'all']);
        $html = ob_get_clean();
        $this->assertStringContainsString('epm-client-data-twisties-wrapper', $html);
        $db->set_user_preference($user_id, 'ui_mode', 'tabs');
        ob_start();
        $shortcodes->client_data_shortcode(['section'=>'all']);
        $html2 = ob_get_clean();
        $this->assertStringContainsString('epm-client-data-tabs-wrapper', $html2);
    }
}
