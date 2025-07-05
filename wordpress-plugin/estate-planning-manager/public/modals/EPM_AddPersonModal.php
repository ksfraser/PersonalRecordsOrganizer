<?php
/**
 * EPM_AddPersonModal
 * Renders the Add Person modal HTML
 */
class EPM_AddPersonModal {
    public static function render() {
        ob_start();
        ?>
        <div id="epm-add-person-modal" class="epm-modal" style="display:none;position:fixed;top:10%;left:50%;transform:translateX(-50%);background:#fff;border:1px solid #ccc;border-radius:5px;padding:30px;z-index:9999;max-width:400px;width:90%;">
            <h3>Add Person</h3>
            <form id="epm-add-person-form" method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                <input type="hidden" name="action" value="epm_add_person">
                <?php wp_nonce_field('epm_add_person', 'epm_add_person_nonce'); ?>
                <label>Name:</label><input type="text" name="full_name" required><br>
                <label>Email:</label><input type="email" name="email"><br>
                <label>Phone:</label><input type="tel" name="phone"><br>
                <label>Address:</label><input type="text" name="address"><br>
                <button type="submit" class="epm-btn epm-btn-primary">Add</button>
                <button type="button" class="epm-btn epm-btn-secondary epm-modal-cancel" style="margin-left:10px;">Cancel</button>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }
}
