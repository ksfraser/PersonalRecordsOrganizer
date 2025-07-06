<?php
/**
 * Handles the admin_post_epm_save_section action
 */
// Ensure all section Models are loaded (in case autoloading is not working)
require_once EPM_PLUGIN_DIR . 'public/models/PersonalModel.php';
require_once EPM_PLUGIN_DIR . 'public/models/BankingModel.php';
require_once EPM_PLUGIN_DIR . 'public/models/InvestmentsModel.php';
require_once EPM_PLUGIN_DIR . 'public/models/RealEstateModel.php';
require_once EPM_PLUGIN_DIR . 'public/models/InsuranceModel.php';
require_once EPM_PLUGIN_DIR . 'public/models/ScheduledPaymentsModel.php';
require_once EPM_PLUGIN_DIR . 'public/models/PersonalPropertyModel.php';
require_once EPM_PLUGIN_DIR . 'public/models/AutoModel.php';
require_once EPM_PLUGIN_DIR . 'public/models/EmergencyContactsModel.php';

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
        // Debug after isset
        if (isset($epm_section_models[$section])) {
            echo '<pre>[EPM DEBUG] isset($epm_section_models[$section]) PASSED for section: ' . htmlspecialchars($section) . '</pre>';
            error_log('[EPM DEBUG] isset($epm_section_models[$section]) PASSED for section: ' . $section);
            $model_class = $epm_section_models[$section];
            if (class_exists($model_class)) {
                echo '<pre>[EPM DEBUG] class_exists PASSED for model_class: ' . htmlspecialchars($model_class) . '</pre>';
                error_log('[EPM DEBUG] class_exists PASSED for model_class: ' . $model_class);
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
            } else {
                echo '<pre>[EPM DEBUG] class_exists FAILED for model_class: ' . htmlspecialchars($model_class) . '</pre>';
                error_log('[EPM DEBUG] class_exists FAILED for model_class: ' . $model_class);
            }
        } else {
            echo '<pre>[EPM DEBUG] isset($epm_section_models[$section]) FAILED for section: ' . htmlspecialchars($section) . '</pre>';
            error_log('[EPM DEBUG] isset($epm_section_models[$section]) FAILED for section: ' . $section);
        }
        // Enhanced debug log and echo for troubleshooting invisible characters
        $section_hex = bin2hex($section);
        $model_keys = array_keys($epm_section_models);
        $model_keys_hex = array_map('bin2hex', $model_keys);
        error_log('[EPM DEBUG] Invalid section: ' . print_r($section, true));
        error_log('[EPM DEBUG] Section (hex): ' . $section_hex);
        error_log('[EPM DEBUG] Model map: ' . print_r($model_keys, true));
        error_log('[EPM DEBUG] Model map (hex): ' . print_r($model_keys_hex, true));
        echo '<pre>[EPM DEBUG] Invalid section: ' . htmlspecialchars(print_r($section, true)) . "\nSection (hex): $section_hex\nModel map: " . htmlspecialchars(print_r($model_keys, true)) . "\nModel map (hex): " . htmlspecialchars(print_r($model_keys_hex, true)) . '</pre>';
        wp_die('Invalid section', 400);
    }
}
