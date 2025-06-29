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
        return $shortcodes->get_form_sections()['personal']['fields'];
    }
}
