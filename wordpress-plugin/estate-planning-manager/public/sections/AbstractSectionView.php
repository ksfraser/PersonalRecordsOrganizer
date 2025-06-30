<?php
/**
 * AbstractSectionView
 *
 * Provides generic render and render_form logic for EPM section views.
 */

namespace EstatePlanningManager\Sections;

use EPM_Shortcodes;

if (!defined('ABSPATH')) exit;

interface SectionViewInterface
{
    public static function get_section_key();
    public static function get_fields(
        /*EPM_Shortcodes*/ $shortcodes
    );
}

abstract class AbstractSectionView implements SectionViewInterface
{
    /**
     * @var EPM_Shortcodes|null
     */
    protected static $shortcodes;

    public static function setShortcodes($shortcodes)
    {
        static::$shortcodes = $shortcodes;
    }

    /**
     * Get the section key (e.g., 'personal', 'banking', etc.)
     * @return string
     */
    abstract public static function get_section_key();

    /**
     * Get the fields array for this section
     * @return array
     */
    abstract public static function get_fields($shortcodes);

    /**
     * Render the section data (read-only or editable)
     * @param int $user_id
     * @param bool $readonly
     */
    public static function render($user_id, $readonly = false) {
        if (!static::$shortcodes) {
            static::$shortcodes = \EPM_Shortcodes::instance();
        }
        $fields = static::get_fields(static::$shortcodes);
        $section = static::get_section_key();
        $data = static::$shortcodes->get_client_data($section, $user_id);
        echo '<div class="epm-section-data">';
        echo '<table class="epm-section-table" style="width:100%;">';
        foreach ($fields as $field) {
            $label = esc_html($field['label']);
            $value = isset($data->{$field['name']}) ? esc_html($data->{$field['name']}) : '';
            echo '<tr><th style="text-align:left;width:30%;">' . $label . '</th><td>' . $value . '</td></tr>';
        }
        echo '</table>';
        echo '</div>';
    }

    /**
     * Render the section form
     * @param int $user_id
     */
    public static function render_form($user_id) {
        if (!static::$shortcodes) {
            static::$shortcodes = \EPM_Shortcodes::instance();
        }
        $fields = static::get_fields(static::$shortcodes);
        $section = static::get_section_key();
        echo '<form method="post" class="epm-section-form">';
        echo '<input type="hidden" name="section" value="' . esc_attr($section) . '">';
        foreach ($fields as $field) {
            static::$shortcodes->render_form_field($field, $user_id);
        }
        echo '<button type="submit" class="epm-btn epm-btn-primary">Save</button>';
        echo '</form>';
    }
}
