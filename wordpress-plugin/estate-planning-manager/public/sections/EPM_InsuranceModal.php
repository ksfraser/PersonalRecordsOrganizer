<?php
// Modal for Add Contact in Insurance section
namespace EstatePlanningManager\Sections;

class EPM_InsuranceModal {
    public static function render($section = 'insurance') {
        ?>
        <div id="epm-add-contact-modal" class="epm-modal" style="display:none;">
            <div class="epm-modal-content">
                <span class="epm-close">&times;</span>
                <h3>Add New Contact</h3>
                <form id="epm-add-contact-form" data-section="<?php echo esc_attr($section); ?>">
                    <label>Type:</label>
                    <select name="contact_type" id="epm-contact-type">
                        <option value="person">Person</option>
                        <option value="organization">Organization</option>
                    </select>
                    <div id="epm-person-fields">
                        <label>Name:</label>
                        <input type="text" name="person_name" required />
                    </div>
                    <div id="epm-org-fields" style="display:none;">
                        <label>Organization Name:</label>
                        <input type="text" name="org_name" />
                    </div>
                    <button type="submit" class="button button-primary">Add</button>
                </form>
            </div>
        </div>
        <?php
    }
}
