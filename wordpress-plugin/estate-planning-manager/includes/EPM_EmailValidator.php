<?php
/**
 * EPM_EmailValidator
 * Validates email addresses for EPM tables and forms.
 */
class EPM_EmailValidator {
    /**
     * Validate email address syntax (RFC 5322 simplified)
     * @param string $email
     * @return bool
     */
    public static function validate($email) {
        return (bool)preg_match('/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/', $email);
    }

    /**
     * Validate email address and optionally check DNS for domain
     * @param string $email
     * @param bool $check_dns
     * @return bool
     */
    public static function validate_with_dns($email, $check_dns = false) {
        if (!self::validate($email)) {
            return false;
        }
        if ($check_dns) {
            $domain = substr(strrchr($email, '@'), 1);
            return checkdnsrr($domain, 'MX') || checkdnsrr($domain, 'A');
        }
        return true;
    }

    /**
     * Validate email address and optionally check DNS for domain
     * @param string $email
     * @param bool $checkDns
     * @return bool
     */
    public static function validateWithDns($email, $checkDns = false) {
        if (!self::validate($email)) {
            return false;
        }
        if ($checkDns) {
            $domain = substr(strrchr($email, '@'), 1);
            return checkdnsrr($domain, 'MX') || checkdnsrr($domain, 'A');
        }
        return true;
    }
}
