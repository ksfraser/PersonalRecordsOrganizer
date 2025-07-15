# SuiteCRM–WordPress API Integration: Admin Config Requirements

## WordPress Plugin Side

### Admin Screen
- Add a new admin settings page under Estate Planning Manager > SuiteCRM Integration.
- Fields:
  - SuiteCRM API URL
  - SuiteCRM API Username
  - SuiteCRM API Password (or Token)
  - Optionally: OAuth2 Client ID, Client Secret, Auth URL, Token URL
- Use WordPress Settings API for storage (in `wp_options`).
- Validate and sanitize all inputs.
- Restrict access to users with `manage_options` capability.

### Storage
- Store config in a single option (e.g., `epm_suitecrm_api_settings`).
- If OAuth2 is used, store refresh/access tokens securely.

### Example Option Structure
```php
array(
  'api_url' => 'https://suitecrm.example.com/api/v4/',
  'username' => 'apiuser',
  'password' => 'apipass',
  'oauth2_client_id' => '',
  'oauth2_client_secret' => '',
  'oauth2_auth_url' => '',
  'oauth2_token_url' => '',
  'access_token' => '',
  'refresh_token' => '',
)
```

---

## SuiteCRM Side

### Admin Screen
- Add a new admin config screen/module (e.g., in Admin > WordPress Integration).
- Fields:
  - WordPress API URL
  - WordPress API Username
  - WordPress API Password (or Token)
  - Optionally: OAuth2 Client ID, Client Secret, Auth URL, Token URL
- Store config in SuiteCRM config table or custom module.
- Restrict access to SuiteCRM admins.

### Storage
- Store config in SuiteCRM config table or custom module record.
- If OAuth2 is used, store refresh/access tokens securely.

### Example Option Structure
```php
array(
  'api_url' => 'https://wordpress.example.com/wp-json/epm/v1/suitecrm-sync',
  'username' => 'wpuser',
  'password' => 'wppass',
  'oauth2_client_id' => '',
  'oauth2_client_secret' => '',
  'oauth2_auth_url' => '',
  'oauth2_token_url' => '',
  'access_token' => '',
  'refresh_token' => '',
)
```

---

## OAuth2 Option
- If using OAuth2, implement the full authorization code flow.
- Store tokens securely and refresh as needed.
- Add UI for connecting/disconnecting and viewing token status.

---

## Next Steps
- Implement admin screens and config storage on both sides.
- Update API code to use stored config values.
- Document security best practices for credentials and tokens.
