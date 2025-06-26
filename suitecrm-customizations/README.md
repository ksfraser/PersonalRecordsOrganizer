# SuiteCRM Customizations for Estate Planning Manager

This directory contains the SuiteCRM customizations needed to support the Estate Planning Manager WordPress plugin integration.

## Overview

The Estate Planning Manager requires custom modules in SuiteCRM to store estate planning data. These modules work in conjunction with the WordPress plugin to provide bidirectional data synchronization.

## Installation Instructions

1. Copy the custom modules to your SuiteCRM installation
2. Run the Module Builder to install the modules
3. Configure the API access and permissions
4. Set up the custom fields and relationships

## Custom Modules Included

1. **EPM_BankAccounts** - Banking information
2. **EPM_Investments** - Investment accounts  
3. **EPM_RealEstate** - Real estate properties
4. **EPM_Insurance** - Insurance policies

## Files Structure

```
suitecrm-customizations/
├── modules/
│   ├── EPM_BankAccounts/
│   ├── EPM_Investments/
│   ├── EPM_RealEstate/
│   └── EPM_Insurance/
├── custom/
│   ├── Extension/
│   └── modules/
├── scripts/
│   ├── install.php
│   └── configure_api.php
└── documentation/
    ├── installation_guide.md
    └── api_setup.md
```

## Requirements

- SuiteCRM 7.10 or higher
- Admin access to SuiteCRM
- API v8 enabled
- OAuth2 configured
