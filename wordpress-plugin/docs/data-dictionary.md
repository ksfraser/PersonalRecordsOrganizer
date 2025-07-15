# Data Dictionary

This data dictionary lists key variables (fields/columns) used in the Estate Planning Manager plugin, the classes that refer to them, and a brief description.

| Variable / Field      | Classes Referencing                | Description                                  |
|----------------------|-------------------------------------|----------------------------------------------|
| client_id            | All section models, all table classes | Unique client identifier (foreign key)       |
| id                   | All section models, all table classes | Primary key for each record                  |
| full_name            | PersonModel, ContactModel, PersonTable, ContactsTable | Person or contact's full name                |
| email                | PersonModel, ContactModel, InstituteModel, PersonTable, ContactsTable, OrganizationTable | Email address                                |
| phone                | PersonModel, ContactModel, InstituteModel, PersonTable, ContactsTable, OrganizationTable | Phone number                                 |
| address              | PersonModel, ContactModel, InstituteModel, PersonTable, ContactsTable, OrganizationTable | Mailing address                              |
| name                 | InstituteModel, OrganizationTable    | Name of institute/organization               |
| account_number       | InstituteModel, OrganizationTable    | Account number (for institutes)              |
| branch               | InstituteModel, OrganizationTable    | Branch name/identifier (for institutes)      |
| created              | All table classes                   | Record creation timestamp                    |
| updated/lastupdated  | All table classes                   | Record last update timestamp                 |
| relationship_type_id | ContactsTable                       | Relationship type (foreign key)              |
| is_advisor           | ContactsTable                       | Advisor flag (boolean)                       |
| notes                | PersonTable, OrganizationTable       | Freeform notes                               |
| relationship         | PersonTable                         | Relationship description                     |
| paid_to_person_id    | ScheduledPaymentsModel, ScheduledPaymentsTable | Payment recipient (person, foreign key)      |
| paid_to_org_id       | ScheduledPaymentsModel, ScheduledPaymentsTable | Payment recipient (organization, foreign key)|
| payment_type         | ScheduledPaymentsModel, ScheduledPaymentsTable | Type of scheduled payment                    |
| amount               | ScheduledPaymentsModel, ScheduledPaymentsTable | Payment amount                              |
| due_date             | ScheduledPaymentsModel, ScheduledPaymentsTable | Payment due date                            |
| ...                  | ...                                 | ... (see models/tables for full list)        |

**Note:**
- This table is not exhaustive; see each model/table class for additional fields.
- Field names are case-sensitive and may differ slightly between models and tables.
- For a full cross-reference, see the source code in `public/models/` and `includes/tables/`.
