# Admin Management Screens – Requirements & Overview

## Overview

The Estate Planning Manager plugin provides several admin screens for managing reference and configuration data. These screens are accessible to users with administrator privileges via the WordPress admin interface.

## Functional Requirements

- Admins can add, edit, and delete bank locations (regions) and bank names.
- Admins can add, edit, and delete insurance categories and insurance types.
- Admins can manage which fields are available for user data entry and export.
- All admin CRUD operations use AJAX with nonce and capability checks for security.
- Admin screens use WordPress admin styles for a consistent UI/UX.
- All changes are logged via the audit logger for compliance.

## Admin Screens List

- **Bank Locations:**
  - Add/edit/delete regions (e.g., Canada, USA, Europe)
  - Linked to available banks
- **Bank Names:**
  - Add/edit/delete banks by region
- **Insurance Categories:**
  - Add/edit/delete insurance categories
- **Insurance Types:**
  - Add/edit/delete insurance types
- **Data Selectors:**
  - Configure which fields appear in exports and user forms

## Data Flow (Admin CRUD)

1. Admin opens the relevant management screen in WordPress admin.
2. Admin performs a CRUD action (add, edit, delete).
3. Action is sent via AJAX to the plugin handler with nonce and capability check.
4. Handler updates the relevant table (e.g., BankLocationsTable, InsuranceTypesTable).
5. Audit log entry is created.
6. UI updates to reflect the change.

## Security & Compliance

- All admin actions require proper WordPress capabilities.
- Nonces are used for all AJAX and form actions.
- All changes are logged for audit/compliance.

## Related Classes
- `admin/class-epm-admin.php`
- `admin/class-epm-admin-selectors.php`
- `admin/class-epm-admin-suggested-updates.php`
- `includes/tables/BankLocationsTable.php`
- `includes/tables/BankNamesTable.php`
- `includes/tables/InsuranceCategoriesTable.php`
- `includes/tables/InsuranceTypesTable.php`
