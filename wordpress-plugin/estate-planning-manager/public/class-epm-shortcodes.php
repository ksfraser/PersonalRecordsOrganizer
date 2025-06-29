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
        // Modular: delegate to section view class if available
        switch ($section) {
            case 'personal_property':
                require_once __DIR__ . '/sections/EPM_PersonalPropertyView.php';
                EPM_PersonalPropertyView::render_form($current_user->ID);
                return;
            case 'investments':
                require_once __DIR__ . '/sections/EPM_InvestmentsView.php';
                EPM_InvestmentsView::render_form($current_user->ID);
                return;
            // ...add other modularized sections here as needed...
        }
        $section_config = $sections[$section];
        echo '<div class="epm-client-form-wrapper">';
        echo '<h3>' . esc_html($section_config['title']) . '</h3>';
        echo '<form class="epm-client-form" data-section="' . esc_attr($section) . '">';
        wp_nonce_field('epm_save_data', 'epm_nonce');
        foreach ($section_config['fields'] as $field) {
            $this->render_form_field($field, $current_user->ID);
        }
        echo '<div class="epm-form-actions">';
        echo '<button type="submit" class="epm-btn epm-btn-primary">Save Data</button>';
        echo '<button type="button" class="epm-btn epm-btn-secondary epm-generate-pdf">Generate PDF</button>';
        if (in_array($section, array('scheduled_payments', 'insurance', 'banking', 'investments', 'real_estate'))) {
            echo '<button type="button" class="epm-btn epm-btn-secondary epm-add-person-btn">Add Person</button>';
            echo '<button type="button" class="epm-btn epm-btn-secondary epm-add-institute-btn">Add Institute</button>';
        }
        echo '</div>';
        echo '</form>';
        echo '</div>';
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
        // Modular section rendering
        switch ($section) {
            case 'banking':
                require_once __DIR__ . '/sections/EPM_BankingView.php';
                EPM_BankingView::render($display_client_id);
                break;
            case 'investments':
                require_once __DIR__ . '/sections/EPM_InvestmentsView.php';
                EPM_InvestmentsView::render($display_client_id);
                break;
            case 'insurance':
                require_once __DIR__ . '/sections/EPM_InsuranceView.php';
                EPM_InsuranceView::render($display_client_id);
                break;
            case 'real_estate':
                require_once __DIR__ . '/sections/EPM_RealEstateView.php';
                EPM_RealEstateView::render($display_client_id);
                break;
            case 'scheduled_payments':
                require_once __DIR__ . '/sections/EPM_ScheduledPaymentsView.php';
                EPM_ScheduledPaymentsView::render($display_client_id);
                break;
            case 'auto_property':
                require_once __DIR__ . '/sections/EPM_AutoView.php';
                EPM_AutoView::render($display_client_id, $readonly);
                break;
            case 'personal_property':
                require_once __DIR__ . '/sections/EPM_PersonalPropertyView.php';
                EPM_PersonalPropertyView::render($display_client_id, $readonly);
                break;
            case 'emergency_contacts':
                require_once __DIR__ . '/sections/EPM_EmergencyContactsView.php';
                EPM_EmergencyContactsView::render($display_client_id, $readonly);
                break;
            default:
                // Fallback to legacy inline rendering for sections not yet modularized
                $this->render_data_section($section, $sections[$section], $display_client_id, true, true);
                break;
        }
    }
    
    /**
     * Render a single data section (add $readonly param, $mask_passwords param)
     */
    private function render_data_section($section_key, $section_config, $client_id, $readonly = false, $mask_passwords = true) {
        $data = $this->get_client_data($section_key, $client_id);
        echo '<div class="epm-data-section" data-section="' . esc_attr($section_key) . '">';
        echo '<h3>' . esc_html($section_config['title']) . '</h3>';
        // Invite button (only if not readonly)
        if (!$readonly && $client_id == get_current_user_id()) {
            echo '<button type="button" class="epm-btn epm-btn-secondary epm-invite-btn" data-section="' . esc_attr($section_key) . '">Invite Someone to View/Edit</button>';
            echo '<div class="epm-invite-form-wrapper" style="display:none; margin:10px 0;">';
            echo '<form class="epm-invite-form" data-section="' . esc_attr($section_key) . '">';
            echo '<input type="email" name="invite_email" placeholder="Enter email address" required style="width:250px; margin-right:10px;">';
            echo '<select name="permission_level"><option value="view">View</option><option value="edit">Edit</option></select>';
            // Password sharing option for invite
            foreach ($section_config['fields'] as $field) {
                if (stripos($field['name'], 'password') !== false) {
                    echo '<label style="margin-left:10px;"><input type="checkbox" name="show_password[' . esc_attr($field['name']) . ']" value="1"> Show ' . esc_html($field['label']) . ' as plaintext</label>';
                }
            }
            echo '<button type="submit" class="epm-btn epm-btn-primary">Send Invite</button>';
            echo '<span class="epm-invite-status" style="margin-left:10px;"></span>';
            echo '</form>';
            echo '</div>';
        }
        if (empty($data)) {
            echo '<p class="epm-no-data">No data available for this section.</p>';
        } else {
            echo '<div class="epm-data-grid">';
            foreach ($section_config['fields'] as $field) {
                $value = isset($data[$field['name']]) ? $data[$field['name']] : '';
                if (!empty($value)) {
                    echo '<div class="epm-data-item">';
                    echo '<label>' . esc_html($field['label']) . ':</label>';
                    // Mask password fields if needed
                    if (stripos($field['name'], 'password') !== false && $mask_passwords) {
                        echo '<span>********</span>';
                    } else {
                        echo '<span>' . esc_html($value) . '</span>';
                    }
                    echo '</div>';
                }
            }
            echo '</div>';
        }
        echo '</div>';
    }
    
    /**
     * Get form sections configuration
     * @return array Form sections configuration
     */
    public function get_form_sections() {
        $db = EPM_Database::instance();
        return array(
            'personal' => array(
                'title' => 'Personal Information',
                'fields' => array(
                    array('name' => 'first_name', 'label' => 'First Name', 'type' => 'text', 'required' => true),
                    array('name' => 'last_name', 'label' => 'Last Name', 'type' => 'text', 'required' => true),
                    array('name' => 'date_of_birth', 'label' => 'Date of Birth', 'type' => 'date'),
                    array('name' => 'sin', 'label' => 'Social Insurance Number', 'type' => 'text'),
                    array('name' => 'address', 'label' => 'Address', 'type' => 'textarea'),
                    array('name' => 'phone', 'label' => 'Phone', 'type' => 'tel'),
                    array('name' => 'email', 'label' => 'Email', 'type' => 'email'),
                )
            ),
            'banking' => array(
                'title' => 'Banking Information',
                'fields' => array(
                    array('name' => 'bank_name', 'label' => 'Bank Name', 'type' => 'text'),
                    array('name' => 'account_type', 'label' => 'Account Type', 'type' => 'select', 'options' => $db->get_selector_options('epm_account_types')),
                    array('name' => 'account_number', 'label' => 'Account Number', 'type' => 'text'),
                    array('name' => 'branch', 'label' => 'Branch', 'type' => 'text'),
                    array('name' => 'balance', 'label' => 'Current Balance', 'type' => 'text'),
                )
            ),
            'investments' => array(
                'title' => 'Investment Accounts',
                'fields' => array(
                    array('name' => 'investment_type', 'label' => 'Investment Type', 'type' => 'select', 'options' => $db->get_selector_options('epm_investment_types')),
                    array('name' => 'institution', 'label' => 'Financial Institution', 'type' => 'text'),
                    array('name' => 'account_number', 'label' => 'Account Number', 'type' => 'text'),
                    array('name' => 'current_value', 'label' => 'Current Value', 'type' => 'text'),
                    array('name' => 'beneficiary', 'label' => 'Beneficiary', 'type' => 'text'),
                )
            ),
            'insurance' => array(
                'title' => 'Insurance Policies',
                'fields' => array(
                    array('name' => 'policy_type', 'label' => 'Policy Type', 'type' => 'select', 'options' => $db->get_selector_options('epm_insurance_types')),
                    array('name' => 'insurance_company', 'label' => 'Insurance Company', 'type' => 'text'),
                    array('name' => 'policy_number', 'label' => 'Policy Number', 'type' => 'text'),
                    array('name' => 'coverage_amount', 'label' => 'Coverage Amount', 'type' => 'text'),
                    array('name' => 'beneficiary', 'label' => 'Beneficiary', 'type' => 'text'),
                )
            ),
            'real_estate' => array(
                'title' => 'Real Estate',
                'fields' => array(
                    array('name' => 'property_type', 'label' => 'Property Type', 'type' => 'select', 'options' => $db->get_selector_options('epm_property_types')),
                    array('name' => 'property_address', 'label' => 'Property Address', 'type' => 'textarea'),
                    array('name' => 'estimated_value', 'label' => 'Estimated Value', 'type' => 'text'),
                    array('name' => 'mortgage_balance', 'label' => 'Mortgage Balance', 'type' => 'text'),
                    array('name' => 'mortgage_company', 'label' => 'Mortgage Company', 'type' => 'text'),
                )
            ),
            'emergency_contacts' => array(
                'title' => 'Emergency Contacts',
                'fields' => array(
                    array('name' => 'contact_name', 'label' => 'Contact Name', 'type' => 'text'),
                    array('name' => 'relationship', 'label' => 'Relationship', 'type' => 'select', 'options' => $db->get_selector_options('epm_relationship_types')),
                    array('name' => 'phone', 'label' => 'Phone Number', 'type' => 'tel'),
                    array('name' => 'email', 'label' => 'Email', 'type' => 'email'),
                    array('name' => 'address', 'label' => 'Address', 'type' => 'textarea'),
                )
            ),
            'scheduled_payments' => array(
                'title' => 'Scheduled Payments',
                'fields' => array(
                    array('name' => 'payment_type', 'label' => 'Payment Type', 'type' => 'text'),
                    array('name' => 'paid_to_org_id', 'label' => 'Paid To (Institute/Organization)', 'type' => 'select', 'options' => $db->get_org_options($client_id), 'class' => 'epm-institute-select', 'required' => true),
                    array('name' => 'is_automatic', 'label' => 'Is Automatic?', 'type' => 'select', 'options' => array('Yes' => 'Yes', 'No' => 'No')),
                    array('name' => 'amount', 'label' => 'Amount', 'type' => 'text'),
                    array('name' => 'due_date', 'label' => 'Due Date', 'type' => 'date'),
                )
            ),
            'auto_property' => array(
                'title' => 'Autos',
                'fields' => array(
                    array('name' => 'auto_model_id', 'label' => 'Vehicle (Make/Model/Year)', 'type' => 'select', 'options' => AutoModelTable::get_all_options(), 'class' => 'epm-auto-model-select', 'required' => true),
                    array('name' => 'own_or_lease', 'label' => 'Own or Lease', 'type' => 'select', 'options' => array('Own' => 'Own', 'Lease' => 'Lease')),
                    array('name' => 'legal_document', 'label' => 'Legal Document', 'type' => 'textarea'),
                    array('name' => 'registration_location', 'label' => 'Registration Location', 'type' => 'text'),
                    array('name' => 'insurance_policy_location', 'label' => 'Insurance Policy Location', 'type' => 'text'),
                    array('name' => 'bill_of_sale_location', 'label' => 'Bill of Sale Location', 'type' => 'text'),
                    array('name' => 'location', 'label' => 'Physical Location', 'type' => 'text'),
                    array('name' => 'safe_deposit_box_location', 'label' => 'Safe Deposit Box Location', 'type' => 'text'),
                    array('name' => 'box_access_names', 'label' => 'Box Access Names', 'type' => 'textarea'),
                    array('name' => 'keys_location', 'label' => 'Keys Location', 'type' => 'text'),
                    array('name' => 'contents_list_location', 'label' => 'Contents List Location', 'type' => 'text'),
                    array('name' => 'owner_person_id', 'label' => 'Owner', 'type' => 'select', 'options' => PersonTable::get_person_options($client_id), 'class' => 'epm-person-select'),
                )
            )
        );
    }
    
    /**
     * Get field value for user
     */
    private function get_field_value($field_name, $user_id, $section = null) {
        // If section is not provided, try to guess from field name (legacy fallback)
        if (!$section) {
            $sections = $this->get_form_sections();
            foreach ($sections as $section_key => $section_config) {
                foreach ($section_config['fields'] as $field) {
                    if ($field['name'] === $field_name) {
                        $section = $section_key;
                        break 2;
                    }
                }
            }
        }
        if (!$section) return '';
        $db = EPM_Database::instance();
        $client_id = $db->get_client_id_by_user_id($user_id);
        if ($client_id) {
            $records = $db->get_client_data($client_id, $section);
            if (!empty($records)) {
                $record = (array)$records[0];
                if (isset($record[$field_name]) && $record[$field_name] !== '') {
                    return $record[$field_name];
                }
            }
        }
        // If no user/client value, try advisor default
        $advisor_id = null;
        if ($client_id) {
            global $wpdb;
            $advisor_id = $wpdb->get_var($wpdb->prepare("SELECT advisor_id FROM {$wpdb->prefix}epm_clients WHERE id = %d", $client_id));
        }
        if (!$advisor_id) {
            // If user is an advisor, use their own ID
            $user = get_user_by('ID', $user_id);
            if ($user && in_array('financial_advisor', (array)$user->roles)) {
                $advisor_id = $user_id;
            }
        }
        if ($advisor_id) {
            $default = $db->get_advisor_default($advisor_id, $field_name);
            if ($default !== null && $default !== '') {
                return $default;
            }
        }
        return '';
    }

    /**
     * Get client data for section
     */
    private function get_client_data($section, $client_id) {
        $db = EPM_Database::instance();
        $records = $db->get_client_data($client_id, $section);
        if (!empty($records)) {
            return (array)$records[0];
        }
        return array();
    }
    
    /**
     * Check if user can view client data
     */
    private function can_view_client_data($user_id, $client_id) {
        // User can always view their own data
        if ($user_id == $client_id) {
            return true;
        }
        
        // Check if user is a financial advisor
        $user = get_user_by('ID', $user_id);
        if ($user && in_array('financial_advisor', $user->roles)) {
            return true;
        }
        
        // Check if user is an administrator
        $user = get_user_by('ID', $user_id);
        if ($user && in_array('administrator', $user->roles)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Enqueue form scripts
     */
    private function enqueue_form_scripts() {
        wp_enqueue_script('jquery');
        
        // Add inline script for form handling
        $script = "
        jQuery(document).ready(function($) {
            $('.epm-client-form').on('submit', function(e) {
                e.preventDefault();
                
                var formData = $(this).serialize();
                var section = $(this).data('section');
                
                $.ajax({
                    url: '" . admin_url('admin-ajax.php') . "',
                    type: 'POST',
                    data: {
                        action: 'epm_save_client_data',
                        section: section,
                        form_data: formData,
                        nonce: $('[name=\"epm_nonce\"]').val()
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Data saved successfully!');
                        } else {
                            alert('Error saving data: ' + response.data);
                        }
                    },
                    error: function() {
                        alert('Error saving data. Please try again.');
                    }
                });
            });
            
            $('.epm-generate-pdf').on('click', function() {
                var section = $(this).closest('form').data('section');
                window.open('" . admin_url('admin-ajax.php') . "?action=epm_generate_pdf&section=' + section, '_blank');
            });
            
            // Invite form handling
            $('.epm-invite-btn').on('click', function() {
                var section = $(this).data('section');
                $('.epm-invite-form-wrapper[data-section=\"' + section + '\"]').toggle();
            });
            
            $('.epm-invite-form').on('submit', function(e) {
                e.preventDefault();
                
                var form = $(this);
                var formData = form.serialize();
                var section = form.data('section');
                
                $.ajax({
                    url: '" . admin_url('admin-ajax.php') . "',
                    type: 'POST',
                    data: {
                        action: 'epm_send_invite',
                        section: section,
                        form_data: formData,
                        nonce: $('[name=\"epm_nonce\"]').val()
                    },
                    success: function(response) {
                        if (response.success) {
                            form.find('.epm-invite-status').text('Invite sent successfully!').css('color', 'green');
                        } else {
                            form.find('.epm-invite-status').text('Error sending invite: ' + response.data).css('color', 'red');
                        }
                    },
                    error: function() {
                        form.find('.epm-invite-status').text('Error sending invite. Please try again.').css('color', 'red');
                    }
                });
            });
            
            // Share modal open/close
            $('.epm-share-btn').on('click', function() {
                $('.epm-share-modal').fadeIn();
            });
            $('.epm-share-cancel').on('click', function(e) {
                e.preventDefault();
                $('.epm-share-modal').fadeOut();
            });
            // Share form submit
            $('.epm-share-form').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                var formData = form.serialize();
                $.ajax({
                    url: '" . admin_url('admin-ajax.php') . "',
                    type: 'POST',
                    data: {
                        action: 'epm_send_share',
                        form_data: formData,
                        nonce: $('[name=\"epm_nonce\"]').val()
                    },
                    success: function(response) {
                        if (response.success) {
                            form.find('.epm-share-status').text('Shared successfully!').css('color', 'green');
                        } else {
                            form.find('.epm-share-status').text('Error: ' + response.data).css('color', 'red');
                        }
                    },
                    error: function() {
                        form.find('.epm-share-status').text('Error sharing. Please try again.').css('color', 'red');
                    }
                });
            });
        });
        ";
        
        wp_add_inline_script('jquery', $script);
        
        // Enqueue frontend CSS
        wp_enqueue_style('epm-frontend', plugins_url('../assets/css/epm-frontend.css', __FILE__), array(), EPM_VERSION);
    }
    
    /**
     * Helper for tests: render a form field (for unit testing)
     */
    public function test_render_form_field($field, $user_id) {
        $this->render_form_field($field, $user_id);
    }

    /**
     * Create pages for each main shortcode on install/update
     */
    public static function create_pages_on_install() {
        $shortcodes = array(
            'epm_client_form' => array('title' => 'Estate Planning Form', 'content' => '[epm_client_form]'),
            'epm_client_data' => array('title' => 'My Estate Data', 'content' => '[epm_client_data]'),
            'epm_manage_shares' => array('title' => 'Manage Shares', 'content' => '[epm_manage_shares]'),
            'epm_shared_with_you' => array('title' => 'Shared With Me', 'content' => '[epm_shared_with_you]'),
        );
        foreach ($shortcodes as $slug => $info) {
            if (!get_page_by_path($slug)) {
                wp_insert_post(array(
                    'post_title'   => $info['title'],
                    'post_name'    => $slug,
                    'post_content' => $info['content'],
                    'post_status'  => 'publish',
                    'post_type'    => 'page',
                ));
            }
        }
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
}
