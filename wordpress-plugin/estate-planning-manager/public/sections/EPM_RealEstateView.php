<?php
namespace EstatePlanningManager\Sections;

use EstatePlanningManager\Models\RealEstateModel;
use EPM_Shortcodes;

if (!defined('ABSPATH')) exit;

class EPM_RealEstateView extends AbstractSectionView
{
    public static function get_section_key()
    {
        return 'real_estate';
    }

    public static function get_fields($shortcodes)
    {
        if ($shortcodes === null) {
            $shortcodes = \EPM_Shortcodes::instance();
        }
        return $shortcodes->get_form_sections()['real_estate']['fields'];
    }

    // Use the new multi-record summary table logic
    public static function render($user_id, $readonly = false)
    {
        $instance = new self();
        $instance->renderSectionView();
    }

    public function getModel() {
        return new RealEstateModel();
    }
    public function getSection() {
        return 'real_estate';
    }
}
