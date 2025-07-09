<?php
// Admin UI for managing contact phones, with type selector
if (!defined('ABSPATH')) exit;
require_once __DIR__ . '/../public/models/PhoneLineTypesModel.php';
use EstatePlanningManager\Models\PhoneLineTypesModel;

class EPM_Admin_Contact_Phones {
    public static function render_page() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_contact_phones';
        $type_options = PhoneLineTypesModel::getOptions();
        $phones = $wpdb->get_results("SELECT * FROM $table_name ORDER BY contact_id, is_primary DESC, id");
        ?>
        <div class="wrap">
            <h1>Contact Phones</h1>
            <button class="button epm-invite-contact-btn" style="margin-bottom:15px;">Invite Contact</button>
            <table class="widefat">
                <thead>
                    <tr><th>ID</th><th>Contact ID</th><th>Phone</th><th>Type</th><th>Primary</th><th>Created</th><th>Last Updated</th></tr>
                </thead>
                <tbody>
                <?php foreach ($phones as $row): ?>
                    <tr>
                        <td><?php echo esc_html($row->id); ?></td>
                        <td><?php echo esc_html($row->contact_id); ?></td>
                        <td><?php echo esc_html($row->phone); ?></td>
                        <td><?php echo isset($type_options[$row->type_id]) ? esc_html($type_options[$row->type_id]) : '-'; ?></td>
                        <td><?php echo $row->is_primary ? 'Yes' : 'No'; ?></td>
                        <td><?php echo esc_html($row->created); ?></td>
                        <td><?php echo esc_html($row->lastupdated); ?></td>
                        <td><button class="button epm-invite-contact-btn" data-contact-id="<?php echo esc_attr($row->contact_id); ?>">Invite</button></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}
