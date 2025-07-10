<?php
namespace EstatePlanningManager\Sections;

require_once __DIR__ . '/AbstractSectionView.php';
require_once __DIR__ . '/../models/DigitalAssetsModel.php';

use EstatePlanningManager\Models\DigitalAssetsModel;
use EPM_Shortcodes;

if (!defined('ABSPATH')) exit;

class EPM_DigitalAssetsView extends AbstractSectionView
{
    public static function get_section_key()
    {
        return 'digital_assets';
    }

    public static function get_fields($shortcodes)
    {
        if ($shortcodes === null) {
            $shortcodes = EPM_Shortcodes::instance();
        }
        return $shortcodes->get_form_sections()['digital_assets']['fields'];
    }

    public static function render($client_id, $readonly = false)
    {
        $instance = new self();
        $instance->renderSectionView($client_id, $readonly);
    }

    public function getModel() {
        return new DigitalAssetsModel();
    }
    public function getSection() {
        return 'digital_assets';
    }
}
