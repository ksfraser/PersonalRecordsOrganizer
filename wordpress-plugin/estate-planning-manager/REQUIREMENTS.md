# Estate Planning Manager Plugin Requirements

## Database Table Requirements
- All section models referenced in `get_form_sections()` must have:
  - A static `getFieldDefinitions()` method defining the schema.
  - A static `createTable($charset_collate)` method that creates the table using the schema.
- The following tables must exist and match their model schemas:
  - `wp_epm_email_accounts`
  - `wp_epm_hosting_services`
  - All other tables for sections in the plugin.

## Model Requirements
- Each model must:
  - Be in the `EstatePlanningManager\Models` namespace.
  - Implement `getTableName()`, `getFieldDefinitions()`, and `createTable()`.
  - Use PSR-4 autoloading and OOP best practices.

## Testing Requirements
- All models must have PHPUnit unit tests in `tests/`.
- Tests must mock WordPress functions/classes as needed.
- Tests for new/updated models:
  - `test-epm-email-accounts-model.php`
  - `test-epm-hosting-services-model.php`
- All tests must pass before merging changes.

## Admin & UI
- Admin and user CRUD must use AJAX with nonce/capability checks.
- UI must match the data model and use WordPress styles.

## Documentation
- Update this file and the README with any new models, tables, or features.
