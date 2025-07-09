<?php
// Modal for adding/editing contact email
if (!defined('ABSPATH')) exit;
?>
<div id="epm-modal-contact-email" style="display:none;">
    <form id="epm-contact-email-form">
        <input type="hidden" name="id" value="">
        <label>Client:
            <select name="contact_id" required>
                <?php
                $client_options = \EstatePlanningManager\Models\PeopleModel::getDropdownOptions();
                foreach ($client_options as $id => $name) {
                    echo '<option value="' . esc_attr($id) . '">' . esc_html($name) . '</option>';
                }
                ?>
            </select>
        </label><br>
        <label>Email:
            <input type="email" name="email" required>
        </label><br>
        <label>Primary:
            <input type="checkbox" name="is_primary" value="1">
        </label><br>
        <button type="submit" class="button button-primary">Save</button>
        <button type="button" class="button epm-modal-cancel">Cancel</button>
    </form>
</div>
