# Entity Mapping: WordPress Tables to SuiteCRM Modules

| WP Table                | SuiteCRM Module         | Notes                                      |
|-------------------------|------------------------|---------------------------------------------|
| epm_accounts            | Accounts               | Institutes/organizations                    |
| epm_contacts            | Contacts/Leads         | People; create Lead if not Contact          |
| epm_gifts               | EPM_Gifts (custom)     | Custom module, links to Contact/Lead        |
| epm_assets              | EPM_Assets (custom)    | Custom module, links to Contact/Lead        |
| epm_liabilities         | EPM_Liabilities (custom)| Custom module, links to Contact/Lead        |
| epm_suggested_updates   | EPM_SuggestedUpdates   | Custom module for sync/audit                |
| ...                     | ...                    | ...                                         |

- All custom modules must have suitecrm_guid and wp_guid fields.
- If Lead is converted to Contact, update all related records and sync GUIDs to WP.
