# Estate Planning Manager: Modular Data & SuiteCRM Integration

## 1. Business Requirements

### 1.1. Centralized, Modular Data Management
- All estate planning data (bank accounts, investments, real estate, insurance, etc.) must be stored in modular, reusable custom tables in WordPress.
- Data structures must be generic and portable, supporting future integration with other platforms (e.g., SuiteCRM, FrontAccounting).

### 1.2. Cross-Platform Data Synchronization
- The system must support two-way synchronization between WordPress and SuiteCRM, ensuring that records are matched and updated across both systems.
- Each record in WordPress must be able to reference its corresponding SuiteCRM record (via `suitecrm_guid`), and vice versa (via `wp_record_id` in SuiteCRM).
- The architecture must allow for future integration with other systems (e.g., FrontAccounting) with minimal changes.

### 1.3. Secure Sharing and Collaboration
- Users must be able to share specific sections of their data with others, with granular permission controls.
- Sharing/invite logic must sync invited users as SuiteCRM contacts or leads, and store SuiteCRM contact/lead IDs in the WordPress database.

### 1.4. Compliance and Maintainability
- The codebase must be modular, maintainable, and compatible with static analysis tools (e.g., Intelephense).
- All major WordPress functions and classes must have stubs to prevent static analysis errors.
- The system must be easily testable, with unit and integration tests for all major components.

---

## 2. Functional Requirements

### 2.1. Modular Table Classes
- Each data table (both selector and user data) must have its own PHP class implementing a common interface (`TableInterface`).
- Each class must provide:
  - `create($charset_collate)`: Creates the table with all required fields, including cross-system GUIDs.
  - `populate($charset_collate)`: Populates the table with default data (if applicable).
- All table classes must be registered in a central `TableFactory` for automatic creation and population.

### 2.2. Cross-System Fields
- All user data tables must include:
  - `suitecrm_guid` (for SuiteCRM record matching)
  - `wp_record_id` (for SuiteCRM to reference the WP record)
- Selector tables do not require cross-system fields.

### 2.3. Data Synchronization Logic
- The system must provide robust sync logic:
  - Push new/updated records from WordPress to SuiteCRM.
  - Pull updates from SuiteCRM and create suggested updates in WordPress.
  - Log all sync operations and errors.
  - Support background and scheduled sync jobs.
- Invited users must be synced as SuiteCRM contacts or leads, and their SuiteCRM IDs stored in the WP database.

### 2.4. Sharing and Permissions
- The sharing/invite system must:
  - Allow users to invite others to view or edit specific sections.
  - Store sharing permissions and invite status in a dedicated table (`epm_share_invites`), including SuiteCRM contact/lead IDs.
  - Sync invited users with SuiteCRM as contacts/leads.

### 2.5. Static Analysis Compatibility
- Provide a `wp-stubs.php` file with stubs for all major WordPress functions and classes, including a comprehensive `wpdb` class, to silence static analysis errors.

### 2.6. User/Admin Features
- Password masking, PDF generation, and user/admin screens must be implemented and tested.
- All features must work seamlessly with the modular data structure.

### 2.7. Normalized Person/Contact Architecture
- All person-like data (advisor, beneficiary, owner, etc.) is now stored in a single normalized table: `epm_persons`.
- User data tables (e.g., insurance, bank accounts, investments, real estate, personal property, scheduled payments) reference people via foreign keys:
  - `beneficiary_person_id`, `advisor_person_id`, `owner_person_id`, etc.
- The `epm_person_xref` table links persons to records/roles for cross-reference and UI support.
- Direct name/email/phone fields for these roles have been removed from user data tables.
- Privacy is enforced: users cannot access or select contacts from other users.
- UI must support auto-fill and person reuse (e.g., dropdowns for existing persons, no duplicate entry required).
- All person data is validated (email with DNS check, international phone support) both frontend and backend.

### 2.8. Normalized Organization/Institute Architecture
- All organization/institute data (e.g., payees, banks, utilities) is now stored in a single normalized table: `epm_organizations`.
- User data tables (e.g., scheduled payments) reference organizations via foreign keys:
  - `paid_to_org_id`, etc.
- Direct org fields (name, address, phone, email, account_number, branch) have been removed from user data tables.
- UI must support organization reuse and auto-fill (e.g., dropdowns for existing orgs, no duplicate entry required).
- All organization data is validated (email with DNS check, international phone support) both frontend and backend.
- Privacy is enforced: users cannot access or select organizations from other users.

### 2.9. DRY Field Definitions (Single Source of Truth)
- All field definitions for each section (Personal, Banking, Investments, Insurance, Real Estate, Scheduled Payments, Auto, Personal Property, Emergency Contacts) must be centralized in the corresponding model class via a static `getFieldDefinitions()` method.
- All other code (views, shortcodes, DB table creation scripts, etc.) must reference this single source of truth for field definitions, never duplicating or hardcoding field arrays elsewhere.
- Automated tests must verify that the model, view, and DB schema are consistent for all field definitions in every section.
- Documentation and developer onboarding must emphasize this DRY approach for maintainability and future extensibility.

---

## 3. Unit and Integration Test Requirements

### 3.1. Table Classes
- Each modular table class must have unit tests verifying:
  - Table creation (schema matches requirements, including GUID fields).
  - Default data population (for selector tables).
  - No errors or warnings during creation/population.

### 3.2. Data Sync Logic
- Unit and integration tests must verify:
  - Correct creation, update, and deletion of records in both WordPress and SuiteCRM.
  - Proper handling of cross-system GUIDs.
  - Accurate logging of sync operations and errors.
  - Correct creation of suggested updates when SuiteCRM data differs from WordPress.

### 3.3. Sharing/Invite Logic
- Tests must verify:
  - Invited users are correctly synced as SuiteCRM contacts/leads.
  - SuiteCRM contact/lead IDs are stored in the `epm_share_invites` table.
  - Permissions and statuses are correctly enforced.

### 3.4. Static Analysis
- The codebase must pass static analysis (e.g., Intelephense) with no missing function/class errors.

---

## 4. Extensibility & Future-Proofing

- The modular table and sync architecture must allow for easy addition of new platforms (e.g., FrontAccounting) by:
  - Adding new GUID fields as needed.
  - Implementing new sync adapters without changing the core data model.
- All business logic must remain platform-agnostic and reusable.

---

**End of Requirements Document**
