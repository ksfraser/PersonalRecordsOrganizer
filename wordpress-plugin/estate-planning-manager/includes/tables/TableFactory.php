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

class TableFactory {
    public static function getTables() {
        return [
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
        ];
    }
}
