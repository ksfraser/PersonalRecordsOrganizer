# UAT: Entity Sync & GUID Management

## Test Cases

### TC01: Create Asset Linked to Lead
- Given: Asset is created in WP and synced to SuiteCRM as linked to Lead
- When: Lead is converted to Contact in SuiteCRM
- Then: Asset record is updated to reference new Contact GUID, and WP is updated

### TC02: Create Gift Linked to Contact
- Given: Gift is created in WP and synced to SuiteCRM as linked to Contact
- When: Contact is updated in SuiteCRM
- Then: Gift record is updated and WP is updated

### TC03: Sync GUIDs on Conversion
- Given: Lead is converted to Contact in SuiteCRM
- When: Conversion event fires
- Then: All related records update GUIDs in SuiteCRM and WP

### TC04: Audit Sync Actions
- Given: Any sync or conversion
- When: Action occurs
- Then: It is logged for audit

## Traceability Matrix
| Test Case | Requirement | Status |
|----------|-------------|--------|
| TC01     | R1, R4, R5  | Not Run |
| TC02     | R2, R3, R5  | Not Run |
| TC03     | R6, R7      | Not Run |
| TC04     | R8, R9      | Not Run |
