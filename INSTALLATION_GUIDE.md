# Estate Planning Manager - Complete Installation Guide

This guide provides step-by-step instructions for installing both the WordPress plugin and SuiteCRM customizations for the Estate Planning Manager system.

## Overview

The Estate Planning Manager consists of two main components:
1. **WordPress Plugin** - The main application for clients and advisors
2. **SuiteCRM Customizations** - Optional CRM integration modules

## Prerequisites

### For WordPress Plugin
- WordPress 5.0 or higher
- PHP 7.3 or higher
- MySQL 5.7 or higher
- 256MB+ PHP memory limit
- SSL certificate (recommended)

### For SuiteCRM Integration (Optional)
- SuiteCRM 7.10 or higher
- Admin access to SuiteCRM
- Command line access to SuiteCRM server

## Part 1: WordPress Plugin Installation

### Option A: Automated Installation (Recommended)

1. **Navigate to the plugin directory**:
   ```bash
   cd wordpress-plugin
   ```

2. **Run the build script**:
   
   **Windows:**
   ```cmd
   build-plugin.bat
   ```
   
   **Linux/Mac:**
   ```bash
   chmod +x build-plugin.sh
   ./build-plugin.sh
   ```

3. **Upload to WordPress**:
   - The script creates `dist/estate-planning-manager.zip`
   - Go to **WordPress Admin > Plugins > Add New**
   - Click **Upload Plugin**
   - Choose the ZIP file and click **Install Now**
   - Click **Activate Plugin**

### Option B: Manual Installation

1. **Copy plugin files**:
   ```bash
   cp -r wordpress-plugin/estate-planning-manager /path/to/wordpress/wp-content/plugins/
   ```

2. **Set permissions**:
   ```bash
   chmod -R 755 /path/to/wordpress/wp-content/plugins/estate-planning-manager
   find /path/to/wordpress/wp-content/plugins/estate-planning-manager -type f -exec chmod 644 {} \;
   ```

3. **Activate in WordPress**:
   - Go to **Plugins** in WordPress admin
   - Find "Estate Planning Manager" and click **Activate**

### Option C: WP-CLI Installation

```bash
# Copy plugin
cp -r wordpress-plugin/estate-planning-manager /path/to/wordpress/wp-content/plugins/

# Activate via WP-CLI
wp plugin activate estate-planning-manager --path=/path/to/wordpress
```

## Part 2: WordPress Plugin Configuration

### Initial Setup

1. **Access Plugin Settings**:
   - Go to **Estate Planning Manager > Settings** in WordPress admin

2. **Configure Basic Settings**:
   - **Company Information**: Enter your firm's details
   - **Security Settings**: Configure encryption preferences
   - **PDF Templates**: Set up document generation options
   - **User Permissions**: Configure access controls

3. **Create User Roles**:
   The plugin automatically creates:
   - **Estate Client**: Can manage their own data
   - **Financial Advisor**: Can manage multiple clients
   - **Administrator**: Full system access

4. **Add Users**:
   - Go to **Users > Add New**
   - Assign appropriate roles to users
   - Configure individual permissions as needed

### Database Tables

The plugin will automatically create all required tables, including:
- `epm_bank_accounts`
- `epm_bank_names` (**new**: for admin-editable bank names by region)
- `epm_bank_location_types` (**new**: for admin-editable bank regions)

### Admin Screens

After activation, new admin screens will be available under Estate Planning Manager:
- **Bank Locations**: Manage regions (Canada/USA/Europe)
- **Bank Names**: Manage banks by region

## Part 3: SuiteCRM Integration (Optional)

### Install SuiteCRM Customizations

1. **Navigate to SuiteCRM customizations**:
   ```bash
   cd suitecrm-customizations
   ```

2. **Copy files to SuiteCRM**:
   ```bash
   # Copy custom modules
   cp -r modules/* /path/to/suitecrm/modules/
   
   # Copy custom language files
   cp -r custom/* /path/to/suitecrm/custom/
   ```

3. **Run installation script**:
   ```bash
   cd /path/to/suitecrm
   php suitecrm-customizations/scripts/install.php
   ```

### Configure SuiteCRM

1. **Enable API v8**:
   - Go to **Admin > System Settings** in SuiteCRM
   - Enable **API v8**
   - Save settings

2. **Create API User**:
   - Go to **Admin > User Management**
   - Create user: `epm_api_user`
   - Set as Admin or create custom role
   - Set strong password

3. **Configure OAuth2**:
   - Go to **Admin > OAuth2 Clients and Tokens**
   - Create new client: "Estate Planning Manager"
   - Generate Client ID and Secret
   - Set redirect URI to your WordPress site

4. **Set up Module Relationships**:
   - Go to **Admin > Studio**
   - Configure relationships between EPM modules and Contacts
   - Add subpanels to Contacts for estate planning data

### Connect WordPress to SuiteCRM

1. **Configure Connection**:
   - Go to **Estate Planning Manager > SuiteCRM Settings** in WordPress
   - Enter SuiteCRM URL and OAuth2 credentials
   - Test the connection
   - Enable synchronization

2. **Configure Sync Settings**:
   - Set synchronization frequency
   - Choose which data to sync
   - Configure conflict resolution preferences

## Part 4: Testing and Verification

### Test WordPress Plugin

1. **Create Test Client**:
   - Add a new user with "Estate Client" role
   - Login as the client
   - Enter sample estate planning data

2. **Test PDF Generation**:
   - Generate a PDF with selected data
   - Verify privacy controls work
   - Check document formatting

3. **Test Admin Functions**:
   - Login as administrator
   - Review audit logs
   - Test data selectors
   - Verify user permissions

### Test SuiteCRM Integration

1. **Verify Modules**:
   - Check that EPM modules appear in SuiteCRM
   - Create test records in each module
   - Verify relationships with Contacts

2. **Test API Connection**:
   - Use API testing tool to verify endpoints
   - Test authentication with OAuth2
   - Verify data can be read and written

3. **Test Synchronization**:
   - Enter data in WordPress
   - Verify it appears in SuiteCRM
   - Make changes in SuiteCRM
   - Check suggested updates in WordPress

## Part 5: Production Deployment

### Security Checklist

- [ ] SSL certificate installed and configured
- [ ] Strong passwords for all user accounts
- [ ] Database user has minimal required permissions
- [ ] File permissions set correctly (755/644)
- [ ] WordPress and plugins updated to latest versions
- [ ] Backup system configured and tested

### Performance Optimization

- [ ] PHP memory limit set to 512MB or higher
- [ ] Database optimized and indexed
- [ ] Caching plugin installed (if needed)
- [ ] Regular database maintenance scheduled

### Backup Strategy

- [ ] Automated daily database backups
- [ ] Weekly full file system backups
- [ ] Backup restoration tested
- [ ] Offsite backup storage configured

## Troubleshooting

### Common WordPress Issues

**Plugin won't activate:**
- Check PHP version (7.3+ required)
- Verify file permissions
- Check WordPress error logs
- Disable other plugins to test for conflicts

**Database errors:**
- Ensure MySQL user has CREATE privileges
- Check database connection settings
- Verify table creation permissions

**PDF generation fails:**
- Check PHP memory limit (256MB minimum)
- Verify write permissions in uploads directory
- Ensure required PHP extensions are installed

### Common SuiteCRM Issues

**Modules don't appear:**
- Check file permissions (755 for directories, 644 for files)
- Run Quick Repair and Rebuild
- Clear browser cache
- Verify module files are in correct location

**API connection fails:**
- Verify API v8 is enabled
- Check OAuth2 client configuration
- Ensure API user has proper permissions
- Review firewall settings

**Sync not working:**
- Check WordPress cron jobs are running
- Verify SuiteCRM API credentials
- Review sync logs for errors
- Test API endpoints manually

### Log Files to Check

- WordPress: `/wp-content/debug.log`
- SuiteCRM: `suitecrm.log`
- PHP: `php_errors.log`
- Web server: Apache/Nginx error logs

## Support and Maintenance

### Regular Maintenance Tasks

**Weekly:**
- Check synchronization logs
- Review system performance
- Monitor user activity

**Monthly:**
- Update WordPress and plugins
- Review user permissions
- Check backup integrity
- Review security logs

**Quarterly:**
- Full system backup and test restoration
- Security audit and penetration testing
- Performance optimization review
- User training and documentation updates

### Getting Support

1. **Documentation**: Review all included documentation files
2. **Logs**: Check WordPress and SuiteCRM error logs
3. **Community**: WordPress and SuiteCRM community forums
4. **Professional**: Contact your system administrator or developer

## Conclusion

The Estate Planning Manager provides a comprehensive solution for managing estate planning records with enterprise-grade security and CRM integration. Following this installation guide ensures a proper setup that will serve your clients and advisors effectively while maintaining the highest standards of data security and privacy compliance.

For ongoing support and updates, maintain regular backups and keep all components updated to their latest versions.
