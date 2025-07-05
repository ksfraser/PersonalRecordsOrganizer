<?php
namespace EstatePlanningManager\Sections;

require_once __DIR__ . '/AbstractSectionView.php';
require_once __DIR__ . '/../models/BankingModel.php';

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

    public static function render($client_id, $readonly = false)
    {
        $instance = new self();
        $instance->renderSectionView($client_id, $readonly);
    }

    public function getModel() {
        return new BankingModel();
    }
    public function getSection() {
        return 'banking';
    }
    public static function render_view($records) {
        global $wpdb;
        $location_table = $wpdb->prefix . 'epm_bank_location_types';
        $locations = $wpdb->get_results("SELECT value, label FROM $location_table WHERE is_active = 1 ORDER BY sort_order ASC");
        echo '<h2>Bank Accounts</h2>';
        echo '<table class="epm-table"><tr><th>Bank Name</th><th>Account Type</th><th>Account Number</th><th>Location</th></tr>';
        foreach ($records as $rec) {
            echo '<tr>';
            echo '<td>' . esc_html($rec->bank_name) . '</td>';
            echo '<td>' . esc_html($rec->account_type) . '</td>';
            echo '<td>' . esc_html($rec->account_number) . '</td>';
            echo '<td>' . esc_html($rec->bank_location) . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        echo '<button type="button" class="epm-btn epm-btn-primary" id="epm-add-bank-btn">Add Bank</button>';
        echo '<div id="epm-add-bank-modal" class="epm-modal" style="display:none;">';
        echo '<div><h3>Add Bank</h3>';
        echo '<form id="epm-add-bank-form">';
        echo '<label for="bank_location">Location</label>';
        echo '<select id="bank_location" name="bank_location">';
        foreach ($locations as $loc) {
            echo '<option value="' . esc_attr($loc->value) . '">' . esc_html($loc->label) . '</option>';
        }
        echo '</select>';
        echo '<label for="bank_name">Bank Name</label>';
        echo '<select id="bank_name" name="bank_name"><option value="">Select...</option></select>';
        echo '<input type="text" id="account_number" name="account_number" placeholder="Account Number" required />';
        echo '<button type="submit" class="epm-btn epm-btn-primary">Save</button>';
        echo '<button type="button" class="epm-btn epm-modal-cancel">Cancel</button>';
        echo '</form></div></div>';
        echo '<script>jQuery(function($){
            $("#epm-add-bank-btn").on("click",function(){$("#epm-add-bank-modal").fadeIn(200);});
            $("#epm-add-bank-modal .epm-modal-cancel").on("click",function(){$("#epm-add-bank-modal").fadeOut(200);});
            $("#bank_location").on("change",function(){
                var loc=$(this).val();
                $.post(ajaxurl,{action:"epm_get_banks_by_location",location:loc},function(resp){
                    var opts=\'<option value="">Select...</option>\';
                    if(resp.success && resp.data){
                        $.each(resp.data,function(i,bank){opts+=\'<option value="\'+bank+\'">\'+bank+\'</option>\';});
                    }
                    $("#bank_name").html(opts);
                });
            });
            $("#epm-add-bank-form").on("submit",function(e){e.preventDefault();alert("Bank added (stub)");$("#epm-add-bank-modal").fadeOut(200);});
        });</script>';
    }
}
