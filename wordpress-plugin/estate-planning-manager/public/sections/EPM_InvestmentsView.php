<?php
namespace EstatePlanningManager\Sections;

use EstatePlanningManager\Models\InvestmentsModel;
use EPM_Shortcodes;

if (!defined('ABSPATH')) exit;

class EPM_InvestmentsView extends AbstractSectionView
{
    public static function get_section_key() {
        return 'investments';
    }

    public static function get_fields($shortcodes) {
        if ($shortcodes === null) {
            $shortcodes = \EPM_Shortcodes::instance();
        }
        return $shortcodes->get_form_sections()['investments']['fields'];
    }

    public static function render($user_id, $readonly = false)
    {
        $instance = new self();
        $instance->renderSectionView();
    }

    public function getModel() {
        return new \EstatePlanningManager\Models\InvestmentsModel();
    }
    public function getSection() {
        return 'investments';
    }
}
