<?php
namespace EstatePlanningManager\Sections;

use EstatePlanningManager\Models\AutoModel;
use EPM_Shortcodes;

if (!defined('ABSPATH')) exit;

class EPM_AutoView extends AbstractSectionView
{
    public static function get_section_key()
    {
        return 'auto_property';
    }

    public static function get_fields($shortcodes)
    {
        if ($shortcodes === null) {
            $shortcodes = \EPM_Shortcodes::instance();
        }
        return $shortcodes->get_form_sections()['auto_property']['fields'];
    }

    public static function render($user_id, $readonly = false)
    {
        $instance = new self();
        $instance->renderSectionView();
    }

    public function getModel() {
        return new AutoModel();
    }
    public function getSection() {
        return 'auto_property';
    }
}
