<?php
/**
 * Tests for advisor defaults (epm_defaults table and fallback logic)
 */
class Test_EPM_Defaults extends EPM_Test_Case {
    public function test_set_and_get_advisor_default() {
        $db = EPM_Database::instance();
        $advisor_id = $this->advisor_user_id;
        $field = 'test_field';
        $value = 'default_value';
        $db->set_advisor_default($advisor_id, $field, $value);
        $fetched = $db->get_advisor_default($advisor_id, $field);
        $this->assertEquals($value, $fetched);
    }
    public function test_fallback_to_advisor_default_in_field_value() {
        $shortcodes = EPM_Shortcodes::instance();
        $db = EPM_Database::instance();
        $advisor_id = $this->advisor_user_id;
        $client_id = $db->get_client_id_by_user_id($this->client_user_id);
        $field = 'test_field';
        $value = 'advisor_default';
        $db->set_advisor_default($advisor_id, $field, $value);
        // No client data set, should fallback
        $result = $shortcodes->test_render_form_field(['name'=>$field,'label'=>'Test','type'=>'text'], $this->client_user_id);
        ob_start();
        $shortcodes->render_form_field(['name'=>$field,'label'=>'Test','type'=>'text'], $this->client_user_id);
        $html = ob_get_clean();
        $this->assertStringContainsString($value, $html);
    }
}
