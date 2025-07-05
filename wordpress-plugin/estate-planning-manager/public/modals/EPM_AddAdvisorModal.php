<?php
/**
 * EPM_AddAdvisorModal
 * Renders the Add Advisor modal HTML
 */
class EPM_AddAdvisorModal {
    public static function render() {
        ob_start();
        ?>
        <div id="epm-add-advisor-modal" class="epm-modal" style="display:none;position:fixed;top:10%;left:50%;transform:translateX(-50%);background:#fff;border:1px solid #ccc;border-radius:5px;padding:30px;z-index:9999;max-width:400px;width:90%;">
            <h3>Add New Advisor</h3>
            <form id="epm-add-advisor-form" method="post" action="#">
                <label>Name:</label><input type="text" name="full_name" required><br>
                <label>Email:</label><input type="email" name="email"><br>
                <label>Phone:</label><input type="tel" name="phone"><br>
                <button type="submit" class="epm-btn epm-btn-primary">Add Advisor</button>
                <button type="button" class="epm-btn epm-btn-secondary epm-modal-cancel" style="margin-left:10px;">Cancel</button>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }
}
