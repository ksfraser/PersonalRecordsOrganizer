<?php
namespace EstatePlanningManager\Sections;

use EstatePlanningManager\Models\PersonalPropertyModel;
use EPM_Shortcodes;

if (!defined('ABSPATH')) exit;
require_once __DIR__ . '/../models/PersonalPropertyModel.php';
require_once __DIR__ . '/AbstractSectionView.php';

class EPM_PersonalPropertyView extends AbstractSectionView
{
    public static function get_section_key() {
        return 'personal_property';
    }

    public static function get_fields($shortcodes) {
        if ($shortcodes === null) {
            $shortcodes = \EPM_Shortcodes::instance();
        }
        return $shortcodes->get_form_sections()['personal_property']['fields'];
    }

    /**
     * Render the personal property section (data should be provided by controller)
     * @param array $records
     */
    public static function render_view($records) {
        echo '<div class="epm-data-section" data-section="personal_property">';
        echo '<h3>Personal Property</h3>';
        if (empty($records)) {
            echo '<p class="epm-no-data">No personal property recorded.</p>';
        } else {
            echo '<div class="epm-data-grid">';
            foreach ($records as $row) {
                echo '<div class="epm-data-item">';
                echo '<label>Type:</label> <span>' . esc_html($row->property_type) . '</span><br>';
                echo '<label>Item Type:</label> <span>' . esc_html($row->item_type) . '</span><br>';
                echo '<label>Vehicle Model:</label> <span>' . esc_html($row->vehicle_model) . '</span><br>';
                echo '<label>Own/Lease:</label> <span>' . esc_html($row->own_or_lease) . '</span><br>';
                echo '<label>Owner:</label> <span>' . esc_html($row->owner) . '</span><br>';
                echo '<label>Registration Location:</label> <span>' . esc_html($row->registration_location) . '</span><br>';
                echo '<label>Insurance Policy Location:</label> <span>' . esc_html($row->insurance_policy_location) . '</span><br>';
                echo '<label>Bill of Sale Location:</label> <span>' . esc_html($row->bill_of_sale_location) . '</span><br>';
                echo '<label>Keys Location:</label> <span>' . esc_html($row->keys_location) . '</span><br>';
                echo '<label>Contents List Location:</label> <span>' . esc_html($row->contents_list_location) . '</span><br>';
                echo '</div>';
            }
            echo '</div>';
        }
        echo '</div>';
    }
    public function getModel() {
        return new \EstatePlanningManager\Models\PersonalPropertyModel();
    }
    public function getSection() {
        return 'personal_property';
    }
}
