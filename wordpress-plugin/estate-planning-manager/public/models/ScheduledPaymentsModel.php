<?php
namespace EstatePlanningManager\Models;

if (!defined('ABSPATH')) exit;

class ScheduledPaymentsModel {
    public static function getByClientId($clientId) {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_scheduled_payments';
        return $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE client_id = %d", $clientId));
    }
}
