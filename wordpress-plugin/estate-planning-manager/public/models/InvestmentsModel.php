<?php
namespace EstatePlanningManager\Models;

if (!defined('ABSPATH')) exit;

class InvestmentsModel {
    public static function getByClientId($clientId) {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_investments';
        return $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE client_id = %d", $clientId));
    }
}
