<?php
namespace EstatePlanningManager\Sections;

use EstatePlanningManager\Models\InsuranceModel;
use EPM_Shortcodes;

if (!defined('ABSPATH')) exit;

class EPM_InsuranceView extends AbstractSectionView
{
    public static function get_section_key()
    {
        return 'insurance';
    }

    public static function get_fields($shortcodes)
    {
        if ($shortcodes === null) {
            $shortcodes = \EPM_Shortcodes::instance();
        }
        return $shortcodes->get_form_sections()['insurance']['fields'];
    }

    public static function render($user_id, $readonly = false)
    {
        $instance = new self();
        $instance->renderSectionView();
    }

    public function getModel() {
        return new \EstatePlanningManager\Models\InsuranceModel();
    }
    public function getSection() {
        return 'insurance';
    }
}
