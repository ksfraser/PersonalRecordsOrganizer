# Estate Planning Manager – Architecture Overview

## 1. High-Level Architecture

```
+-------------------+         +-------------------+         +-------------------+
|   WordPress Core  | <-----> | Estate Planning   | <-----> |   SuiteCRM        |
|                   |         | Manager Plugin    |         |   (API)           |
+-------------------+         +-------------------+         +-------------------+
                                   |   |   |   |
                                   v   v   v   v
                             +---------------------+
                             | Modular Components  |
                             | (Admin, Public, API)|
                             +---------------------+
                                   |   |   |
                                   v   v   v
                             +---------------------+
                             | Database Tables     |
                             +---------------------+
```

## 2. Data Flow Diagram (DFD Level 1)

- User Data Entry → Plugin Handler → DB Table
- DB Table → SuiteCRM API (sync)
- Plugin Handler → Audit Log
- Admin Actions → Plugin Handler → DB Table

## 3. Message Flow (Add Contact Sequence)

1. User opens Add Contact modal and submits form.
2. Modal form data sent via AJAX to plugin handler.
3. Handler validates nonce, permissions, and data.
4. Handler calls ContactModel::getFieldDefinitions() for validation.
5. Data is saved via Table class (ContactsTable).
6. Audit log entry created.
7. If SuiteCRM sync enabled, data is sent to SuiteCRM API.
8. Success/failure message returned to frontend.

## 4. Key UML Class Diagram (Markdown)

```
+---------------------+
| AbstractSectionModel|
+---------------------+
| +getTableName()     |
| +getFieldDefinitions()|
+---------------------+
          ^
          |
+---------------------+      +---------------------+
| ContactModel        |      | PersonModel         |
+---------------------+      +---------------------+
| +getTableName()     |      | +getTableName()     |
| +getFieldDefinitions()|    | +getFieldDefinitions()|
+---------------------+      +---------------------+

+---------------------+
| ContactsTable       |
+---------------------+
| +create()           |
| +populate()         |
+---------------------+
```

## 5. ERD (Entity Relationship Diagram)

```
[Client] 1---* [Contact]
[Client] 1---* [Person]
[Client] 1---* [Institute]
[Contact] *---* [Person] (via relationship table, if needed)
```

## 6. Use Case Example

- Add New Contact: User → Modal → AJAX → Handler → Model → Table → DB → (SuiteCRM Sync)
