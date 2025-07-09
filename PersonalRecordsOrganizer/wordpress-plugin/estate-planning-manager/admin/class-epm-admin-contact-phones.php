<?php
// Admin UI for managing contact phones, with type selector and filters
if (!defined('ABSPATH')) exit;
require_once __DIR__ . '/../public/models/PhoneLineTypesModel.php';
require_once __DIR__ . '/../public/models/PeopleModel.php';
use EstatePlanningManager\Models\PhoneLineTypesModel;
use EstatePlanningManager\Models\PeopleModel;

class EPM_Admin_Contact_Phones {
    public static function render_page() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_contact_phones';
        $type_options = PhoneLineTypesModel::getOptions();
        $contact_options = PeopleModel::getDropdownOptions();
        $filter_contact_id = isset($_GET['contact_id']) ? intval($_GET['contact_id']) : '';
        $search_phone = isset($_GET['search_phone']) ? sanitize_text_field($_GET['search_phone']) : '';
        // Build WHERE clause
        $where = [];
        $params = [];
        if ($filter_contact_id) {
            $where[] = 'contact_id = %d';
            $params[] = $filter_contact_id;
        }
        if ($search_phone) {
            $where[] = 'phone LIKE %s';
            $params[] = '%' . $search_phone . '%';
        }
        $where_sql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
        $sql = "SELECT * FROM $table_name $where_sql ORDER BY contact_id, is_primary DESC, id";
        $phones = $params ? $wpdb->get_results($wpdb->prepare($sql, ...$params)) : $wpdb->get_results($sql);
        ?>
        <div class="wrap">
            <h1>Contact Phones</h1>
            <form method="get" style="margin-bottom:15px;">
                <input type="hidden" name="page" value="epm-contact-phones">
                <label>Contact:
                    <select name="contact_id">
                        <option value="">All Contacts</option>
                        <?php foreach ($contact_options as $id => $name): ?>
                            <option value="<?php echo esc_attr($id); ?>" <?php selected($filter_contact_id, $id); ?>><?php echo esc_html($name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label style="margin-left:10px;">Phone:
                    <input type="text" name="search_phone" value="<?php echo esc_attr($search_phone); ?>" placeholder="Search phone">
                </label>
                <button type="submit" class="button">Filter</button>
            </form>
            <table class="widefat">
                <thead>
                    <tr><th>ID</th><th>Contact ID</th><th>Phone</th><th>Type</th><th>Primary</th><th>Created</th><th>Last Updated</th><th>Matching Users/Customers</th></tr>
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
                        <td>
                        <?php
                        $matches = [];
                        // WordPress users
                        $users = get_users([
                            'meta_key' => 'billing_phone',
                            'meta_value' => $row->phone
                        ]);
                        if ($users) {
                            foreach ($users as $user) {
                                $matches[] = esc_html($user->user_login) . ' (' . esc_html($user->user_email) . ')';
                            }
                        }
                        // WooCommerce customers (if installed)
                        if (class_exists('WooCommerce')) {
                            $wc_users = get_users([
                                'meta_key' => 'billing_phone',
                                'meta_value' => $row->phone
                            ]);
                            foreach ($wc_users as $wc_user) {
                                $matches[] = esc_html($wc_user->user_login) . ' (WooCommerce)';
                            }
                        }
                        echo $matches ? implode('<br>', $matches) : '<em>None</em>';
                        ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}
