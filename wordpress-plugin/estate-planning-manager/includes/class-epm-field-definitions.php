<?php
/**
 * Field definitions and predefined selectors for Estate Planning Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

class EPM_Field_Definitions {
    
    /**
     * Get options from database selector table
     */
    private static function get_selector_options($table_name) {
        global $wpdb;
        
        $full_table_name = $wpdb->prefix . 'epm_' . $table_name;
        
        $results = $wpdb->get_results(
            "SELECT value, label FROM $full_table_name WHERE is_active = 1 ORDER BY sort_order ASC, label ASC"
        );
        
        $options = array();
        foreach ($results as $result) {
            $options[$result->value] = $result->label;
        }
        
        return $options;
    }
    
    /**
     * Get relationship types from database
     */
    public static function get_relationship_types() {
        $db_options = self::get_selector_options('relationship_types');
        
        // Fallback to hardcoded if database is empty
        if (empty($db_options)) {
            return array(
                'spouse' => 'Spouse',
                'parent' => 'Parent',
                'child' => 'Child',
                'sibling' => 'Sibling',
                'grandparent' => 'Grandparent',
                'grandchild' => 'Grandchild',
                'aunt_uncle' => 'Aunt/Uncle',
                'nephew_niece' => 'Nephew/Niece',
                'cousin' => 'Cousin',
                'in_law' => 'In-law',
                'friend' => 'Friend',
                'business_partner' => 'Business Partner',
                'legal_professional' => 'Legal Professional',
                'financial_advisor' => 'Financial Advisor',
                'other' => 'Other'
            );
        }
        
        return $db_options;
    }
    
    /**
     * Get relationship types (legacy method for compatibility)
     */
    public static function get_relationship_types_legacy() {
        return array(
            'spouse' => 'Spouse',
            'parent' => 'Parent',
            'child' => 'Child',
            'sibling' => 'Sibling',
            'grandparent' => 'Grandparent',
            'grandchild' => 'Grandchild',
            'aunt_uncle' => 'Aunt/Uncle',
            'nephew_niece' => 'Nephew/Niece',
            'cousin' => 'Cousin',
            'in_law' => 'In-law',
            'friend' => 'Friend',
            'business_partner' => 'Business Partner',
            'legal_professional' => 'Legal Professional',
            'financial_advisor' => 'Financial Advisor',
            'other' => 'Other'
        );
    }
    
    /**
     * Get bank account types
     */
    public static function get_bank_account_types() {
        return array(
            'savings' => 'Savings',
            'chequing' => 'Chequing',
            'hisa' => 'High Interest Savings Account (HISA)',
            'tfsa' => 'Tax-Free Savings Account (TFSA)',
            'rrsp' => 'Registered Retirement Savings Plan (RRSP)',
            'rrif' => 'Registered Retirement Income Fund (RRIF)',
            'resp' => 'Registered Education Savings Plan (RESP)',
            'fhsa' => 'First Home Savings Account (FHSA)',
            'lira' => 'Locked-in Retirement Account (LIRA)',
            'lif' => 'Life Income Fund (LIF)',
            'joint' => 'Joint Account',
            'trust' => 'Trust Account',
            'business' => 'Business Account',
            'usd' => 'USD Account',
            'other' => 'Other'
        );
    }
    
    /**
     * Get investment types
     */
    public static function get_investment_types() {
        return array(
            'stocks' => 'Stocks',
            'bonds' => 'Bonds',
            'mutual_funds' => 'Mutual Funds',
            'etfs' => 'Exchange-Traded Funds (ETFs)',
            'gics' => 'Guaranteed Investment Certificates (GICs)',
            'term_deposits' => 'Term Deposits',
            'cryptocurrency' => 'Cryptocurrency',
            'reits' => 'Real Estate Investment Trusts (REITs)',
            'pension_plans' => 'Pension Plans',
            'annuities' => 'Annuities',
            'life_insurance' => 'Life Insurance Policies',
            'segregated_funds' => 'Segregated Funds',
            'options' => 'Options',
            'futures' => 'Futures',
            'commodities' => 'Commodities',
            'foreign_currency' => 'Foreign Currency',
            'other' => 'Other'
        );
    }
    
    /**
     * Get real estate property types
     */
    public static function get_property_types() {
        return array(
            'primary_residence' => 'Primary Residence',
            'secondary_residence' => 'Secondary Residence',
            'rental_property' => 'Rental Property',
            'commercial_property' => 'Commercial Property',
            'vacant_land' => 'Vacant Land',
            'cottage_cabin' => 'Cottage/Cabin',
            'condominium' => 'Condominium',
            'townhouse' => 'Townhouse',
            'farm_agricultural' => 'Farm/Agricultural',
            'timeshare' => 'Timeshare',
            'mobile_home' => 'Mobile Home',
            'cooperative' => 'Cooperative',
            'other' => 'Other'
        );
    }
    
    /**
     * Get insurance types
     */
    public static function get_insurance_types() {
        return array(
            'life_insurance' => 'Life Insurance',
            'disability_insurance' => 'Disability Insurance',
            'health_insurance' => 'Health Insurance',
            'dental_insurance' => 'Dental Insurance',
            'vision_insurance' => 'Vision Insurance',
            'home_insurance' => 'Home Insurance',
            'auto_insurance' => 'Auto Insurance',
            'travel_insurance' => 'Travel Insurance',
            'umbrella_insurance' => 'Umbrella Insurance',
            'professional_liability' => 'Professional Liability',
            'critical_illness' => 'Critical Illness Insurance',
            'long_term_care' => 'Long-term Care Insurance',
            'mortgage_insurance' => 'Mortgage Insurance',
            'business_insurance' => 'Business Insurance',
            'other' => 'Other'
        );
    }
    
    /**
     * Get employment status types
     */
    public static function get_employment_status_types() {
        return array(
            'full_time_employee' => 'Full-time Employee',
            'part_time_employee' => 'Part-time Employee',
            'self_employed' => 'Self-employed',
            'contractor' => 'Contractor/Freelancer',
            'retired' => 'Retired',
            'unemployed' => 'Unemployed',
            'student' => 'Student',
            'homemaker' => 'Homemaker',
            'on_leave' => 'On Leave',
            'seasonal' => 'Seasonal Worker',
            'volunteer' => 'Volunteer',
            'other' => 'Other'
        );
    }
    
    /**
     * Get document types
     */
    public static function get_document_types() {
        return array(
            'will' => 'Will',
            'poa_financial' => 'Power of Attorney (Financial)',
            'poa_personal_care' => 'Power of Attorney (Personal Care)',
            'living_will' => 'Living Will/Advance Directive',
            'trust_document' => 'Trust Document',
            'beneficiary_designation' => 'Beneficiary Designation',
            'funeral_instructions' => 'Funeral Instructions',
            'organ_donation' => 'Organ Donation Consent',
            'guardianship' => 'Guardianship Document',
            'business_succession' => 'Business Succession Plan',
            'prenuptial_agreement' => 'Prenuptial Agreement',
            'separation_agreement' => 'Separation Agreement',
            'other' => 'Other'
        );
    }
    
    /**
     * Get payment frequency types
     */
    public static function get_payment_frequency_types() {
        return array(
            'weekly' => 'Weekly',
            'bi_weekly' => 'Bi-weekly',
            'monthly' => 'Monthly',
            'quarterly' => 'Quarterly',
            'semi_annually' => 'Semi-annually',
            'annually' => 'Annually',
            'one_time' => 'One-time',
            'irregular' => 'Irregular',
            'other' => 'Other'
        );
    }
    
    /**
     * Get debt types
     */
    public static function get_debt_types() {
        return array(
            'mortgage' => 'Mortgage',
            'home_equity_loan' => 'Home Equity Loan',
            'auto_loan' => 'Auto Loan',
            'personal_loan' => 'Personal Loan',
            'credit_card' => 'Credit Card',
            'line_of_credit' => 'Line of Credit',
            'student_loan' => 'Student Loan',
            'business_loan' => 'Business Loan',
            'tax_debt' => 'Tax Debt',
            'medical_debt' => 'Medical Debt',
            'family_loan' => 'Family/Friend Loan',
            'other' => 'Other'
        );
    }
    
    /**
     * Get digital asset types
     */
    public static function get_digital_asset_types() {
        return array(
            'email_accounts' => 'Email Accounts',
            'social_media' => 'Social Media Accounts',
            'cloud_storage' => 'Cloud Storage',
            'cryptocurrency' => 'Cryptocurrency Wallets',
            'online_banking' => 'Online Banking',
            'investment_accounts' => 'Online Investment Accounts',
            'domain_names' => 'Domain Names',
            'websites' => 'Websites/Blogs',
            'digital_photos' => 'Digital Photos/Videos',
            'software_licenses' => 'Software Licenses',
            'gaming_accounts' => 'Gaming Accounts',
            'subscription_services' => 'Subscription Services',
            'loyalty_programs' => 'Loyalty Programs',
            'other' => 'Other'
        );
    }
    
    /**
     * Get personal property categories
     */
    public static function get_personal_property_categories() {
        return array(
            'jewelry' => 'Jewelry',
            'artwork' => 'Artwork',
            'antiques' => 'Antiques',
            'collectibles' => 'Collectibles',
            'vehicles' => 'Vehicles',
            'boats' => 'Boats/Watercraft',
            'electronics' => 'Electronics',
            'furniture' => 'Furniture',
            'appliances' => 'Appliances',
            'tools' => 'Tools/Equipment',
            'books' => 'Books/Library',
            'musical_instruments' => 'Musical Instruments',
            'sports_equipment' => 'Sports Equipment',
            'clothing' => 'Clothing/Accessories',
            'household_items' => 'Household Items',
            'business_assets' => 'Business Assets',
            'other' => 'Other'
        );
    }
    
    /**
     * Get all field definitions with their selectors
     */
    public static function get_all_field_definitions() {
        return array(
            'relationship_types' => self::get_relationship_types(),
            'bank_account_types' => self::get_bank_account_types(),
            'investment_types' => self::get_investment_types(),
            'property_types' => self::get_property_types(),
            'insurance_types' => self::get_insurance_types(),
            'employment_status_types' => self::get_employment_status_types(),
            'document_types' => self::get_document_types(),
            'payment_frequency_types' => self::get_payment_frequency_types(),
            'debt_types' => self::get_debt_types(),
            'digital_asset_types' => self::get_digital_asset_types(),
            'personal_property_categories' => self::get_personal_property_categories()
        );
    }
    
    /**
     * Validate a field value against its allowed options
     */
    public static function validate_field_value($field_type, $value, $other_value = '') {
        $definitions = self::get_all_field_definitions();
        
        if (!isset($definitions[$field_type])) {
            return false;
        }
        
        $allowed_values = array_keys($definitions[$field_type]);
        
        if (!in_array($value, $allowed_values)) {
            return false;
        }
        
        // If "other" is selected, ensure other_value is provided
        if ($value === 'other' && empty($other_value)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Get display value for a field
     */
    public static function get_display_value($field_type, $value, $other_value = '') {
        $definitions = self::get_all_field_definitions();
        
        if (!isset($definitions[$field_type])) {
            return $value;
        }
        
        if ($value === 'other' && !empty($other_value)) {
            return $other_value;
        }
        
        return isset($definitions[$field_type][$value]) ? $definitions[$field_type][$value] : $value;
    }
}
