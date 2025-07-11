<?php
namespace EstatePlanningManager\Models;

require_once __DIR__ . '/AbstractSectionModel.php';

class PersonalModel extends AbstractSectionModel {
    public function getTableName() {
        global $wpdb;
        return $wpdb->prefix . 'epm_personal';
    }
    public function getOwnerIdForSection($section, $client_id) {
        return $client_id;
    }
    public function getSummaryFields() {
        return ['id', 'name'];
    }
    public function getFormFields() {
        return [
            ['name' => 'full_legal_name', 'label' => 'Full Legal Name'],
            ['name' => 'date_of_birth', 'label' => 'Date of Birth'],
            ['name' => 'place_of_birth', 'label' => 'Place of Birth'],
            ['name' => 'birth_certificate_location', 'label' => 'Birth Certificate is located'],
            ['name' => 'sin', 'label' => 'SIN'],
            ['name' => 'sin_card_location', 'label' => 'Location of SIN card'],
            ['name' => 'citizenship_countries', 'label' => 'Countries where you are a citizen'],
            ['name' => 'citizenship_papers_location', 'label' => 'Citizenship papers are located'],
            ['name' => 'passports_location', 'label' => 'Passports are located'],
            ['name' => 'drivers_license_location', 'label' => 'Drivers License is located'],
            ['name' => 'marriage_certificate_location', 'label' => 'Marriage Certificate is located'],
            ['name' => 'divorce_papers_location', 'label' => 'Divorce Papers are located'],
        ];
    }
    public static function get_section_key() {
        return 'personal';
    }
    public static function getFieldDefinitions() {
        return [
            'full_legal_name' => [
                'label' => 'Full Legal Name',
                'type' => 'text',
                'required' => true,
                'db_type' => 'VARCHAR(255)'
            ],
            'date_of_birth' => [
                'label' => 'Date of Birth',
                'type' => 'date',
                'required' => true,
                'db_type' => 'DATE'
            ],
            'place_of_birth' => [
                'label' => 'Place of Birth',
                'type' => 'text',
                'required' => false,
                'db_type' => 'VARCHAR(255)'
            ],
            'birth_certificate_location' => [
                'label' => 'Birth Certificate is located',
                'type' => 'text',
                'required' => false,
                'db_type' => 'VARCHAR(255)'
            ],
            'sin' => [
                'label' => 'SIN',
                'type' => 'text',
                'required' => false,
                'db_type' => 'VARCHAR(32)'
            ],
            'sin_card_location' => [
                'label' => 'Location of SIN card',
                'type' => 'text',
                'required' => false,
                'db_type' => 'VARCHAR(255)'
            ],
            'citizenship_countries' => [
                'label' => 'Countries where you are a citizen',
                'type' => 'text',
                'required' => false,
                'db_type' => 'VARCHAR(255)'
            ],
            'citizenship_papers_location' => [
                'label' => 'Citizenship papers are located',
                'type' => 'text',
                'required' => false,
                'db_type' => 'VARCHAR(255)'
            ],
            'passports_location' => [
                'label' => 'Passports are located',
                'type' => 'text',
                'required' => false,
                'db_type' => 'VARCHAR(255)'
            ],
            'drivers_license_location' => [
                'label' => 'Drivers License is located',
                'type' => 'text',
                'required' => false,
                'db_type' => 'VARCHAR(255)'
            ],
            'marriage_certificate_location' => [
                'label' => 'Marriage Certificate is located',
                'type' => 'text',
                'required' => false,
                'db_type' => 'VARCHAR(255)'
            ],
            'divorce_papers_location' => [
                'label' => 'Divorce Papers are located',
                'type' => 'text',
                'required' => false,
                'db_type' => 'VARCHAR(255)'
            ],
        ];
    }
}
