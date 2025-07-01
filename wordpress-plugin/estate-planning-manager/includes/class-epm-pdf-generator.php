<?php
/**
 * PDF Generator Class
 * 
 * Handles PDF generation for Estate Planning Manager
 * Follows Single Responsibility Principle - only handles PDF generation
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class EPM_PDF_Generator {
    
    /**
     * Instance of this class
     * @var EPM_PDF_Generator
     */
    private static $instance = null;
    
    /**
     * Get instance (Singleton pattern)
     * @return EPM_PDF_Generator
     */
    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Initialize PDF generator
     */
    public function init() {
        add_action('wp_ajax_epm_generate_pdf', array($this, 'handle_pdf_generation'));
        add_action('wp_ajax_nopriv_epm_generate_pdf', array($this, 'handle_pdf_generation'));
    }
    
    /**
     * Handle AJAX PDF generation request
     */
    public function handle_pdf_generation() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'epm_generate_pdf')) {
            wp_die(__('Security check failed.', 'estate-planning-manager'));
        }
        
        $client_id = intval($_POST['client_id']);
        $sections = isset($_POST['sections']) ? $_POST['sections'] : array();
        $template = sanitize_text_field($_POST['template']);
        
        // Check permissions
        $current_user_id = get_current_user_id();
        if (!EPM_Security::instance()->can_user_access_client_data($current_user_id, $client_id)) {
            wp_die(__('Access denied.', 'estate-planning-manager'));
        }
        
        // Generate PDF
        $pdf_result = $this->generate_client_pdf($client_id, $sections, $template);
        
        if (is_wp_error($pdf_result)) {
            wp_send_json_error($pdf_result->get_error_message());
        } else {
            wp_send_json_success(array(
                'download_url' => $pdf_result['url'],
                'filename' => $pdf_result['filename']
            ));
        }
    }
    
    /**
     * Generate PDF for client data
     */
    public function generate_client_pdf($client_id, $sections = array(), $template = 'complete_estate_plan') {
        // Get client data
        $client_data = $this->get_client_data_for_pdf($client_id, $sections);
        
        if (empty($client_data)) {
            return new WP_Error('no_data', __('No data available for PDF generation.', 'estate-planning-manager'));
        }
        
        // Load PDF library (using TCPDF as example)
        if (!class_exists('TCPDF')) {
            require_once EPM_PLUGIN_DIR . 'includes/tcpdf/tcpdf.php';
        }
        
        // Create PDF
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // Set document information
        $pdf->SetCreator('Estate Planning Manager');
        $pdf->SetAuthor('Estate Planning Manager');
        $pdf->SetTitle('Estate Planning Document');
        $pdf->SetSubject('Estate Planning Information');
        
        // Set margins
        $pdf->SetMargins(15, 20, 15);
        $pdf->SetHeaderMargin(10);
        $pdf->SetFooterMargin(10);
        
        // Set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, 25);
        
        // Set font
        $pdf->SetFont('helvetica', '', 10);
        
        // Generate content based on template
        switch ($template) {
            case 'complete_estate_plan':
                $this->generate_complete_estate_plan($pdf, $client_data);
                break;
            case 'financial_summary':
                $this->generate_financial_summary($pdf, $client_data);
                break;
            case 'emergency_contacts':
                $this->generate_emergency_contacts($pdf, $client_data);
                break;
            case 'legal_documents':
                $this->generate_legal_documents($pdf, $client_data);
                break;
            default:
                $this->generate_complete_estate_plan($pdf, $client_data);
                break;
        }
        
        // Generate filename
        $filename = $this->generate_filename($client_id, $template);
        
        // Save PDF to uploads directory
        $upload_dir = wp_upload_dir();
        $pdf_dir = $upload_dir['basedir'] . '/estate-planning-manager-pdfs/';
        
        if (!file_exists($pdf_dir)) {
            wp_mkdir_p($pdf_dir);
        }
        
        $file_path = $pdf_dir . $filename;
        $pdf->Output($file_path, 'F');
        
        // Log PDF generation
        EPM_Audit_Logger::instance()->log_action(
            get_current_user_id(),
            'pdf_generated',
            'pdf_generation',
            $client_id,
            array(
                'template' => $template,
                'sections' => $sections,
                'filename' => $filename
            )
        );
        
        return array(
            'url' => $upload_dir['baseurl'] . '/estate-planning-manager-pdfs/' . $filename,
            'path' => $file_path,
            'filename' => $filename
        );
    }
    
    /**
     * Generate complete estate plan PDF
     */
    private function generate_complete_estate_plan($pdf, $client_data) {
        // Cover page
        $pdf->AddPage();
        $pdf->SetFont('helvetica', 'B', 20);
        $pdf->Cell(0, 20, 'Estate Planning Document', 0, 1, 'C');
        
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Ln(10);
        $pdf->Cell(0, 10, 'Prepared for: ' . ($client_data['basic_personal']['full_legal_name'] ?? 'Client'), 0, 1, 'C');
        $pdf->Cell(0, 10, 'Generated on: ' . date('F j, Y'), 0, 1, 'C');
        
        // Table of contents
        $pdf->AddPage();
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'Table of Contents', 0, 1, 'L');
        $pdf->Ln(5);
        
        $pdf->SetFont('helvetica', '', 11);
        $sections = array(
            'Personal Information' => 3,
            'Family & Contacts' => 4,
            'Legal Documents' => 5,
            'Financial Assets' => 6,
            'Real Estate' => 7,
            'Insurance' => 8,
            'Digital Assets' => 9
        );
        
        foreach ($sections as $section => $page) {
            $pdf->Cell(0, 8, $section . str_repeat('.', 50 - strlen($section)) . $page, 0, 1, 'L');
        }
        
        // Generate each section
        foreach ($client_data as $section => $data) {
            if (!empty($data)) {
                $this->add_section_to_pdf($pdf, $section, $data);
            }
        }
    }
    
    /**
     * Generate financial summary PDF
     */
    private function generate_financial_summary($pdf, $client_data) {
        $pdf->AddPage();
        $pdf->SetFont('helvetica', 'B', 18);
        $pdf->Cell(0, 15, 'Financial Summary', 0, 1, 'C');
        
        // Bank accounts
        if (!empty($client_data['bank_accounts'])) {
            $this->add_section_to_pdf($pdf, 'bank_accounts', $client_data['bank_accounts']);
        }
        
        // Investments
        if (!empty($client_data['investments'])) {
            $this->add_section_to_pdf($pdf, 'investments', $client_data['investments']);
        }
        
        // Real estate
        if (!empty($client_data['real_estate'])) {
            $this->add_section_to_pdf($pdf, 'real_estate', $client_data['real_estate']);
        }
        
        // Insurance
        if (!empty($client_data['insurance'])) {
            $this->add_section_to_pdf($pdf, 'insurance', $client_data['insurance']);
        }
    }
    
    /**
     * Generate emergency contacts PDF
     */
    private function generate_emergency_contacts($pdf, $client_data) {
        $pdf->AddPage();
        $pdf->SetFont('helvetica', 'B', 18);
        $pdf->Cell(0, 15, 'Emergency Contacts', 0, 1, 'C');
        
        // Family contacts
        if (!empty($client_data['family_contacts'])) {
            $this->add_section_to_pdf($pdf, 'family_contacts', $client_data['family_contacts']);
        }
        
        // Key contacts
        if (!empty($client_data['key_contacts'])) {
            $this->add_section_to_pdf($pdf, 'key_contacts', $client_data['key_contacts']);
        }
    }
    
    /**
     * Generate legal documents PDF
     */
    private function generate_legal_documents($pdf, $client_data) {
        $pdf->AddPage();
        $pdf->SetFont('helvetica', 'B', 18);
        $pdf->Cell(0, 15, 'Legal Documents Summary', 0, 1, 'C');
        
        // Wills and POA
        if (!empty($client_data['wills_poa'])) {
            $this->add_section_to_pdf($pdf, 'wills_poa', $client_data['wills_poa']);
        }
        
        // Funeral arrangements
        if (!empty($client_data['funeral_organ'])) {
            $this->add_section_to_pdf($pdf, 'funeral_organ', $client_data['funeral_organ']);
        }
    }
    
    /**
     * Add section to PDF
     */
    private function add_section_to_pdf($pdf, $section, $data) {
        $pdf->AddPage();
        
        // Section title
        $pdf->SetFont('helvetica', 'B', 16);
        $section_title = ucwords(str_replace('_', ' ', $section));
        $pdf->Cell(0, 12, $section_title, 0, 1, 'L');
        $pdf->Ln(5);
        
        $pdf->SetFont('helvetica', '', 10);
        
        // Handle different data structures
        if (is_array($data) && isset($data[0])) {
            // Multiple records
            foreach ($data as $record) {
                $this->add_record_to_pdf($pdf, $record);
                $pdf->Ln(5);
            }
        } else {
            // Single record
            $this->add_record_to_pdf($pdf, $data);
        }
    }
    
    /**
     * Add record to PDF
     */
    private function add_record_to_pdf($pdf, $record) {
        foreach ($record as $key => $value) {
            if (!empty($value) && !in_array($key, array('id', 'client_id', 'created_at', 'updated_at'))) {
                $label = ucwords(str_replace('_', ' ', $key));
                
                // Handle sensitive data
                if (in_array($key, array('account_number', 'policy_number', 'sin'))) {
                    $value = $this->mask_sensitive_data($value);
                }
                
                $pdf->Cell(50, 6, $label . ':', 0, 0, 'L');
                $pdf->Cell(0, 6, $value, 0, 1, 'L');
            }
        }
    }
    
    /**
     * Mask sensitive data
     */
    private function mask_sensitive_data($value) {
        if (strlen($value) <= 4) {
            return str_repeat('*', strlen($value));
        }
        
        return str_repeat('*', strlen($value) - 4) . substr($value, -4);
    }
    
    /**
     * Get client data for PDF
     */
    private function get_client_data_for_pdf($client_id, $sections = array()) {
        $data = array();
        
        // If no specific sections requested, get all
        if (empty($sections)) {
            $sections = array(
                'basic_personal', 'family_contacts', 'key_contacts', 'wills_poa',
                'funeral_organ', 'taxes', 'military_service', 'employment',
                'volunteer', 'bank_accounts', 'investments', 'real_estate',
                'personal_property', 'digital_assets', 'scheduled_payments',
                'debtors_creditors', 'insurance'
            );
        }
        
        foreach ($sections as $section) {
            $section_data = EPM_Database::instance()->get_client_data($client_id, $section);
            if (!empty($section_data)) {
                $data[$section] = $section_data;
            }
        }
        
        return $data;
    }
    
    /**
     * Generate filename
     */
    private function generate_filename($client_id, $template) {
        $timestamp = date('Y-m-d_H-i-s');
        return "estate_plan_{$client_id}_{$template}_{$timestamp}.pdf";
    }
    
    /**
     * Clean old PDF files
     */
    public function clean_old_pdfs($days = 30) {
        $upload_dir = wp_upload_dir();
        $pdf_dir = $upload_dir['basedir'] . '/estate-planning-manager-pdfs/';
        
        if (!file_exists($pdf_dir)) {
            return;
        }
        
        $files = glob($pdf_dir . '*.pdf');
        $cutoff_time = time() - ($days * 24 * 60 * 60);
        
        foreach ($files as $file) {
            if (filemtime($file) < $cutoff_time) {
                unlink($file);
            }
        }
    }
}
