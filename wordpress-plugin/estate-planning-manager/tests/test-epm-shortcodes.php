<?php
/**
 * Tests for EPM_Shortcodes class (UI selector fields)
 */

class Test_EPM_Shortcodes extends EPM_Test_Case {
    /**
     * Test that selector fields render correct options in the form
     */
    public function test_selector_fields_render_options() {
        $shortcodes = EPM_Shortcodes::instance();
        $sections = $shortcodes->get_form_sections();

        // Map of section => [field_name => selector_table]
        $selector_fields = [
            'banking' => ['account_type' => 'epm_account_types'],
            'investments' => ['investment_type' => 'epm_investment_types'],
            'insurance' => ['policy_type' => 'epm_insurance_types'],
            'real_estate' => ['property_type' => 'epm_property_types'],
            'emergency_contacts' => ['relationship' => 'epm_relationship_types'],
        ];

        foreach ($selector_fields as $section => $fields) {
            foreach ($fields as $field_name => $selector_table) {
                $options = EPM_Database::instance()->get_selector_options($selector_table);
                $this->assertNotEmpty($options, "Selector options for $selector_table should not be empty");

                // Simulate rendering the field
                ob_start();
                $shortcodes->render_form_field([
                    'name' => $field_name,
                    'label' => 'Test Label',
                    'type' => 'select',
                    'options' => $options
                ], 1);
                $html = ob_get_clean();

                // Check that each option value and label appears in the HTML
                foreach ($options as $value => $label) {
                    $this->assertStringContainsString((string)$value, $html, "Option value $value missing in $section.$field_name");
                    $this->assertStringContainsString((string)$label, $html, "Option label $label missing in $section.$field_name");
                }
            }
        }
    }

    /**
     * Test default is read-only view with Edit button if data exists
     */
    public function test_default_view_mode_with_edit_button() {
        $shortcodes = EPM_Shortcodes::instance();
        $user_id = $this->client_user_id;
        // Simulate data for 'personal' section
        update_user_meta($user_id, 'epm_first_name', 'Test');
        update_user_meta($user_id, 'epm_last_name', 'User');
        // Should show read-only data and Edit button
        ob_start();
        $shortcodes->client_data_shortcode(['section' => 'personal']);
        $html = ob_get_clean();
        $this->assertStringContainsString('Test', $html);
        $this->assertStringContainsString('Edit', $html);
    }

    /**
     * Test form is shown immediately if no data exists
     */
    public function test_form_shown_if_no_data() {
        $shortcodes = EPM_Shortcodes::instance();
        $user_id = $this->client_user_id;
        // Ensure no data for 'banking'
        delete_user_meta($user_id, 'epm_bank_name');
        delete_user_meta($user_id, 'epm_account_type');
        // Should show form (input fields)
        ob_start();
        $shortcodes->client_data_shortcode(['section' => 'banking']);
        $html = ob_get_clean();
        $this->assertStringContainsString('form', $html);
        $this->assertStringNotContainsString('Edit', $html);
    }
}
