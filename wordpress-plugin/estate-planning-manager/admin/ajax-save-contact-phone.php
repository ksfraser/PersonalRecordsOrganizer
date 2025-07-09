<?php
// AJAX handler for saving a contact phone (add/edit)
if (!defined('ABSPATH')) exit;
require_once __DIR__ . '/../public/models/PhoneLineTypesModel.php';
global $wpdb;
$table = $wpdb->prefix . 'epm_contact_phones';
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$contact_id = isset($_POST['contact_id']) ? intval($_POST['contact_id']) : 0;
$phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
$type_id = isset($_POST['type_id']) ? intval($_POST['type_id']) : 0;
$is_primary = isset($_POST['is_primary']) ? 1 : 0;
if (!$contact_id || !$phone || !$type_id) {
    wp_send_json_error(['message' => 'Missing required fields.']);
}
$data = [
    'contact_id' => $contact_id,
    'phone' => $phone,
    'type_id' => $type_id,
    'is_primary' => $is_primary,
    'lastupdated' => current_time('mysql', 1)
];
if ($id) {
    $wpdb->update($table, $data, ['id' => $id]);
} else {
    $data['created'] = current_time('mysql', 1);
    $wpdb->insert($table, $data);
    $id = $wpdb->insert_id;
}
wp_send_json_success(['id' => $id]);
