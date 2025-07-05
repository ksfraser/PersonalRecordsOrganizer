<?php
/**
 * Handles the registration and saving of the log level setting in the Estate Planning Manager plugin.
 */
class EPM_LogLevelHandler {
    public static function register() {
        add_action('admin_init', [__CLASS__, 'register_log_level_setting']);
    }

    public static function register_log_level_setting() {
        register_setting('general', 'epm_log_level', [
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => 'info'
        ]);
        add_settings_field(
            'epm_log_level',
            'EPM Log Level',
            [__CLASS__, 'render_log_level_field'],
            'general'
        );
    }

    public static function render_log_level_field() {
        $value = get_option('epm_log_level', 'info');
        echo '<select id="epm_log_level" name="epm_log_level">'
            . '<option value="debug"' . selected($value, 'debug', false) . '>Debug</option>'
            . '<option value="info"' . selected($value, 'info', false) . '>Info</option>'
            . '<option value="warning"' . selected($value, 'warning', false) . '>Warning</option>'
            . '<option value="error"' . selected($value, 'error', false) . '>Error</option>'
            . '</select>';
    }
}
