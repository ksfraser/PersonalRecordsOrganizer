# SuiteCRM–WordPress Integration Requirements

## Overview
This document describes the requirements and API specifications for bidirectional data synchronization between the Estate Planning Manager WordPress plugin and SuiteCRM.

## Integration Flows

### 1. WordPress to SuiteCRM
- On save in WordPress (user UI or admin), the plugin sends updates to SuiteCRM using the SuiteCRM REST API.
- Supported modules: Contacts, Person, Institute, and other mapped entities.
- All sync actions are logged and auditable.

### 2. SuiteCRM to WordPress
- SuiteCRM modules implement an `after_save` hook to POST updates to a WordPress REST API endpoint.
- Supported modules: Contacts, Person, Institute, and other mapped entities.
- All sync actions are logged and auditable.

## API Specifications

### WordPress REST API Endpoint
- **Endpoint:** `/wp-json/epm/v1/suitecrm-sync`
- **Method:** POST
- **Authentication:** API key or OAuth2 (configurable)
- **Payload Example:**
```json
{
  "module": "Contacts",
  "action": "save",
  "data": {
    "id": "123",
    "full_name": "John Doe",
    "email": "john@example.com",
    ...
  }
}
```
- **Response:**
  - 200 OK: Success
  - 400/401/500: Error details

### SuiteCRM after_save Hook
- **Location:** Each module's logic hooks (e.g., `custom/modules/Contacts/logic_hooks.php`)
- **Action:** On `after_save`, POST updated record to WordPress endpoint
- **Payload:** See above
- **Error Handling:** Log errors and notify admin if sync fails

### WordPress to SuiteCRM API
- **Endpoint:** `/api/v4/module/<ModuleName>` (SuiteCRM REST API)
- **Method:** POST/PUT
- **Authentication:** OAuth2 or API key
- **Payload:**
```json
{
  "id": "123",
  "full_name": "John Doe",
  "email": "john@example.com",
  ...
}
```
- **Response:**
  - 200 OK: Success
  - 400/401/500: Error details

## Logging & Auditing
- All sync actions (success/failure) are logged in both systems.
- Audit logs are accessible to admins for compliance and troubleshooting.

## Security
- All endpoints require authentication and nonce/capability checks.
- Data is validated and sanitized before processing.

## Error Handling
- Sync failures are logged and trigger admin notifications.
- Validation errors return clear messages to the user or API caller.

## Extensibility
- New modules/entities can be added by updating logic hooks and endpoint payloads.
- Mapping is managed via model map in WordPress and SuiteCRM module config.

---

## Next Steps
- Implement REST API endpoint in WordPress (`epm/v1/suitecrm-sync`).
- Add after_save logic hooks in SuiteCRM modules.
- Update model mapping and logging as needed.
