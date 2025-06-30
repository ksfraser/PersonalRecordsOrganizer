<?php
namespace EstatePlanningManager\Sections;

use EstatePlanningManager\Models\ScheduledPaymentsModel;
use EPM_Shortcodes;

if (!defined('ABSPATH')) exit;
require_once __DIR__ . '/../models/ScheduledPaymentsModel.php';
require_once __DIR__ . '/AbstractSectionView.php';

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

    /**
     * Render the scheduled payments section (data should be provided by controller)
     * @param array $records
     */
    public static function render_view($records)
    {
        echo '<div class="epm-data-section" data-section="scheduled_payments">';
        echo '<h3>Scheduled Payments</h3>';
        if (empty($records)) {
            echo '<p class="epm-no-data">No scheduled payments recorded.</p>';
        } else {
            echo '<table class="epm-data-table">';
            echo '<tr><th>Type</th><th>Paid To</th><th>Automatic?</th><th>Amount</th><th>Due Date</th></tr>';
            foreach ($records as $row) {
                echo '<tr>';
                echo '<td>' . esc_html($row->payment_type) . '</td>';
                echo '<td>' . esc_html($row->paid_to) . '</td>';
                echo '<td>' . esc_html($row->is_automatic) . '</td>';
                echo '<td>' . esc_html($row->amount) . '</td>';
                echo '<td>' . esc_html($row->due_date) . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }
        echo '</div>';
    }
    public function getModel() {
        return new \EstatePlanningManager\Models\ScheduledPaymentsModel();
    }
    public function getSection() {
        return 'scheduled_payments';
    }
}
