# Section Sharing & Model Requirements (Updated)

## Section Model Requirements
- Each section model **must implement** a `getFormFields()` method.
    - This method returns an array of all user-facing fields (excluding `id` and `client_id`) for summary tables and forms.
    - Example:
      ```php
      public function getFormFields() {
          return [
              ['name' => 'contact_name', 'label' => 'Contact Name'],
              ['name' => 'relationship', 'label' => 'Relationship'],
              // ...
          ];
      }
      ```
- All summary tables and forms in the UI are generated from `getFormFields()`.
- All models must keep `getFormFields()` in sync with the actual table schema and validation logic.
- Unit tests (PHPUnit) must verify:
    - Each model implements `getFormFields()`.
    - The returned fields match the expected schema for each section.
    - All fields are present in both the summary table and the add/edit form.

## Old Behavior
- Users could only see their own data in each section.
- No support for sharing sections with other users.
- No UI for toggling between own/shared data.

## New Behavior
- Users can share individual sections with other users.
- Main UI now has a selector to toggle between "My Data" and "Shared With Me".
- In "Shared With Me" mode, a dropdown lists only users who have shared data with the logged-in user.
- When a user is selected, only the sections that user has shared are shown, all in read-only mode.
- Sections not shared with the logged-in user do not appear in the shared list.
- All sharing logic is enforced at the data and UI level.

## Database
- New table: `epm_section_shares`
  - `owner_id` (int): The user who owns the data.
  - `shared_with_id` (int): The user with whom the section is shared.
  - `section_key` (varchar): The section being shared (e.g., 'personal', 'banking').

## Implementation
- `EPM_Shortcodes::get_users_who_shared_with($user_id)` returns users who have shared data with the current user.
- `EPM_Shortcodes::get_sections_shared_with_user($owner_id, $viewer_id)` returns section keys shared by $owner_id with $viewer_id.
- UI logic ensures only shared sections are visible in shared mode, and only for selected users.
- All shared data is read-only for the viewer.

## Tests
- Unit tests verify that sharing logic and UI selectors work as intended.
- Tests ensure only shared sections and users appear in the shared view.

## Justification
- Enables secure, granular sharing of estate planning data.
- Prevents unauthorized access or editing of another user's data.
- Improves usability for professionals and families managing shared records.
