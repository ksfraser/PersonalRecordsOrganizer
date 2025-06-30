<?php
namespace EstatePlanningManager\Sections;

use EstatePlanningManager\Models\PersonalPropertyModel;
use EPM_Shortcodes;

if (!defined('ABSPATH')) exit;

class EPM_PersonalPropertyView extends AbstractSectionView
{
    public static function get_section_key() {
        return 'personal_property';
    }

    public static function get_fields($shortcodes) {
        if ($shortcodes === null) {
            $shortcodes = \EPM_Shortcodes::instance();
        }
        return $shortcodes->get_form_sections()['personal_property']['fields'];
    }

    public static function render($user_id, $readonly = false)
    {
        $instance = new self();
        $instance->renderSectionView();
    }

    public function getModel() {
        return new PersonalPropertyModel();
    }
    public function getSection() {
        return 'personal_property';
    }
}
