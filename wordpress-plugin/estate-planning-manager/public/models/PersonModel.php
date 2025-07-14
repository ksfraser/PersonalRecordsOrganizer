<?php
namespace EstatePlanningManager\Models;

class PersonModel {
    public static function getFieldDefinitions() {
        return [
            ['name' => 'full_name', 'label' => 'Name', 'type' => 'text', 'required' => true],
            ['name' => 'email', 'label' => 'Email', 'type' => 'email'],
            ['name' => 'phone', 'label' => 'Phone', 'type' => 'tel'],
            ['name' => 'address', 'label' => 'Address', 'type' => 'text'],
        ];
    }
}
