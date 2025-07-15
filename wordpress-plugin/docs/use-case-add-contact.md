# Use Case: Add New Contact

**Primary Actor:** Estate Client

**Precondition:** User is authenticated and has permission

**Main Flow:**
1. User selects "Add Contact"
2. System displays modal form
3. User enters contact details and submits
4. System validates and saves data
5. System logs action and (optionally) syncs with SuiteCRM
6. System confirms success to user

**Alternate Flows:**
- Validation fails: System displays error and allows correction.
