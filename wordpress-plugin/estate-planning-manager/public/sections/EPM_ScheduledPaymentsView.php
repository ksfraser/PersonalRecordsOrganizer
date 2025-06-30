<?php
namespace EstatePlanningManager\Sections;

use EstatePlanningManager\Models\ScheduledPaymentsModel;
use EPM_Shortcodes;

if (!defined('ABSPATH')) exit;

class EPM_ScheduledPaymentsView extends AbstractSectionView
{
    public static function get_section_key()
    {
        return 'scheduled_payments';
    }

    public static function get_fields($shortcodes)
    {
        if ($shortcodes === null) {
            $shortcodes = \EPM_Shortcodes::instance();
        }
        return $shortcodes->get_form_sections()['scheduled_payments']['fields'];
    }

    public static function render($user_id, $readonly = false)
    {
        $instance = new self();
        $instance->renderSectionView();
    }

    public function getModel() {
        return new \EstatePlanningManager\Models\ScheduledPaymentsModel();
    }
    public function getSection() {
        return 'scheduled_payments';
    }
}
