<?php
/**
 * Admin interface for managing selector options
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/trait-epm-singleton.php';

class EPM_Admin_Selectors {
    use EPM_Singleton;
    
    /**
     * Initialize admin interface
     */
    public function init() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'handle_form_submissions'));
        add_action('wp_ajax_epm_delete_selector', array($this, 'ajax_delete_selector'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_submenu_page(
            'estate-planning-manager',
            'Manage Selectors',
            'Selectors',
            'manage_options',
            'epm-selectors',
            array($this, 'render_admin_page')
        );
    }
    
    /**
     * Enqueue admin scripts
     */
    public function enqueue_admin_scripts($hook) {
        if (strpos($hook, 'epm-selectors') === false) {
            return;
        }
        
        wp_enqueue_script('jquery');
        wp_enqueue_script(
            'epm-admin-selectors',
            plugin_dir_url(__FILE__) . '../assets/js/admin-selectors.js',
            array('jquery'),
            '1.0.0',
            true
        );
        
        wp_localize_script('epm-admin-selectors', 'epm_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('epm_selector_nonce'),
            'confirm_delete' => __('Are you sure you want to delete this option? This action cannot be undone.', 'estate-planning-manager')
        ));
    }
    
    /**
     * Handle form submissions
     */
    public function handle_form_submissions() {
        if (!isset($_POST['epm_selector_action']) || !wp_verify_nonce($_POST['epm_selector_nonce'], 'epm_selector_action')) {
            return;
        }
        
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        
        $action = sanitize_text_field($_POST['epm_selector_action']);
        $selector_type = sanitize_text_field($_POST['selector_type']);
        
        switch ($action) {
            case 'add':
                $this->add_selector_option($selector_type);
                break;
            case 'edit':
                $this->edit_selector_option($selector_type);
                break;
        }
    }
    
    /**
     * Add new selector option
     */
    private function add_selector_option($selector_type) {
        global $wpdb;
        
        $value = sanitize_text_field($_POST['option_value']);
        $label = sanitize_text_field($_POST['option_label']);
        $sort_order = intval($_POST['sort_order']);
        
        if (empty($value) || empty($label)) {
            add_action('admin_notices', function() {
                echo '<div class="notice notice-error"><p>Value and Label are required fields.</p></div>';
            });
            return;
        }
        
        $table_name = $wpdb->prefix . 'epm_' . $selector_type;
        
        // Check if value already exists
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE value = %s",
            $value
        ));
        
        if ($existing > 0) {
            add_action('admin_notices', function() {
                echo '<div class="notice notice-error"><p>This value already exists.</p></div>';
            });
            return;
        }
        
        $result = $wpdb->insert(
            $table_name,
            array(
                'value' => $value,
                'label' => $label,
                'is_active' => 1,
                'sort_order' => $sort_order
            ),
            array('%s', '%s', '%d', '%d')
        );
        
        if ($result !== false) {
            add_action('admin_notices', function() {
                echo '<div class="notice notice-success"><p>Option added successfully.</p></div>';
            });
        } else {
            add_action('admin_notices', function() {
                echo '<div class="notice notice-error"><p>Error adding option.</p></div>';
            });
        }
    }
    
    /**
     * Edit selector option
     */
    private function edit_selector_option($selector_type) {
        global $wpdb;
        
        $id = intval($_POST['option_id']);
        $value = sanitize_text_field($_POST['option_value']);
        $label = sanitize_text_field($_POST['option_label']);
        $sort_order = intval($_POST['sort_order']);
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        if (empty($value) || empty($label)) {
            add_action('admin_notices', function() {
                echo '<div class="notice notice-error"><p>Value and Label are required fields.</p></div>';
            });
            return;
        }
        
        $table_name = $wpdb->prefix . 'epm_' . $selector_type;
        
        // Check if value already exists for different ID
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE value = %s AND id != %d",
            $value, $id
        ));
        
        if ($existing > 0) {
            add_action('admin_notices', function() {
                echo '<div class="notice notice-error"><p>This value already exists.</p></div>';
            });
            return;
        }
        
        $result = $wpdb->update(
            $table_name,
            array(
                'value' => $value,
                'label' => $label,
                'is_active' => $is_active,
                'sort_order' => $sort_order
            ),
            array('id' => $id),
            array('%s', '%s', '%d', '%d'),
            array('%d')
        );
        
        if ($result !== false) {
            add_action('admin_notices', function() {
                echo '<div class="notice notice-success"><p>Option updated successfully.</p></div>';
            });
        } else {
            add_action('admin_notices', function() {
                echo '<div class="notice notice-error"><p>Error updating option.</p></div>';
            });
        }
    }
    
    /**
     * AJAX handler for deleting selector options
     */
    public function ajax_delete_selector() {
        if (!wp_verify_nonce($_POST['nonce'], 'epm_selector_nonce')) {
            wp_die('Security check failed');
        }
        
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }
        
        global $wpdb;
        
        $selector_type = sanitize_text_field($_POST['selector_type']);
        $option_id = intval($_POST['option_id']);
        
        $table_name = $wpdb->prefix . 'epm_' . $selector_type;
        
        // Get the option value before deletion
        $option_value = $wpdb->get_var($wpdb->prepare(
            "SELECT value FROM $table_name WHERE id = %d",
            $option_id
        ));
        
        if (!$option_value) {
            wp_send_json_error('Option not found');
            return;
        }
        
        // Check if this option is being used in any client data
        $usage_count = $this->check_selector_usage($selector_type, $option_value);
        
        if ($usage_count > 0) {
            wp_send_json_error("Cannot delete this option. It is currently being used by $usage_count client record(s).");
            return;
        }
        
        $result = $wpdb->delete(
            $table_name,
            array('id' => $option_id),
            array('%d')
        );
        
        if ($result !== false) {
            wp_send_json_success('Option deleted successfully');
        } else {
            wp_send_json_error('Error deleting option');
        }
    }
    
    /**
     * Check if a selector option is being used in client data
     */
    private function check_selector_usage($selector_type, $option_value) {
        global $wpdb;
        
        $usage_count = 0;
        
        // Map selector types to their corresponding data tables and fields
        $selector_mappings = array(
            'relationship_types' => array(
                array('table' => 'epm_family_contacts', 'field' => 'relationship')
            ),
            'account_types' => array(
                array('table' => 'epm_bank_accounts', 'field' => 'account_type'),
                array('table' => 'epm_investments', 'field' => 'account_type')
            ),
            'contact_types' => array(
                array('table' => 'epm_key_contacts', 'field' => 'contact_type')
            ),
            'insurance_categories' => array(
                array('table' => 'epm_insurance', 'field' => 'insurance_category')
            ),
            'insurance_types' => array(
                array('table' => 'epm_insurance', 'field' => 'insurance_type')
            ),
            'property_types' => array(
                array('table' => 'epm_real_estate', 'field' => 'property_type'),
                array('table' => 'epm_personal_property', 'field' => 'property_type')
            ),
            'investment_types' => array(
                array('table' => 'epm_investments', 'field' => 'investment_type')
            ),
            'payment_types' => array(
                array('table' => 'epm_scheduled_payments', 'field' => 'payment_type')
            ),
            'debt_types' => array(
                array('table' => 'epm_debtors_creditors', 'field' => 'debt_type')
            )
        );
        
        if (!isset($selector_mappings[$selector_type])) {
            return 0;
        }
        
        foreach ($selector_mappings[$selector_type] as $mapping) {
            $table_name = $wpdb->prefix . $mapping['table'];
            $field_name = $mapping['field'];
            
            $count = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $table_name WHERE $field_name = %s",
                $option_value
            ));
            
            $usage_count += intval($count);
        }
        
        return $usage_count;
    }
    
    /**
     * Render admin page
     */
    public function render_admin_page() {
        $current_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'relationship_types';
        $edit_id = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
        
        $tabs = array(
            'relationship_types' => 'Relationship Types',
            'account_types' => 'Account Types',
            'contact_types' => 'Contact Types',
            'insurance_categories' => 'Insurance Categories',
            'insurance_types' => 'Insurance Types',
            'property_types' => 'Property Types',
            'investment_types' => 'Investment Types',
            'payment_types' => 'Payment Types',
            'debt_types' => 'Debt Types'
        );
        
        ?>
        <div class="wrap">
            <h1>Manage Selector Options</h1>
            
            <nav class="nav-tab-wrapper">
                <?php foreach ($tabs as $tab_key => $tab_label): ?>
                    <a href="?page=epm-selectors&tab=<?php echo esc_attr($tab_key); ?>" 
                       class="nav-tab <?php echo $current_tab === $tab_key ? 'nav-tab-active' : ''; ?>">
                        <?php echo esc_html($tab_label); ?>
                    </a>
                <?php endforeach; ?>
            </nav>
            
            <div class="tab-content">
                <?php if ($edit_id > 0): ?>
                    <?php $this->render_edit_form($current_tab, $edit_id); ?>
                <?php else: ?>
                    <?php $this->render_add_form($current_tab); ?>
                    <?php $this->render_options_list($current_tab); ?>
                <?php endif; ?>
            </div>
        </div>
        
        <style>
        .tab-content { margin-top: 20px; }
        .epm-form-table { max-width: 600px; }
        .epm-options-table { margin-top: 30px; }
        .epm-options-table th { text-align: left; }
        .epm-delete-btn { color: #a00; cursor: pointer; }
        .epm-delete-btn:hover { color: #dc3232; }
        .epm-inactive { opacity: 0.6; }
        </style>
        <?php
    }
    
    /**
     * Render add form
     */
    private function render_add_form($selector_type) {
        ?>
        <div class="card">
            <h2>Add New Option</h2>
            <form method="post" action="">
                <?php wp_nonce_field('epm_selector_action', 'epm_selector_nonce'); ?>
                <input type="hidden" name="epm_selector_action" value="add">
                <input type="hidden" name="selector_type" value="<?php echo esc_attr($selector_type); ?>">
                
                <table class="form-table epm-form-table">
                    <tr>
                        <th scope="row"><label for="option_value">Value</label></th>
                        <td>
                            <input type="text" id="option_value" name="option_value" class="regular-text" required>
                            <p class="description">Internal value (lowercase, underscores for spaces)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="option_label">Label</label></th>
                        <td>
                            <input type="text" id="option_label" name="option_label" class="regular-text" required>
                            <p class="description">Display label shown to users</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="sort_order">Sort Order</label></th>
                        <td>
                            <input type="number" id="sort_order" name="sort_order" class="small-text" value="0">
                            <p class="description">Lower numbers appear first</p>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button('Add Option'); ?>
            </form>
        </div>
        <?php
    }
    
    /**
     * Render edit form
     */
    private function render_edit_form($selector_type, $edit_id) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_' . $selector_type;
        $option = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d",
            $edit_id
        ));
        
        if (!$option) {
            echo '<div class="notice notice-error"><p>Option not found.</p></div>';
            return;
        }
        
        ?>
        <div class="card">
            <h2>Edit Option</h2>
            <form method="post" action="">
                <?php wp_nonce_field('epm_selector_action', 'epm_selector_nonce'); ?>
                <input type="hidden" name="epm_selector_action" value="edit">
                <input type="hidden" name="selector_type" value="<?php echo esc_attr($selector_type); ?>">
                <input type="hidden" name="option_id" value="<?php echo esc_attr($option->id); ?>">
                
                <table class="form-table epm-form-table">
                    <tr>
                        <th scope="row"><label for="option_value">Value</label></th>
                        <td>
                            <input type="text" id="option_value" name="option_value" class="regular-text" 
                                   value="<?php echo esc_attr($option->value); ?>" required>
                            <p class="description">Internal value (lowercase, underscores for spaces)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="option_label">Label</label></th>
                        <td>
                            <input type="text" id="option_label" name="option_label" class="regular-text" 
                                   value="<?php echo esc_attr($option->label); ?>" required>
                            <p class="description">Display label shown to users</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="sort_order">Sort Order</label></th>
                        <td>
                            <input type="number" id="sort_order" name="sort_order" class="small-text" 
                                   value="<?php echo esc_attr($option->sort_order); ?>">
                            <p class="description">Lower numbers appear first</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="is_active">Active</label></th>
                        <td>
                            <input type="checkbox" id="is_active" name="is_active" value="1" 
                                   <?php checked($option->is_active, 1); ?>>
                            <label for="is_active">Option is active and available for selection</label>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button('Update Option'); ?>
                <a href="?page=epm-selectors&tab=<?php echo esc_attr($selector_type); ?>" class="button">Cancel</a>
            </form>
        </div>
        <?php
    }
    
    /**
     * Render options list
     */
    private function render_options_list($selector_type) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_' . $selector_type;
        $options = $wpdb->get_results(
            "SELECT * FROM $table_name ORDER BY sort_order ASC, label ASC"
        );
        
        ?>
        <div class="card epm-options-table">
            <h2>Current Options</h2>
            
            <?php if (empty($options)): ?>
                <p>No options found.</p>
            <?php else: ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>Value</th>
                            <th>Label</th>
                            <th>Sort Order</th>
                            <th>Status</th>
                            <th>Usage</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($options as $option): ?>
                            <?php $usage_count = $this->check_selector_usage($selector_type, $option->value); ?>
                            <tr class="<?php echo $option->is_active ? '' : 'epm-inactive'; ?>">
                                <td><code><?php echo esc_html($option->value); ?></code></td>
                                <td><?php echo esc_html($option->label); ?></td>
                                <td><?php echo esc_html($option->sort_order); ?></td>
                                <td>
                                    <?php echo $option->is_active ? 
                                        '<span style="color: green;">Active</span>' : 
                                        '<span style="color: red;">Inactive</span>'; ?>
                                </td>
                                <td>
                                    <?php if ($usage_count > 0): ?>
                                        <strong><?php echo $usage_count; ?> record(s)</strong>
                                    <?php else: ?>
                                        <span style="color: #666;">Not used</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="?page=epm-selectors&tab=<?php echo esc_attr($selector_type); ?>&edit=<?php echo esc_attr($option->id); ?>">
                                        Edit
                                    </a>
                                    <?php if ($usage_count === 0): ?>
                                        | <span class="epm-delete-btn" 
                                                data-selector-type="<?php echo esc_attr($selector_type); ?>"
                                                data-option-id="<?php echo esc_attr($option->id); ?>">
                                            Delete
                                        </span>
                                    <?php else: ?>
                                        | <span style="color: #666;" title="Cannot delete - option is in use">Delete</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        <?php
    }
}
