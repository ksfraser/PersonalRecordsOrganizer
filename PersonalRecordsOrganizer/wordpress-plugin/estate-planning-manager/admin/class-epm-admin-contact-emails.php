<?php
// Admin UI for managing contact emails, with client filter and email search
if (!defined('ABSPATH')) exit;
require_once __DIR__ . '/../public/models/PeopleModel.php';
use EstatePlanningManager\Models\PeopleModel;

class EPM_Admin_Contact_Emails {
    public static function render_page() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_contact_emails';
        $client_options = PeopleModel::getDropdownOptions();
        $filter_client_id = isset($_GET['client_id']) ? intval($_GET['client_id']) : '';
        $search_email = isset($_GET['search_email']) ? sanitize_text_field($_GET['search_email']) : '';
        // Build WHERE clause
        $where = [];
        $params = [];
        if ($filter_client_id) {
            $where[] = 'client_id = %d';
            $params[] = $filter_client_id;
        }
        if ($search_email) {
            $where[] = 'email LIKE %s';
            $params[] = '%' . $search_email . '%';
        }
        $where_sql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
        $sql = "SELECT * FROM $table_name $where_sql ORDER BY client_id, id";
        $emails = $params ? $wpdb->get_results($wpdb->prepare($sql, ...$params)) : $wpdb->get_results($sql);
        ?>
        <div class="wrap">
            <h1>Contact Emails</h1>
            <button class="button epm-invite-contact-btn" style="margin-bottom:15px;">Invite Contact</button>
            <form method="get" style="margin-bottom:15px;">
                <input type="hidden" name="page" value="epm-contact-emails">
                <label>Client:
                    <select name="client_id">
                        <option value="">All Clients</option>
                        <?php foreach ($client_options as $id => $name): ?>
                            <option value="<?php echo esc_attr($id); ?>" <?php selected($filter_client_id, $id); ?>><?php echo esc_html($name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label style="margin-left:10px;">Email:
                    <input type="text" name="search_email" value="<?php echo esc_attr($search_email); ?>" placeholder="Search email">
                </label>
                <button type="submit" class="button">Filter</button>
            </form>
            <table class="widefat">
                <thead>
                    <tr><th>ID</th><th>Client ID</th><th>Email</th><th>Is Primary</th><th>Created</th><th>Last Updated</th><th>Matching Users</th><th>Action</th></tr>
                </thead>
                <tbody>
                <?php foreach ($emails as $row): ?>
                    <tr>
                        <td><?php echo esc_html($row->id); ?></td>
                        <td><?php echo esc_html($row->client_id); ?></td>
                        <td><?php echo esc_html($row->email); ?></td>
                        <td><?php echo $row->is_primary ? 'Yes' : 'No'; ?></td>
                        <td><?php echo esc_html($row->created); ?></td>
                        <td><?php echo esc_html($row->lastupdated); ?></td>
                        <td><?php
                            $users = get_users(['search' => '*' . $row->email . '*', 'search_columns' => ['user_email']]);
                            if ($users) {
                                foreach ($users as $user) {
                                    echo esc_html($user->user_login) . ' (' . esc_html($user->user_email) . ')<br>';
                                }
                            } else {
                                echo '<em>None</em>';
                            }
                        ?></td>
                        <td><button class="button epm-invite-contact-btn" data-contact-id="<?php echo esc_attr($row->client_id); ?>" data-email="<?php echo esc_attr($row->email); ?>">Invite</button></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}
