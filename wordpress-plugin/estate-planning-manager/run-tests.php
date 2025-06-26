<?php
/**
 * Test runner script for Estate Planning Manager
 * 
 * This script provides a simple way to run tests without requiring
 * a full WordPress test environment setup.
 */

// Define constants for testing
define('EPM_TESTING', true);
define('ABSPATH', dirname(__FILE__) . '/');

// Basic WordPress function mocks for testing
if (!function_exists('wp_set_current_user')) {
    function wp_set_current_user($user_id) {
        global $current_user;
        $current_user = (object) array('ID' => $user_id);
        return $current_user;
    }
}

if (!function_exists('get_current_user_id')) {
    function get_current_user_id() {
        global $current_user;
        return isset($current_user->ID) ? $current_user->ID : 0;
    }
}

if (!function_exists('wp_create_nonce')) {
    function wp_create_nonce($action) {
        return md5($action . time());
    }
}

if (!function_exists('wp_verify_nonce')) {
    function wp_verify_nonce($nonce, $action) {
        return !empty($nonce);
    }
}

if (!function_exists('sanitize_text_field')) {
    function sanitize_text_field($str) {
        return strip_tags(trim($str));
    }
}

if (!function_exists('sanitize_email')) {
    function sanitize_email($email) {
        return filter_var($email, FILTER_SANITIZE_EMAIL);
    }
}

if (!function_exists('get_option')) {
    function get_option($option, $default = false) {
        static $options = array();
        return isset($options[$option]) ? $options[$option] : $default;
    }
}

if (!function_exists('update_option')) {
    function update_option($option, $value) {
        static $options = array();
        $options[$option] = $value;
        return true;
    }
}

if (!function_exists('delete_option')) {
    function delete_option($option) {
        static $options = array();
        unset($options[$option]);
        return true;
    }
}

if (!function_exists('plugin_dir_path')) {
    function plugin_dir_path($file) {
        return dirname($file) . '/';
    }
}

if (!function_exists('plugin_dir_url')) {
    function plugin_dir_url($file) {
        return 'http://localhost/wp-content/plugins/' . basename(dirname($file)) . '/';
    }
}

// Mock WordPress database
if (!isset($wpdb)) {
    $wpdb = new stdClass();
    $wpdb->prefix = 'wp_';
    $wpdb->prepare = function($query) {
        $args = func_get_args();
        array_shift($args);
        return vsprintf(str_replace('%s', "'%s'", $query), $args);
    };
    $wpdb->get_results = function($query) { return array(); };
    $wpdb->get_row = function($query) { return null; };
    $wpdb->get_var = function($query) { return null; };
    $wpdb->insert = function($table, $data, $format = null) { return 1; };
    $wpdb->update = function($table, $data, $where, $format = null, $where_format = null) { return 1; };
    $wpdb->query = function($query) { return 1; };
}

// Simple test framework
class SimpleTestCase {
    protected $assertions = 0;
    protected $failures = 0;
    protected $test_name = '';
    
    public function setUp(): void {
        // Override in subclasses
    }
    
    public function tearDown(): void {
        // Override in subclasses
    }
    
    public function assertTrue($condition, $message = '') {
        $this->assertions++;
        if (!$condition) {
            $this->failures++;
            echo "FAIL: {$this->test_name} - {$message}\n";
            return false;
        }
        return true;
    }
    
    public function assertFalse($condition, $message = '') {
        return $this->assertTrue(!$condition, $message);
    }
    
    public function assertEquals($expected, $actual, $message = '') {
        $this->assertions++;
        if ($expected !== $actual) {
            $this->failures++;
            echo "FAIL: {$this->test_name} - {$message} (Expected: " . var_export($expected, true) . ", Actual: " . var_export($actual, true) . ")\n";
            return false;
        }
        return true;
    }
    
    public function assertNotNull($value, $message = '') {
        return $this->assertTrue($value !== null, $message);
    }
    
    public function assertNotEmpty($value, $message = '') {
        return $this->assertTrue(!empty($value), $message);
    }
    
    public function assertArrayHasKey($key, $array, $message = '') {
        return $this->assertTrue(array_key_exists($key, $array), $message);
    }
    
    public function assertStringContains($needle, $haystack, $message = '') {
        return $this->assertTrue(strpos($haystack, $needle) !== false, $message);
    }
    
    public function runTests() {
        $methods = get_class_methods($this);
        $test_methods = array_filter($methods, function($method) {
            return strpos($method, 'test_') === 0;
        });
        
        echo "Running " . count($test_methods) . " tests for " . get_class($this) . "\n";
        echo str_repeat('-', 50) . "\n";
        
        foreach ($test_methods as $method) {
            $this->test_name = $method;
            $this->setUp();
            
            try {
                $this->$method();
                echo "PASS: {$method}\n";
            } catch (Exception $e) {
                $this->failures++;
                echo "ERROR: {$method} - " . $e->getMessage() . "\n";
            }
            
            $this->tearDown();
        }
        
        echo str_repeat('-', 50) . "\n";
        echo "Tests: {$this->assertions}, Failures: {$this->failures}\n";
        
        if ($this->failures > 0) {
            echo "FAILED\n";
            return false;
        } else {
            echo "SUCCESS\n";
            return true;
        }
    }
}

// Basic test for core functionality
class EPM_Core_Test extends SimpleTestCase {
    
    public function test_constants_defined() {
        $this->assertTrue(defined('EPM_TESTING'), 'EPM_TESTING constant should be defined');
        $this->assertTrue(defined('ABSPATH'), 'ABSPATH constant should be defined');
    }
    
    public function test_wordpress_functions_available() {
        $this->assertTrue(function_exists('wp_set_current_user'), 'wp_set_current_user function should be available');
        $this->assertTrue(function_exists('sanitize_text_field'), 'sanitize_text_field function should be available');
        $this->assertTrue(function_exists('get_option'), 'get_option function should be available');
    }
    
    public function test_database_mock_available() {
        global $wpdb;
        $this->assertNotNull($wpdb, 'Global $wpdb should be available');
        $this->assertEquals('wp_', $wpdb->prefix, 'Database prefix should be wp_');
    }
    
    public function test_basic_sanitization() {
        $dirty_input = '<script>alert("xss")</script>Hello World';
        $clean_output = sanitize_text_field($dirty_input);
        $this->assertStringContains('Hello World', $clean_output);
        $this->assertTrue(strpos($clean_output, '<script>') === false, 'Script tags should be removed');
    }
    
    public function test_email_sanitization() {
        $dirty_email = 'test@example.com<script>';
        $clean_email = sanitize_email($dirty_email);
        $this->assertEquals('test@example.com', $clean_email);
    }
    
    public function test_option_functions() {
        $option_name = 'test_option';
        $option_value = 'test_value';
        
        // Test setting option
        $result = update_option($option_name, $option_value);
        $this->assertTrue($result, 'update_option should return true');
        
        // Test getting option
        $retrieved_value = get_option($option_name);
        $this->assertEquals($option_value, $retrieved_value, 'Retrieved option should match set value');
        
        // Test deleting option
        $delete_result = delete_option($option_name);
        $this->assertTrue($delete_result, 'delete_option should return true');
        
        // Test getting deleted option
        $deleted_value = get_option($option_name, 'default');
        $this->assertEquals('default', $deleted_value, 'Deleted option should return default value');
    }
}

// Run the tests
echo "Estate Planning Manager - Test Runner\n";
echo "====================================\n\n";

$core_test = new EPM_Core_Test();
$success = $core_test->runTests();

echo "\n";

if ($success) {
    echo "All core tests passed! The basic testing environment is working.\n";
    echo "\nTo run the full PHPUnit test suite:\n";
    echo "1. Install dependencies: composer install\n";
    echo "2. Set up WordPress test environment\n";
    echo "3. Run: composer test\n";
} else {
    echo "Some core tests failed. Please check the setup.\n";
    exit(1);
}

echo "\nTest files created:\n";
echo "- tests/test-epm-database.php (Database operations)\n";
echo "- tests/test-epm-security.php (Security and permissions)\n";
echo "- tests/test-epm-pdf-generator.php (PDF generation)\n";
echo "- tests/test-epm-audit-logger.php (Audit logging)\n";
echo "- tests/test-epm-suitecrm-api.php (SuiteCRM integration)\n";
echo "- tests/class-epm-test-case.php (Base test case)\n";
echo "- tests/class-epm-test-factory.php (Test data factory)\n";
echo "- phpunit.xml (PHPUnit configuration)\n";
echo "- composer.json (Dependencies and scripts)\n";
