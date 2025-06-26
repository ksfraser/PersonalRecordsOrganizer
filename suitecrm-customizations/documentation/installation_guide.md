# SuiteCRM Installation Guide for Estate Planning Manager

This guide provides step-by-step instructions for installing the SuiteCRM customizations required for the Estate Planning Manager WordPress plugin integration.

## Prerequisites

- SuiteCRM 7.10 or higher installed and running
- Admin access to SuiteCRM
- Command line access to the SuiteCRM server
- PHP 7.3 or higher
- MySQL 5.7 or higher

## Installation Steps

### 1. Backup Your SuiteCRM Installation

Before making any changes, create a complete backup of your SuiteCRM installation:

```bash
# Backup database
mysqldump -u [username] -p [database_name] > suitecrm_backup.sql

# Backup files
tar -czf suitecrm_files_backup.tar.gz /path/to/suitecrm/
```

### 2. Copy Customization Files

Copy the customization files to your SuiteCRM installation:

```bash
# Navigate to your SuiteCRM root directory
cd /path/to/suitecrm/

# Copy custom modules
cp -r /path/to/suitecrm-customizations/modules/* modules/

# Copy custom language files
cp -r /path/to/suitecrm-customizations/custom/* custom/
```

### 3. Run the Installation Script

Execute the installation script to set up the custom modules:

```bash
# Navigate to SuiteCRM root directory
cd /path/to/suitecrm/

# Run the installation script
php suitecrm-customizations/scripts/install.php
```

The script will:
- Create database tables for custom modules
- Install and register the modules
- Set up relationships with existing modules
- Configure permissions
- Rebuild the SuiteCRM cache

### 4. Manual Module Installation (Alternative)

If the automated script doesn't work, you can install modules manually:

#### 4.1 Install Bank Accounts Module

1. Go to **Admin > Module Builder**
2. Click **New Package**
3. Enter package details:
   - Name: `Estate Planning Manager`
   - Author: `Your Name`
   - Description: `Estate planning data modules`
4. Click **Save**
5. Click **New Module** in the package
6. Enter module details:
   - Module Name: `Bank Accounts`
   - Singular Label: `Bank Account`
   - Plural Label: `Bank Accounts`
   - Type: `Basic`
7. Add custom fields:
   - `bank_name` (Text, Required)
   - `account_type` (Dropdown)
   - `account_number` (Text)
   - `branch` (Text)
   - `wp_client_id` (Text)
   - `wp_record_id` (Text)
8. Click **Save & Deploy**

#### 4.2 Repeat for Other Modules

Follow the same process for:
- **Investments Module**
- **Real Estate Module** 
- **Insurance Module**

### 5. Configure Dropdown Lists

Add custom dropdown options:

1. Go to **Admin > Dropdown Editor**
2. Add the following dropdown lists:

#### Account Type List (`epm_account_type_list`)
- `checking` → `Checking`
- `savings` → `Savings`
- `money_market` → `Money Market`
- `cd` → `Certificate of Deposit`
- `business_checking` → `Business Checking`
- `business_savings` → `Business Savings`
- `joint_checking` → `Joint Checking`
- `joint_savings` → `Joint Savings`
- `trust` → `Trust Account`
- `other` → `Other`

#### Investment Type List (`epm_investment_type_list`)
- `rrsp` → `RRSP`
- `rrif` → `RRIF`
- `tfsa` → `TFSA`
- `resp` → `RESP`
- `non_registered` → `Non-Registered`
- `pension` → `Pension`
- `group_rrsp` → `Group RRSP`
- `lira` → `LIRA`
- `lif` → `LIF`
- `other` → `Other`

### 6. Set Up API Access

Configure API access for WordPress integration:

#### 6.1 Enable API v8
1. Go to **Admin > System Settings**
2. Find **API Settings** section
3. Enable **API v8**
4. Save settings

#### 6.2 Create API User
1. Go to **Admin > User Management**
2. Create new user:
   - Username: `epm_api_user`
   - First Name: `EPM`
   - Last Name: `API User`
   - Email: `api@yourdomain.com`
   - Status: `Active`
   - Admin: `Yes` (or create custom role)
3. Set a strong password
4. Save user

#### 6.3 Configure OAuth2
1. Go to **Admin > OAuth2 Clients and Tokens**
2. Click **Create OAuth2 Client**
3. Enter details:
   - Name: `Estate Planning Manager`
   - Client ID: `epm_client` (or generate)
   - Client Secret: Generate secure secret
   - Redirect URI: `https://yourwordpresssite.com/wp-admin/`
4. Save client

### 7. Configure Module Relationships

Set up relationships between modules and Contacts:

1. Go to **Admin > Studio**
2. Select **Contacts** module
3. Click **Relationships**
4. Add relationships for each EPM module:
   - **Bank Accounts**: One-to-Many
   - **Investments**: One-to-Many
   - **Real Estate**: One-to-Many
   - **Insurance**: One-to-Many

### 8. Set Up Subpanels

Add subpanels to Contacts for EPM modules:

1. Go to **Admin > Studio**
2. Select **Contacts** module
3. Click **Layouts > Subpanels**
4. Add subpanels for:
   - Bank Accounts
   - Investments
   - Real Estate
   - Insurance

### 9. Configure Security and Permissions

Set up proper security:

#### 9.1 Role Management
1. Go to **Admin > Role Management**
2. Create role: `Estate Planning Advisor`
3. Set permissions for EPM modules:
   - Access: `Enabled`
   - View: `All`
   - List: `All`
   - Edit: `Owner`
   - Delete: `Owner`
   - Import: `Enabled`
   - Export: `Enabled`

#### 9.2 Security Groups (if enabled)
1. Go to **Admin > Security Groups**
2. Create groups for different advisor teams
3. Assign users to appropriate groups

### 10. Test the Installation

Verify the installation:

1. **Check Modules**: Ensure all EPM modules appear in the module list
2. **Test CRUD Operations**: Create, read, update, and delete records
3. **Verify Relationships**: Check that records link to Contacts properly
4. **Test API Access**: Use API testing tool to verify endpoints work
5. **Check Permissions**: Ensure security settings work as expected

### 11. Configure WordPress Plugin

In your WordPress admin:

1. Go to **Estate Planning Manager > Settings**
2. Enter SuiteCRM connection details:
   - **SuiteCRM URL**: `https://your-suitecrm-domain.com`
   - **Client ID**: From OAuth2 setup
   - **Client Secret**: From OAuth2 setup
   - **Username**: API user username
   - **Password**: API user password
3. Test the connection
4. Enable synchronization

## Troubleshooting

### Common Issues

#### Module Not Appearing
- Check file permissions (755 for directories, 644 for files)
- Verify module files are in correct location
- Run Quick Repair and Rebuild
- Clear browser cache

#### API Connection Failed
- Verify API v8 is enabled
- Check OAuth2 client configuration
- Ensure API user has proper permissions
- Check firewall settings

#### Database Errors
- Verify database user has CREATE/ALTER permissions
- Check MySQL version compatibility
- Review error logs for specific issues

#### Permission Denied
- Check file ownership and permissions
- Verify web server user can write to custom directories
- Review SuiteCRM security settings

### Log Files

Check these log files for errors:
- `suitecrm.log`
- `php_errors.log`
- `apache/nginx error logs`

### Support Commands

Useful commands for troubleshooting:

```bash
# Check file permissions
find /path/to/suitecrm -type f -name "*.php" | xargs ls -la

# Clear cache
rm -rf cache/*

# Check database tables
mysql -u [user] -p [database] -e "SHOW TABLES LIKE 'epm_%';"

# Test API endpoint
curl -X GET "https://your-suitecrm.com/Api/V8/modules" \
  -H "Authorization: Bearer [token]"
```

## Post-Installation

After successful installation:

1. **Train Users**: Provide training on new modules
2. **Import Data**: Import existing estate planning data
3. **Set Up Workflows**: Configure any automated processes
4. **Monitor Performance**: Watch for any performance issues
5. **Regular Backups**: Set up automated backup schedule

## Maintenance

Regular maintenance tasks:

- **Weekly**: Check synchronization logs
- **Monthly**: Review user permissions
- **Quarterly**: Update modules if needed
- **Annually**: Full system backup and review

## Support

For support with the installation:

1. Check the troubleshooting section above
2. Review SuiteCRM documentation
3. Contact your system administrator
4. Reach out to the Estate Planning Manager support team

---

**Note**: This installation modifies your SuiteCRM system. Always test in a development environment first and maintain regular backups.
