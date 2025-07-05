<?php
require_once __DIR__ . '/RelationshipTypesTable.php';
require_once __DIR__ . '/AccountTypesTable.php';
require_once __DIR__ . '/ContactTypesTable.php';
require_once __DIR__ . '/InsuranceCategoriesTable.php';
require_once __DIR__ . '/InsuranceTypesTable.php';
require_once __DIR__ . '/PropertyTypesTable.php';
require_once __DIR__ . '/InvestmentTypesTable.php';
require_once __DIR__ . '/PaymentTypesTable.php';
require_once __DIR__ . '/DebtTypesTable.php';
require_once __DIR__ . '/EmploymentStatusTable.php';
require_once __DIR__ . '/DocumentTypesTable.php';
require_once __DIR__ . '/DigitalAssetTypesTable.php';
require_once __DIR__ . '/PersonalPropertyCategoriesTable.php';
require_once __DIR__ . '/BankAccountsTable.php';
require_once __DIR__ . '/InvestmentsTable.php';
require_once __DIR__ . '/RealEstateTable.php';
require_once __DIR__ . '/PersonalPropertyTable.php';
require_once __DIR__ . '/DigitalAssetsTable.php';
require_once __DIR__ . '/ScheduledPaymentsTable.php';
require_once __DIR__ . '/DebtorsCreditorsTable.php';
require_once __DIR__ . '/InsuranceTable.php';
require_once __DIR__ . '/PersonTable.php';
require_once __DIR__ . '/PersonXrefTable.php';
require_once __DIR__ . '/ClientsTable.php';
require_once __DIR__ . '/UserPreferencesTable.php';
require_once __DIR__ . '/FamilyContactsTable.php';
require_once __DIR__ . '/KeyContactsTable.php';
require_once __DIR__ . '/FamilyContactsTable.php';
require_once __DIR__ . '/KeyContactsTable.php';
require_once __DIR__ . '/SuggestedUpdatesTable.php';
require_once __DIR__ . '/BankNamesTable.php';
require_once __DIR__ . '/BankLocationTypesTable.php';
require_once __DIR__ . '/EmergencyContactsTable.php';
require_once __DIR__ . '/AutoPropertyTable.php';
require_once __DIR__ . '/ContactsTable.php';

class TableFactory {
    public static function getTables() {
        return [
            new ClientsTable(),
            new UserPreferencesTable(),
            new RelationshipTypesTable(),
            new AccountTypesTable(),
            new ContactTypesTable(),
            new InsuranceCategoriesTable(),
            new InsuranceTypesTable(),
            new PropertyTypesTable(),
            new InvestmentTypesTable(),
            new PaymentTypesTable(),
            new DebtTypesTable(),
            new EmploymentStatusTable(),
            new DocumentTypesTable(),
            new DigitalAssetTypesTable(),
            new PersonalPropertyCategoriesTable(),
            new BankAccountsTable(),
            new InvestmentsTable(),
            new RealEstateTable(),
            new PersonalPropertyTable(),
            new DigitalAssetsTable(),
            new ScheduledPaymentsTable(),
            new DebtorsCreditorsTable(),
            new InsuranceTable(),
            new PersonTable(),
            new PersonXrefTable(),
            new FamilyContactsTable(),
            new KeyContactsTable(),
            new SuggestedUpdatesTable(),
            new BankNamesTable(),
            new BankLocationTypesTable(),
            new EmergencyContactsTable(),
            new AutoPropertyTable(),
            new ContactsTable(),
        ];
    }

    public static function dropAllTables() {
        global $wpdb;
        $tables = [
            'epm_investments',
            'epm_bank_accounts',
            'epm_real_estate',
            'epm_personal_property',
            'epm_digital_assets',
            'epm_debtors_creditors',
            'epm_person_xref',
            'epm_insurance',
            'epm_persons',
            'epm_organizations',
            'epm_clients'
        ];
        foreach ($tables as $table) {
            $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}$table");
        }
    }
}
