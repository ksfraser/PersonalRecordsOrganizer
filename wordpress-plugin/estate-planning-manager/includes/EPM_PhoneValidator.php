<?php
/**
 * EPM_PhoneValidator
 * Validates international phone numbers for EPM tables and forms.
 */
class EPM_PhoneValidator {
    /**
     * Validate international phone numbers (E.164 and common formats)
     * @param string $phone
     * @return bool
     */
    public static function validate($phone) {
        // Accepts +countrycode, spaces, dashes, parentheses, min 7 digits
        return (bool)preg_match('/^\+?[0-9 .\-()]{7,20}$/', $phone);
    }
}
