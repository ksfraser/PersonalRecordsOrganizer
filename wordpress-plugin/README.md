# Estate Planning Manager WordPress Plugin

A comprehensive estate planning records management system with SuiteCRM integration for financial advisors.

## Overview

The Estate Planning Manager transforms spreadsheet-based estate planning records into a robust, multi-user database application with enterprise-grade features including:

- **Multi-User Database**: Secure access for clients and financial advisors
- **SuiteCRM Integration**: Bidirectional data synchronization
- **PDF Generation**: Selective export with privacy controls
- **Data Security**: AES-256 encryption and PIPEDA compliance
- **Audit Logging**: Complete activity tracking
- **Admin CRUD Screens**: Add, edit, and delete Insurance Categories and Insurance Types directly from the admin interface

## Installation Methods

### Method 1: WordPress Admin Upload (Recommended)

1. **Create Plugin Package**:
   ```bash
   cd wordpress-plugin
   php create-plugin-package.php
   ```
   This creates `dist/estate-planning-manager.zip`

2. **Upload to WordPress**:
   - Go to **Plugins > Add New** in WordPress admin
   - Click **Upload Plugin**
   - Choose the `estate-planning-manager.zip` file
   - Click **Install Now**
   - Click **Activate Plugin**

### Method 2: Manual Installation

1. **Download/Copy Files**:
   - Copy the `estate-planning-manager` folder to `/wp-content/plugins/`
   - Ensure proper file permissions (755 for directories, 644 for files)

2. **Activate Plugin**:
   - Go to **Plugins** in WordPress admin
   - Find "Estate Planning Manager" and click **Activate**

### Method 3: WP-CLI Installation

```bash
# Copy plugin to WordPress
cp -r estate-planning-manager /path/to/wordpress/wp-content/plugins/

# Activate via WP-CLI
wp plugin activate estate-planning-manager
```

## Requirements

### WordPress Requirements
- WordPress 5.0 or higher
- PHP 7.3 or higher
- MySQL 5.7 or higher
- SSL certificate (recommended for security)

### Server Requirements
- Memory: 256MB minimum, 512MB recommended
- Disk Space: 50MB for plugin files
- PHP Extensions:
  - `openssl` (for encryption)
  - `curl` (for SuiteCRM integration)
  - `json` (for data handling)
  - `zip` (for PDF generation)

### Optional Requirements
- SuiteCRM 7.10+ (for CRM integration)
- SMTP server (for email notifications)

## Initial Setup

### 1. Plugin Activation

After activation, the plugin will:
- Create necessary database tables
- Set up user roles (Estate Client, Financial Advisor)
- Configure default settings
- Create sample data (optional)

### 2. Basic Configuration

1. Go to **Estate Planning Manager > Settings**
2. Configure basic options:
   - **Company Information**: Your firm's details
   - **Security Settings**: Encryption preferences
   - **PDF Templates**: Document generation options
   - **User Permissions**: Access control settings

### 3. SuiteCRM Integration (Optional)

If you have SuiteCRM, configure the integration:

1. **Install SuiteCRM Modules**:
   - Follow the SuiteCRM installation guide in `/suitecrm-customizations/`
   - Install the custom Estate Planning modules

2. **Configure Connection**:
   - Go to **Estate Planning Manager > SuiteCRM Settings**
   - Enter your SuiteCRM URL and credentials
   - Test the connection
   - Enable synchronization

### 4. User Setup

1. **Create User Roles**:
   - **Estate Clients**: Can manage their own data
   - **Financial Advisors**: Can manage multiple clients
   - **Administrators**: Full system access

2. **Add Users**:
   - Go to **Users > Add New**
   - Assign appropriate roles
   - Configure permissions

## Usage Guide

### For Clients

1. **Login**: Use provided credentials to access the system
2. **Enter Data**: Fill in estate planning information across different sections
3. **Generate PDFs**: Create documents with selected information
4. **Share Data**: Export specific sections for sharing with advisors

### For Financial Advisors

1. **Client Management**: View and manage multiple client records
2. **Data Review**: Review client-entered information
3. **SuiteCRM Sync**: Synchronize data with CRM system
4. **Reporting**: Generate reports and summaries

### For Administrators

1. **System Configuration**: Manage plugin settings and options
2. **User Management**: Create and manage user accounts
3. **Data Selectors**: Configure which fields appear in exports
4. **Audit Reports**: Monitor system activity and security
5. **Bank Locations & Names**: Manage available bank regions and banks (see new admin screens)

## Features

### Data Management
- **Personal Information**: Basic client details
- **Bank Accounts**: Banking information with encryption, **dynamic bank location and bank name selectors** (admin-editable)
- **Investments**: RRSP, TFSA, and other investment accounts
- **Real Estate**: Property records and valuations
- **Insurance**: Life, disability, and other policies
- **Legal Documents**: Wills, powers of attorney, etc.
- **Emergency Contacts**: Important contact information

### Security Features
- **Data Encryption**: AES-256 encryption for sensitive fields
- **Access Controls**: Role-based permissions
- **Audit Logging**: Complete activity tracking
- **PIPEDA Compliance**: Canadian privacy law compliance
- **Secure Sharing**: Controlled data export and sharing

### PDF Generation
- **Selective Export**: Choose which data to include
- **Multiple Templates**: Different document formats
- **Privacy Controls**: Exclude sensitive information
- **Professional Formatting**: High-quality document output

### SuiteCRM Integration
- **Bidirectional Sync**: Data flows both ways
- **Suggested Updates**: Review changes before applying
- **Contact Management**: Automatic contact creation
- **Custom Modules**: Estate planning specific modules

### Admin Management Screens
- **Bank Locations**: Add/edit regions (Canada/USA/Europe) for banks
- **Bank Names**: Add/edit banks by region
- **Insurance Categories**: Add/edit insurance categories
- **Insurance Types**: Add/edit insurance types

### Modular Architecture
- **Action Handlers**: All major plugin actions (form submissions, AJAX, admin screens) are now handled by dedicated classes in `includes/handlers/`
- **Modal Logic**: Modal forms are modularized in `public/modals/`

## Troubleshooting

### Common Issues

#### Plugin Won't Activate
- Check PHP version (7.3+ required)
- Verify file permissions
- Check for plugin conflicts
- Review error logs

#### Database Errors
- Ensure MySQL user has CREATE privileges
- Check database connection
- Verify table creation permissions

#### SuiteCRM Connection Failed
- Verify SuiteCRM URL and credentials
- Check API v8 is enabled
- Ensure OAuth2 is configured
- Review firewall settings

#### PDF Generation Issues
- Check PHP memory limit (256MB+)
- Verify write permissions in uploads directory
- Ensure required PHP extensions are installed

### Support Resources

1. **Plugin Documentation**: Check included documentation files
2. **WordPress Logs**: Review `/wp-content/debug.log`
3. **Server Logs**: Check PHP and web server error logs
4. **Database**: Verify table creation and data integrity

### Getting Help

For technical support:
1. Check the troubleshooting section above
2. Review WordPress and PHP error logs
3. Verify all requirements are met
4. Contact your system administrator

## Development

### File Structure
```
estate-planning-manager/
├── estate-planning-manager.php     # Main plugin file
├── includes/                       # Core classes
├── admin/                         # Admin interface
├── public/                        # Frontend functionality
├── assets/                        # CSS/JS files
└── tests/                         # Test suite
```

### Hooks and Filters

The plugin provides various hooks for customization:
- `epm_init` - After plugin initialization
- `epm_client_saved` - After client data is saved
- `epm_pdf_generated` - After PDF generation
- `epm_sync_completed` - After SuiteCRM sync

### Custom Development

To extend the plugin:
1. Use provided hooks and filters
2. Create custom modules following the existing pattern
3. Add custom PDF templates
4. Extend the SuiteCRM integration

## License

This plugin is licensed under the GPL v2 or later.

## Changelog

### Version 1.0.0
- Initial release
- Multi-user database functionality
- SuiteCRM integration
- PDF generation with selective export
- Security and encryption features
- Audit logging system

## Support

For support and updates, please contact your system administrator or the plugin developer.
