<?php
/**
 * Tests for EPM_PDF_Generator class
 */

class Test_EPM_PDF_Generator extends EPM_Test_Case {
    
    /**
     * Test PDF generation with basic data
     */
    public function test_generate_pdf_with_basic_data() {
        // Create test data
        EPM_Test_Factory::create_client_data($this->test_client_id, 'basic_personal');
        EPM_Test_Factory::create_client_data($this->test_client_id, 'family_contacts');
        
        $sections = array('basic_personal', 'family_contacts');
        
        $pdf_path = EPM_PDF_Generator::instance()->generate_client_pdf($this->test_client_id, $sections);
        
        $this->assertNotFalse($pdf_path);
        $this->assertFileExists($pdf_path);
        $this->assertStringEndsWith('.pdf', $pdf_path);
    }
    
    /**
     * Test PDF generation with all sections
     */
    public function test_generate_pdf_with_all_sections() {
        // Create test data for multiple sections
        $sections = array(
            'basic_personal', 'family_contacts', 'key_contacts',
            'bank_accounts', 'investments', 'real_estate', 'insurance'
        );
        
        foreach ($sections as $section) {
            EPM_Test_Factory::create_client_data($this->test_client_id, $section);
        }
        
        $pdf_path = EPM_PDF_Generator::instance()->generate_client_pdf($this->test_client_id, $sections);
        
        $this->assertNotFalse($pdf_path);
        $this->assertFileExists($pdf_path);
        
        // Check file size is reasonable (should be larger with more data)
        $file_size = filesize($pdf_path);
        $this->assertGreaterThan(1000, $file_size); // At least 1KB
    }
    
    /**
     * Test PDF generation with custom template
     */
    public function test_generate_pdf_with_custom_template() {
        EPM_Test_Factory::create_client_data($this->test_client_id, 'basic_personal');
        
        $sections = array('basic_personal');
        $template_options = array(
            'header_text' => 'Custom Estate Planning Report',
            'footer_text' => 'Confidential Document',
            'include_watermark' => true
        );
        
        $pdf_path = EPM_PDF_Generator::instance()->generate_client_pdf(
            $this->test_client_id, 
            $sections, 
            $template_options
        );
        
        $this->assertNotFalse($pdf_path);
        $this->assertFileExists($pdf_path);
    }
    
    /**
     * Test PDF generation with empty data
     */
    public function test_generate_pdf_with_empty_data() {
        $sections = array('basic_personal');
        
        $pdf_path = EPM_PDF_Generator::instance()->generate_client_pdf($this->test_client_id, $sections);
        
        // Should still generate PDF even with no data
        $this->assertNotFalse($pdf_path);
        $this->assertFileExists($pdf_path);
    }
    
    /**
     * Test PDF security features
     */
    public function test_pdf_security_features() {
        EPM_Test_Factory::create_client_data($this->test_client_id, 'basic_personal');
        
        $sections = array('basic_personal');
        $security_options = array(
            'password_protect' => true,
            'password' => 'test123',
            'restrict_printing' => true,
            'restrict_copying' => true
        );
        
        $pdf_path = EPM_PDF_Generator::instance()->generate_client_pdf(
            $this->test_client_id, 
            $sections, 
            array(), 
            $security_options
        );
        
        $this->assertNotFalse($pdf_path);
        $this->assertFileExists($pdf_path);
    }
    
    /**
     * Test PDF metadata
     */
    public function test_pdf_metadata() {
        EPM_Test_Factory::create_client_data($this->test_client_id, 'basic_personal');
        
        $sections = array('basic_personal');
        $metadata = array(
            'title' => 'Estate Planning Report',
            'author' => 'Estate Planning Manager',
            'subject' => 'Personal Estate Information',
            'keywords' => 'estate, planning, personal, information'
        );
        
        $pdf_path = EPM_PDF_Generator::instance()->generate_client_pdf(
            $this->test_client_id, 
            $sections, 
            array(), 
            array(), 
            $metadata
        );
        
        $this->assertNotFalse($pdf_path);
        $this->assertFileExists($pdf_path);
    }
    
    /**
     * Test HTML to PDF conversion
     */
    public function test_html_to_pdf_conversion() {
        $html_content = '<h1>Test Document</h1><p>This is a test paragraph with <strong>bold text</strong>.</p>';
        
        $pdf_path = EPM_PDF_Generator::instance()->convert_html_to_pdf($html_content, 'test-document.pdf');
        
        $this->assertNotFalse($pdf_path);
        $this->assertFileExists($pdf_path);
        $this->assertStringContains('test-document.pdf', $pdf_path);
    }
    
    /**
     * Test PDF template rendering
     */
    public function test_pdf_template_rendering() {
        $client_data = array(
            'basic_personal' => array(
                (object) array(
                    'full_legal_name' => 'John Test Doe',
                    'date_of_birth' => '1980-05-15',
                    'place_of_birth' => 'Toronto, ON'
                )
            )
        );
        
        $html = EPM_PDF_Generator::instance()->render_template('basic_personal', $client_data);
        
        $this->assertNotEmpty($html);
        $this->assertStringContains('John Test Doe', $html);
        $this->assertStringContains('1980-05-15', $html);
        $this->assertStringContains('Toronto, ON', $html);
    }
    
    /**
     * Test PDF file cleanup
     */
    public function test_pdf_file_cleanup() {
        EPM_Test_Factory::create_client_data($this->test_client_id, 'basic_personal');
        
        $sections = array('basic_personal');
        $pdf_path = EPM_PDF_Generator::instance()->generate_client_pdf($this->test_client_id, $sections);
        
        $this->assertFileExists($pdf_path);
        
        // Test cleanup
        $result = EPM_PDF_Generator::instance()->cleanup_temp_files();
        $this->assertTrue($result);
    }
    
    /**
     * Test PDF generation error handling
     */
    public function test_pdf_generation_error_handling() {
        // Test with invalid client ID
        $invalid_client_id = 99999;
        $sections = array('basic_personal');
        
        $pdf_path = EPM_PDF_Generator::instance()->generate_client_pdf($invalid_client_id, $sections);
        
        $this->assertFalse($pdf_path);
    }
    
    /**
     * Test PDF page formatting
     */
    public function test_pdf_page_formatting() {
        EPM_Test_Factory::create_client_data($this->test_client_id, 'basic_personal');
        EPM_Test_Factory::create_client_data($this->test_client_id, 'family_contacts');
        
        $sections = array('basic_personal', 'family_contacts');
        $format_options = array(
            'page_size' => 'A4',
            'orientation' => 'portrait',
            'margins' => array(
                'top' => 20,
                'right' => 15,
                'bottom' => 20,
                'left' => 15
            )
        );
        
        $pdf_path = EPM_PDF_Generator::instance()->generate_client_pdf(
            $this->test_client_id, 
            $sections, 
            $format_options
        );
        
        $this->assertNotFalse($pdf_path);
        $this->assertFileExists($pdf_path);
    }
    
    /**
     * Test PDF with images and attachments
     */
    public function test_pdf_with_images() {
        EPM_Test_Factory::create_client_data($this->test_client_id, 'basic_personal');
        
        $sections = array('basic_personal');
        $options = array(
            'include_logo' => true,
            'logo_path' => plugin_dir_path(__FILE__) . '../assets/logo.png'
        );
        
        $pdf_path = EPM_PDF_Generator::instance()->generate_client_pdf(
            $this->test_client_id, 
            $sections, 
            $options
        );
        
        $this->assertNotFalse($pdf_path);
        $this->assertFileExists($pdf_path);
    }
    
    /**
     * Test PDF generation performance
     */
    public function test_pdf_generation_performance() {
        // Create substantial test data
        for ($i = 0; $i < 5; $i++) {
            EPM_Test_Factory::create_client_data($this->test_client_id, 'family_contacts');
            EPM_Test_Factory::create_client_data($this->test_client_id, 'bank_accounts');
            EPM_Test_Factory::create_client_data($this->test_client_id, 'investments');
        }
        
        $sections = array('family_contacts', 'bank_accounts', 'investments');
        
        $start_time = microtime(true);
        $pdf_path = EPM_PDF_Generator::instance()->generate_client_pdf($this->test_client_id, $sections);
        $end_time = microtime(true);
        
        $generation_time = $end_time - $start_time;
        
        $this->assertNotFalse($pdf_path);
        $this->assertFileExists($pdf_path);
        $this->assertLessThan(30, $generation_time); // Should complete within 30 seconds
    }
    
    /**
     * Test PDF accessibility features
     */
    public function test_pdf_accessibility_features() {
        EPM_Test_Factory::create_client_data($this->test_client_id, 'basic_personal');
        
        $sections = array('basic_personal');
        $accessibility_options = array(
            'tagged_pdf' => true,
            'alt_text_images' => true,
            'reading_order' => true,
            'high_contrast' => false
        );
        
        $pdf_path = EPM_PDF_Generator::instance()->generate_client_pdf(
            $this->test_client_id, 
            $sections, 
            array(), 
            array(), 
            array(), 
            $accessibility_options
        );
        
        $this->assertNotFalse($pdf_path);
        $this->assertFileExists($pdf_path);
    }
    
    /**
     * Test PDF digital signature
     */
    public function test_pdf_digital_signature() {
        EPM_Test_Factory::create_client_data($this->test_client_id, 'basic_personal');
        
        $sections = array('basic_personal');
        $signature_options = array(
            'digital_signature' => true,
            'certificate_path' => '/path/to/certificate.p12',
            'certificate_password' => 'cert_password',
            'signature_reason' => 'Document verification',
            'signature_location' => 'Toronto, ON'
        );
        
        $pdf_path = EPM_PDF_Generator::instance()->generate_client_pdf(
            $this->test_client_id, 
            $sections, 
            array(), 
            array(), 
            array(), 
            array(), 
            $signature_options
        );
        
        // Should still generate even if certificate doesn't exist (for testing)
        $this->assertNotFalse($pdf_path);
        $this->assertFileExists($pdf_path);
    }
    
    /**
     * Clean up test files
     */
    public function tearDown(): void {
        // Clean up generated PDF files
        EPM_PDF_Generator::instance()->cleanup_temp_files();
        
        parent::tearDown();
    }
}
