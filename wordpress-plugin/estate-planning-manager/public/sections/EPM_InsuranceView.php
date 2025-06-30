<?php
namespace EstatePlanningManager\Sections;

require_once __DIR__ . '/AbstractSectionView.php';
require_once __DIR__ . '/../models/InsuranceModel.php';

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

    public static function render($client_id, $readonly = false)
    {
        $instance = new self();
        $instance->renderSectionView($client_id, $readonly);
    }

    public function getModel() {
        return new InsuranceModel();
    }
    public function getSection() {
        return 'insurance';
    }
}
