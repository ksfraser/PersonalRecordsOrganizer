<?php
/**
 * Shortcodes Class
 * 
 * Handles shortcodes for Estate Planning Manager
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

require_once dirname(__DIR__) . '/includes/class-epm-logger.php';

class EPM_Shortcodes {
    
    /**
     * Instance of this class
     * @var EPM_Shortcodes
     */
    private static $instance = null;
    
    /**
     * Get instance (Singleton pattern)
     * @return EPM_Shortcodes
     */
    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        // Constructor logic here
    }
    
    /**
     * Initialize shortcodes
     */
    public function init() {
        add_shortcode('epm_client_form', array($this, 'client_form_shortcode'));
        add_shortcode('epm_client_data', array($this, 'client_data_shortcode'));
        add_shortcode('epm_manage_shares', array($this, 'manage_shares_shortcode'));
        add_shortcode('epm_shared_with_you', array($this, 'shared_with_you_shortcode'));
    }
    
    /**
     * Client form shortcode
     */
    public function client_form_shortcode($atts) {
        $atts = shortcode_atts(array(
            'section' => 'personal',
        ), $atts);
        // Log all shortcode GET requests if log level is DEBUG
        if (function_exists('EPM_Logger::log') && EPM_Logger::get_log_level() >= 4) {
            EPM_Logger::log('Shortcode GET: ' . print_r($_GET, true), EPM_Logger::DEBUG);
        }
        ob_start();
        $this->render_client_form($atts['section']);
        return ob_start();
    }
    
    /**
     * Client data shortcode
     */
    public function client_data_shortcode($atts) {
        $atts = shortcode_atts(array(
            'section' => 'all',
            'client_id' => null,
        ), $atts);
        
        ob_start();
        $this->render_client_data($atts['section'], $atts['client_id']);
        return ob_get_clean();
    }
    
    /**
     * Manage Shares Shortcode
     */
    public function manage_shares_shortcode($atts) {
        if (!is_user_logged_in()) {
            return '<div class="epm-error">Please log in to manage your shares.</div>';
        }
        ob_start();
        $this->render_manage_shares();
        return ob_get_clean();
    }

    /**
     * Shared With You Shortcode
     */
    public function shared_with_you_shortcode($atts) {
        if (!is_user_logged_in()) {
            return '<div class="epm-error">Please log in to view shares.</div>';
        }
        ob_start();
        $this->render_shared_with_you();
        return ob_get_clean();
    }
    
    /**
     * Render client form
     */
    private function render_client_form($section) {
        if (!is_user_logged_in()) {
            echo '<div class="epm-error">Please log in to access this form.</div>';
            return;
        }
        $current_user = wp_get_current_user();
        $sections = $this->get_form_sections();
        if (!isset($sections[$section])) {
            echo '<div class="epm-error">Invalid section specified.</div>';
            return;
        }
        // Log user data changes (form save)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['section']) && function_exists('EPM_Logger::log')) {
            $user = wp_get_current_user();
            $msg = 'USER ' . $user->user_login . ' changed section ' . sanitize_text_field($_POST['section']);
            EPM_Logger::log($msg, EPM_Logger::INFO);
        }
        // Modular: delegate to section view class for ALL sections
        $view_class = $this->get_section_view_class($section);
        if ($view_class && class_exists($view_class)) {
            $view_class::render_form($current_user->ID);
        } else {
            echo '<div class="epm-error">Section view class not found for: ' . esc_html($section) . '</div>';
        }
        // Add Person/Institute Modals and scripts for sections that need them
        if (in_array($section, array('scheduled_payments', 'insurance', 'banking', 'investments', 'real_estate'))) {
            // Add Person Modal
            echo '<div id="epm-add-person-modal" class="epm-modal" style="display:none;position:fixed;top:10%;left:50%;transform:translateX(-50%);background:#fff;border:1px solid #ccc;border-radius:5px;padding:30px;z-index:9999;max-width:400px;width:90%;">';
            echo '<h3>Add Person</h3>';
            echo '<form id="epm-add-person-form">';
            echo '<label>Name:</label><input type="text" name="full_name" required><br>';
            echo '<label>Email:</label><input type="email" name="email"><br>';
            echo '<label>Phone:</label><input type="tel" name="phone"><br>';
            echo '<label>Address:</label><input type="text" name="address"><br>';
            echo '<button type="submit" class="epm-btn epm-btn-primary">Add</button>';
            echo '<button type="button" class="epm-btn epm-btn-secondary epm-modal-cancel" style="margin-left:10px;">Cancel</button>';
            echo '</form>';
            echo '</div>';
            // Add Institute Modal
            echo '<div id="epm-add-institute-modal" class="epm-modal" style="display:none;position:fixed;top:10%;left:50%;transform:translateX(-50%);background:#fff;border:1px solid #ccc;border-radius:5px;padding:30px;z-index:9999;max-width:400px;width:90%;">';
            echo '<h3>Add Institute/Organization</h3>';
            echo '<form id="epm-add-institute-form">';
            echo '<label>Name:</label><input type="text" name="name" required><br>';
            echo '<label>Email:</label><input type="email" name="email"><br>';
            echo '<label>Phone:</label><input type="tel" name="phone"><br>';
            echo '<label>Address:</label><input type="text" name="address"><br>';
            echo '<label>Account Number:</label><input type="text" name="account_number"><br>';
            echo '<label>Branch:</label><input type="text" name="branch"><br>';
            echo '<button type="submit" class="epm-btn epm-btn-primary">Add</button>';
            echo '<button type="button" class="epm-btn epm-btn-secondary epm-modal-cancel" style="margin-left:10px;">Cancel</button>';
            echo '</form>';
            echo '</div>';
        }
        $this->enqueue_form_scripts();
        // Show log level indicator for admins
        if (current_user_can('manage_options')) {
            $log_level = EPM_Logger::get_log_level();
            $log_label = ['1'=>'Error','2'=>'Warning','3'=>'Info','4'=>'Debug'];
            echo '<div style="float:right;margin-top:-40px;"><span style="background:#222;color:#fff;padding:2px 8px;border-radius:3px;font-size:12px;">Log Level: ' . esc_html($log_label[$log_level]) . '</span> ';
            echo '<a href="' . admin_url('options-general.php?page=epm-log-viewer') . '" target="_blank" style="color:#0073aa;">View Log</a></div>';
        }
    }
    
    /**
     * Render Manage Shares screen
     */
    private function render_manage_shares() {
        $current_user = wp_get_current_user();
        $db = EPM_Database::instance();
        global $wpdb;
        $client_id = $db->get_client_id_by_user_id($current_user->ID);
        $table = $wpdb->prefix . 'epm_share_invites';
        $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE client_id = %d ORDER BY created_at DESC", $client_id));
        echo '<h3>Manage Shares</h3>';
        if (!$results) {
            echo '<p>No shares found.</p>';
            return;
        }
        echo '<table class="epm-manage-shares-table" style="width:100%; border-collapse:collapse;">';
        echo '<tr><th>Email</th><th>Sections</th><th>Permission</th><th>Status</th><th>Actions</th></tr>';
        foreach ($results as $row) {
            $sections = esc_html(implode(', ', json_decode($row->sections, true)));
            echo '<tr>';
            echo '<td>' . esc_html($row->invitee_email) . '</td>';
            echo '<td>' . $sections . '</td>';
            echo '<td>' . esc_html($row->permission_level) . '</td>';
            echo '<td>' . esc_html($row->status) . '</td>';
            echo '<td>';
            if ($row->status === 'pending' || $row->status === 'accepted') {
                echo '<button class="epm-btn epm-btn-danger epm-revoke-share-btn" data-invite-id="' . esc_attr($row->id) . '">Revoke</button>';
                // Add Generate PDF and Send button
                echo '<button class="epm-btn epm-btn-secondary epm-generate-pdf-send-btn" data-invite-id="' . esc_attr($row->id) . '" style="margin-left:8px;">Generate PDF and Send</button>';
            } else {
                echo '-';
            }
            echo '</td>';
            echo '</tr>';
        }
        echo '</table>';
        // JS for revoke and PDF send
        echo "<script>jQuery(document).ready(function($){\n$('.epm-revoke-share-btn').on('click',function(){\nif(!confirm('Are you sure you want to revoke this share?'))return;\nvar btn=$(this);\nbtn.prop('disabled',true);\n$.post('" . admin_url('admin-ajax.php') . "',{action:'epm_revoke_share',invite_id:btn.data('invite-id'),nonce:'" . wp_create_nonce('epm_revoke_share') . "'},function(resp){if(resp.success){btn.closest('tr').find('td').eq(3).text('revoked');btn.remove();}else{alert('Error: '+resp.data);btn.prop('disabled',false);}});\n});\n// PDF and send\n$('.epm-generate-pdf-send-btn').on('click',function(){\nvar btn=$(this);\nbtn.prop('disabled',true);\nbtn.text('Sending...');\n$.post('" . admin_url('admin-ajax.php') . "',{action:'epm_generate_pdf_and_send',invite_id:btn.data('invite-id'),nonce:'" . wp_create_nonce('epm_generate_pdf_and_send') . "'},function(resp){if(resp.success){btn.text('Sent!').css('color','green');}else{btn.text('Error').css('color','red');alert('Error: '+resp.data);}btn.prop('disabled',false);});\n});\n});</script>";
    }

    /**
     * Render Shared With You screen
     */
    private function render_shared_with_you() {
        $current_user = wp_get_current_user();
        global $wpdb;
        $table = $wpdb->prefix . 'epm_share_invites';
        $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE invitee_email = %s AND status = 'accepted'", $current_user->user_email));
        echo '<h3>Data Shared With You</h3>';
        if (!$results) {
            echo '<p>No data has been shared with you.</p>';
            return;
        }
        foreach ($results as $row) {
            $sections = json_decode($row->sections, true);
            $owner_id = $wpdb->get_var($wpdb->prepare("SELECT user_id FROM {$wpdb->prefix}epm_clients WHERE id = %d", $row->client_id));
            $owner = get_user_by('ID', $owner_id);
            echo '<div class="epm-shared-block" style="border:1px solid #ccc; margin-bottom:20px; padding:10px;">';
            echo '<div><strong>From:</strong> ' . esc_html($owner ? $owner->display_name : 'Unknown') . ' (' . esc_html($owner ? $owner->user_email : '') . ')</div>';
            echo '<div><strong>Sections:</strong> ' . esc_html(implode(', ', $sections)) . '</div>';
            echo '<div><strong>Permission:</strong> ' . esc_html($row->permission_level) . '</div>';
            // Render each shared section in read-only mode
            foreach ($sections as $section) {
                $this->render_client_data($section, $owner_id, true); // read-only
            }
            echo '</div>';
        }
    }

    /**
     * Render client data display (add $readonly param)
     */
    private function render_client_data($section, $client_id = null, $readonly = false) {
        if (!is_user_logged_in()) {
            echo '<div class="epm-error">Please log in to view this data.</div>';
            return;
        }
        $current_user = wp_get_current_user();
        $display_client_id = $client_id ? $client_id : $current_user->ID;
        $is_owner = ($display_client_id == $current_user->ID);
        $sections = $this->get_form_sections();
        $edit_mode = isset($_GET['edit']) && $_GET['edit'] == '1';
        if ($readonly) $edit_mode = false;
        $db = EPM_Database::instance();
        // UI preference switch (only for owner)
        if ($section === 'all' && $is_owner && !$readonly) {
            $ui_pref = $db->get_user_preference($current_user->ID, 'ui_mode');
            if (!$ui_pref) $ui_pref = 'tabs';
            echo '<div class="epm-ui-switcher" style="margin-bottom:15px;">';
            echo 'View mode: ';
            echo '<button class="epm-ui-mode-btn' . ($ui_pref === 'tabs' ? ' epm-ui-active' : '') . '" data-ui-mode="tabs">Tabs</button>';
            echo '<button class="epm-ui-mode-btn' . ($ui_pref === 'twisties' ? ' epm-ui-active' : '') . '" data-ui-mode="twisties">Twisties</button>';
            echo '<span class="epm-ui-mode-status" style="margin-left:10px;"></span>';
            echo '</div>';
            echo '<script>jQuery(function($){$(".epm-ui-mode-btn").on("click",function(e){e.preventDefault();var btn=$(this);var mode=btn.data("ui-mode");$.post(ajaxurl,{action:"epm_set_ui_mode",ui_mode:mode},function(resp){if(resp.success){$(".epm-ui-mode-btn").removeClass("epm-ui-active");btn.addClass("epm-ui-active");$(".epm-ui-mode-status").text("Saved!").css("color","green");setTimeout(function() { location.reload(); }, 500);}else{$(".epm-ui-mode-status").text("Error: "+resp.data).css("color","red");}});});});</script>';
        }
        // Determine UI mode
        $ui_mode = 'tabs';
        if ($section === 'all') {
            $ui_mode = $db->get_user_preference($display_client_id, 'ui_mode') ?: 'tabs';
        }
        // If section is 'all', show all sections as tabs or twisties
        if ($section === 'all') {
            if ($ui_mode === 'twisties') {
                echo '<div class="epm-client-data-twisties-wrapper">';
                foreach ($sections as $section_key => $section_config) {
                    echo '<div class="epm-twisty-section">';
                    echo '<div class="epm-twisty-header" style="cursor:pointer;background:#f9f9f9;padding:10px;border:1px solid #eee;margin-bottom:2px;">' . esc_html($section_config['title']) . ' <span class="epm-twisty-arrow">&#9654;</span></div>';
                    echo '<div class="epm-twisty-content" style="display:none;padding:10px;border:1px solid #eee;border-top:none;">';
                    $this->render_client_data($section_key, $display_client_id, $readonly);
                    echo '</div>';
                    echo '</div>';
                }
                echo '</div>';
                echo '<script>jQuery(function($){$(".epm-twisty-header").on("click",function(){$(this).next(".epm-twisty-content").slideToggle(200);$(this).find(".epm-twisty-arrow").toggleClass("epm-twisty-open");});});</script>';
                echo '<style>.epm-twisty-arrow{float:right;transition:transform 0.2s;}.epm-twisty-open{transform:rotate(90deg);}</style>';
                return;
            } else {
                echo '<div class="epm-client-data-tabs-wrapper">';
                echo '<ul class="epm-tabs-nav">';
                $first = true;
                foreach ($sections as $section_key => $section_config) {
                    echo '<li class="epm-tab-nav-item' . ($first ? ' epm-tab-active' : '') . '" data-epm-tab="' . esc_attr($section_key) . '">' . esc_html($section_config['title']) . '</li>';
                    $first = false;
                }
                echo '</ul>';
                $first = true;
                foreach ($sections as $section_key => $section_config) {
                    echo '<div class="epm-tab-content' . ($first ? ' epm-tab-content-active' : '') . '" id="epm-tab-' . esc_attr($section_key) . '">';
                    $this->render_client_data($section_key, $display_client_id, $readonly);
                    echo '</div>';
                    $first = false;
                }
                echo '</div>';
                echo '<style>.epm-tabs-nav{display:flex;list-style:none;padding:0;margin:0 0 20px 0;border-bottom:2px solid #eee;}.epm-tab-nav-item{padding:10px 20px;cursor:pointer;border:1px solid #eee;border-bottom:none;background:#f9f9f9;margin-right:5px;border-radius:5px 5px 0 0;}.epm-tab-active{background:#fff;border-bottom:2px solid #fff;font-weight:bold;}.epm-tab-content{display:none;}.epm-tab-content-active{display:block;}</style>';
                echo '<script>jQuery(function($){$(".epm-tab-nav-item").on("click",function(){var tab=$(this).data("epm-tab");$(".epm-tab-nav-item").removeClass("epm-tab-active");$(this).addClass("epm-tab-active");$(".epm-tab-content").removeClass("epm-tab-content-active");$("#epm-tab-"+tab).addClass("epm-tab-content-active");});});</script>';
                return;
            }
        }
        // Modular section rendering for ALL sections
        $view_class = $this->get_section_view_class($section);
        if ($view_class && class_exists($view_class)) {
            $view_class::render($display_client_id, $readonly);
        } else {
            echo '<div class="epm-error">Section view class not found for: ' . esc_html($section) . '</div>';
        }
    }

    /**
     * Helper: Get the view class name for a section and require the file
     */
    private function get_section_view_class($section) {
        $map = array(
            'personal' => 'EPM_PersonalView',
            'banking' => 'EPM_BankingView',
            'investments' => 'EPM_InvestmentsView',
            'insurance' => 'EPM_InsuranceView',
            'real_estate' => 'EPM_RealEstateView',
            'scheduled_payments' => 'EPM_ScheduledPaymentsView',
            'auto_property' => 'EPM_AutoView',
            'personal_property' => 'EPM_PersonalPropertyView',
            'emergency_contacts' => 'EPM_EmergencyContactsView',
        );
        if (isset($map[$section])) {
            $class = $map[$section];
            $file = __DIR__ . '/sections/' . $class . '.php';
            if (file_exists($file)) {
                require_once $file;
                return $class;
            }
        }
        return null;
    }
    
    /**
     * Render a form field
     */
    public function render_form_field($field, $user_id) {
        $value = $this->get_field_value($field['name'], $user_id);
        echo '<div class="epm-form-field">';
        echo '<label for="' . esc_attr($field['name']) . '">' . esc_html($field['label']) . '</label>';
        switch ($field['type']) {
            case 'text':
            case 'email':
            case 'tel':
            case 'date':
                echo '<input type="' . esc_attr($field['type']) . '" ';
                echo 'id="' . esc_attr($field['name']) . '" ';
                echo 'name="' . esc_attr($field['name']) . '" ';
                echo 'value="' . esc_attr($value) . '" ';
                if (isset($field['required']) && $field['required']) {
                    echo 'required ';
                }
                echo '/>';
                break;
            case 'textarea':
                echo '<textarea ';
                echo 'id="' . esc_attr($field['name']) . '" ';
                echo 'name="' . esc_attr($field['name']) . '" ';
                if (isset($field['required']) && $field['required']) {
                    echo 'required ';
                }
                echo '>' . esc_textarea($value) . '</textarea>';
                break;
            case 'select':
                echo '<select ';
                echo 'id="' . esc_attr($field['name']) . '" ';
                echo 'name="' . esc_attr($field['name']) . '" ';
                if (isset($field['required']) && $field['required']) {
                    echo 'required ';
                }
                echo '>';
                echo '<option value="">Select...</option>';
                if (isset($field['options'])) {
                    foreach ($field['options'] as $option_value => $option_label) {
                        echo '<option value="' . esc_attr($option_value) . '"';
                        if ($value == $option_value) {
                            echo ' selected';
                        }
                        echo '>' . esc_html($option_label) . '</option>';
                    }
                }
                echo '</select>';
                break;
        }
        echo '</div>';
    }

    /**
     * Add admin setting for log level
     */
    public static function add_log_level_setting() {
        add_option('epm_log_level', 1); // Default to ERROR
        add_settings_field('epm_log_level', 'EPM Log Level', [self::class, 'render_log_level_field'], 'general');
        register_setting('general', 'epm_log_level', [
            'type' => 'integer',
            'sanitize_callback' => 'absint',
            'default' => 1
        ]);
    }
    public static function render_log_level_field() {
        $level = get_option('epm_log_level', 1);
        echo '<select name="epm_log_level" id="epm_log_level">';
        echo '<option value="1"' . selected($level, 1, false) . '>Error</option>';
        echo '<option value="2"' . selected($level, 2, false) . '>Warning</option>';
        echo '<option value="3"' . selected($level, 3, false) . '>Info (User Changes)</option>';
        echo '<option value="4"' . selected($level, 4, false) . '>Debug (All Queries)</option>';
        echo '</select>';
        echo '<p class="description">Controls what is logged to the plugin log file. Errors are always logged. Info logs user data changes. Debug logs all queries (POST/GET/AJAX).</p>';
    }

    // --- BEGIN: Ensure all required methods exist and are public/protected as needed ---

    /**
     * Get form sections configuration
     * @return array Form sections configuration
     */
    public function get_form_sections() {
        // This should be implemented as before, or moved to a config file if needed.
        // For brevity, you may want to keep the previous implementation here.
        $sections = array(
            'personal' => array('title' => 'Personal Information', 'fields' => array(
                array('name' => 'full_name', 'label' => 'Full Name', 'type' => 'text', 'required' => true),
                array('name' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true),
                array('name' => 'phone', 'label' => 'Phone', 'type' => 'tel'),
                array('name' => 'address', 'label' => 'Address', 'type' => 'text'),
            )),
            'banking' => array('title' => 'Banking Information', 'fields' => array(
                array('name' => 'bank_name', 'label' => 'Bank Name', 'type' => 'text', 'required' => true),
                array('name' => 'account_number', 'label' => 'Account Number', 'type' => 'text', 'required' => true),
                array('name' => 'routing_number', 'label' => 'Routing Number', 'type' => 'text', 'required' => true),
            )),
            'investments' => array('title' => 'Investment Information', 'fields' => array(
                array('name' => 'investment_type', 'label' => 'Type of Investment', 'type' => 'text', 'required' => true),
                array('name' => 'investment_value', 'label' => 'Value of Investment', 'type' => 'text', 'required' => true),
            )),
            'insurance' => array('title' => 'Insurance Information', 'fields' => array(
                array('name' => 'insurance_company', 'label' => 'Insurance Company', 'type' => 'text', 'required' => true),
                array('name' => 'policy_number', 'label' => 'Policy Number', 'type' => 'text', 'required' => true),
                array('name' => 'coverage_amount', 'label' => 'Coverage Amount', 'type' => 'text', 'required' => true),
            )),
            'real_estate' => array('title' => 'Real Estate Information', 'fields' => array(
                array('name' => 'property_address', 'label' => 'Property Address', 'type' => 'text', 'required' => true),
                array('name' => 'property_value', 'label' => 'Property Value', 'type' => 'text', 'required' => true),
            )),
            'scheduled_payments' => array('title' => 'Scheduled Payments', 'fields' => array(
                array('name' => 'payment_amount', 'label' => 'Payment Amount', 'type' => 'text', 'required' => true),
                array('name' => 'payment_date', 'label' => 'Payment Date', 'type' => 'date', 'required' => true),
            )),
            'auto_property' => array('title' => 'Automobile Property', 'fields' => array(
                array('name' => 'vehicle_make', 'label' => 'Vehicle Make', 'type' => 'text', 'required' => true),
                array('name' => 'vehicle_model', 'label' => 'Vehicle Model', 'type' => 'text', 'required' => true),
                array('name' => 'vehicle_year', 'label' => 'Vehicle Year', 'type' => 'text', 'required' => true),
            )),
            'personal_property' => array('title' => 'Personal Property', 'fields' => array(
                array('name' => 'property_description', 'label' => 'Property Description', 'type' => 'text', 'required' => true),
                array('name' => 'property_value', 'label' => 'Property Value', 'type' => 'text', 'required' => true),
            )),
            'emergency_contacts' => array('title' => 'Emergency Contacts', 'fields' => array(
                array('name' => 'contact_name', 'label' => 'Contact Name', 'type' => 'text', 'required' => true),
                array('name' => 'relationship', 'label' => 'Relationship', 'type' => 'text', 'required' => true),
                array('name' => 'contact_phone', 'label' => 'Contact Phone', 'type' => 'tel', 'required' => true),
            )),
        );
        return apply_filters('epm_form_sections', $sections);
    }

    /**
     * Enqueue form scripts (should be public for use in view classes if needed)
     */
    public function enqueue_form_scripts() {
        if (!function_exists('wp_enqueue_script')) return;
        wp_enqueue_script('jquery');
        // Inline JS for modals and form handling
        $inline_js = "
        jQuery(document).ready(function($){
            // Open modals
            $('.epm-open-modal').on('click',function(){
                var target=$(this).data('target');
                $(target).fadeIn(200);
            });
            // Close modals
            $('.epm-modal-cancel, .epm-modal-bg').on('click',function(){
                $(this).closest('.epm-modal').fadeOut(200);
            });
            // Form submissions
            $('#epm-add-person-form, #epm-add-institute-form').on('submit',function(e){
                e.preventDefault();
                var form=$(this);
                var data=form.serialize();
                var url=form.attr('action');
                $.post(url,data,function(resp){
                    if(resp.success){
                        alert('Saved successfully!');
                        location.reload();
                    }else{
                        alert('Error: '+resp.data);
                    }
                });
            });
        });
        ";
        // Inline CSS for modals
        $inline_css = "
        <style>
        .epm-modal { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:10000; }
        .epm-modal > div { background:#fff; padding:20px; border-radius:5px; margin:50px auto; max-width:500px; width:90%; }
        .epm-modal h3 { margin-top:0; }
        .epm-modal label { display:block; margin-bottom:5px; }
        .epm-modal input, .epm-modal select, .epm-modal textarea { width:100%; padding:10px; margin-bottom:10px; border:1px solid #ccc; border-radius:3px; }
        .epm-modal button { padding:10px 15px; border:none; border-radius:3px; cursor:pointer; }
        .epm-modal button.epm-btn-primary { background:#0073aa; color:#fff; }
        .epm-modal button.epm-btn-secondary { background:#f1f1f1; color:#333; }
        </style>
        ";
        echo '<script>' . $inline_js . '</script>';
        echo $inline_css;
    }

    /**
     * Get field value for user
     */
    public function get_field_value($field_name, $user_id, $section = null) {
        $db = EPM_Database::instance();
        $value = $db->get_user_meta($user_id, $field_name);
        // For certain fields, get value from client data if not set in user meta
        if (empty($value) && $section) {
            $client_data = $this->get_client_data($section, $user_id);
            if ($client_data) {
                $value = $client_data->$field_name;
            }
        }
        return $value;
    }

    /**
     * Get client data for section
     */
    public function get_client_data($section, $client_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_clients';
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $client_id));
    }

    /**
     * Check if user can view client data
     */
    public function can_view_client_data($user_id, $client_id) {
        $db = EPM_Database::instance();
        $user_client_id = $db->get_client_id_by_user_id($user_id);
        return ($user_client_id == $client_id);
    }
    // --- END: Ensure all required methods exist and are public/protected as needed ---
}
