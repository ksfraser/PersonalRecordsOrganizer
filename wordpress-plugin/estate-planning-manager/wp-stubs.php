<?php
// WordPress function stubs for IDE/static analysis
if (!function_exists('esc_html')) { function esc_html($s) { return $s; } }
if (!function_exists('esc_attr')) { function esc_attr($s) { return $s; } }
if (!function_exists('esc_url')) { function esc_url($s) { return $s; } }
if (!function_exists('esc_textarea')) { function esc_textarea($s) { return $s; } }
if (!function_exists('wp_enqueue_script')) { function wp_enqueue_script($h) {} }
if (!function_exists('wp_enqueue_style')) { function wp_enqueue_style($h) {} }
if (!function_exists('wp_add_inline_script')) { function wp_add_inline_script($h, $s) {} }
if (!function_exists('wp_nonce_field')) { function wp_nonce_field($a, $b) {} }
if (!function_exists('admin_url')) { function admin_url($p = '') { return $p; } }
if (!function_exists('add_shortcode')) { function add_shortcode($a, $b) {} }
if (!function_exists('get_user_by')) { function get_user_by($f, $v) { return false; } }
if (!function_exists('is_user_logged_in')) { function is_user_logged_in() { return true; } }
if (!function_exists('wp_get_current_user')) { function wp_get_current_user() { return (object)['ID'=>1,'user_email'=>'test@example.com','display_name'=>'Test User','roles'=>['administrator']]; } }
if (!function_exists('wp_create_nonce')) { function wp_create_nonce($a) { return 'nonce'; } }
if (!function_exists('wp_registration_url')) { function wp_registration_url() { return '/register'; } }
if (!function_exists('wp_mail')) { function wp_mail($to, $subj, $msg) {} }
if (!function_exists('add_query_arg')) { function add_query_arg($a, $b = null) { return ''; } }
if (!function_exists('maybe_serialize')) { function maybe_serialize($v) { return serialize($v); } }
if (!function_exists('current_time')) { function current_time($t) { return date('Y-m-d H:i:s'); } }
if (!function_exists('get_page_by_path')) { function get_page_by_path($p) { return false; } }
if (!function_exists('wp_insert_post')) { function wp_insert_post($a) { return 0; } }
if (!function_exists('check_ajax_referer')) { function check_ajax_referer($a, $b, $c = false) { return true; } }
if (!function_exists('get_current_user_id')) { function get_current_user_id() { return 1; } }
if (!function_exists('sanitize_email')) { function sanitize_email($e) { return $e; } }
if (!function_exists('sanitize_text_field')) { function sanitize_text_field($t) { return $t; } }
if (!function_exists('wp_send_json_error')) { function wp_send_json_error($m) { die($m); } }
if (!function_exists('wp_send_json_success')) { function wp_send_json_success($m) { die($m); } }
if (!function_exists('wp_generate_password')) { function wp_generate_password($l = 12, $s = false) { return str_repeat('a', $l); } }

if (!class_exists('wpdb')) {
    class wpdb {
        public $prefix = 'wp_';
        public function get_results($query, $output = OBJECT) { return []; }
        public function get_var($query, $x = 0, $y = 0) { return null; }
        public function get_row($query, $output = OBJECT, $y = 0) { return null; }
        public function insert($table, $data, $format = null) { return true; }
        public function update($table, $data, $where, $format = null, $where_format = null) { return true; }
        public function delete($table, $where, $where_format = null) { return true; }
        public function prepare($query, ...$args) { return $query; }
        public function query($query) { return true; }
        public function get_col($query, $column_offset = 0) { return []; }
        public function get_charset_collate() { return 'DEFAULT CHARSET=utf8mb4'; }
        public function insert_id() { return 1; }
        public function esc_like($text) { return $text; }
        public function set_charset($dbh, $charset = null, $collate = null) { return true; }
        public function check_connection($allow_bail = true) { return true; }
        public function flush() { return true; }
        public function replace($table, $data, $format = null) { return true; }
        public function get_blog_prefix($blog_id = null) { return $this->prefix; }
        public function get_table_prefix() { return $this->prefix; }
        public function get_table_charset($table) { return 'utf8mb4'; }
        public function get_table_collation($table) { return 'utf8mb4_unicode_ci'; }
        public function get_server_info() { return '5.7.0'; }
        public function get_client_info() { return 'mysqlnd 5.0.12-dev'; }
        public function get_host_info() { return 'localhost via TCP/IP'; }
        public function get_proto_info() { return '10'; }
        public function get_last_error() { return ''; }
        public function print_error($str = '') { return false; }
        public function show_errors($show = true) { return true; }
        public function hide_errors() { return true; }
        public function suppress_errors($suppress = true) { return true; }
        public function last_error() { return ''; }
        public function last_query() { return ''; }
        public function last_result() { return []; }
        public function num_rows() { return 0; }
        public function rows_affected() { return 0; }
        public function get_charset() { return 'utf8mb4'; }
        public function get_collate() { return 'utf8mb4_unicode_ci'; }
        public function esc_sql($data) { return $data; }
        public function has_cap($db_cap) { return true; }
        public function get_caller() { return 'wpdb'; }
        public function timer_start() { return 0.0; }
        public function timer_stop() { return 0.0; }
        public function timer_elapsed() { return 0.0; }
        public function get_table_from_query($query) { return ''; }
        public function get_column_from_query($query) { return ''; }
        public function get_row_from_query($query) { return null; }
        public function get_var_from_query($query) { return null; }
        public function get_col_info($info_type = 'name', $col_offset = -1) { return null; }
        public function get_col_length($table, $column) { return 255; }
        public function get_col_type($table, $column) { return 'varchar'; }
        public function get_col_charset($table, $column) { return 'utf8mb4'; }
        public function get_col_collation($table, $column) { return 'utf8mb4_unicode_ci'; }
        public function get_col_default($table, $column) { return null; }
        public function get_col_extra($table, $column) { return null; }
        public function get_col_comment($table, $column) { return ''; }
        public function get_col_nullable($table, $column) { return true; }
        public function get_col_unsigned($table, $column) { return false; }
        public function get_col_auto_increment($table, $column) { return false; }
        public function get_col_primary_key($table, $column) { return false; }
        public function get_col_unique_key($table, $column) { return false; }
        public function get_col_index($table, $column) { return false; }
        public function get_col_fulltext_index($table, $column) { return false; }
        public function get_col_spatial_index($table, $column) { return false; }
        public function get_col_foreign_key($table, $column) { return false; }
        public function get_col_references($table, $column) { return null; }
        public function get_col_on_update($table, $column) { return null; }
        public function get_col_on_delete($table, $column) { return null; }
        public function get_col_comment_length($table, $column) { return 0; }
        public function get_col_collation_length($table, $column) { return 0; }
        public function get_col_charset_length($table, $column) { return 0; }
        public function get_col_type_length($table, $column) { return 0; }
        public function get_col_default_length($table, $column) { return 0; }
        public function get_col_extra_length($table, $column) { return 0; }
        public function get_col_nullable_length($table, $column) { return 0; }
        public function get_col_unsigned_length($table, $column) { return 0; }
        public function get_col_auto_increment_length($table, $column) { return 0; }
        public function get_col_primary_key_length($table, $column) { return 0; }
        public function get_col_unique_key_length($table, $column) { return 0; }
        public function get_col_index_length($table, $column) { return 0; }
        public function get_col_fulltext_index_length($table, $column) { return 0; }
        public function get_col_spatial_index_length($table, $column) { return 0; }
        public function get_col_foreign_key_length($table, $column) { return 0; }
        public function get_col_references_length($table, $column) { return 0; }
        public function get_col_on_update_length($table, $column) { return 0; }
        public function get_col_on_delete_length($table, $column) { return 0; }
    }
}
