<?php
namespace EstatePlanningManager\Sections;

use EstatePlanningManager\Models\RealEstateModel;
use EPM_Shortcodes;

if (!defined('ABSPATH')) exit;
require_once __DIR__ . '/../models/RealEstateModel.php';
require_once __DIR__ . '/AbstractSectionView.php';

class EPM_RealEstateView extends AbstractSectionView
{
    public static function get_section_key(): string
    {
        return 'real_estate';
    }

    public static function get_fields(EPM_Shortcodes $shortcodes): array
    {
        return $shortcodes->get_form_sections()['real_estate']['fields'];
    }

    /**
     * Render the real estate section (data should be provided by controller)
     * @param array $records
     */
    public static function render_view(array $records): void
    {
        echo '<div class="epm-data-section" data-section="real_estate">';
        echo '<h3>Real Estate</h3>';
        if (empty($records)) {
            echo '<p class="epm-no-data">No real estate recorded.</p>';
        } else {
            echo '<table class="epm-data-table">';
            echo '<tr><th>Type</th><th>Title Held By</th><th>Address</th><th>Has Mortgage?</th><th>Lender</th></tr>';
            foreach ($records as $row) {
                echo '<tr>';
                echo '<td>' . esc_html($row->property_type) . '</td>';
                echo '<td>' . esc_html($row->title_held_by) . '</td>';
                echo '<td>' . esc_html($row->address) . '</td>';
                echo '<td>' . esc_html($row->has_mortgage) . '</td>';
                echo '<td>' . esc_html($row->lender) . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }
        echo '</div>';
    }
}
