<?php
/**
 * Handles the admin_init action for EPM log level setting and log viewer field
 */
class EPM_AdminInitHandler {
    public static function handle() {
        register_setting('general', 'epm_log_level', [
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => 'info'
        ]);
        add_settings_field(
            'epm_log_level',
            'EPM Log Level',
            function() {
                $value = get_option('epm_log_level', 'info');
                echo '<select id="epm_log_level" name="epm_log_level">'
                    . '<option value="debug"' . selected($value, 'debug', false) . '>Debug</option>'
                    . '<option value="info"' . selected($value, 'info', false) . '>Info</option>'
                    . '<option value="warning"' . selected($value, 'warning', false) . '>Warning</option>'
                    . '<option value="error"' . selected($value, 'error', false) . '>Error</option>'
                    . '</select>';
            },
            'general'
        );
    }
}
