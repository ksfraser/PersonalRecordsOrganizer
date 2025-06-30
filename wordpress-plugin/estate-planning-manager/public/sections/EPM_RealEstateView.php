<?php
namespace EstatePlanningManager\Sections;

require_once __DIR__ . '/AbstractSectionView.php';
require_once __DIR__ . '/../models/RealEstateModel.php';

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
    public static function render($client_id, $readonly = false)
    {
        $instance = new self();
        $instance->renderSectionView($client_id, $readonly);
    }

    public function getModel() {
        return new RealEstateModel();
    }
    public function getSection() {
        return 'real_estate';
    }
}
