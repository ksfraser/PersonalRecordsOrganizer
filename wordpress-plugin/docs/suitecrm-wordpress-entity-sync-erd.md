# ERD: Entity Sync & GUID Management

```
[WP Table: epm_assets] <--> [SuiteCRM Module: EPM_Assets]
[WP Table: epm_gifts] <--> [SuiteCRM Module: EPM_Gifts]
[WP Table: epm_liabilities] <--> [SuiteCRM Module: EPM_Liabilities]
[WP Table: epm_contacts] <--> [SuiteCRM Module: Contacts/Leads]
[WP Table: epm_accounts] <--> [SuiteCRM Module: Accounts]

All modules/tables have suitecrm_guid and wp_guid fields for traceability.
On Lead conversion, all related records update GUIDs and sync to WP.
```
