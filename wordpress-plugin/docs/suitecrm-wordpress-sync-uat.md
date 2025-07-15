# UAT – SuiteCRM–WordPress Sync (July 2025)

## Test Cases

### TC01: Sync Contact Change from SuiteCRM
- Given: A contact is updated in SuiteCRM
- When: The sync logic hook fires
- Then: A suggested update appears in WordPress admin for review

### TC02: Admin Accepts Suggested Update
- Given: A pending suggested update
- When: Admin clicks Accept
- Then: The change is applied and status is set to accepted

### TC03: Admin Denies Suggested Update
- Given: A pending suggested update
- When: Admin clicks Deny
- Then: Status is set to denied, no change applied

### TC04: Admin Cancels Review
- Given: A pending suggested update
- When: Admin clicks Cancel
- Then: No change is made

### TC05: Sync Multiple Fields
- Given: Multiple fields are changed in SuiteCRM
- When: Sync fires
- Then: Separate suggested updates are created for each field

### TC06: Sync Record Deletion
- Given: A record is deleted in SuiteCRM
- When: Sync fires
- Then: A suggested deletion appears in WordPress

### TC07: Security – Unauthorized Access
- Given: A user without permissions
- When: They attempt to approve/deny
- Then: Access is denied

### TC08: Audit Logging
- Given: Any sync or admin action
- When: Action occurs
- Then: It is logged for audit

## Sample Data
- Contacts: GUID, first_name, last_name, email, phone, etc.
- Suggested updates: old_value/new_value as JSON

## Traceability Matrix
| Test Case | Requirement | Status |
|----------|-------------|--------|
| TC01     | FR-1, FR-2  | Not Run |
| TC02     | FR-4, FR-5  | Not Run |
| TC03     | FR-6        | Not Run |
| TC04     | FR-7        | Not Run |
| TC05     | FR-8        | Not Run |
| TC06     | FR-9        | Not Run |
| TC07     | NFR-1, NFR-5| Not Run |
| TC08     | NFR-4       | Not Run |
