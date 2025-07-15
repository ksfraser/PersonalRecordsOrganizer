# Data Dictionary – SuiteCRM–WordPress Sync (July 2025)

## Table: epm_suggested_updates
| Field            | Type         | Description                                 |
|------------------|-------------|---------------------------------------------|
| id               | BIGINT(20) PK| Unique identifier                           |
| client_id        | BIGINT(20) FK| Linked client                               |
| section          | VARCHAR(100) | Section/UI area (e.g., contacts, accounts)  |
| field            | VARCHAR(100) | Field name being updated                    |
| old_value        | TEXT (JSON)  | Previous value (JSON for flexibility)       |
| new_value        | TEXT (JSON)  | Suggested value (JSON for flexibility)      |
| notes            | TEXT         | Optional notes                              |
| status           | VARCHAR(50)  | pending/accepted/denied                     |
| created_at       | DATETIME     | When suggestion was created                 |
| updated_at       | DATETIME     | Last update timestamp                       |
| source           | VARCHAR(50)  | Origin of suggestion (suitecrm/wp)          |
| source_record_id | VARCHAR(100) | GUID from SuiteCRM or WP                    |

## Table: epm_clients
| Field         | Type         | Description                |
|---------------|-------------|----------------------------|
| id            | BIGINT(20) PK| Unique identifier          |
| suitecrm_guid | VARCHAR(100) | GUID from SuiteCRM         |
| wp_guid       | VARCHAR(100) | GUID from WordPress        |
| ...           | ...         | Other client fields        |

## Table: epm_contacts
| Field         | Type         | Description                |
|---------------|-------------|----------------------------|
| id            | BIGINT(20) PK| Unique identifier          |
| client_id     | BIGINT(20) FK| Linked client              |
| first_name    | VARCHAR(100) | First name                 |
| last_name     | VARCHAR(100) | Last name                  |
| email         | VARCHAR(100) | Email address              |
| phone_work    | VARCHAR(50)  | Work phone                 |
| phone_mobile  | VARCHAR(50)  | Mobile phone               |
| account_id    | VARCHAR(100) | Linked account             |
| wp_guid       | VARCHAR(100) | GUID from WordPress        |
| ...           | ...         | Other contact fields       |
