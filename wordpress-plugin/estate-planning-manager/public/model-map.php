
<?php
/**
 * Centralized section-to-model mapping for Estate Planning Manager
 * @package EstatePlanningManager
 * @phpdoc UML: <<utility>>
 */
namespace EstatePlanningManager;

/**
 * Class ModelMap
 * Utility for section-to-model mapping
 * @phpdoc UML: <<utility>>
 */
class ModelMap {
    /**
     * Get the admin/fk types table mapping array
     * @return array
     */
    public static function getTypesTableMap() {
        return [
            'RelationshipTypesTable',
            'AccountTypesTable',
            'ContactTypesTable',
            'InsuranceCategoryTable',
            'InsuranceTypeTable',
            'PropertyTypesTable',
            'InvestmentTypesTable',
            'PaymentTypesTable',
            'DebtTypesTable',
            'EmploymentStatusTable',
            'DocumentTypesTable',
            'DigitalAssetTypesTable',
            'PersonalPropertyCategoriesTable',
            'BankNamesTable',
            'BankLocationTypesTable',
            'ScheduledPaymentTypesTable',
            'FrequencyTypesTable',
            'SocialMediaPlatformTypesTable',
            'PasswordStorageTypesTable',
            'ContactPhonesTable',
            'ContactEmailsTable',
            'ContactAddressesTable',
            'PhoneLineTypesTable',
        ];
    }
    /**
     * Get the section-to-model mapping array
     * @return array
     */
    public static function getSectionModelMap() {
        return [
            'personal' => '\EstatePlanningManager\Models\PersonalModel',
            'banking' => '\EstatePlanningManager\Models\BankingModel',
            'investments' => '\EstatePlanningManager\Models\InvestmentsModel',
            'real_estate' => '\EstatePlanningManager\Models\RealEstateModel',
            'insurance' => '\EstatePlanningManager\Models\InsuranceModel',
            'scheduled_payments' => '\EstatePlanningManager\Models\ScheduledPaymentsModel',
            'personal_property' => '\EstatePlanningManager\Models\PersonalPropertyModel',
            'auto_property' => '\EstatePlanningManager\Models\AutoModel',
            'emergency_contacts' => '\EstatePlanningManager\Models\EmergencyContactsModel',
            'key_contacts' => '\EstatePlanningManager\Models\KeyContactsModel',
            'family_contacts' => '\EstatePlanningManager\Models\FamilyContactsModel',
            'volunteering' => '\EstatePlanningManager\Models\VolunteeringModel',
            'password_storage' => '\EstatePlanningManager\Models\PasswordManagementModel',
            'digital_assets' => '\EstatePlanningManager\Models\DigitalAssetsModel',
            'social_media_accounts' => '\EstatePlanningManager\Models\SocialMediaAccountsModel',
            'email_accounts' => '\EstatePlanningManager\Models\EmailAccountsModel',
            'hosting_services' => '\EstatePlanningManager\Models\HostingServicesModel',
            'online_accounts' => '\EstatePlanningManager\Models\OnlineAccountsModel',
            'safety_deposit_box' => '\EstatePlanningManager\Models\SafetyDepositBoxModel',
            'debtors' => '\EstatePlanningManager\Models\DebtorsModel',
            'creditors' => '\EstatePlanningManager\Models\CreditorsModel',
            'charitable_gifts' => '\EstatePlanningManager\Models\CharitableGiftsModel',
            'other_contracts' => '\EstatePlanningManager\Models\OtherContractualObligationsModel',
            'employment_records' => '\EstatePlanningManager\Models\EmploymentRecordsModel',
        ];
    }
}
