<?php
// Modal form for adding/editing a contact phone
if (!defined('ABSPATH')) exit;
require_once __DIR__ . '/../../public/models/PhoneLineTypesModel.php';
use EstatePlanningManager\Models\PhoneLineTypesModel;
$type_options = PhoneLineTypesModel::getOptions();
$phone = isset($phone) ? $phone : [
    'id' => '',
    'contact_id' => '',
    'phone' => '',
    'type_id' => '',
    'is_primary' => 0
];
?>
<div id="epm-contact-phone-modal" style="display:none;">
    <form id="epm-contact-phone-form">
        <input type="hidden" name="id" value="<?php echo esc_attr($phone['id']); ?>">
        <input type="hidden" name="contact_id" value="<?php echo esc_attr($phone['contact_id']); ?>">
        <table class="form-table">
            <tr>
                <th><label for="epm-phone">Phone</label></th>
                <td><input type="text" name="phone" id="epm-phone" value="<?php echo esc_attr($phone['phone']); ?>" required></td>
            </tr>
            <tr>
                <th><label for="epm-type-id">Type</label></th>
                <td>
                    <select name="type_id" id="epm-type-id" required>
                        <option value="">Select type...</option>
                        <?php foreach ($type_options as $id => $label): ?>
                            <option value="<?php echo esc_attr($id); ?>" <?php selected($phone['type_id'], $id); ?>><?php echo esc_html($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="epm-is-primary">Primary</label></th>
                <td><input type="checkbox" name="is_primary" id="epm-is-primary" value="1" <?php checked($phone['is_primary'], 1); ?>></td>
            </tr>
        </table>
        <p class="submit">
            <button type="submit" class="button button-primary">Save Phone</button>
        </p>
    </form>
</div>
