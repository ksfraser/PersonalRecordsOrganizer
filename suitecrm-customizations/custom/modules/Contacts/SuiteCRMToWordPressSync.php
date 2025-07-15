<?php
/**
 * SuiteCRM Logic Hook for WordPress Sync
 * Place in custom/modules/<ModuleName>/SuiteCRMToWordPressSync.php
 */
class SuiteCRMToWordPressSync {
    function syncToWordPress($bean, $event, $arguments) {
        // Load config
        $config = isset($GLOBALS['sugar_config']['epm_wp_api']) ? $GLOBALS['sugar_config']['epm_wp_api'] : [];
        if (empty($config['api_url']) || empty($config['username']) || empty($config['password'])) {
            // Config not set, abort
            return;
        }
        $data = array(
            'module' => $bean->module_name,
            'action' => 'save',
            'sync_source' => 'suitecrm',
            'data' => array(
                'id' => $bean->id, // SuiteCRM GUID
                'first_name' => $bean->first_name,
                'last_name' => $bean->last_name,
                'email' => isset($bean->email1) ? $bean->email1 : '',
                'phone_work' => isset($bean->phone_work) ? $bean->phone_work : '',
                'phone_mobile' => isset($bean->phone_mobile) ? $bean->phone_mobile : '',
                'account_id' => isset($bean->account_id) ? $bean->account_id : '',
                'wp_guid' => isset($bean->wp_guid) ? $bean->wp_guid : '', // For bi-directional sync
                // Add other fields as needed
            )
        );
        $ch = curl_init($config['api_url']);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_USERPWD, $config['username'] . ':' . $config['password']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        // Optionally log $response
    }
}
