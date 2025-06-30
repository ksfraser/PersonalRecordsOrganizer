<?php
namespace EstatePlanningManager\Sections;
/**
 * EPM_PersonalView
 * Handles rendering of the Personal Information section (form and data)
 */

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
    public static function render($user_id, $readonly = false) {
        $instance = new self();
        $instance->renderSectionView();
    }
    public function getModel() {
        return new \EstatePlanningManager\Models\PersonalModel();
    }
    public function getSection() {
        return 'personal';
    }
}
