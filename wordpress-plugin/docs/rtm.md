# Requirements Traceability Matrix (RTM)

| Requirement ID | Description                                 | Source         | Status   | Test Reference                        |
|----------------|---------------------------------------------|----------------|----------|---------------------------------------|
| REQ-01         | All models must not contain createTable      | Coding Guide   | Complete | test-contact-model-requirements.php   |
| REQ-02         | Table creation handled by Table classes      | Coding Guide   | Complete | test-person-model-requirements.php    |
| REQ-03         | Field definitions must be present in models  | Coding Guide   | Complete | test-institute-model-requirements.php |
| REQ-04         | Data sync with SuiteCRM                      | Architecture   | Ongoing  | test-epm-suitecrm-api.php             |
| REQ-05         | Audit logging for all data changes           | Architecture   | Ongoing  | test-epm-audit-logger.php             |
| REQ-06         | Admins can manage bank locations and names via admin screens | Features/Admin UI | Complete | Manual/admin UI test                |
| REQ-07         | Admins can manage insurance categories and types via admin screens | Features/Admin UI | Complete | Manual/admin UI test                |
