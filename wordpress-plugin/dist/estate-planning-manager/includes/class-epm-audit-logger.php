<?php
/**
 * Audit Logger Class
 * 
 * Handles all audit logging operations for Estate Planning Manager
 * Follows Single Responsibility Principle - only handles audit logging
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class EPM_Audit_Logger {
    
    /**
     * Instance of this class
     * @var EPM_Audit_Logger
     */
    private static $instance = null;
    
    /**
     * Get instance (Singleton pattern)
     * @return EPM_Audit_Logger
     */
    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Initialize audit logging
     */
    public function init() {
        // Hook into data changes
        add_action('epm_data_saved', array($this, 'log_data_change'), 10, 4);
        add_action('epm_data_deleted', array($this, 'log_data_deletion'), 10, 3);
        add_action('epm_sharing_changed', array($this, 'log_sharing_change'), 10, 3);
    }
    
    /**
     * Log an action
     */
    public function log_action($user_id, $action, $section = null, $record_id = null, $data = array()) {
        if (!get_option('epm_audit_logging', true)) {
            return false;
        }
        
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_audit_log';
        
        // Get client ID if available
        $client_id = null;
        if ($record_id && $section) {
            $client_id = $this->get_client_id_from_record($section, $record_id);
        }
        
        $log_data = array(
            'user_id' => $user_id,
            'client_id' => $client_id,
            'action' => $action,
            'section' => $section,
            'record_id' => $record_id,
            'new_values' => !empty($data) ? json_encode($data) : null,
            'ip_address' => $this->get_client_ip(),
            'user_agent' => $this->get_user_agent()
        );
        
        return $wpdb->insert(
            $table_name,
            $log_data,
            array('%d', '%d', '%s', '%s', '%d', '%s', '%s', '%s')
        );
    }
    
    /**
     * Log data change
     */
    public function log_data_change($user_id, $section, $record_id, $data) {
        // Get old values for comparison
        $old_data = $this->get_old_data($section, $record_id);
        
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_audit_log';
        
        $client_id = $this->get_client_id_from_record($section, $record_id);
        
        $log_data = array(
            'user_id' => $user_id,
            'client_id' => $client_id,
            'action' => 'data_update',
            'section' => $section,
            'record_id' => $record_id,
            'old_values' => $old_data ? json_encode($old_data) : null,
            'new_values' => json_encode($data),
            'ip_address' => $this->get_client_ip(),
            'user_agent' => $this->get_user_agent()
        );
        
        return $wpdb->insert(
            $table_name,
            $log_data,
            array('%d', '%d', '%s', '%s', '%d', '%s', '%s', '%s', '%s')
        );
    }
    
    /**
     * Log data deletion
     */
    public function log_data_deletion($user_id, $section, $record_id) {
        // Get data before deletion
        $deleted_data = $this->get_old_data($section, $record_id);
        
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_audit_log';
        
        $client_id = $this->get_client_id_from_record($section, $record_id);
        
        $log_data = array(
            'user_id' => $user_id,
            'client_id' => $client_id,
            'action' => 'data_delete',
            'section' => $section,
            'record_id' => $record_id,
            'old_values' => $deleted_data ? json_encode($deleted_data) : null,
            'ip_address' => $this->get_client_ip(),
            'user_agent' => $this->get_user_agent()
        );
        
        return $wpdb->insert(
            $table_name,
            $log_data,
            array('%d', '%d', '%s', '%s', '%d', '%s', '%s', '%s')
        );
    }
    
    /**
     * Log sharing permission change
     */
    public function log_sharing_change($user_id, $client_id, $sharing_data) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_audit_log';
        
        $log_data = array(
            'user_id' => $user_id,
            'client_id' => $client_id,
            'action' => 'sharing_change',
            'section' => 'sharing_permissions',
            'new_values' => json_encode($sharing_data),
            'ip_address' => $this->get_client_ip(),
            'user_agent' => $this->get_user_agent()
        );
        
        return $wpdb->insert(
            $table_name,
            $log_data,
            array('%d', '%d', '%s', '%s', '%s', '%s', '%s')
        );
    }
    
    /**
     * Get audit logs for a client
     */
    public function get_client_audit_logs($client_id, $limit = 50, $offset = 0) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_audit_log';
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT al.*, u.display_name as user_name 
             FROM $table_name al 
             LEFT JOIN {$wpdb->users} u ON al.user_id = u.ID 
             WHERE al.client_id = %d 
             ORDER BY al.created_at DESC 
             LIMIT %d OFFSET %d",
            $client_id,
            $limit,
            $offset
        ));
    }
    
    /**
     * Get audit logs for a user
     */
    public function get_user_audit_logs($user_id, $limit = 50, $offset = 0) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_audit_log';
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT al.*, u.display_name as user_name 
             FROM $table_name al 
             LEFT JOIN {$wpdb->users} u ON al.user_id = u.ID 
             WHERE al.user_id = %d 
             ORDER BY al.created_at DESC 
             LIMIT %d OFFSET %d",
            $user_id,
            $limit,
            $offset
        ));
    }
    
    /**
     * Get audit logs by action
     */
    public function get_logs_by_action($action, $limit = 50, $offset = 0) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_audit_log';
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT al.*, u.display_name as user_name 
             FROM $table_name al 
             LEFT JOIN {$wpdb->users} u ON al.user_id = u.ID 
             WHERE al.action = %s 
             ORDER BY al.created_at DESC 
             LIMIT %d OFFSET %d",
            $action,
            $limit,
            $offset
        ));
    }
    
    /**
     * Get security-related logs
     */
    public function get_security_logs($limit = 100, $offset = 0) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_audit_log';
        
        $security_actions = array(
            'user_login',
            'user_logout',
            'failed_login',
            'permission_denied',
            'data_access_denied',
            'suspicious_activity'
        );
        
        $placeholders = implode(',', array_fill(0, count($security_actions), '%s'));
        
        $query = $wpdb->prepare(
            "SELECT al.*, u.display_name as user_name 
             FROM $table_name al 
             LEFT JOIN {$wpdb->users} u ON al.user_id = u.ID 
             WHERE al.action IN ($placeholders) 
             ORDER BY al.created_at DESC 
             LIMIT %d OFFSET %d",
            array_merge($security_actions, array($limit, $offset))
        );
        
        return $wpdb->get_results($query);
    }
    
    /**
     * Clean old audit logs
     */
    public function clean_old_logs($days = 365) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_audit_log';
        
        return $wpdb->query($wpdb->prepare(
            "DELETE FROM $table_name WHERE created_at < DATE_SUB(NOW(), INTERVAL %d DAY)",
            $days
        ));
    }
    
    /**
     * Export audit logs
     */
    public function export_logs($client_id = null, $start_date = null, $end_date = null) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_audit_log';
        
        $where_conditions = array();
        $params = array();
        
        if ($client_id) {
            $where_conditions[] = 'al.client_id = %d';
            $params[] = $client_id;
        }
        
        if ($start_date) {
            $where_conditions[] = 'al.created_at >= %s';
            $params[] = $start_date;
        }
        
        if ($end_date) {
            $where_conditions[] = 'al.created_at <= %s';
            $params[] = $end_date;
        }
        
        $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
        
        $query = "SELECT al.*, u.display_name as user_name 
                  FROM $table_name al 
                  LEFT JOIN {$wpdb->users} u ON al.user_id = u.ID 
                  $where_clause 
                  ORDER BY al.created_at DESC";
        
        if (!empty($params)) {
            $query = $wpdb->prepare($query, $params);
        }
        
        return $wpdb->get_results($query);
    }
    
    /**
     * Get client IP address
     */
    private function get_client_ip() {
        $ip_keys = array(
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        );
        
        foreach ($ip_keys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    
                    if (filter_var($ip, FILTER_VALIDATE_IP, 
                        FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'unknown';
    }
    
    /**
     * Get user agent
     */
    private function get_user_agent() {
        return isset($_SERVER['HTTP_USER_AGENT']) ? 
            substr($_SERVER['HTTP_USER_AGENT'], 0, 255) : 'unknown';
    }
    
    /**
     * Get client ID from record
     */
    private function get_client_id_from_record($section, $record_id) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_' . $section;
        
        return $wpdb->get_var($wpdb->prepare(
            "SELECT client_id FROM $table_name WHERE id = %d",
            $record_id
        ));
    }
    
    /**
     * Get old data for comparison
     */
    private function get_old_data($section, $record_id) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_' . $section;
        
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d",
            $record_id
        ), ARRAY_A);
    }
    
    /**
     * Generate audit report
     */
    public function generate_audit_report($client_id, $start_date = null, $end_date = null) {
        $logs = $this->export_logs($client_id, $start_date, $end_date);
        
        $report = array(
            'client_id' => $client_id,
            'generated_at' => current_time('mysql'),
            'period' => array(
                'start' => $start_date ?: 'All time',
                'end' => $end_date ?: 'Present'
            ),
            'summary' => array(
                'total_actions' => count($logs),
                'unique_users' => count(array_unique(array_column($logs, 'user_id'))),
                'sections_accessed' => count(array_unique(array_filter(array_column($logs, 'section')))),
                'data_changes' => count(array_filter($logs, function($log) {
                    return in_array($log->action, array('data_update', 'data_delete'));
                }))
            ),
            'logs' => $logs
        );
        
        return $report;
    }
    
    /**
     * Check for suspicious activity
     */
    public function check_suspicious_activity($user_id, $timeframe = 3600) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'epm_audit_log';
        
        // Check for rapid successive actions
        $rapid_actions = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name 
             WHERE user_id = %d 
             AND created_at > DATE_SUB(NOW(), INTERVAL %d SECOND)",
            $user_id,
            $timeframe
        ));
        
        // Check for failed login attempts
        $failed_logins = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name 
             WHERE user_id = %d 
             AND action = 'failed_login' 
             AND created_at > DATE_SUB(NOW(), INTERVAL %d SECOND)",
            $user_id,
            $timeframe
        ));
        
        $suspicious = false;
        $reasons = array();
        
        if ($rapid_actions > 50) {
            $suspicious = true;
            $reasons[] = 'Rapid successive actions detected';
        }
        
        if ($failed_logins > 5) {
            $suspicious = true;
            $reasons[] = 'Multiple failed login attempts';
        }
        
        if ($suspicious) {
            $this->log_action(
                $user_id,
                'suspicious_activity',
                'security',
                null,
                array('reasons' => $reasons)
            );
        }
        
        return array(
            'suspicious' => $suspicious,
            'reasons' => $reasons,
            'rapid_actions' => $rapid_actions,
            'failed_logins' => $failed_logins
        );
    }
}
