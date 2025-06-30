<?php
namespace EstatePlanningManager\Models;

if (!defined('ABSPATH')) exit;

class Sanitizer {
    /**
     * Sanitize a textarea field, fallback to sanitize_text_field if needed
     */
    public static function textarea($value) {
        if (function_exists('sanitize_textarea_field')) {
            return sanitize_textarea_field($value);
        }
        return sanitize_text_field($value);
    }
    /**
     * Sanitize a text field
     */
    public static function text($value) {
        return sanitize_text_field($value);
    }
    /**
     * Sanitize an email field
     */
    public static function email($value) {
        return sanitize_email($value);
    }
    /**
     * Sanitize an integer field
     */
    public static function int($value) {
        return is_numeric($value) ? intval($value) : null;
    }
}
