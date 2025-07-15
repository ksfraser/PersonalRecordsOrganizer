# SuiteCRM–WordPress Sync Requirements (July 2025)

## Functional Requirements
- All SuiteCRM modules (Contacts, Accounts, etc.) can sync changes to WordPress.
- Each sync triggers a suggested update in WordPress for admin review.
- Suggested updates store old and new values as JSON for flexible display.
- Admins can Accept, Deny, or Cancel suggested updates.
- Accept applies the change and marks the update as accepted.
- Deny marks the update as denied.
- Cancel leaves the update pending.
- Sync is bi-directional using GUIDs for record matching.
- Only Financial Advisors have SuiteCRM access; WordPress admins review and approve changes.
- All sync actions are logged for audit.

## Non-Functional Requirements
- Secure API authentication (username/password or OAuth2).
- All config settings are stored securely and editable via admin screens.
- UI for suggested updates uses section-based modals for comparison.
- All changes are traceable and auditable.
- System must prevent infinite sync loops.

## Data Model
- `epm_suggested_updates` table:
  - `id`, `client_id`, `section`, `field`, `old_value`, `new_value`, `notes`, `status`, `created_at`, `updated_at`, `source`, `source_record_id`
- GUID fields in both SuiteCRM and WordPress for record matching.

## Edge Cases
- If no matching client is found, log and skip update.
- If multiple fields change, create separate suggested updates for each field.
- If a record is deleted in SuiteCRM, suggest deletion in WordPress.

## Security
- All API calls require authentication and nonce/capability checks.
- Only authorized users can approve/deny updates.
