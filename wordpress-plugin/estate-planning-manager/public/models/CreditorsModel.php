<?php
namespace EstatePlanningManager\Models;

require_once __DIR__ . '/AbstractSectionModel.php';
require_once __DIR__ . '/Sanitizer.php';
require_once __DIR__ . '/PeopleModel.php';

if (!defined('ABSPATH')) exit;

class CreditorsModel extends AbstractSectionModel {
    public function getTableName() {
        global $wpdb;
        return $wpdb->prefix . 'epm_creditors';
    }
    public function getFormFields() {
        $peopleOptions = $this->getPeopleOptions();
        $orgOptions = $this->getOrganizationOptions();
        $scheduledPaymentOptions = $this->getScheduledPaymentOptions();
        return [
            ['name' => 'person_org', 'label' => 'Creditor Type', 'type' => 'select', 'options' => [
                ['value' => 'person', 'label' => 'Person'],
                ['value' => 'organization', 'label' => 'Organization']
            ]],
            ['name' => 'contact_id_person', 'label' => 'Contact (Person)', 'type' => 'select', 'options' => $peopleOptions],
            ['name' => 'contact_id_org', 'label' => 'Contact (Organization)', 'type' => 'select', 'options' => $orgOptions],
            ['name' => 'add_contact', 'label' => '', 'type' => 'button', 'button_label' => 'Add Contact'],
            ['name' => 'scheduled_payment_id', 'label' => 'Linked Scheduled Payment', 'type' => 'select', 'options' => $scheduledPaymentOptions],
            ['name' => 'account_number', 'label' => 'Account Number', 'type' => 'text'],
            ['name' => 'amount', 'label' => 'Amount', 'type' => 'text'],
            ['name' => 'date_of_loan', 'label' => 'Date of Loan', 'type' => 'date'],
            ['name' => 'document_location', 'label' => 'Document Location', 'type' => 'text'],
            ['name' => 'description', 'label' => 'Description', 'type' => 'textarea'],
        ];
    }
    protected function getPeopleOptions() {
        if (class_exists('EstatePlanningManager\\Models\\PeopleModel')) {
            $opts = [];
            foreach (\EstatePlanningManager\Models\PeopleModel::getAllForDropdown() as $person) {
                $opts[] = [ 'value' => $person['id'], 'label' => $person['full_name'] ];
            }
            return $opts;
        }
        return [];
    }
    protected function getOrganizationOptions() {
        if (class_exists('EstatePlanningManager\\Models\\OrganizationModel')) {
            $opts = [];
            foreach (\EstatePlanningManager\Models\OrganizationModel::getAllForDropdown() as $org) {
                $opts[] = [ 'value' => $org['id'], 'label' => $org['name'] ];
            }
            return $opts;
        }
        return [];
    }
    protected function getScheduledPaymentOptions() {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_scheduled_payments';
        $rows = $wpdb->get_results("SELECT id, payment_type FROM $table ORDER BY payment_type ASC", ARRAY_A);
        $options = [];
        foreach ($rows as $row) {
            $options[] = [ 'value' => $row['id'], 'label' => $row['payment_type'] ];
        }
        return $options;
    }
}
