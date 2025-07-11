<?php
namespace EstatePlanningManager\Sections;

require_once __DIR__ . '/AbstractSectionView.php';
require_once __DIR__ . '/../models/PersonalModel.php';

use EstatePlanningManager\Models\PersonalModel;
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
    public static function render($client_id, $readonly = false) {
        echo '<link rel="stylesheet" type="text/css" href="' . plugin_dir_url(dirname(__DIR__, 2)) . 'assets/css/epm-section-modal.css" />';
        $instance = new self();
        $instance->renderSectionView($client_id, $readonly);
    }
    public function getModel() {
        return new PersonalModel();
    }
    public function getSection() {
        return 'personal';
    }
}
