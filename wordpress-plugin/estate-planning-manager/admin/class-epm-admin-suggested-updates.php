<?php
/**
 * Admin Suggested Updates Management Class
 * 
 * Handles the admin interface for managing suggested updates from SuiteCRM
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/trait-epm-singleton.php';

class EPM_Admin_Suggested_Updates {
    use EPM_Singleton;
    
    /**
     * Initialize admin suggested updates
     */
    public function init() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('wp_ajax_epm_approve_suggested_update', array($this, 'approve_suggested_update'));
        add_action('wp_ajax_epm_reject_suggested_update', array($this, 'reject_suggested_update'));
        add_action('wp_ajax_epm_bulk_approve_updates', array($this, 'bulk_approve_updates'));
        add_action('wp_ajax_epm_bulk_reject_updates', array($this, 'bulk_reject_updates'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_submenu_page(
            'estate-planning-manager',
            __('Suggested Updates', 'estate-planning-manager'),
            __('Suggested Updates', 'estate-planning-manager'),
            'manage_options',
            'epm-suggested-updates',
            array($this, 'render_admin_page')
        );
    }
    
    /**
     * Enqueue scripts and styles
     */
    public function enqueue_scripts($hook) {
        if ($hook !== 'estate-planning-manager_page_epm-suggested-updates') {
            return;
        }
        
        wp_enqueue_script(
            'epm-suggested-updates',
            plugin_dir_url(__FILE__) . '../assets/js/admin-suggested-updates.js',
            array('jquery'),
            '1.0.0',
            true
        );
        
        wp_localize_script('epm-suggested-updates', 'epm_suggested_updates', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('epm_suggested_updates_nonce'),
            'strings' => array(
                'confirm_approve' => __('Are you sure you want to approve this update?', 'estate-planning-manager'),
                'confirm_reject' => __('Are you sure you want to reject this update?', 'estate-planning-manager'),
                'confirm_bulk_approve' => __('Are you sure you want to approve all selected updates?', 'estate-planning-manager'),
                'confirm_bulk_reject' => __('Are you sure you want to reject all selected updates?', 'estate-planning-manager'),
                'success' => __('Operation completed successfully.', 'estate-planning-manager'),
                'error' => __('An error occurred. Please try again.', 'estate-planning-manager')
            )
        ));
        
        wp_enqueue_style(
            'epm-suggested-updates',
            plugin_dir_url(__FILE__) . '../assets/css/admin-suggested-updates.css',
            array(),
            '1.0.0'
        );
    }
    
    /**
     * Render admin page
     */
    public function render_admin_page() {
        // Handle bulk actions
        if (isset($_POST['action']) && isset($_POST['suggested_updates'])) {
            $this->handle_bulk_action();
        }
        
        // Get filter parameters
        $status_filter = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : 'pending';
        $client_filter = isset($_GET['client_id']) ? intval($_GET['client_id']) : 0;
        $section_filter = isset($_GET['section']) ? sanitize_text_field($_GET['section']) : '';
        
        // Get suggested updates
        $suggested_updates = $this->get_suggested_updates($status_filter, $client_filter, $section_filter);
        
        // Get clients for filter dropdown
        $clients = $this->get_clients_with_suggestions();
        
        // Get sections for filter dropdown
        $sections = $this->get_sections_with_suggestions();
        
        ?>
        <div class="wrap">
            <h1><?php _e('Suggested Updates from SuiteCRM', 'estate-planning-manager'); ?></h1>
            
            <div class="epm-suggested-updates-filters">
                <form method="get" action="">
                    <input type="hidden" name="page" value="epm-suggested-updates">
                    
                    <select name="status">
                        <option value=""><?php _e('All Statuses', 'estate-planning-manager'); ?></option>
                        <option value="pending" <?php selected($status_filter, 'pending'); ?>><?php _e('Pending', 'estate-planning-manager'); ?></option>
                        <option value="approved" <?php selected($status_filter, 'approved'); ?>><?php _e('Approved', 'estate-planning-manager'); ?></option>
                        <option value="rejected" <?php selected($status_filter, 'rejected'); ?>><?php _e('Rejected', 'estate-planning-manager'); ?></option>
                    </select>
                    
                    <select name="client_id">
                        <option value=""><?php _e('All Clients', 'estate-planning-manager'); ?></option>
                        <?php foreach ($clients as $client): ?>
                            <option value="<?php echo esc_attr($client->id); ?>" <?php selected($client_filter, $client->id); ?>>
                                <?php echo esc_html($client->display_name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    
                    <select name="section">
                        <option value=""><?php _e('All Sections', 'estate-planning-manager'); ?></option>
                        <?php foreach ($sections as $section): ?>
                            <option value="<?php echo esc_attr($section); ?>" <?php selected($section_filter, $section); ?>>
                                <?php echo esc_html(ucwords(str_replace('_', ' ', $section))); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    
                    <?php submit_button(__('Filter', 'estate-planning-manager'), 'secondary', 'filter', false); ?>
                </form>
            </div>
            
            <?php if (!empty($suggested_updates)): ?>
                <form method="post" action="">
                    <?php wp_nonce_field('epm_bulk_action_nonce'); ?>
                    
                    <div class="tablenav top">
                        <div class="alignleft actions bulkactions">
                            <select name="action">
                                <option value=""><?php _e('Bulk Actions', 'estate-planning-manager'); ?></option>
                                <option value="approve"><?php _e('Approve', 'estate-planning-manager'); ?></option>
                                <option value="reject"><?php _e('Reject', 'estate-planning-manager'); ?></option>
                            </select>
                            <?php submit_button(__('Apply', 'estate-planning-manager'), 'action', 'bulk_action', false); ?>
                        </div>
                    </div>
                    
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <td class="manage-column column-cb check-column">
                                    <input type="checkbox" id="cb-select-all">
                                </td>
                                <th><?php _e('Client', 'estate-planning-manager'); ?></th>
                                <th><?php _e('Section', 'estate-planning-manager'); ?></th>
                                <th><?php _e('Field', 'estate-planning-manager'); ?></th>
                                <th><?php _e('Current Value', 'estate-planning-manager'); ?></th>
                                <th><?php _e('Suggested Value', 'estate-planning-manager'); ?></th>
                                <th><?php _e('Source', 'estate-planning-manager'); ?></th>
                                <th><?php _e('Date', 'estate-planning-manager'); ?></th>
                                <th><?php _e('Status', 'estate-planning-manager'); ?></th>
                                <th><?php _e('Actions', 'estate-planning-manager'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($suggested_updates as $update): ?>
                                <tr>
                                    <th scope="row" class="check-column">
                                        <input type="checkbox" name="suggested_updates[]" value="<?php echo esc_attr($update->id); ?>">
                                    </th>
                                    <td><?php echo esc_html($update->client_name); ?></td>
                                    <td><?php echo esc_html(ucwords(str_replace('_', ' ', $update->section))); ?></td>
                                    <td><?php echo esc_html(ucwords(str_replace('_', ' ', $update->field_name))); ?></td>
                                    <td>
                                        <div class="epm-value-display">
                                            <?php echo $this->format_value_display($update->current_value, $update->field_name); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="epm-value-display epm-suggested-value">
                                            <?php echo $this->format_value_display($update->suggested_value, $update->field_name); ?>
                                        </div>
                                    </td>
                                    <td><?php echo esc_html(ucfirst($update->source)); ?></td>
                                    <td><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($update->created_at))); ?></td>
                                    <td>
                                        <span class="epm-status epm-status-<?php echo esc_attr($update->status); ?>">
                                            <?php echo esc_html(ucfirst($update->status)); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($update->status === 'pending'): ?>
                                            <button type="button" class="button button-primary epm-approve-update" 
                                                    data-update-id="<?php echo esc_attr($update->id); ?>">
                                                <?php _e('Approve', 'estate-planning-manager'); ?>
                                            </button>
                                            <button type="button" class="button epm-reject-update" 
                                                    data-update-id="<?php echo esc_attr($update->id); ?>">
                                                <?php _e('Reject', 'estate-planning-manager'); ?>
                                            </button>
                                        <?php else: ?>
                                            <span class="description"><?php _e('No actions available', 'estate-planning-manager'); ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </form>
            <?php else: ?>
                <div class="notice notice-info">
                    <p><?php _e('No suggested updates found.', 'estate-planning-manager'); ?></p>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }
    
    /**
     * Get suggested updates with filters
     */
    private function get_suggested_updates($status_filter = '', $client_filter = 0, $section_filter = '') {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_suggested_updates';
        $clients_table = $wpdb->prefix . 'epm_clients';
        $users_table = $wpdb->users;
        
        $where_conditions = array('1=1');
        $where_values = array();
        
        if (!empty($status_filter)) {
            $where_conditions[] = 'su.status = %s';
            $where_values[] = $status_filter;
        }
        
        if ($client_filter > 0) {
            $where_conditions[] = 'su.client_id = %d';
            $where_values[] = $client_filter;
        }
        
        if (!empty($section_filter)) {
            $where_conditions[] = 'su.section = %s';
            $where_values[] = $section_filter;
        }
        
        $where_clause = implode(' AND ', $where_conditions);
        
        $sql = "
            SELECT su.*, u.display_name as client_name
            FROM $table_name su
            LEFT JOIN $clients_table c ON su.client_id = c.id
            LEFT JOIN $users_table u ON c.user_id = u.ID
            WHERE $where_clause
            ORDER BY su.created_at DESC
        ";
        
        if (!empty($where_values)) {
            return $wpdb->get_results($wpdb->prepare($sql, $where_values));
        } else {
            return $wpdb->get_results($sql);
        }
    }
    
    /**
     * Get clients with suggestions
     */
    private function get_clients_with_suggestions() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_suggested_updates';
        $clients_table = $wpdb->prefix . 'epm_clients';
        $users_table = $wpdb->users;
        
        return $wpdb->get_results("
            SELECT DISTINCT c.id, u.display_name
            FROM $table_name su
            LEFT JOIN $clients_table c ON su.client_id = c.id
            LEFT JOIN $users_table u ON c.user_id = u.ID
            ORDER BY u.display_name
        ");
    }
    
    /**
     * Get sections with suggestions
     */
    private function get_sections_with_suggestions() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_suggested_updates';
        
        $results = $wpdb->get_col("
            SELECT DISTINCT section
            FROM $table_name
            ORDER BY section
        ");
        
        return $results;
    }
    
    /**
     * Format value display
     */
    private function format_value_display($value, $field_name) {
        if (empty($value)) {
            return '<em>' . __('(empty)', 'estate-planning-manager') . '</em>';
        }
        
        // Handle special field types
        if ($field_name === 'new_record') {
            $data = json_decode($value, true);
            if ($data) {
                $output = '<strong>' . __('New Record:', 'estate-planning-manager') . '</strong><br>';
                foreach ($data as $key => $val) {
                    if (!empty($val)) {
                        $output .= esc_html(ucwords(str_replace('_', ' ', $key))) . ': ' . esc_html($val) . '<br>';
                    }
                }
                return $output;
            }
        }
        
        // Truncate long values
        if (strlen($value) > 100) {
            return esc_html(substr($value, 0, 100)) . '...';
        }
        
        return esc_html($value);
    }
    
    /**
     * Handle bulk actions
     */
    private function handle_bulk_action() {
        if (!wp_verify_nonce($_POST['_wpnonce'], 'epm_bulk_action_nonce')) {
            wp_die(__('Security check failed.', 'estate-planning-manager'));
        }
        
        $action = sanitize_text_field($_POST['action']);
        $update_ids = array_map('intval', $_POST['suggested_updates']);
        
        if (empty($update_ids)) {
            return;
        }
        
        switch ($action) {
            case 'approve':
                foreach ($update_ids as $update_id) {
                    $this->process_approve_update($update_id);
                }
                add_action('admin_notices', function() {
                    echo '<div class="notice notice-success"><p>' . __('Selected updates have been approved.', 'estate-planning-manager') . '</p></div>';
                });
                break;
                
            case 'reject':
                foreach ($update_ids as $update_id) {
                    $this->process_reject_update($update_id);
                }
                add_action('admin_notices', function() {
                    echo '<div class="notice notice-success"><p>' . __('Selected updates have been rejected.', 'estate-planning-manager') . '</p></div>';
                });
                break;
        }
    }
    
    /**
     * AJAX handler for approving suggested update
     */
    public function approve_suggested_update() {
        check_ajax_referer('epm_suggested_updates_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions.', 'estate-planning-manager'));
        }
        
        $update_id = intval($_POST['update_id']);
        
        if ($this->process_approve_update($update_id)) {
            wp_send_json_success(__('Update approved successfully.', 'estate-planning-manager'));
        } else {
            wp_send_json_error(__('Failed to approve update.', 'estate-planning-manager'));
        }
    }
    
    /**
     * AJAX handler for rejecting suggested update
     */
    public function reject_suggested_update() {
        check_ajax_referer('epm_suggested_updates_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions.', 'estate-planning-manager'));
        }
        
        $update_id = intval($_POST['update_id']);
        
        if ($this->process_reject_update($update_id)) {
            wp_send_json_success(__('Update rejected successfully.', 'estate-planning-manager'));
        } else {
            wp_send_json_error(__('Failed to reject update.', 'estate-planning-manager'));
        }
    }
    
    /**
     * Process approve update
     */
    private function process_approve_update($update_id) {
        global $wpdb;
        
        // Get the suggested update
        $table_name = $wpdb->prefix . 'epm_suggested_updates';
        $update = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d AND status = 'pending'",
            $update_id
        ));
        
        if (!$update) {
            return false;
        }
        
        // Apply the update to the actual data
        $success = $this->apply_suggested_update($update);
        
        if ($success) {
            // Mark as approved
            $wpdb->update(
                $table_name,
                array(
                    'status' => 'approved',
                    'reviewed_by_user_id' => get_current_user_id(),
                    'reviewed_at' => current_time('mysql')
                ),
                array('id' => $update_id),
                array('%s', '%d', '%s'),
                array('%d')
            );
            
            // Log the action
            EPM_Audit_Logger::instance()->log_action(
                'suggested_update_approved',
                'suggested_updates',
                $update_id,
                null,
                array('client_id' => $update->client_id, 'section' => $update->section)
            );
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Process reject update
     */
    private function process_reject_update($update_id) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_suggested_updates';
        
        $result = $wpdb->update(
            $table_name,
            array(
                'status' => 'rejected',
                'reviewed_by_user_id' => get_current_user_id(),
                'reviewed_at' => current_time('mysql')
            ),
            array('id' => $update_id, 'status' => 'pending'),
            array('%s', '%d', '%s'),
            array('%d', '%s')
        );
        
        if ($result) {
            // Log the action
            EPM_Audit_Logger::instance()->log_action(
                'suggested_update_rejected',
                'suggested_updates',
                $update_id
            );
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Apply suggested update to actual data
     */
    private function apply_suggested_update($update) {
        if ($update->field_name === 'new_record') {
            // Handle new record creation
            return $this->create_new_record($update);
        } else {
            // Handle field update
            return $this->update_existing_field($update);
        }
    }
    
    /**
     * Create new record from suggested update
     */
    private function create_new_record($update) {
        $data = json_decode($update->suggested_value, true);
        
        if (!$data) {
            return false;
        }
        
        // Add client_id to data
        $data['client_id'] = $update->client_id;
        
        return EPM_Database::instance()->save_client_data($update->client_id, $update->section, $data);
    }
    
    /**
     * Update existing field
     */
    private function update_existing_field($update) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_' . $update->section;
        
        if ($update->record_id) {
            // Update specific record
            $result = $wpdb->update(
                $table_name,
                array($update->field_name => $update->suggested_value),
                array('id' => $update->record_id, 'client_id' => $update->client_id),
                array('%s'),
                array('%d', '%d')
            );
        } else {
            // Update client's main record for this section
            $result = $wpdb->update(
                $table_name,
                array($update->field_name => $update->suggested_value),
                array('client_id' => $update->client_id),
                array('%s'),
                array('%d')
            );
        }
        
        return $result !== false;
    }
}
