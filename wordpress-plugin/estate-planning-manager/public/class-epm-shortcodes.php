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

use EstatePlanningManager\Sections\AbstractSectionView;

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
        // add_shortcode('epm_client_form', array($this, 'client_form_shortcode'));
        // add_shortcode('epm_client_data', array($this, 'client_data_shortcode'));
        add_shortcode('epm_manage_shares', array($this, 'manage_shares_shortcode'));
        add_shortcode('epm_shared_with_you', array($this, 'shared_with_you_shortcode'));
        add_shortcode('estate_planning_manager', array($this, 'estate_planning_manager_shortcode'));
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
        return ob_get_clean();
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
        $db = EPM_Database::instance();
        $client_id = $atts['client_id'] ? $atts['client_id'] : $db->get_client_id_by_user_id(get_current_user_id());
        $this->render_client_data($atts['section'], $client_id);
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
     * Main Estate Planning Manager Shortcode
     */
    public function estate_planning_manager_shortcode($atts) {
        ob_start();
        self::render_main_ui(get_current_user_id());
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
            require_once __DIR__ . '/modals/EPM_AddPersonModal.php';
            require_once __DIR__ . '/modals/EPM_AddInstituteModal.php';
            echo EPM_AddPersonModal::render();
            echo EPM_AddInstituteModal::render();
        }
        $this->enqueue_form_scripts();
        // (Log level indicator and View Log link moved to admin UI)
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
        $db = EPM_Database::instance();
        $display_client_id = $client_id ? $client_id : $db->get_client_id_by_user_id($current_user->ID);
        // Log resolved client_id
        $epm_log_file = dirname(__DIR__, 2) . '/logs/epm.log';
        file_put_contents($epm_log_file, "EPM DEBUG: render_client_data: section=$section, user_id=" . $current_user->ID . ", resolved_client_id=" . var_export($display_client_id, true) . "\n", FILE_APPEND);
        $is_owner = ($display_client_id == $db->get_client_id_by_user_id($current_user->ID));
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
            // Use new MVC/PSR approach if available
            $model_class = null;
            switch ($section) {
                case 'real_estate':
                    $model_class = '\EstatePlanningManager\Models\RealEstateModel';
                    break;
                case 'banking':
                    $model_class = '\EstatePlanningManager\Models\BankingModel';
                    break;
                case 'investments':
                    $model_class = '\EstatePlanningManager\Models\InvestmentsModel';
                    break;
                case 'insurance':
                    $model_class = '\EstatePlanningManager\Models\InsuranceModel';
                    break;
                case 'scheduled_payments':
                    $model_class = '\EstatePlanningManager\Models\ScheduledPaymentsModel';
                    break;
                // Add other sections as needed
            }
            if ($model_class && class_exists($model_class) && method_exists($view_class, 'render_view')) {
                $records = $model_class::getByClientId($display_client_id);
                // Inject EPM_Shortcodes instance if needed
                if (method_exists($view_class, 'setShortcodes')) {
                    $view_class::setShortcodes($this);
                }
                $view_class::render_view($records);
            } else {
                $view_class::render($display_client_id, $readonly);
            }
        } else {
            echo '<div class="epm-error">Section view class not found for: ' . esc_html($section) . '</div>';
        }
    }

    /**
     * Helper: Get the view class name for a section and require the file
     */
    private function get_section_view_class($section) {
        $map = array(
            'personal' => 'EstatePlanningManager\\Sections\\EPM_PersonalView',
            'banking' => 'EstatePlanningManager\\Sections\\EPM_BankingView',
            'investments' => 'EstatePlanningManager\\Sections\\EPM_InvestmentsView',
            'insurance' => 'EstatePlanningManager\\Sections\\EPM_InsuranceView',
            'real_estate' => 'EstatePlanningManager\\Sections\\EPM_RealEstateView',
            'scheduled_payments' => 'EstatePlanningManager\\Sections\\EPM_ScheduledPaymentsView',
            'auto_property' => 'EstatePlanningManager\\Sections\\EPM_AutoView',
            'personal_property' => 'EstatePlanningManager\\Sections\\EPM_PersonalPropertyView',
            'emergency_contacts' => 'EstatePlanningManager\\Sections\\EPM_EmergencyContactsView',
            'family_contacts' => 'EstatePlanningManager\\Sections\\EPM_FamilyContactsView',
            'volunteering' => 'EstatePlanningManager\\Sections\\EPM_VolunteeringView',
            'password_storage' => 'EstatePlanningManager\\Sections\\EPM_PasswordManagementView',
            'digital_assets' => 'EstatePlanningManager\\Sections\\EPM_DigitalAssetsView',
            'social_media_accounts' => 'EstatePlanningManager\\Sections\\EPM_SocialMediaAccountsView',
            'email_accounts' => 'EstatePlanningManager\\Sections\\EPM_EmailAccountsView',
            'hosting_services' => 'EstatePlanningManager\\Sections\\EPM_HostingServicesView',
            'key_contacts' => 'EstatePlanningManager\\Sections\\EPM_KeyContactsView',
            'debtors' => 'EstatePlanningManager\\Sections\\EPM_DebtorsView',
            'creditors' => 'EstatePlanningManager\\Sections\\EPM_CreditorsView',
            'charitable_gifts' => 'EstatePlanningManager\\Sections\\EPM_CharitableGiftsView',
            'other_contracts' => 'EstatePlanningManager\\Sections\\EPM_OtherContractualObligationsView',
        );
        if (isset($map[$section])) {
            $class = $map[$section];
            $file = __DIR__ . '/sections/' . substr($class, strrpos($class, '\\') + 1) . '.php';
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
                        if ($value == $option_value || (empty($value) && isset($field['default']) && $field['default'] == $option_value)) {
                            echo ' selected';
                        }
                        echo '>' . esc_html($option_label) . '</option>';
                    }
                }
                echo '</select>';
                // Add New Advisor button for advisor_person_id
                if ($field['name'] === 'advisor_person_id') {
                    require_once __DIR__ . '/modals/EPM_AddAdvisorModal.php';
                    echo ' <button type="button" class="epm-btn epm-btn-secondary" id="epm-add-advisor-btn" style="margin-left:8px;">Add New Advisor</button>';
                    echo EPM_AddAdvisorModal::render();
                    // JS to open/close modal and add advisor (stub: just closes modal)
                    echo '<script>jQuery(function($){
                        $("#epm-add-advisor-btn").on("click",function(){
                            $("#epm-add-advisor-modal").fadeIn(200);
                        });
                        $("#epm-add-advisor-modal .epm-modal-cancel").on("click",function(){
                            $("#epm-add-advisor-modal").fadeOut(200);
                        });
                        $("#epm-add-advisor-form").on("submit",function(e){
                            e.preventDefault();
                            // In production, AJAX to server to add advisor and refresh dropdown
                            alert("Advisor added (stub, refresh page to see in dropdown)");
                            $("#epm-add-advisor-modal").fadeOut(200);
                        });
                    });</script>';
                }
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

    /**
     * Create required pages on plugin activation
     */
    public static function create_pages_on_install() {
        $pages = [
            [
                'title' => 'Estate Planning Manager',
                'slug' => 'estate-planning-manager',
                'shortcode' => '[epm_client_form]'
            ],
            [
                'title' => 'My Estate Data',
                'slug' => 'my-estate-data',
                'shortcode' => '[epm_client_data]'
            ],
            [
                'title' => 'Manage Shares',
                'slug' => 'manage-shares',
                'shortcode' => '[epm_manage_shares]'
            ],
            [
                'title' => 'Shared With You',
                'slug' => 'shared-with-you',
                'shortcode' => '[epm_shared_with_you]'
            ],
        ];
        foreach ($pages as $page) {
            if (!get_page_by_path($page['slug'])) {
                wp_insert_post([
                    'post_title'   => $page['title'],
                    'post_name'    => $page['slug'],
                    'post_content' => $page['shortcode'],
                    'post_status'  => 'publish',
                    'post_type'    => 'page',
                ]);
            }
        }
    }

    /**
     * Plugin activation: create required tables
     */
    public static function activate() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        // Create section shares table
        $table_name = $wpdb->prefix . 'epm_section_shares';
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            owner_id BIGINT UNSIGNED NOT NULL,
            shared_with_id BIGINT UNSIGNED NOT NULL,
            section_key VARCHAR(64) NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            KEY idx_owner (owner_id),
            KEY idx_shared_with (shared_with_id),
            KEY idx_section (section_key)
        ) ENGINE=InnoDB $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        // Create all section tables (add missing ones)
        $modelTableMap = [
            'PersonalModel',
            'BankingModel',
            'InvestmentsModel',
            'InsuranceModel',
            'RealEstateModel',
            'ScheduledPaymentsModel',
            'AutoModel',
            'PersonalPropertyModel',
            'EmergencyContactsModel',
            'FamilyContactsModel',
            'VolunteeringModel',
            'PasswordManagementModel',
            'DigitalAssetsModel',
            'SocialMediaAccountsModel',
            'EmailAccountsModel',
            'HostingServicesModel',
            'KeyContactsModel',
            'DebtorsModel',
            'CreditorsModel',
            'CharitableGiftsModel',
            'OtherContractualObligationsModel',
            'SafetyDepositBoxModel',
            'OnlineAccountsModel',
            'EmploymentRecordsModel',
        ];
        foreach ($modelTableMap as $model) {
            $file = __DIR__ . "/models/{$model}.php";
            if (file_exists($file)) {
                require_once $file;
                $fqcn = "\\EstatePlanningManager\\Models\\$model";
                if (method_exists($fqcn, 'createTable')) {
                    $fqcn::createTable($charset_collate);
                }
            }
        }
    }

    // --- BEGIN: Ensure all required methods exist and are public/protected as needed ---

    /**
     * Get form sections configuration
     * @return array Form sections configuration
     */
    public function get_form_sections() {
        // Require all model files so static methods are available
        require_once __DIR__ . '/models/PersonalModel.php';
        require_once __DIR__ . '/models/BankingModel.php';
        require_once __DIR__ . '/models/InvestmentsModel.php';
        require_once __DIR__ . '/models/InsuranceModel.php';
        require_once __DIR__ . '/models/RealEstateModel.php';
        require_once __DIR__ . '/models/ScheduledPaymentsModel.php';
        require_once __DIR__ . '/models/AutoModel.php';
        require_once __DIR__ . '/models/PersonalPropertyModel.php';
        require_once __DIR__ . '/models/EmergencyContactsModel.php';
        require_once __DIR__ . '/models/FamilyContactsModel.php';
        require_once __DIR__ . '/models/VolunteeringModel.php';
        require_once __DIR__ . '/models/PasswordManagementModel.php';
        require_once __DIR__ . '/models/DigitalAssetsModel.php';
        require_once __DIR__ . '/models/SocialMediaAccountsModel.php';
        require_once __DIR__ . '/models/EmailAccountsModel.php';
        require_once __DIR__ . '/models/HostingServicesModel.php';
        require_once __DIR__ . '/models/KeyContactsModel.php';
        require_once __DIR__ . '/models/DebtorsModel.php';
        require_once __DIR__ . '/models/CreditorsModel.php';
        require_once __DIR__ . '/models/CharitableGiftsModel.php';
        require_once __DIR__ . '/models/OtherContractualObligationsModel.php';
        require_once __DIR__ . '/models/SafetyDepositBoxModel.php';
        require_once __DIR__ . '/models/OnlineAccountsModel.php';
        require_once __DIR__ . '/models/EmploymentRecordsModel.php';

        $sections = array(
            // Personal & family
            'personal' => array(
                'title' => 'Personal Information',
                'fields' => $this->normalize_fields(\EstatePlanningManager\Models\PersonalModel::getFieldDefinitions())
            ),
            'emergency_contacts' => array(
                'title' => 'Emergency Contacts',
                'fields' => $this->normalize_fields(\EstatePlanningManager\Models\EmergencyContactsModel::getFieldDefinitions())
            ),
            'key_contacts' => array(
                'title' => 'Key Contacts',
                'fields' => $this->normalize_fields(\EstatePlanningManager\Models\KeyContactsModel::getFieldDefinitions())
            ),
            'family_contacts' => array(
                'title' => 'Family Contacts',
                'fields' => $this->normalize_fields(\EstatePlanningManager\Models\FamilyContactsModel::getFieldDefinitions())
            ),
            'volunteering' => array(
                'title' => 'Volunteering',
                'fields' => $this->normalize_fields(\EstatePlanningManager\Models\VolunteeringModel::getFieldDefinitions())
            ),
            'password_storage' => array(
                'title' => 'Password Storage',
                'fields' => $this->normalize_fields(\EstatePlanningManager\Models\PasswordManagementModel::getFieldDefinitions())
            ),
            'digital_assets' => array(
                'title' => 'Digital Assets',
                'fields' => $this->normalize_fields(\EstatePlanningManager\Models\DigitalAssetsModel::getFieldDefinitions())
            ),
            'social_media_accounts' => array(
                'title' => 'Social Media Accounts',
                'fields' => $this->normalize_fields(\EstatePlanningManager\Models\SocialMediaAccountsModel::getFieldDefinitions())
            ),
            'email_accounts' => array(
                'title' => 'Email Accounts',
                'fields' => $this->normalize_fields(\EstatePlanningManager\Models\EmailAccountsModel::getFieldDefinitions())
            ),
            'hosting_services' => array(
                'title' => 'Hosting Services',
                'fields' => $this->normalize_fields(\EstatePlanningManager\Models\HostingServicesModel::getFieldDefinitions())
            ),
            'online_accounts' => array(
                'title' => 'Online Accounts',
                'fields' => $this->normalize_fields(\EstatePlanningManager\Models\OnlineAccountsModel::getFieldDefinitions())
            ),
            // Assets
            'safety_deposit_box' => array(
                'title' => 'Safety Deposit Box',
                'fields' => $this->normalize_fields(\EstatePlanningManager\Models\SafetyDepositBoxModel::getFieldDefinitions())
            ),
            'debtors' => array(
                'title' => 'Debtors',
                'fields' => $this->normalize_fields(\EstatePlanningManager\Models\DebtorsModel::getFieldDefinitions())
            ),
            'real_estate' => array(
                'title' => 'Real Estate Information',
                'fields' => $this->normalize_fields(\EstatePlanningManager\Models\RealEstateModel::getFieldDefinitions())
            ),
            'banking' => array(
                'title' => 'Banking Information',
                'fields' => $this->normalize_fields(\EstatePlanningManager\Models\BankingModel::getFieldDefinitions())
            ),
            'investments' => array(
                'title' => 'Investment Information',
                'fields' => $this->normalize_fields(\EstatePlanningManager\Models\InvestmentsModel::getFieldDefinitions())
            ),
            'insurance' => array(
                'title' => 'Insurance Information',
                'fields' => $this->normalize_fields(\EstatePlanningManager\Models\InsuranceModel::getFieldDefinitions())
            ),
            'auto_property' => array(
                'title' => 'Automobile Property',
                'fields' => $this->normalize_fields(\EstatePlanningManager\Models\AutoModel::getFieldDefinitions())
            ),
            'personal_property' => array(
                'title' => 'Personal Property',
                'fields' => $this->normalize_fields(\EstatePlanningManager\Models\PersonalPropertyModel::getFieldDefinitions())
            ),
            // Liabilities
            'creditors' => array(
                'title' => 'Creditors (Loans, Credit Cards, etc)',
                'fields' => $this->normalize_fields(\EstatePlanningManager\Models\CreditorsModel::getFieldDefinitions())
            ),
            'charitable_gifts' => array(
                'title' => 'Charitable Gifts',
                'fields' => $this->normalize_fields(\EstatePlanningManager\Models\CharitableGiftsModel::getFieldDefinitions())
            ),
            'other_contracts' => array(
                'title' => 'Other Contractual Obligations',
                'fields' => $this->normalize_fields(\EstatePlanningManager\Models\OtherContractualObligationsModel::getFieldDefinitions())
            ),
            // Income
            'employment' => array(
                'title' => 'Employment',
                'fields' => $this->normalize_fields(\EstatePlanningManager\Models\EmploymentRecordsModel::getFieldDefinitions())
            ),
            // Expenses
            'scheduled_payments' => array(
                'title' => 'Scheduled Payments',
                'fields' => $this->normalize_fields(\EstatePlanningManager\Models\ScheduledPaymentsModel::getFieldDefinitions())
            ),
        );
        return apply_filters('epm_form_sections', $sections);
    }

    private static $normalize_field_warnings = [];
    /**
     * Normalize field definitions to ensure each field is an array with a 'name' key
     * @param array $fields
     * @return array
     */
    private function normalize_fields($fields) {
        $out = [];
        foreach ($fields as $name => $def) {
            if (!is_string($name) || $name === '' || !is_array($def)) {
                // Admin warning if field skipped
                if (is_admin() && current_user_can('manage_options')) {
                    $msg = "EPM Warning: Skipped invalid field definition in section. Field key: '" . print_r($name, true) . "'";
                    if (!in_array($msg, self::$normalize_field_warnings)) {
                        self::$normalize_field_warnings[] = $msg;
                        add_action('admin_notices', function() use ($msg) {
                            echo '<div class="notice notice-warning"><p>' . esc_html($msg) . '</p></div>';
                        });
                    }
                }
                continue;
            }
            $out[] = array_merge(['name' => $name], $def);
        }
        return $out;
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
            if ($client_data && isset($client_data->$field_name)) {
                $value = $client_data->$field_name;
            }
        }
        // Special: for owner_person_id/advisor_person_id, return name if possible
        if (in_array($field_name, ['owner_person_id', 'advisor_person_id']) && !empty($value)) {
            if (class_exists('EstatePlanningManager\\Models\\PeopleModel')) {
                foreach (\EstatePlanningManager\Models\PeopleModel::getAllForDropdown() as $person) {
                    if ($person['id'] == $value) return $person['full_name'];
                }
            }
        }
        return $value;
    }

    /**
     * Get client data for section
     */
    public function get_client_data($section, $client_id) {
        global $wpdb;
        // Use centralized model map class
        require_once __DIR__ . '/model-map.php';
        $model_map = \EstatePlanningManager\ModelMap::getSectionModelMap();
        if (isset($model_map[$section]) && class_exists($model_map[$section])) {
            $model = new $model_map[$section]();
            $records = $model->getAllRecordsForClient($client_id);
            return !empty($records) ? (object)$records[0] : null;
        } else {
            // Fallback to clients table for unknown section
            $table = $wpdb->prefix . 'epm_clients';
            return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $client_id));
        }
    }

    /**
     * Check if user can view client data
     */
    public function can_view_client_data($user_id, $client_id) {
        $db = EPM_Database::instance();
        $user_client_id = $db->get_client_id_by_user_id($user_id);
        return ($user_client_id == $client_id);
    }

    /**
     * Render main UI selector for 'My Data' vs 'Shared With Me'
     */
    public static function render_main_ui($user_id) {
        $view_mode = isset($_GET['epm_view_mode']) && $_GET['epm_view_mode'] === 'shared' ? 'shared' : 'own';
        echo '<div class="epm-view-mode-selector">';
        echo '<form method="get">';
        echo '<label for="epm_view_mode">View: </label>';
        echo '<select name="epm_view_mode" id="epm_view_mode" onchange="this.form.submit()">';
        echo '<option value="own"' . ($view_mode === 'own' ? ' selected' : '') . '>My Data</option>';
        echo '<option value="shared"' . ($view_mode === 'shared' ? ' selected' : '') . '>Shared With Me</option>';
        echo '</select>';
        // Preserve other query params
        foreach ($_GET as $k => $v) {
            if ($k !== 'epm_view_mode' && $k !== 'epm_shared_user') {
                echo '<input type="hidden" name="' . esc_attr($k) . '" value="' . esc_attr($v) . '">';
            }
        }
        if ($view_mode === 'shared') {
            $shared_users = self::get_users_who_shared_with($user_id);
            echo '<label for="epm_shared_user"> Select User: </label>';
            echo '<select name="epm_shared_user" id="epm_shared_user" onchange="this.form.submit()">';
            echo '<option value="">-- Select --</option>';
            foreach ($shared_users as $shared_user) {
                $selected = (isset($_GET['epm_shared_user']) && $_GET['epm_shared_user'] == $shared_user['ID']) ? ' selected' : '';
                echo '<option value="' . esc_attr($shared_user['ID']) . '"' . $selected . '>' . esc_html($shared_user['display_name']) . '</option>';
            }
            echo '</select>';
        }
        echo '</form>';
        echo '</div>';
        if ($view_mode === 'own') {
            self::render_sections_for_user($user_id);
        } else {
            $shared_user_id = isset($_GET['epm_shared_user']) ? intval($_GET['epm_shared_user']) : 0;
            if ($shared_user_id) {
                self::render_sections_shared_with_user($shared_user_id, $user_id);
            } else {
                echo '<div class="epm-shared-select-notice">Please select a user to view shared data.</div>';
            }
        }
    }
    public static function get_users_who_shared_with($user_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_section_shares';
        $results = $wpdb->get_results($wpdb->prepare("SELECT DISTINCT owner_id FROM $table WHERE shared_with_id = %d", $user_id));
        if (!$results) return [];
        $user_ids = array_map(function($row) { return $row->owner_id; }, $results);
        if (empty($user_ids)) return [];
        $placeholders = implode(',', array_fill(0, count($user_ids), '%d'));
        $users = $wpdb->get_results($wpdb->prepare(
            "SELECT ID, display_name FROM {$wpdb->users} WHERE ID IN ($placeholders)",
            ...$user_ids
        ), ARRAY_A);
        return $users ? $users : [];
    }
    public static function get_sections_shared_with_user($owner_id, $viewer_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_section_shares';
        $results = $wpdb->get_results($wpdb->prepare("SELECT section_key FROM $table WHERE owner_id = %d AND shared_with_id = %d", $owner_id, $viewer_id));
        if (!$results) return [];
        return array_map(function($row) { return $row->section_key; }, $results);
    }

    /**
     * Render all sections for the given user (owner view)
     */
    public static function render_sections_for_user($user_id) {
        $instance = self::instance();
        $db = EPM_Database::instance();
        $client_id = $db->get_client_id_by_user_id($user_id);
        $sections = $instance->get_form_sections();
        foreach (array_keys($sections) as $section_key) {
            $view_class = $instance->get_section_view_class($section_key);
            if ($view_class && class_exists($view_class)) {
                $view_class::render($client_id, false);
            }
        }
    }
    // --- END: Ensure all required methods exist and are public/protected as needed ---
}
