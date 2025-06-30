<?php
use PHPUnit\Framework\TestCase;

class EPM_Database_GetUserMeta_Test extends TestCase
{
    public function test_returns_value_when_meta_exists()
    {
        $db = EPM_Database::instance();
        $user_id = 123;
        $key = 'test_key';
        // Simulate get_user_meta returns a value
        if (!function_exists('get_user_meta')) {
            function get_user_meta($user_id, $key, $single) {
                if ($user_id === 123 && $key === 'test_key') return 'foo';
                return '';
            }
        }
        $this->assertEquals('foo', $db->get_user_meta($user_id, $key));
    }

    public function test_returns_null_when_meta_empty()
    {
        $db = EPM_Database::instance();
        $user_id = 123;
        $key = 'empty_key';
        // Simulate get_user_meta returns empty string
        if (!function_exists('get_user_meta')) {
            function get_user_meta($user_id, $key, $single) {
                return '';
            }
        }
        $this->assertNull($db->get_user_meta($user_id, $key));
    }

    public function test_returns_null_when_function_missing()
    {
        $db = EPM_Database::instance();
        $user_id = 123;
        $key = 'any_key';
        // Temporarily undefine get_user_meta
        if (function_exists('get_user_meta')) {
            runkit_function_remove('get_user_meta');
        }
        $this->assertNull($db->get_user_meta($user_id, $key));
    }
}
