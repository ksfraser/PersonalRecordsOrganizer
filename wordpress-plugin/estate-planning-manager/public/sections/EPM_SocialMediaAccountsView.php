<?php
namespace EstatePlanningManager\Sections;

require_once __DIR__ . '/../models/SocialMediaAccountsModel.php';
use EstatePlanningManager\Models\SocialMediaAccountsModel;

class EPM_SocialMediaAccountsView extends AbstractSectionView {
    public static function get_section_key() { return 'social_media_accounts'; }
    public static function get_fields($shortcodes = null) {
        $model = new SocialMediaAccountsModel();
        return $model->getFormFields();
    }
    public static function render($client_id, $readonly = false) {
        $instance = new self();
        // Render the section view as usual
        $instance->renderSectionView($client_id, $readonly);
        // Add the Add Type button and modal markup
        ?>
        <button type="button" id="epm-add-sm-type-btn" class="button">Add Social Media Type</button>
        <div id="epm-add-sm-type-modal" style="display:none; position:fixed; left:50%; top:30%; background:#fff; border:1px solid #ccc; padding:20px; z-index:10000;">
            <h3>Add Social Media Platform Type</h3>
            <form id="epm-add-sm-type-form">
                <label>Value: <input type="text" name="value" required></label><br>
                <label>Label: <input type="text" name="label" required></label><br>
                <button type="submit" class="button button-primary">Add</button>
                <button type="button" id="epm-cancel-sm-type" class="button">Cancel</button>
            </form>
            <div id="epm-add-sm-type-msg"></div>
        </div>
        <script>
        jQuery(function($){
            $('#epm-add-sm-type-btn').on('click', function(){
                $('#epm-add-sm-type-modal').show();
            });
            $('#epm-cancel-sm-type').on('click', function(){
                $('#epm-add-sm-type-modal').hide();
            });
            $('#epm-add-sm-type-form').on('submit', function(e){
                e.preventDefault();
                var data = $(this).serialize();
                $.post(ajaxurl, {
                    action: 'epm_add_social_media_type',
                    data: data,
                    _ajax_nonce: '<?php echo wp_create_nonce('epm_add_social_media_type'); ?>'
                }, function(response){
                    if(response.success){
                        $('#epm-add-sm-type-msg').text('Added!');
                        $('#epm-add-sm-type-form')[0].reset();
                        // Add new option to all social media type selects
                        var val = $('#epm-add-sm-type-form input[name="value"]').val();
                        var label = $('#epm-add-sm-type-form input[name="label"]').val();
                        $('select[name="type"]').each(function(){
                            if($(this).find('option[value="'+val+'"]').length === 0) {
                                $(this).append('<option value="'+val+'">'+label+'</option>');
                            }
                        });
                        $('#epm-add-sm-type-modal').hide();
                    } else {
                        $('#epm-add-sm-type-msg').text(response.data || 'Error.');
                    }
                });
            });
        });
        </script>
        <?php
    }
    public function getModel() { return new SocialMediaAccountsModel(); }
    public function getSection() { return 'social_media_accounts'; }
}
