<?php
namespace EstatePlanningManager\Sections;

require_once __DIR__ . '/AbstractSectionView.php';
require_once __DIR__ . '/../models/BankingModel.php';

use EstatePlanningManager\Models\BankingModel;
use EPM_Shortcodes;

if (!defined('ABSPATH')) exit;

class EPM_BankingView extends AbstractSectionView
{
    public static function get_section_key()
    {
        return 'banking';
    }

    public static function get_fields($shortcodes)
    {
        if ($shortcodes === null) {
            $shortcodes = \EPM_Shortcodes::instance();
        }
        return $shortcodes->get_form_sections()['banking']['fields'];
    }

    public static function render($client_id, $readonly = false)
    {
        $instance = new self();
        $instance->renderSectionView($client_id, $readonly);
    }

    public function getModel() {
        return new BankingModel();
    }
    public function getSection() {
        return 'banking';
    }
}
