<?php
namespace EstatePlanningManager\Sections;

require_once __DIR__ . '/AbstractSectionView.php';
require_once __DIR__ . '/../models/InvestmentsModel.php';

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

    public static function render($client_id, $readonly = false)
    {
        $instance = new self();
        $instance->renderSectionView($client_id, $readonly);
    }

    public function getModel() {
        return new InvestmentsModel();
    }
    public function getSection() {
        return 'investments';
    }
}
