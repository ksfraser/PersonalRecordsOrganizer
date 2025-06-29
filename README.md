# Estate Planning Manager

A comprehensive WordPress plugin and SuiteCRM integration system for managing estate planning records with multi-user support, data sharing controls, and PDF generation capabilities.

## Architecture Overview

Estate Planning Manager uses a modular, extensible architecture for all database tables and cross-platform data sync. Key architectural features:

- **Modular Table Classes:** All selector and user data tables are defined as individual classes implementing a common `TableInterface`. Each class contains its own schema and default data logic, making tables reusable and easy to maintain.
- **TableFactory:** A central `TableFactory` returns all registered table classes. The main database installer (`EPM_Database::create_tables()`) uses this factory to create and populate all tables in a single pass.
- **Cross-Platform Sync Fields:** All major user data tables include both a `suitecrm_guid` and a `wp_record_id` field, enabling two-way record matching and synchronization with SuiteCRM (and future platforms).
- **Separation of Concerns:** Table creation and population logic is fully separated from business logic, UI, and API code. This makes the system more testable and extensible.
- **Extensibility:** The architecture is designed to support additional platforms (e.g., FrontAccounting) by reusing the same table classes and adding new sync fields if needed.

## Overview

This system transforms spreadsheet-based estate planning data into a secure, multi-user database application with the following key features:

- **Multi-user Support**: Clients and financial advisors with role-based access
- **Data Security**: Encryption, audit logging, and PIPEDA compliance
- **PDF Generation**: Customizable PDF exports with selective data sharing
- **SuiteCRM Integration**: Bi-directional sync with CRM system
- **Comprehensive Data Management**: 17 different sections covering all aspects of estate planning

## System Architecture

### WordPress Plugin Structure
```
wordpress-plugin/estate-planning-manager/
├── estate-planning-manager.php          # Main plugin file
├── includes/                            # Core functionality
│   ├── class-epm-database.php          # Database operations
│   ├── class-epm-security.php          # Security & encryption
│   ├── class-epm-suitecrm-api.php      # SuiteCRM integration
│   ├── class-epm-pdf-generator.php     # PDF generation
│   ├── class-epm-audit-logger.php      # Audit logging
│   ├── class-epm-permissions.php       # Permission management
│   └── class-epm-data-sync.php         # Data synchronization
├── admin/                               # Admin interface
├── public/                              # Frontend interface
├── assets/                              # CSS, JS, images
├── templates/                           # Template files
└── languages/                           # Translation files
```

### SuiteCRM Module Structure
```
suitecrm-module/EstateManager/
├── modules/                             # Custom modules
├── custom/                              # Customizations
└── include/                             # Helper files
```

## Data Sections

The system manages 17 comprehensive sections of estate planning data:

### Personal Information
1. **Basic Personal** - Legal name, birth info, citizenship, documents
2. **Family Contacts** - Family members and relationships
3. **Key Contacts** - Professional contacts (lawyers, doctors, etc.)

### Legal Documents
4. **Wills & POA** - Will and Power of Attorney information
5. **Funeral & Organ Donation** - End-of-life arrangements
6. **Taxes** - Tax advisor and filing information

### Background
7. **Military Service** - Veteran information and benefits
8. **Employment** - Work history and benefits
9. **Volunteer Work** - Charitable activities and commitments

### Financial Assets
10. **Bank Accounts** - Banking relationships and accounts
11. **Investments** - Investment accounts and advisors
12. **Real Estate** - Property ownership and mortgages
13. **Personal Property** - Vehicles, valuables, safe deposit boxes
14. **Digital Assets** - Online accounts and digital property

### Financial Obligations
15. **Scheduled Payments** - Recurring bills and obligations
16. **Debtors & Creditors** - Loans and debts
17. **Insurance** - All insurance policies and beneficiaries

## Key Features

### Security & Compliance
- **Data Encryption**: AES-256-CBC encryption for sensitive data
- **Audit Logging**: Complete audit trail of all data access and changes
- **Role-Based Access**: Granular permissions for clients and advisors
- **PIPEDA Compliance**: Privacy controls and data sharing permissions
- **Rate Limiting**: Protection against brute force attacks
- **Secure File Uploads**: Validated file types and sizes

### User Roles
- **Estate Planning Client**: Can manage own data, generate PDFs, share selectively
- **Financial Advisor**: Can view assigned client data, generate reports
- **Administrator**: Full system access and configuration

### PDF Generation
- **Multiple Templates**: Complete estate plan, financial summary, emergency contacts, legal documents
- **Selective Export**: Choose specific sections to include
- **Data Masking**: Automatic masking of sensitive information
- **Professional Formatting**: Clean, organized PDF output

### SuiteCRM Integration
- **Contact Sync**: Automatic contact creation and updates
- **Custom Modules**: Dedicated modules for financial data
- **Bi-directional Sync**: Data flows both ways
- **Error Handling**: Comprehensive sync logging and error recovery

## Database Schema

### Core Tables
- `epm_clients` - Client records and advisor assignments
- `epm_basic_personal` - Personal information
- `epm_family_contacts` - Family member contacts
- `epm_key_contacts` - Professional contacts
- `epm_wills_poa` - Legal documents
- `epm_funeral_organ` - End-of-life arrangements
- `epm_taxes` - Tax information
- `epm_military_service` - Military service records
- `epm_employment` - Employment history
- `epm_volunteer` - Volunteer activities
- `epm_bank_accounts` - Banking information
- `epm_investments` - Investment accounts
- `epm_real_estate` - Property records
- `epm_personal_property` - Personal assets
- `epm_digital_assets` - Digital accounts
- `epm_scheduled_payments` - Recurring payments
- `epm_debtors_creditors` - Debt information
- `epm_insurance` - Insurance policies

### System Tables
- `epm_sharing_permissions` - Data sharing controls
- `epm_audit_log` - Complete audit trail
- `epm_sync_log` - SuiteCRM synchronization logs

## Installation

### WordPress Plugin
1. Upload the plugin folder to `/wp-content/plugins/`
2. Activate the plugin through WordPress admin
3. Configure SuiteCRM connection settings
4. Set up user roles and permissions

### SuiteCRM Module
1. Upload module files to SuiteCRM directory
2. Run Module Loader to install custom modules
3. Configure API access and permissions
4. Test connection with WordPress plugin

## Configuration

### WordPress Settings
- SuiteCRM URL and credentials
- Encryption settings
- Audit logging preferences
- PDF template options
- Data retention policies

### SuiteCRM Settings
- API access configuration
- Custom module permissions
- Sync frequency settings
- Data mapping preferences

## Security Considerations

### Data Protection
- All sensitive data encrypted at rest
- Secure transmission using HTTPS
- Regular security audits and monitoring
- Compliance with privacy regulations

### Access Control
- Multi-factor authentication support
- Session management and timeouts
- IP-based access restrictions
- Regular permission reviews

### Backup & Recovery
- Automated database backups
- Encrypted backup storage
- Disaster recovery procedures
- Data retention policies

## API Documentation

### WordPress Hooks
```php
// Data save hook
do_action('epm_data_saved', $user_id, $section, $record_id, $data);

// Data delete hook
do_action('epm_data_deleted', $user_id, $section, $record_id);

// Sharing change hook
do_action('epm_sharing_changed', $user_id, $client_id, $sharing_data);

// SuiteCRM sync hook
do_action('epm_sync_client_data', $client_id, $section, $data);
```

### SuiteCRM API Endpoints
- Contact creation and updates
- Custom module data sync
- Note creation for non-standard data
- Relationship management

## Development

### Code Standards
- WordPress Coding Standards
- PSR-4 Autoloading
- Single Responsibility Principle
- Comprehensive error handling
- Extensive documentation

### Testing
- Unit tests for core functionality
- Integration tests for SuiteCRM sync
- Security testing and penetration testing
- Performance testing under load

## Support & Maintenance

### Regular Maintenance
- Database optimization
- Log file cleanup
- Security updates
- Performance monitoring

### Troubleshooting
- Comprehensive error logging
- Debug mode for development
- Sync status monitoring
- Performance metrics

## License

This software is licensed under GPL v2 or later.

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Submit a pull request

## Changelog

### Version 1.0.0
- Initial release
- Complete estate planning data management
- SuiteCRM integration
- PDF generation
- Security and audit features
- Multi-user support

## Support

For support, please contact [your support email] or create an issue in the repository.
