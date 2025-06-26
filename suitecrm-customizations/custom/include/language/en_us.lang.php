<?php
/**
 * Custom dropdown lists for Estate Planning Manager modules
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$app_list_strings['epm_account_type_list'] = array(
    'checking' => 'Checking',
    'savings' => 'Savings',
    'money_market' => 'Money Market',
    'cd' => 'Certificate of Deposit',
    'business_checking' => 'Business Checking',
    'business_savings' => 'Business Savings',
    'joint_checking' => 'Joint Checking',
    'joint_savings' => 'Joint Savings',
    'trust' => 'Trust Account',
    'other' => 'Other',
);

$app_list_strings['epm_investment_type_list'] = array(
    'rrsp' => 'RRSP',
    'rrif' => 'RRIF',
    'tfsa' => 'TFSA',
    'resp' => 'RESP',
    'non_registered' => 'Non-Registered',
    'pension' => 'Pension',
    'group_rrsp' => 'Group RRSP',
    'lira' => 'LIRA',
    'lif' => 'LIF',
    'other' => 'Other',
);

$app_list_strings['epm_property_type_list'] = array(
    'primary_residence' => 'Primary Residence',
    'secondary_residence' => 'Secondary Residence',
    'rental_property' => 'Rental Property',
    'commercial' => 'Commercial Property',
    'vacant_land' => 'Vacant Land',
    'cottage' => 'Cottage/Cabin',
    'condo' => 'Condominium',
    'townhouse' => 'Townhouse',
    'other' => 'Other',
);

$app_list_strings['epm_insurance_type_list'] = array(
    'life' => 'Life Insurance',
    'disability' => 'Disability Insurance',
    'critical_illness' => 'Critical Illness',
    'long_term_care' => 'Long Term Care',
    'health' => 'Health Insurance',
    'dental' => 'Dental Insurance',
    'travel' => 'Travel Insurance',
    'home' => 'Home Insurance',
    'auto' => 'Auto Insurance',
    'umbrella' => 'Umbrella Policy',
    'other' => 'Other',
);

$app_list_strings['epm_document_type_list'] = array(
    'will' => 'Will',
    'power_of_attorney' => 'Power of Attorney',
    'healthcare_directive' => 'Healthcare Directive',
    'trust_document' => 'Trust Document',
    'beneficiary_designation' => 'Beneficiary Designation',
    'marriage_certificate' => 'Marriage Certificate',
    'divorce_decree' => 'Divorce Decree',
    'birth_certificate' => 'Birth Certificate',
    'passport' => 'Passport',
    'other' => 'Other',
);
