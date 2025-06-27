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
    }
    
    /**
     * Client form shortcode
     */
    public function client_form_shortcode($atts) {
        $atts = shortcode_atts(array(
            'section' => 'personal',
        ), $atts);
        
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
        echo '</div>';
        
        echo '</form>';
        echo '</div>';
        
        $this->enqueue_form_scripts();
    }
    
    /**
     * Render client data display
     */
    private function render_client_data($section, $client_id = null) {
        if (!is_user_logged_in()) {
            echo '<div class="epm-error">Please log in to view this data.</div>';
            return;
        }
        
        $current_user = wp_get_current_user();
        $display_client_id = $client_id ? $client_id : $current_user->ID;
        
        // Check permissions
        if (!$this->can_view_client_data($current_user->ID, $display_client_id)) {
            echo '<div class="epm-error">You do not have permission to view this data.</div>';
            return;
        }
        
        $sections = $this->get_form_sections();
        
        echo '<div class="epm-client-data-wrapper">';
        
        if ($section === 'all') {
            foreach ($sections as $section_key => $section_config) {
                $this->render_data_section($section_key, $section_config, $display_client_id);
            }
        } else {
            if (isset($sections[$section])) {
                $this->render_data_section($section, $sections[$section], $display_client_id);
            } else {
                echo '<div class="epm-error">Invalid section specified.</div>';
            }
        }
        
        echo '</div>';
    }
    
    /**
     * Render a single data section
     */
    private function render_data_section($section_key, $section_config, $client_id) {
        $data = $this->get_client_data($section_key, $client_id);
        
        echo '<div class="epm-data-section" data-section="' . esc_attr($section_key) . '">';
        echo '<h3>' . esc_html($section_config['title']) . '</h3>';
        
        if (empty($data)) {
            echo '<p class="epm-no-data">No data available for this section.</p>';
        } else {
            echo '<div class="epm-data-grid">';
            foreach ($section_config['fields'] as $field) {
                $value = isset($data[$field['name']]) ? $data[$field['name']] : '';
                if (!empty($value)) {
                    echo '<div class="epm-data-item">';
                    echo '<label>' . esc_html($field['label']) . ':</label>';
                    echo '<span>' . esc_html($value) . '</span>';
                    echo '</div>';
                }
            }
            echo '</div>';
        }
        
        echo '</div>';
    }
    
    /**
     * Render a form field
     */
    private function render_form_field($field, $user_id) {
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
                    array('name' => 'relationship', 'label' => 'Relationship', 'type' => 'text'),
                    array('name' => 'phone', 'label' => 'Phone Number', 'type' => 'tel'),
                    array('name' => 'email', 'label' => 'Email', 'type' => 'email'),
                    array('name' => 'address', 'label' => 'Address', 'type' => 'textarea'),
                )
            )
        );
    }
    
    /**
     * Get field value for user
     */
    private function get_field_value($field_name, $user_id) {
        // This would typically get data from the database
        // For now, return empty string as placeholder
        return get_user_meta($user_id, 'epm_' . $field_name, true);
    }
    
    /**
     * Get client data for section
     */
    private function get_client_data($section, $client_id) {
        // This would typically get data from the database
        // For now, return sample data structure
        $data = array();
        $sections = $this->get_form_sections();
        
        if (isset($sections[$section])) {
            foreach ($sections[$section]['fields'] as $field) {
                $data[$field['name']] = get_user_meta($client_id, 'epm_' . $field['name'], true);
            }
        }
        
        return array_filter($data); // Remove empty values
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
        });
        ";
        
        wp_add_inline_script('jquery', $script);
        
        // Add basic CSS
        $css = "
        .epm-client-form-wrapper, .epm-client-data-wrapper {
            max-width: 800px;
            margin: 20px 0;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: #fff;
        }
        .epm-form-field {
            margin-bottom: 15px;
        }
        .epm-form-field label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .epm-form-field input,
        .epm-form-field textarea,
        .epm-form-field select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }
        .epm-form-actions {
            margin-top: 20px;
            text-align: right;
        }
        .epm-btn {
            padding: 10px 20px;
            margin-left: 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        .epm-btn-primary {
            background: #0073aa;
            color: white;
        }
        .epm-btn-secondary {
            background: #666;
            color: white;
        }
        .epm-data-section {
            margin-bottom: 30px;
            padding: 15px;
            border: 1px solid #eee;
            border-radius: 3px;
        }
        .epm-data-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }
        .epm-data-item label {
            font-weight: bold;
            color: #666;
        }
        .epm-data-item span {
            display: block;
            margin-top: 5px;
        }
        .epm-error {
            color: #d63638;
            background: #fcf0f1;
            border: 1px solid #d63638;
            padding: 10px;
            border-radius: 3px;
        }
        .epm-no-data {
            color: #666;
            font-style: italic;
        }
        ";
        
        wp_add_inline_style('wp-admin', $css);
    }
}
