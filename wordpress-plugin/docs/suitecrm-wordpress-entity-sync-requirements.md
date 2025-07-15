# Requirements: Entity Sync & GUID Management

- Every WordPress table must have a corresponding SuiteCRM module/table.
- Institutes/organizations map to Accounts.
- People/contacts map to Contacts or Leads (create Lead if not Contact).
- All other tables (assets, liabilities, gifts, etc.) must have SuiteCRM custom modules, each referencing Lead or Contact by GUID.
- If a Lead is converted to a Contact, update all related records and sync GUIDs to WordPress.
- All records must store both SuiteCRM and WordPress GUIDs for traceability.
- Sync logic must update GUIDs on both sides as needed.
- All syncs must be logged and auditable.
- Admin screens must allow review and approval of suggested updates.
- UATs must cover lead conversion, GUID sync, and entity mapping.
