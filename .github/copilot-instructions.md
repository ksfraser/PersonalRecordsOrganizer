# Copilot Instructions for Estate Planning Manager

## Project Overview
This repository contains the Estate Planning Manager WordPress plugin and related SuiteCRM/WordPress customizations. The plugin is designed to help users organize personal, family, and financial records, with robust CRUD, onboarding, and admin features.

## Coding Guidelines
- **Namespace:** Use `EstatePlanningManager` for all plugin PHP classes.
- **File Structure:**
  - Models: `public/models/`
  - Section Views: `public/sections/`
  - Admin: `admin/`
  - Assets: `assets/`
  - Tests: `tests/`
- **Shortcodes:** All user-facing sections must have a model in `public/models/` and a view in `public/sections/`.
- **Database:**
  - Each model should have a `getTableName()` and a static `getFieldDefinitions()` method.
  - Table creation should be handled on plugin activation.
- **CRUD:** All admin and user CRUD operations should use AJAX where possible, with proper nonce and capability checks.
- **UI/UX:**
  - Admin screens should use WordPress admin styles.
  - User forms should be grouped like a financial statement: personal/family, assets, liabilities, income, expenses.
  - Use modals for add/edit actions where appropriate.
- **Testing:**
  - Add/maintain unit tests for all new models, tables, and admin features in `tests/`.

## Copilot Usage
- When adding a new section:
  0. Create a unit test for the planned code change.  The unit test should fail at this time since the code isn't written.
  1. Create a model in `public/models/` with required methods.
  2. Create a section view in `public/sections/`.
  3. Add the section to `get_form_sections()` in `public/class-epm-shortcodes.php`.
  4. Ensure table creation logic is present if needed.
  5. Add admin CRUD if the section is editable by admins.
  6. Run the unit test again.  This time it should pass since the code exists.
- When refactoring:
  - Ensure all referenced models/views exist and are mapped.
  - Use output buffering for all shortcode and activation logic.
  - Maintain backward compatibility for existing data.
- When updating onboarding/invite logic:
  - Ensure the correct role is set in invite records and on registration.
  - Use AJAX for invite/accept flows.

## Best Practices
- Use `require_once` for all model/view includes.
- Use WordPress nonces and capability checks for all AJAX and form actions.
- Follow PSR-4 autoloading and OOP best practices.
- Document all new methods and classes with PHPDoc.
- Keep UI consistent with WordPress admin and user experience.
- Use DRY and SOLID principles in all code.
- Use dependency injection where possible.
- Use composer for managing dependencies.
- Use Composer's autoloading for all classes.
- Update the README and documentation with any new features or changes.
- Requirements should not be deleted unless specifically indicated as obsolete. Instead, mark them as deprecated and provide migration paths if necessary.
- Use GitHub issues for tracking bugs, feature requests, and discussions.
- Use Mock classes for unit tests to isolate functionality.
- Use PHPUnit for unit tests and ensure all tests pass before merging changes.  
- Mock Wordpress functions/classes that we are calling to pass Lint.  Wrap in ifndef so that WP code isn't replaced nor conflicted with. Have these mocked functions throw Exceptions in production so we can catch non existant functions
- Include UML documentation in the function and class documentation in the @phpdoc blocks.
- View sections should be modular and reusable across different models.
- View renderers should be responsible for displaying data, not fetching it.
- View renderers should return a string of HTML, not echo it directly.
- Separate business logic from presentation logic as much as possible.
- Separate Data Access Logic from Business Logic as much as possible.




# Estate Planning Manager – AI Coding Agent Instructions

## Architecture & Major Components
- **WordPress Plugin**: Modular, OOP structure. Main entry: `estate-planning-manager.php`.
- **Core Classes**: Located in `includes/` (database, security, SuiteCRM API, PDF generation, audit logging).
- **Section Models & Views**: Each data section (e.g., banking, insurance) has a model in `public/models/` and a view in `public/sections/`. Mapped via `public/model-map.php`.
- **Admin & Frontend**: Admin UI in `admin/`, user-facing UI in `public/`, assets in `assets/`.
- **SuiteCRM Integration**: Sync logic in `includes/class-epm-suitecrm-api.php`, custom modules in `/suitecrm-customizations/`.
- **Testing**: PHPUnit tests in `tests/`, with test factories and base cases for modular coverage.

## Key Patterns & Conventions
- **Section Mapping**: Use `ModelMap::getSectionModelMap()` for section-to-model resolution. All CRUD and UI logic should reference this map.
- **Table Creation**: All tables are created on plugin activation. Table classes are in `includes/tables/`.
- **AJAX & Security**: All CRUD and admin actions use AJAX with nonce and capability checks. See `public/class-epm-ajax-handler.php`.
- **Modular UI**: Section views use a common `renderSectionView()` method. No inline rendering in shortcode handlers.
- **Data Sync**: Data saved in WordPress is automatically synced to SuiteCRM. Sync hooks: `do_action('epm_sync_client_data', ...)`.
- **Audit Logging**: All data changes and security events are logged via `includes/class-epm-audit-logger.php`.
- **Testing**: Add/maintain unit tests for all new features. Use mocks for WordPress functions/classes. Run tests with `composer test` or `php run-tests.php`.

## Developer Workflows
- **Build**: Use `build-plugin.bat` (Windows) or `build-plugin.sh` (Linux/Mac) in `wordpress-plugin/`.
- **Test**: Run all tests with `composer test` or individual files with `phpunit tests/test-epm-database.php`.
- **Debug**: Enable WordPress debug mode and review audit logs. Use modular logging for troubleshooting.
- **SuiteCRM Integration**: Configure via admin settings. Test sync with dedicated test cases in `tests/test-epm-suitecrm-api.php`.

## Project-Specific Practices
- **Namespace**: All PHP classes use `EstatePlanningManager`.
- **Autoloading**: PSR-4 via Composer. All new classes must be autoloadable.
- **Data Flow**: All section data flows through mapped models and views. No direct DB access in views.
- **Extensibility**: Add new sections by updating model map, creating model/view, and adding tests.
- **Compliance**: All code must pass static analysis (see `wp-stubs.php` for WordPress mocks).
- **Documentation**: Update README and UML docs for new features. Use PHPDoc with UML for all classes.

## Integration Points
- **SuiteCRM**: Data sync via API, custom modules in `/suitecrm-customizations/`.
- **PDF Generation**: Templates in `includes/class-epm-pdf-generator.php`.
- **Audit Logging**: All changes tracked for compliance.

## Example: Adding a New Section
1. Create a failing unit test for the new section in `tests/`.
2. Add model to `public/models/` and view to `public/sections/`.
3. Update `ModelMap::getSectionModelMap()` and `get_form_sections()` in `public/class-epm-shortcodes.php`.
4. Ensure table creation logic is present.
5. Add admin CRUD if needed.
6. Run and pass all tests.

## Contact & Support
For questions, open a GitHub issue or contact the maintainer.

## Contact
For questions, open an issue or contact the project maintainer.


