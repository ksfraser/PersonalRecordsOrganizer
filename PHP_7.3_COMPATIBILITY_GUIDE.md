# PHP 7.3 Compatibility Guide for Estate Planning Manager

This guide outlines the changes needed to make the Estate Planning Manager plugin compatible with PHP 7.3.

## Current Status

The plugin is currently set to require PHP 7.4+, but most of the code is already compatible with PHP 7.3. Only a few minor adjustments are needed.

## Required Changes

### 1. Plugin Header Update

**File:** `wordpress-plugin/estate-planning-manager/estate-planning-manager.php`

**Change Line 15:**
```php
// FROM:
* Requires PHP: 7.4

// TO:
* Requires PHP: 7.3
```

### 2. Security Class - Random Bytes Function

**File:** `wordpress-plugin/estate-planning-manager/includes/class-epm-security.php`

**Issue:** The `random_bytes()` function is used in `generate_secure_token()` method (line ~280).

**Current Code:**
```php
public function generate_secure_token($length = 32) {
    return bin2hex(random_bytes($length / 2));
}
```

**PHP 7.3 Compatible Replacement:**
```php
public function generate_secure_token($length = 32) {
    // Use WordPress's built-in secure random function for PHP 7.3 compatibility
    if (function_exists('random_bytes')) {
        return bin2hex(random_bytes($length / 2));
    }
    
    // Fallback for older PHP versions
    return bin2hex(openssl_random_pseudo_bytes($length / 2));
}
```

### 3. Documentation Updates

Update all documentation files to reflect PHP 7.3 compatibility:

**Files to Update:**
- `wordpress-plugin/README.md`
- `INSTALLATION_GUIDE.md`
- `suitecrm-customizations/documentation/installation_guide.md`

**Change:**
```markdown
// FROM:
- PHP 7.4 or higher

// TO:
- PHP 7.3 or higher
```

## Implementation Steps

### Step 1: Update Plugin Header

```bash
# Edit the main plugin file
# Change "Requires PHP: 7.4" to "Requires PHP: 7.3"
```

### Step 2: Update Security Class

```bash
# Edit includes/class-epm-security.php
# Replace the generate_secure_token method with the compatible version
```

### Step 3: Update Documentation

```bash
# Update all README and installation guide files
# Change PHP version requirements from 7.4 to 7.3
```

### Step 4: Test Compatibility

```bash
# Test the plugin on a PHP 7.3 environment
# Verify all functions work correctly
# Check for any deprecation warnings
```

## Detailed Code Changes

### 1. Main Plugin File

```php
<?php
/**
 * Plugin Name: Estate Planning Manager
 * Plugin URI: https://github.com/your-repo/estate-planning-manager
 * Description: A comprehensive estate planning records management system with SuiteCRM integration for financial advisors.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://yourwebsite.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: estate-planning-manager
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.3
 * Network: false
 */
```

### 2. Security Class Method

```php
/**
 * Generate secure token
 */
public function generate_secure_token($length = 32) {
    // Use random_bytes if available (PHP 7.0+)
    if (function_exists('random_bytes')) {
        try {
            return bin2hex(random_bytes($length / 2));
        } catch (Exception $e) {
            // Fall through to alternative method
        }
    }
    
    // Fallback using OpenSSL (available in PHP 5.3+)
    if (function_exists('openssl_random_pseudo_bytes')) {
        $bytes = openssl_random_pseudo_bytes($length / 2, $strong);
        if ($strong) {
            return bin2hex($bytes);
        }
    }
    
    // Final fallback using WordPress function
    return substr(str_replace(array('+', '/', '='), '', base64_encode(wp_generate_password($length, true, true))), 0, $length);
}
```

## PHP 7.3 Feature Compatibility Check

### Features NOT Used (Good for 7.3 compatibility):
- ✅ Null coalescing assignment operator (`??=`) - Not used
- ✅ Arrow functions (`fn()`) - Not used
- ✅ Numeric literal separator (`1_000_000`) - Not used
- ✅ Array spread operator in array expressions - Not used
- ✅ `array_key_first()` and `array_key_last()` - Not used

### Features Used That Are Compatible:
- ✅ Null coalescing operator (`??`) - Available in PHP 7.0+
- ✅ Scalar type declarations - Available in PHP 7.0+
- ✅ Return type declarations - Available in PHP 7.0+
- ✅ Class constant visibility - Available in PHP 7.1+
- ✅ Anonymous classes - Available in PHP 7.0+

## Testing Checklist

### PHP 7.3 Environment Testing:
- [ ] Plugin activates without errors
- [ ] Database tables are created successfully
- [ ] User roles are created properly
- [ ] Encryption/decryption functions work
- [ ] PDF generation works
- [ ] SuiteCRM API integration functions
- [ ] All admin interfaces load correctly
- [ ] No PHP warnings or notices

### Specific Function Testing:
- [ ] `EPM_Security::generate_secure_token()` works
- [ ] `EPM_Security::encrypt_data()` and `decrypt_data()` work
- [ ] All database operations complete successfully
- [ ] File upload validation works
- [ ] Rate limiting functions properly

## WordPress Compatibility

The plugin remains compatible with:
- WordPress 5.0+ (no changes needed)
- MySQL 5.7+ (no changes needed)
- All WordPress hooks and filters used are available in WP 5.0+

## Server Requirements Update

### Updated Requirements:
- **PHP:** 7.3 or higher (changed from 7.4)
- **WordPress:** 5.0 or higher
- **MySQL:** 5.7 or higher
- **Memory:** 256MB minimum, 512MB recommended
- **PHP Extensions:**
  - `openssl` (for encryption)
  - `curl` (for SuiteCRM integration)
  - `json` (for data handling)
  - `zip` (for PDF generation)

## Deployment Notes

### For Existing Installations:
- The changes are backward compatible
- No database migrations needed
- Existing encrypted data will continue to work
- No user data will be affected

### For New Installations:
- Can now be installed on PHP 7.3 servers
- All functionality remains the same
- Performance characteristics unchanged

## Verification Script

Create a simple PHP script to verify compatibility:

```php
<?php
// PHP 7.3 Compatibility Check for Estate Planning Manager

echo "PHP Version: " . PHP_VERSION . "\n";

// Check required functions
$required_functions = [
    'openssl_encrypt',
    'openssl_decrypt', 
    'openssl_random_pseudo_bytes',
    'wp_generate_password',
    'base64_encode',
    'base64_decode'
];

foreach ($required_functions as $func) {
    if (function_exists($func)) {
        echo "✅ $func - Available\n";
    } else {
        echo "❌ $func - Missing\n";
    }
}

// Check optional functions
if (function_exists('random_bytes')) {
    echo "✅ random_bytes - Available (preferred)\n";
} else {
    echo "⚠️ random_bytes - Not available (will use fallback)\n";
}

echo "\nCompatibility: " . (version_compare(PHP_VERSION, '7.3.0', '>=') ? "✅ Compatible" : "❌ Incompatible") . "\n";
?>
```

## Summary

The Estate Planning Manager plugin requires minimal changes to be compatible with PHP 7.3:

1. **Update plugin header** to reflect PHP 7.3 requirement
2. **Modify one method** in the security class for better compatibility
3. **Update documentation** to reflect new requirements

All other code is already compatible with PHP 7.3, making this a straightforward update that maintains full functionality while expanding server compatibility.
