<?php
namespace EstatePlanningManager\Models;

class PhoneLineTypesModel {
    public static function getAll() {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_phone_line_types';
        $array_a = defined('ARRAY_A') ? ARRAY_A : 'ARRAY_A';
        return $wpdb->get_results("SELECT id, value, label FROM $table WHERE is_active = 1 ORDER BY sort_order, label", $array_a);
    }
    public static function getOptions() {
        $types = self::getAll();
        $options = [];
        foreach ($types as $type) {
            $options[$type['id']] = $type['label'];
        }
        return $options;
    }
}
