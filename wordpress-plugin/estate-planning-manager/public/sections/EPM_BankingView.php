<?php
namespace EstatePlanningManager\Sections;

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

    public static function render($user_id, $readonly = false)
    {
        $instance = new self();
        $instance->renderSectionView();
    }

    public function getModel() {
        return new \EstatePlanningManager\Models\BankingModel();
    }
    public function getSection() {
        return 'banking';
    }
}
