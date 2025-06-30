<?php
namespace EstatePlanningManager\Sections;
/**
 * EPM_PersonalView
 * Handles rendering of the Personal Information section (form and data)
 */
if (!defined('ABSPATH')) exit;
require_once __DIR__ . '/AbstractSectionView.php';

class EPM_PersonalView extends AbstractSectionView {
    public static function get_section_key() {
        return 'personal';
    }
    public static function get_fields($shortcodes) {
        if ($shortcodes === null) {
            $shortcodes = \EPM_Shortcodes::instance();
        }
        return $shortcodes->get_form_sections()['personal']['fields'];
    }
    // Override render to fetch/display from user meta
    public static function render($user_id, $readonly = false) {
        if (!static::$shortcodes) {
            static::$shortcodes = \EPM_Shortcodes::instance();
        }
        $fields = static::get_fields(static::$shortcodes);
        echo '<div class="epm-section-data">';
        echo '<table class="epm-section-table" style="width:100%;">';
        foreach ($fields as $field) {
            $label = esc_html($field['label']);
            $value = get_user_meta($user_id, $field['name'], true);
            echo '<tr><th style="text-align:left;width:30%;">' . $label . '</th><td>' . esc_html($value) . '</td></tr>';
        }
        echo '</table>';
        echo '</div>';
    }
    // Render the section form (use default from AbstractSectionView)
    public static function render_form($user_id) {
        parent::render_form($user_id);
    }
}
