# User Acceptance Tests – SuiteCRM–WordPress API Admin Config

## 1. WordPress Plugin Admin Config Screen

### Test Case: Admin Can Access SuiteCRM Integration Settings
- **Given**: A user with `manage_options` capability
- **When**: The user navigates to Estate Planning Manager > SuiteCRM Integration
- **Then**: The SuiteCRM Integration settings page is displayed

### Test Case: Admin Can Save API Credentials
- **Given**: The SuiteCRM Integration settings page is open
- **When**: The admin enters valid API URL, username, and password and clicks Save
- **Then**: The settings are stored in `wp_options` and a success message is shown

### Test Case: Invalid Inputs Are Rejected
- **Given**: The settings page is open
- **When**: The admin enters an invalid URL or leaves required fields blank
- **Then**: Validation errors are displayed and settings are not saved

### Test Case: OAuth2 Configuration
- **Given**: OAuth2 fields are present
- **When**: The admin enters valid OAuth2 credentials and initiates connection
- **Then**: The plugin completes the OAuth2 flow and stores tokens securely

### Test Case: Only Authorized Users Can Access Settings
- **Given**: A user without `manage_options` capability
- **When**: The user tries to access the SuiteCRM Integration settings page
- **Then**: Access is denied

---

## 2. SuiteCRM Admin Config Screen

### Test Case: Admin Can Access WordPress Integration Settings
- **Given**: A SuiteCRM admin user
- **When**: The user navigates to Admin > WordPress Integration
- **Then**: The WordPress Integration settings page is displayed

### Test Case: Admin Can Save API Credentials
- **Given**: The WordPress Integration settings page is open
- **When**: The admin enters valid API URL, username, and password and clicks Save
- **Then**: The settings are stored in SuiteCRM config or custom module and a success message is shown

### Test Case: Invalid Inputs Are Rejected
- **Given**: The settings page is open
- **When**: The admin enters an invalid URL or leaves required fields blank
- **Then**: Validation errors are displayed and settings are not saved

### Test Case: OAuth2 Configuration
- **Given**: OAuth2 fields are present
- **When**: The admin enters valid OAuth2 credentials and initiates connection
- **Then**: SuiteCRM completes the OAuth2 flow and stores tokens securely

### Test Case: Only Authorized Users Can Access Settings
- **Given**: A non-admin SuiteCRM user
- **When**: The user tries to access the WordPress Integration settings page
- **Then**: Access is denied

---

## 3. API Usage

### Test Case: API Uses Stored Credentials
- **Given**: Valid credentials are saved in config
- **When**: The plugin/module makes an API call
- **Then**: The call uses the stored credentials and succeeds

### Test Case: API Fails with Invalid Credentials
- **Given**: Invalid credentials are saved
- **When**: The plugin/module makes an API call
- **Then**: The call fails and an error is logged/displayed

### Test Case: OAuth2 Token Refresh
- **Given**: OAuth2 tokens are expired
- **When**: The plugin/module attempts an API call
- **Then**: The token is refreshed automatically and the call succeeds

---

## 4. Security

### Test Case: Credentials and Tokens Are Stored Securely
- **Given**: Credentials and tokens are saved
- **When**: The system is inspected
- **Then**: Sensitive data is not exposed to unauthorized users

### Test Case: Audit Logging
- **Given**: Admin changes config settings
- **When**: The change is made
- **Then**: The change is logged for audit purposes

---

## 5. UI/UX

### Test Case: UI Is Consistent With Platform Standards
- **Given**: The settings page is displayed
- **When**: The admin interacts with the UI
- **Then**: The UI matches WordPress/SuiteCRM admin styles and is user-friendly

---

## 6. Documentation

### Test Case: Help/Documentation Is Available
- **Given**: The admin settings page is open
- **When**: The admin looks for help or documentation
- **Then**: Contextual help or links to documentation are available
