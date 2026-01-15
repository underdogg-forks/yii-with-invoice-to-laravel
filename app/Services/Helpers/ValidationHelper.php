<?php

namespace App\Services\Helpers;

class ValidationHelper
{
    /**
     * Validate VAT number for EU countries.
     */
    public function validateVatNumber(string $vat, string $country): bool
    {
        // Remove spaces and convert to uppercase
        $vat = strtoupper(str_replace(' ', '', $vat));
        $country = strtoupper($country);

        // Country-specific VAT validation patterns
        $patterns = [
            'AT' => '/^ATU\d{8}$/',                    // Austria
            'BE' => '/^BE0?\d{9}$/',                   // Belgium
            'BG' => '/^BG\d{9,10}$/',                  // Bulgaria
            'CY' => '/^CY\d{8}[A-Z]$/',                // Cyprus
            'CZ' => '/^CZ\d{8,10}$/',                  // Czech Republic
            'DE' => '/^DE\d{9}$/',                     // Germany
            'DK' => '/^DK\d{8}$/',                     // Denmark
            'EE' => '/^EE\d{9}$/',                     // Estonia
            'EL' => '/^EL\d{9}$/',                     // Greece
            'ES' => '/^ES[A-Z0-9]\d{7}[A-Z0-9]$/',     // Spain
            'FI' => '/^FI\d{8}$/',                     // Finland
            'FR' => '/^FR[A-Z0-9]{2}\d{9}$/',          // France
            'GB' => '/^GB(\d{9}|\d{12}|(GD|HA)\d{3})$/', // United Kingdom
            'HR' => '/^HR\d{11}$/',                    // Croatia
            'HU' => '/^HU\d{8}$/',                     // Hungary
            'IE' => '/^IE\d[A-Z0-9]\d{5}[A-Z]$/',      // Ireland
            'IT' => '/^IT\d{11}$/',                    // Italy
            'LT' => '/^LT(\d{9}|\d{12})$/',            // Lithuania
            'LU' => '/^LU\d{8}$/',                     // Luxembourg
            'LV' => '/^LV\d{11}$/',                    // Latvia
            'MT' => '/^MT\d{8}$/',                     // Malta
            'NL' => '/^NL\d{9}B\d{2}$/',               // Netherlands
            'PL' => '/^PL\d{10}$/',                    // Poland
            'PT' => '/^PT\d{9}$/',                     // Portugal
            'RO' => '/^RO\d{2,10}$/',                  // Romania
            'SE' => '/^SE\d{12}$/',                    // Sweden
            'SI' => '/^SI\d{8}$/',                     // Slovenia
            'SK' => '/^SK\d{10}$/',                    // Slovakia
        ];

        if (!isset($patterns[$country])) {
            return false;
        }

        return preg_match($patterns[$country], $vat) === 1;
    }

    /**
     * Validate IBAN.
     */
    public function validateIban(string $iban): bool
    {
        // Remove spaces and convert to uppercase
        $iban = strtoupper(str_replace(' ', '', $iban));

        // Check length (15-34 characters)
        if (strlen($iban) < 15 || strlen($iban) > 34) {
            return false;
        }

        // Check format (2 letters, 2 digits, rest alphanumeric)
        if (!preg_match('/^[A-Z]{2}\d{2}[A-Z0-9]+$/', $iban)) {
            return false;
        }

        // Move first 4 characters to end
        $rearranged = substr($iban, 4) . substr($iban, 0, 4);

        // Replace letters with numbers (A=10, B=11, etc.)
        $numeric = '';
        for ($i = 0; $i < strlen($rearranged); $i++) {
            $char = $rearranged[$i];
            if (ctype_alpha($char)) {
                $numeric .= (ord($char) - ord('A') + 10);
            } else {
                $numeric .= $char;
            }
        }

        // Calculate mod 97
        return bcmod($numeric, '97') === '1';
    }

    /**
     * Validate phone number.
     */
    public function validatePhoneNumber(string $phone, ?string $country = null): bool
    {
        // Remove common formatting characters
        $cleaned = preg_replace('/[\s\-\(\)\.]+/', '', $phone);

        // Check if it starts with + or 00 (international format)
        if (preg_match('/^(\+|00)\d{7,15}$/', $cleaned)) {
            return true;
        }

        // Check if it's a valid national number (7-15 digits)
        if (preg_match('/^\d{7,15}$/', $cleaned)) {
            return true;
        }

        return false;
    }

    /**
     * Check if email is from disposable email service.
     */
    public function isDisposableEmail(string $email): bool
    {
        $disposableDomains = [
            'tempmail.com',
            '10minutemail.com',
            'guerrillamail.com',
            'mailinator.com',
            'throwaway.email',
            'temp-mail.org',
            'yopmail.com',
        ];

        $domain = substr(strrchr($email, '@'), 1);

        return in_array(strtolower($domain), $disposableDomains);
    }

    /**
     * Validate business rule.
     */
    public function validateBusinessRule(string $rule, mixed $value): bool
    {
        // This can be extended with custom business rules
        return match($rule) {
            'positive' => is_numeric($value) && $value > 0,
            'non_negative' => is_numeric($value) && $value >= 0,
            'future_date' => strtotime($value) > time(),
            'past_date' => strtotime($value) < time(),
            default => true,
        };
    }
}
