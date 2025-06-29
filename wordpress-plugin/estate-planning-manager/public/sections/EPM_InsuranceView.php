<?php
namespace EstatePlanningManager\Sections;

use EstatePlanningManager\Models\InsuranceModel;
use EPM_Shortcodes;

if (!defined('ABSPATH')) exit;
require_once __DIR__ . '/../models/InsuranceModel.php';
require_once __DIR__ . '/AbstractSectionView.php';

class EPM_InsuranceView extends AbstractSectionView
{
    public static function get_section_key()
    {
        return 'insurance';
    }

    public static function get_fields($shortcodes)
    {
        return $shortcodes->get_form_sections()['insurance']['fields'];
    }

    /**
     * Render the insurance section (data should be provided by controller)
     * @param array $records
     */
    public static function render_view($records)
    {
        echo '<div class="epm-data-section" data-section="insurance">';
        echo '<h3>Insurance Policies</h3>';
        if (empty($records)) {
            echo '<p class="epm-no-data">No insurance policies recorded.</p>';
        } else {
            echo '<table class="epm-data-table">';
            echo '<tr><th>Category</th><th>Type</th><th>Company</th><th>Policy #</th><th>Beneficiary</th><th>Advisor</th><th>Owner</th></tr>';
            foreach ($records as $row) {
                echo '<tr>';
                echo '<td>' . esc_html($row->insurance_category) . '</td>';
                echo '<td>' . esc_html($row->insurance_type) . '</td>';
                echo '<td>' . esc_html($row->insurance_company) . '</td>';
                echo '<td>' . esc_html($row->policy_number) . '</td>';
                echo '<td>' . esc_html($row->beneficiary) . '</td>';
                echo '<td>' . esc_html($row->advisor) . '</td>';
                echo '<td>' . esc_html($row->owner) . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }
        echo '</div>';
    }
}
