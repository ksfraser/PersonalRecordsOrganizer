<?php
/**
 * Handles the admin_post_epm_save_section action
 */
class EPM_SaveSectionHandler {
    public static function handle() {
        if (!is_user_logged_in()) {
            wp_die('Unauthorized', 403);
        }
        $section = sanitize_text_field($_POST['section'] ?? '');
        $nonce = $_POST['epm_save_section_nonce'] ?? '';
        $redirect_to = isset($_POST['redirect_to']) ? esc_url_raw($_POST['redirect_to']) : home_url();
        if (!$section || !$nonce || !wp_verify_nonce($nonce, 'epm_save_section_' . $section)) {
            wp_die('Invalid nonce', 400);
        }
        $epm_section_models = [
            'personal' => '\EstatePlanningManager\Models\PersonalModel',
            'banking' => '\EstatePlanningManager\Models\BankingModel',
            'investments' => '\EstatePlanningManager\Models\InvestmentsModel',
            'real_estate' => '\EstatePlanningManager\Models\RealEstateModel',
            'insurance' => '\EstatePlanningManager\Models\InsuranceModel',
            'scheduled_payments' => '\EstatePlanningManager\Models\ScheduledPaymentsModel',
            'personal_property' => '\EstatePlanningManager\Models\PersonalPropertyModel',
            'auto_property' => '\EstatePlanningManager\Models\AutoModel',
            'emergency_contacts' => '\EstatePlanningManager\Models\EmergencyContactsModel',
        ];
        if (isset($epm_section_models[$section])) {
            $model_class = $epm_section_models[$section];
            if (class_exists($model_class)) {
                $model = new $model_class();
                $data = $_POST;
                unset($data['action'], $data['section'], $data['epm_save_section_nonce'], $data['redirect_to']);
                $data['client_id'] = get_current_user_id();
                $result = $model->saveRecord($data);
                if ($result) {
                    wp_redirect($redirect_to);
                    exit;
                } else {
                    error_log('[EPM] Save failed for section: ' . $section);
                    wp_die('Failed to save record.', 500);
                }
            }
        }
        wp_die('Invalid section', 400);
    }
}
