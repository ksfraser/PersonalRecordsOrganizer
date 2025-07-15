<?php
/**
 * SuiteCRM Admin Config Module for WordPress API Integration
 * Place in custom/modules/EPM_WordPressIntegration/AdminConfig.php
 * Requires SuiteCRM admin access
 */
class EPM_WordPressIntegration_AdminConfig {
    public static function render_admin_page() {
        // Load config
        $config = self::get_config();
        // Handle POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['wp_api_action'])) {
            // Validate admin
            if (!is_admin($GLOBALS['current_user'])) {
                echo '<div class="error"><p>Access denied.</p></div>';
                return;
            }
            // Sanitize
            $api_url = trim($_POST['wp_api_url']);
            $username = trim($_POST['wp_api_username']);
            $password = trim($_POST['wp_api_password']);
            $client_id = trim($_POST['oauth2_client_id']);
            $client_secret = trim($_POST['oauth2_client_secret']);
            $auth_url = trim($_POST['oauth2_auth_url']);
            $token_url = trim($_POST['oauth2_token_url']);
            // Save config
            self::save_config([
                'api_url' => $api_url,
                'username' => $username,
                'password' => $password,
                'oauth2_client_id' => $client_id,
                'oauth2_client_secret' => $client_secret,
                'oauth2_auth_url' => $auth_url,
                'oauth2_token_url' => $token_url,
            ]);
            echo '<div class="success"><p>Settings saved.</p></div>';
            $config = self::get_config();
        }
        // Render form
        echo '<h2>WordPress API Integration Settings</h2>';
        echo '<form method="post">';
        echo '<input type="hidden" name="wp_api_action" value="1">';
        echo '<label>API URL: <input type="text" name="wp_api_url" value="' . htmlspecialchars($config['api_url']) . '" required></label><br>';
        echo '<label>Username: <input type="text" name="wp_api_username" value="' . htmlspecialchars($config['username']) . '" required></label><br>';
        echo '<label>Password: <input type="password" name="wp_api_password" value="' . htmlspecialchars($config['password']) . '" required></label><br>';
        echo '<label>OAuth2 Client ID: <input type="text" name="oauth2_client_id" value="' . htmlspecialchars($config['oauth2_client_id']) . '"></label><br>';
        echo '<label>OAuth2 Client Secret: <input type="text" name="oauth2_client_secret" value="' . htmlspecialchars($config['oauth2_client_secret']) . '"></label><br>';
        echo '<label>OAuth2 Auth URL: <input type="text" name="oauth2_auth_url" value="' . htmlspecialchars($config['oauth2_auth_url']) . '"></label><br>';
        echo '<label>OAuth2 Token URL: <input type="text" name="oauth2_token_url" value="' . htmlspecialchars($config['oauth2_token_url']) . '"></label><br>';
        echo '<button type="submit">Save Settings</button>';
        echo '</form>';
    }
    public static function get_config() {
        // Load from SuiteCRM config or custom table
        // Example: $GLOBALS['sugar_config']['epm_wp_api']
        return isset($GLOBALS['sugar_config']['epm_wp_api']) ? $GLOBALS['sugar_config']['epm_wp_api'] : [
            'api_url' => '',
            'username' => '',
            'password' => '',
            'oauth2_client_id' => '',
            'oauth2_client_secret' => '',
            'oauth2_auth_url' => '',
            'oauth2_token_url' => '',
        ];
    }
    public static function save_config($config) {
        // Save to SuiteCRM config or custom table
        $GLOBALS['sugar_config']['epm_wp_api'] = $config;
        // Optionally persist to DB
    }
}
