# ERD – SuiteCRM–WordPress Sync (July 2025)

```
[SuiteCRM Contact] <--> [epm_suggested_updates]
[SuiteCRM Account] <--> [epm_suggested_updates]
[SuiteCRM GUID] <--> [WordPress GUID]

Table: epm_suggested_updates
- id (PK)
- client_id (FK)
- section
- field
- old_value (JSON)
- new_value (JSON)
- notes
- status (pending/accepted/denied)
- created_at
- updated_at
- source (suitecrm/wp)
- source_record_id (SuiteCRM GUID)

Table: epm_clients
- id (PK)
- suitecrm_guid
- wp_guid
- ...
```
