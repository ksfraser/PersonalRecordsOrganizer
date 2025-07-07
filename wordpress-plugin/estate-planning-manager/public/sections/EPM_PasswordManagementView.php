<?php
namespace EstatePlanningManager\Sections;

require_once __DIR__ . '/../models/PasswordManagementModel.php';
use EstatePlanningManager\Models\PasswordManagementModel;

class EPM_PasswordManagementView extends AbstractSectionView {
    public static function get_section_key() { return 'password_management'; }
    public static function get_fields($shortcodes = null) {
        $model = new PasswordManagementModel();
        return $model->getFormFields();
    }
    public static function render($client_id, $readonly = false) {
        $instance = new self();
        $instance->renderSectionView($client_id, $readonly);
        // Add the Add Storage Type button and modal markup
        ?>
        <button type="button" id="epm-add-storage-type-btn" class="button">Add Storage Type</button>
        <div id="epm-add-storage-type-modal" style="display:none; position:fixed; left:50%; top:30%; background:#fff; border:1px solid #ccc; padding:20px; z-index:10000;">
            <h3>Add Password Storage Type</h3>
            <form id="epm-add-storage-type-form">
                <label>Value: <input type="text" name="value" required></label><br>
                <label>Label: <input type="text" name="label" required></label><br>
                <button type="submit" class="button button-primary">Add</button>
                <button type="button" id="epm-cancel-storage-type" class="button">Cancel</button>
            </form>
            <div id="epm-add-storage-type-msg"></div>
        </div>
        <script>
        jQuery(function($){
            $('#epm-add-storage-type-btn').on('click', function(){
                $('#epm-add-storage-type-modal').show();
            });
            $('#epm-cancel-storage-type').on('click', function(){
                $('#epm-add-storage-type-modal').hide();
            });
            $('#epm-add-storage-type-form').on('submit', function(e){
                e.preventDefault();
                var data = $(this).serialize();
                $.post(ajaxurl, {
                    action: 'epm_add_password_storage_type',
                    data: data,
                    _ajax_nonce: '<?php echo wp_create_nonce('epm_add_password_storage_type'); ?>'
                }, function(response){
                    if(response.success){
                        $('#epm-add-storage-type-msg').text('Added!');
                        $('#epm-add-storage-type-form')[0].reset();
                        // Add new option to all storage_type selects
                        var val = $('#epm-add-storage-type-form input[name="value"]').val();
                        var label = $('#epm-add-storage-type-form input[name="label"]').val();
                        $('select[name="storage_type"]').each(function(){
                            if($(this).find('option[value="'+val+'"]').length === 0) {
                                $(this).append('<option value="'+val+'">'+label+'</option>');
                            }
                        });
                        $('#epm-add-storage-type-modal').hide();
                    } else {
                        $('#epm-add-storage-type-msg').text(response.data || 'Error.');
                    }
                });
            });
        });
        </script>
        <?php
    }
    public function getModel() { return new PasswordManagementModel(); }
    public function getSection() { return 'password_management'; }
}
