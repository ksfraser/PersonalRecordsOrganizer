<?php
namespace EstatePlanningManager\Sections;

class EPM_OtherContractualObligationsView {
    public static function render($data = []) {
        ?>
        <div class="epm-section epm-other-contractual-obligations-section">
            <h2>Other Contractual Obligations</h2>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="epm_save_section">
                <input type="hidden" name="section" value="other_contractual_obligations">
                <?php wp_nonce_field('epm_save_section_other_contractual_obligations', 'epm_save_section_nonce'); ?>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="2" class="widefat"><?php echo esc_textarea($data['description'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Location of Documents</label>
                    <input type="text" name="location_of_documents" class="widefat" value="<?php echo esc_attr($data['location_of_documents'] ?? ''); ?>" />
                </div>
                <div class="form-group">
                    <label>Notes</label>
                    <textarea name="notes" rows="2" class="widefat"><?php echo esc_textarea($data['notes'] ?? ''); ?></textarea>
                </div>
                <button type="submit" class="button button-primary">Save</button>
            </form>
        </div>
        <?php
    }
}
