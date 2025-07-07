<?php
namespace EstatePlanningManager\Sections;

require_once __DIR__ . '/AbstractSectionView.php';
require_once __DIR__ . '/../models/ScheduledPaymentsModel.php';

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
            $shortcodes = EPM_Shortcodes::instance();
        }
        return $shortcodes->get_form_sections()['scheduled_payments']['fields'];
    }

    public static function render($client_id, $readonly = false)
    {
        $instance = new self();
        $instance->renderSectionView($client_id, $readonly);
        // Add the Add Payment Type button and modal markup
        ?>
        <button type="button" id="epm-add-payment-type-btn" class="button">Add Payment Type</button>
        <div id="epm-add-payment-type-modal" style="display:none; position:fixed; left:50%; top:30%; background:#fff; border:1px solid #ccc; padding:20px; z-index:10000;">
            <h3>Add Scheduled Payment Type</h3>
            <form id="epm-add-payment-type-form">
                <label>Value: <input type="text" name="value" required></label><br>
                <label>Label: <input type="text" name="label" required></label><br>
                <button type="submit" class="button button-primary">Add</button>
                <button type="button" id="epm-cancel-payment-type" class="button">Cancel</button>
            </form>
            <div id="epm-add-payment-type-msg"></div>
        </div>
        <script>
        jQuery(function($){
            $('#epm-add-payment-type-btn').on('click', function(){
                $('#epm-add-payment-type-modal').show();
            });
            $('#epm-cancel-payment-type').on('click', function(){
                $('#epm-add-payment-type-modal').hide();
            });
            $('#epm-add-payment-type-form').on('submit', function(e){
                e.preventDefault();
                var data = $(this).serialize();
                $.post(ajaxurl, {
                    action: 'epm_add_scheduled_payment_type',
                    data: data,
                    _ajax_nonce: '<?php echo wp_create_nonce('epm_add_scheduled_payment_type'); ?>'
                }, function(response){
                    if(response.success){
                        $('#epm-add-payment-type-msg').text('Added!');
                        $('#epm-add-payment-type-form')[0].reset();
                        // Add new option to all payment_type selects
                        var val = $('#epm-add-payment-type-form input[name="value"]').val();
                        var label = $('#epm-add-payment-type-form input[name="label"]').val();
                        $('select[name="payment_type"]').each(function(){
                            if($(this).find('option[value="'+val+'"]').length === 0) {
                                $(this).append('<option value="'+val+'">'+label+'</option>');
                            }
                        });
                        $('#epm-add-payment-type-modal').hide();
                    } else {
                        $('#epm-add-payment-type-msg').text(response.data || 'Error.');
                    }
                });
            });
        });
        </script>
        <?php
    }

    public function getModel() {
        return new ScheduledPaymentsModel();
    }
    public function getSection() {
        return 'scheduled_payments';
    }
}
