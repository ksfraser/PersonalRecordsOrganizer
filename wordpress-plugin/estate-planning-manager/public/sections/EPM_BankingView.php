<?php
namespace EstatePlanningManager\Sections;

use EstatePlanningManager\Models\BankingModel;
use EPM_Shortcodes;

if (!defined('ABSPATH')) exit;
require_once __DIR__ . '/../models/BankingModel.php';
require_once __DIR__ . '/AbstractSectionView.php';

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

    /**
     * Render the banking section (data should be provided by controller)
     * @param array $records
     */
    public static function render_view($records)
    {
        echo '<div class="epm-data-section" data-section="banking">';
        echo '<h3>Bank Accounts</h3>';
        if (empty($records)) {
            echo '<p class="epm-no-data">No bank accounts recorded.</p>';
        } else {
            echo '<table class="epm-data-table">';
            echo '<tr><th>Bank</th><th>Account Type</th><th>Account #</th><th>Branch</th><th>Owner</th><th>Advisor</th></tr>';
            foreach ($records as $row) {
                echo '<tr>';
                echo '<td>' . esc_html($row->bank) . '</td>';
                echo '<td>' . esc_html($row->account_type) . '</td>';
                echo '<td>' . esc_html($row->account_number) . '</td>';
                echo '<td>' . esc_html($row->branch) . '</td>';
                echo '<td>' . esc_html($row->owner) . '</td>';
                echo '<td>' . esc_html($row->advisor) . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }
        echo '</div>';
    }
}
