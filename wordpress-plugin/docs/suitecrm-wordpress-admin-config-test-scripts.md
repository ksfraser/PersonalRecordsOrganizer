# Test Scripts & Matrix – SuiteCRM–WordPress API Admin Config

## 1. Test Matrix (Requirement Traceability)
| Test ID | Requirement | Test Description | Sample Data | Expected Result |
|--------|-------------|-----------------|------------|-----------------|
| TC01   | WP-Admin-UI | Access WP SuiteCRM Integration settings | N/A | Settings page loads for admin |
| TC02   | WP-Admin-Fields | Enter API URL, username, password | https://suitecrm.example.com/api/v4/, apiuser, apipass | Data saved, success message |
| TC03   | WP-Admin-Validation | Enter invalid URL | not-a-url, apiuser, apipass | Validation error shown |
| TC04   | WP-Admin-OAuth2 | Enter OAuth2 credentials | clientid, secret, authurl, tokenurl | OAuth2 flow completes, tokens stored |
| TC05   | WP-Admin-Access | Non-admin access | N/A | Access denied |
| TC06   | WP-API-Usage | API call with stored credentials | apiuser/apipass | API call succeeds |
| TC07   | WP-API-Fail | API call with invalid credentials | wronguser/wrongpass | API call fails, error logged |
| TC08   | WP-API-OAuth2-Refresh | Expired token | expired_token | Token refreshed, call succeeds |
| TC09   | WP-Security | Inspect storage | N/A | Credentials/tokens not exposed |
| TC10   | WP-Audit | Change config | N/A | Change logged |
| TC11   | WP-UI-UX | Interact with UI | N/A | UI matches WP admin style |
| TC12   | WP-Help | Access help/docs | N/A | Help available |
| TC13   | SC-Admin-UI | Access SuiteCRM WP Integration settings | N/A | Settings page loads for admin |
| TC14   | SC-Admin-Fields | Enter API URL, username, password | https://wordpress.example.com/wp-json/epm/v1/suitecrm-sync, wpuser, wppass | Data saved, success message |
| TC15   | SC-Admin-Validation | Enter invalid URL | not-a-url, wpuser, wppass | Validation error shown |
| TC16   | SC-Admin-OAuth2 | Enter OAuth2 credentials | clientid, secret, authurl, tokenurl | OAuth2 flow completes, tokens stored |
| TC17   | SC-Admin-Access | Non-admin access | N/A | Access denied |
| TC18   | SC-API-Usage | API call with stored credentials | wpuser/wppass | API call succeeds |
| TC19   | SC-API-Fail | API call with invalid credentials | wronguser/wrongpass | API call fails, error logged |
| TC20   | SC-API-OAuth2-Refresh | Expired token | expired_token | Token refreshed, call succeeds |
| TC21   | SC-Security | Inspect storage | N/A | Credentials/tokens not exposed |
| TC22   | SC-Audit | Change config | N/A | Change logged |
| TC23   | SC-UI-UX | Interact with UI | N/A | UI matches SuiteCRM admin style |
| TC24   | SC-Help | Access help/docs | N/A | Help available |

---

## 2. Sample Data
- API URLs: https://suitecrm.example.com/api/v4/, https://wordpress.example.com/wp-json/epm/v1/suitecrm-sync
- Usernames: apiuser, wpuser
- Passwords: apipass, wppass
- OAuth2: clientid, secret, https://auth.example.com, https://token.example.com
- Invalid: not-a-url, wronguser, wrongpass, expired_token

---

## 3. Test Scripts

### TC01: Access WP SuiteCRM Integration Settings
1. Log in as WordPress admin
2. Navigate to Estate Planning Manager > SuiteCRM Integration
3. Verify settings page loads

### TC02: Enter API Credentials (WP)
1. On settings page, enter:
   - API URL: https://suitecrm.example.com/api/v4/
   - Username: apiuser
   - Password: apipass
2. Click Save
3. Verify success message and data stored in wp_options

### TC03: Enter Invalid URL (WP)
1. Enter API URL: not-a-url
2. Enter valid username/password
3. Click Save
4. Verify validation error

### TC04: OAuth2 Flow (WP)
1. Enter OAuth2 credentials
2. Initiate connection
3. Complete authorization
4. Verify tokens stored

### TC05: Non-admin Access (WP)
1. Log in as non-admin
2. Attempt to access settings page
3. Verify access denied

### TC06: API Call With Stored Credentials (WP)
1. Ensure valid credentials are saved
2. Trigger API call
3. Verify call succeeds

### TC07: API Call With Invalid Credentials (WP)
1. Save invalid credentials
2. Trigger API call
3. Verify call fails, error logged

### TC08: OAuth2 Token Refresh (WP)
1. Expire access token
2. Trigger API call
3. Verify token is refreshed and call succeeds

### TC09: Inspect Storage Security (WP)
1. Inspect wp_options
2. Verify credentials/tokens are not exposed to unauthorized users

### TC10: Audit Logging (WP)
1. Change config settings
2. Verify change is logged

### TC11: UI/UX Consistency (WP)
1. Interact with settings UI
2. Verify it matches WP admin style

### TC12: Help/Documentation (WP)
1. Access help/docs from settings page
2. Verify help is available

### TC13–TC24: Repeat above for SuiteCRM side, substituting platform and sample data

---

## 4. Traceability Matrix
| Test ID | Requirement ID | Requirement Description | Status |
|--------|----------------|------------------------|--------|
| TC01   | WP-Admin-UI    | Admin can access settings | Not Run |
| TC02   | WP-Admin-Fields| Admin can save credentials | Not Run |
| TC03   | WP-Admin-Validation | Validation of inputs | Not Run |
| TC04   | WP-Admin-OAuth2| OAuth2 config and flow | Not Run |
| TC05   | WP-Admin-Access| Access control | Not Run |
| ...    | ...            | ...                    | ...    |
| TC24   | SC-Help        | Help/docs available | Not Run |

Update Status as tests are executed.
