# SuiteCRM–WordPress API Example Code

## 1. WordPress REST API Endpoint for SuiteCRM Sync

Add to your plugin (e.g., in `class-epm-suitecrm-api.php`):

```php
add_action('rest_api_init', function() {
    register_rest_route('epm/v1', '/suitecrm-sync', array(
        'methods' => 'POST',
        'callback' => 'epm_handle_suitecrm_sync',
        'permission_callback' => 'epm_suitecrm_api_permission',
    ));
});

function epm_suitecrm_api_permission() {
    // Check nonce, API key, or capability
    return current_user_can('manage_options');
}

function epm_handle_suitecrm_sync($request) {
    $params = $request->get_json_params();
    // Validate and sanitize $params
    // Map module and action
    // Save/update record in WP
    // Log and audit
    // Prevent sync loop: if $params['sync_source'] == 'suitecrm', do NOT trigger outbound sync
    if (isset($params['sync_source']) && $params['sync_source'] === 'suitecrm') {
        // Save/update locally only
        return rest_ensure_response(['status' => 'success', 'note' => 'No outbound sync triggered']);
    }
    // Otherwise, normal logic
    return rest_ensure_response(['status' => 'success']);
}
```

## 2. SuiteCRM after_save Hook Example

Add to `custom/modules/Contacts/logic_hooks.php`:

```php
$hook_array['after_save'][] = array(
    1,
    'Sync to WordPress',
    'custom/modules/Contacts/SuiteCRMToWordPressSync.php',
    'SuiteCRMToWordPressSync',
    'syncContactToWordPress'
);
```

Create `SuiteCRMToWordPressSync.php`:

```php
class SuiteCRMToWordPressSync {
    function syncContactToWordPress($bean, $event, $arguments) {
        $data = array(
            'module' => 'Contacts',
            'action' => 'save',
            'sync_source' => 'suitecrm', // Prevent sync loop
            'data' => array(
                'id' => $bean->id,
                'full_name' => $bean->full_name,
                'email' => $bean->email1,
                // ...other fields...
            )
        );
        $ch = curl_init('https://your-wordpress-site.com/wp-json/epm/v1/suitecrm-sync');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        // Optionally log $response
    }
}
```

## 3. WordPress to SuiteCRM Sync Example

Add to your plugin (e.g., in `class-epm-suitecrm-api.php`):

```php
function epm_sync_to_suitecrm($module, $data, $sync_source = null) {
    // Prevent sync loop: if $sync_source == 'wordpress', do NOT trigger outbound sync
    if ($sync_source === 'wordpress') {
        // Save/update locally only
        return null;
    }
    $url = 'https://your-suitecrm-site.com/api/v4/module/' . $module;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer YOUR_TOKEN'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    // Optionally log $response
    return $response;
}
```

---

## Notes
- Add error handling, logging, and security checks as needed.
- Update field mappings and authentication for your environment.
- Extend logic hooks and endpoints for additional modules/entities.
- Always set a `sync_source` flag in payloads to prevent infinite sync loops between systems.
