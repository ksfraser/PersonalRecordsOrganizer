<?php
// This file contains default values extracted from the spreadsheet for use in the EPM defaults table.
// Format: [section][subsection][field] => value

return [
    'personal' => [
        'advisor' => [
            'advisor_name' => 'John Smith',
            'advisor_email' => 'john@advisor.com',
            'advisor_phone' => '555-1234',
            'advisor_address' => '123 Main St',
        ],
        // Add more subsections and fields as needed
    ],
    'banking' => [
        // Example: default bank account
        'default_account' => [
            'bank_name' => 'ABC Bank',
            'account_type' => 'Chequing',
            'account_number' => '123456789',
            'branch' => 'Downtown',
            'balance' => '10000.00',
        ],
    ],
    'investments' => [
        'default_investment' => [
            'investment_type' => 'RRSP',
            'institution' => 'XYZ Investments',
            'account_number' => 'INV-987654',
            'current_value' => '25000.00',
            'beneficiary' => 'Jane Doe',
        ],
    ],
    'insurance' => [
        'default_policy' => [
            'policy_type' => 'Life',
            'insurance_company' => 'BestLife',
            'policy_number' => 'LIFE-1234',
            'coverage_amount' => '500000',
            'beneficiary' => 'Jane Doe',
        ],
    ],
    'real_estate' => [
        'default_property' => [
            'property_type' => 'House',
            'property_address' => '456 Elm St',
            'estimated_value' => '600000',
            'mortgage_balance' => '200000',
            'mortgage_company' => 'BigBank',
        ],
    ],
    'emergency_contacts' => [
        'default_contact' => [
            'contact_name' => 'Mary Johnson',
            'relationship' => 'Sister',
            'phone' => '555-9876',
            'email' => 'mary.johnson@email.com',
            'address' => '789 Oak Ave',
        ],
    ],
];
