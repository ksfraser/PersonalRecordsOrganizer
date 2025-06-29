<?php
namespace EstatePlanningManager\Sections;

use EstatePlanningManager\Models\InvestmentsModel;
use EPM_Shortcodes;

if (!defined('ABSPATH')) exit;
require_once __DIR__ . '/../models/InvestmentsModel.php';
require_once __DIR__ . '/AbstractSectionView.php';

class EPM_InvestmentsView extends AbstractSectionView
{
    public static function get_section_key(): string
    {
        return 'investments';
    }

    public static function get_fields(EPM_Shortcodes $shortcodes): array
    {
        return $shortcodes->get_form_sections()['investments']['fields'];
    }

    /**
     * Render the investments section (data should be provided by controller)
     * @param array $records
     */
    public static function render_view(array $records): void
    {
        echo '<div class="epm-data-section" data-section="investments">';
        echo '<h3>Investment Accounts</h3>';
        if (empty($records)) {
            echo '<p class="epm-no-data">No investments recorded.</p>';
        } else {
            echo '<table class="epm-data-table">';
            echo '<tr><th>Type</th><th>Company</th><th>Account #</th><th>Beneficiary</th><th>Advisor</th><th>Lender</th></tr>';
            foreach ($records as $row) {
                echo '<tr>';
                echo '<td>' . esc_html($row->investment_type) . '</td>';
                echo '<td>' . esc_html($row->financial_company) . '</td>';
                echo '<td>' . esc_html($row->account_number) . '</td>';
                echo '<td>' . esc_html($row->beneficiary) . '</td>';
                echo '<td>' . esc_html($row->advisor) . '</td>';
                echo '<td>' . esc_html($row->lender) . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }
        echo '</div>';
    }
}
