<?php
// Modal for Add Group Insurance Sponsor in Insurance section
namespace EstatePlanningManager\Sections;

class EPM_GroupSponsorModal {
    public static function render($section = 'insurance') {
        ?>
        <div id="epm-add-sponsor-modal" class="epm-modal" style="display:none;">
            <div class="epm-modal-content">
                <span class="epm-close">&times;</span>
                <h3>Add Group Insurance Sponsor</h3>
                <form id="epm-add-sponsor-form" data-section="<?php echo esc_attr($section); ?>">
                    <label>Organization Name:</label>
                    <input type="text" name="org_name" required />
                    <label>Email:</label>
                    <input type="email" name="email" />
                    <label>Phone:</label>
                    <input type="text" name="phone" />
                    <button type="submit" class="button button-primary">Add Sponsor</button>
                </form>
            </div>
        </div>
        <?php
    }
}
