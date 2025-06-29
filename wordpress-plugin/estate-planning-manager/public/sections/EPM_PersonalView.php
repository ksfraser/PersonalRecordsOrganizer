<?php
/**
 * EPM_PersonalView
 * Handles rendering of the Personal Information section (form and data)
 */
if (!defined('ABSPATH')) exit;

class EPM_PersonalView {
    /**
     * Render the personal info form
     */
    public static function render_form($user_id) {
        $shortcodes = EPM_Shortcodes::instance();
        $fields = $shortcodes->get_form_sections()['personal']['fields'];
        echo '<div class="epm-client-form-wrapper">';
        echo '<h3>Personal Information</h3>';
        echo '<form class="epm-client-form" data-section="personal">';
        wp_nonce_field('epm_save_data', 'epm_nonce');
        foreach ($fields as $field) {
            $shortcodes->render_form_field($field, $user_id);
        }
        echo '<div class="epm-form-actions">';
        echo '<button type="submit" class="epm-btn epm-btn-primary">Save Data</button>';
        echo '<button type="button" class="epm-btn epm-btn-secondary epm-generate-pdf">Generate PDF</button>';
        echo '</div>';
        echo '</form>';
        echo '</div>';
    }

    /**
     * Render the personal info data (read-only)
     */
    public static function render($user_id, $readonly = false) {
        $shortcodes = EPM_Shortcodes::instance();
        $fields = $shortcodes->get_form_sections()['personal']['fields'];
        $data = $shortcodes->get_client_data('personal', $user_id);
        echo '<div class="epm-data-section" data-section="personal">';
        echo '<h3>Personal Information</h3>';
        if (empty($data)) {
            echo '<p class="epm-no-data">No data available for this section.</p>';
        } else {
            echo '<div class="epm-data-grid">';
            foreach ($fields as $field) {
                $value = isset($data->{$field['name']}) ? $data->{$field['name']} : '';
                if (!empty($value)) {
                    echo '<div class="epm-data-item">';
                    echo '<label>' . esc_html($field['label']) . ':</label>';
                    echo '<span>' . esc_html($value) . '</span>';
                    echo '</div>';
                }
            }
            echo '</div>';
        }
        echo '</div>';
    }
}
