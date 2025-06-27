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
}
