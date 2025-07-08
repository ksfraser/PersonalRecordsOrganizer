<?php
namespace EstatePlanningManager\Sections;

require_once __DIR__ . '/AbstractSectionView.php';
require_once __DIR__ . '/../models/InsuranceModel.php';

use EstatePlanningManager\Models\InsuranceModel;
use EPM_Shortcodes;

if (!defined('ABSPATH')) exit;

class EPM_InsuranceView extends AbstractSectionView
{
    public static function get_section_key()
    {
        return 'insurance';
    }

    public static function get_fields($shortcodes)
    {
        if ($shortcodes === null) {
            $shortcodes = \EPM_Shortcodes::instance();
        }
        return $shortcodes->get_form_sections()['insurance']['fields'];
    }

    public static function render(
        $client_id,
        $readonly = false
    ) {
        $instance = new self();
        // Render Add Contact button for modal
        echo '<button type="button" class="button epm-add-contact-btn" data-section="insurance">Add Contact</button>';
        
        // Render Add Sponsor button and modal (hidden by default, shown via JS if is_group_insurance is checked)
        echo '<button type="button" class="button epm-add-sponsor-btn" data-section="insurance" style="display:none;">Add Sponsor</button>';
        if (class_exists('EstatePlanningManager\\Sections\\EPM_GroupSponsorModal')) {
            \EstatePlanningManager\Sections\EPM_GroupSponsorModal::render('insurance');
        }
        \EstatePlanningManager\Sections\EPM_InsuranceModal::render('insurance');
        $instance->renderSectionView($client_id, $readonly);
        // Add JS to toggle Add Sponsor button, property selector, and insurance type selector
        echo '<script type="text/javascript">
        jQuery(document).ready(function($){
            function toggleSponsorBtn() {
                var checked = $("input[name=\\"is_group_insurance\\"]").is(":checked");
                $(".epm-add-sponsor-btn").toggle(checked);
            }
            $(document).on("change", "input[name=\\"is_group_insurance\\"]", toggleSponsorBtn);
            toggleSponsorBtn();

            // Property and insurance type selector logic
            function togglePropertyAndTypeSelectors() {
                var type = $("select[name=\\"property_type\\"]").val();
                var $propertyRow = $("[name=\\"property_id\\"]").closest(".form-row, tr, .epm-form-row");
                var $typeRow = $("[name=\\"insurance_type\\"]").closest(".form-row, tr, .epm-form-row");
                if(type === "auto" || type === "house") {
                    $propertyRow.show();
                } else {
                    $propertyRow.hide();
                }
                if(type === "life") {
                    $typeRow.show();
                } else {
                    $typeRow.show(); // Always show, backend will render as text or select as needed
                }
            }
            $(document).on("change", "select[name=\\"property_type\\"]", togglePropertyAndTypeSelectors);
            togglePropertyAndTypeSelectors();
        });
        </script>';
    }

    protected function getModel() {
        return new InsuranceModel();
    }
    protected function getSection() {
        return 'insurance';
    }
}
