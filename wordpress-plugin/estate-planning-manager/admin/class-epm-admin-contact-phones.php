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
        $client_options = \EstatePlanningManager\Models\PeopleModel::getDropdownOptions();
        ?>
        <div class="wrap">
            <h1>Contact Phones</h1>
            <button class="button epm-invite-contact-btn" style="margin-bottom:15px;">Invite Contact</button>
            <form method="get" style="margin-bottom:15px;">
                <input type="hidden" name="page" value="epm-contact-phones">
                <label>Contact:
                    <select name="contact_id">
                        <option value="">All Contacts</option>
                        <?php foreach ($client_options as $id => $name): ?>
                            <option value="<?php echo esc_attr($id); ?>" <?php if(isset($_GET['contact_id']) && $_GET['contact_id'] == $id) echo 'selected'; ?>><?php echo esc_html($name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label style="margin-left:10px;">Phone:
                    <input type="text" name="search_phone" value="<?php echo isset($_GET['search_phone']) ? esc_attr($_GET['search_phone']) : ''; ?>" placeholder="Search phone">
                </label>
                <button type="submit" class="button">Filter</button>
            </form>
            <table class="widefat">
                <thead>
                    <tr><th>ID</th><th>Contact ID</th><th>Phone</th><th>Type</th><th>Primary</th><th>Created</th><th>Last Updated</th></tr>
                </thead>
                <tbody>
                <?php
                // Filtering logic
                $where = [];
                $params = [];
                if (!empty($_GET['contact_id'])) {
                    $where[] = 'contact_id = %d';
                    $params[] = intval($_GET['contact_id']);
                }
                if (!empty($_GET['search_phone'])) {
                    $where[] = 'phone LIKE %s';
                    $params[] = '%' . sanitize_text_field($_GET['search_phone']) . '%';
                }
                $where_sql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
                $sql = "SELECT * FROM $table_name $where_sql ORDER BY contact_id, is_primary DESC, id";
                $phones = $params ? $wpdb->get_results($wpdb->prepare($sql, ...$params)) : $wpdb->get_results($sql);

                foreach ($phones as $row): ?>
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
