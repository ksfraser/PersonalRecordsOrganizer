<?php
/**
 * Handles the admin_post_epm_add_institute action
 */
class EPM_AddInstituteHandler {
    public static function handle() {
        if (!current_user_can('read')) {
            wp_die('Unauthorized', 403);
        }
        if (!isset($_POST['epm_add_institute_nonce']) || !wp_verify_nonce($_POST['epm_add_institute_nonce'], 'epm_add_institute')) {
            wp_die('Invalid nonce', 400);
        }
        $data = [
            'name' => sanitize_text_field($_POST['name'] ?? ''),
            'email' => sanitize_email($_POST['email'] ?? ''),
            'phone' => sanitize_text_field($_POST['phone'] ?? ''),
            'address' => sanitize_text_field($_POST['address'] ?? ''),
            'account_number' => sanitize_text_field($_POST['account_number'] ?? ''),
            'branch' => sanitize_text_field($_POST['branch'] ?? ''),
            'user_id' => get_current_user_id(),
        ];
        require_once dirname(__DIR__, 2) . '/public/models/PeopleModel.php';
        \EstatePlanningManager\Models\PeopleModel::addInstitute($data);
        // For now, just redirect back
        wp_redirect(wp_get_referer() ?: home_url());
        exit;
    }
}
