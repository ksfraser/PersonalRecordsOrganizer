<?php
// Section view for Creditors with Add Contact modal and dynamic selector
namespace EstatePlanningManager\Sections;

class EPM_CreditorsView {
    public static function render($data = []) {
        ?>
        <div class="epm-section epm-creditors-section">
            <h2>Creditors</h2>
            <!-- Creditors Table or List Here -->
            <button type="button" class="button epm-add-contact-btn" data-section="creditors">Add Contact</button>
            <select id="epm-creditors-contact-selector">
                <!-- Options dynamically loaded -->
            </select>
        </div>
        <!-- Modal Markup -->
        <div id="epm-add-contact-modal" class="epm-modal" style="display:none;">
            <div class="epm-modal-content">
                <span class="epm-close">&times;</span>
                <h3>Add New Contact</h3>
                <form id="epm-add-contact-form" data-section="creditors">
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
