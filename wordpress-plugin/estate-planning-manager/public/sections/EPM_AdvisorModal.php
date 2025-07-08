<?php
// Modal for Add Advisor in Insurance section
namespace EstatePlanningManager\Sections;

class EPM_AdvisorModal {
    public static function render($section = 'insurance') {
        ?>
        <div id="epm-add-advisor-modal" class="epm-modal" style="display:none;">
            <div class="epm-modal-content">
                <span class="epm-close">&times;</span>
                <h3>Add New Advisor</h3>
                <form id="epm-add-advisor-form" data-section="<?php echo esc_attr($section); ?>">
                    <label>Name:</label>
                    <input type="text" name="name" required />
                    <label>Email:</label>
                    <input type="email" name="email" />
                    <label>Phone:</label>
                    <input type="text" name="phone" />
                    <button type="submit" class="button button-primary">Add Advisor</button>
                </form>
            </div>
        </div>
        <?php
    }
}
