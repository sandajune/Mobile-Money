<?php

namespace App\Helpers;

class PhoneHelper
{
    /**
     * Extract the 3-digit prefix from a phone number
     * @param string $phoneNumber Phone number (e.g., "0331234567")
     * @return string|null The 3-digit prefix or null if invalid
     */
    public static function extractPrefix($phoneNumber)
    {
        if (empty($phoneNumber) || !is_string($phoneNumber)) {
            return null;
        }
        
        // Remove any spaces or special characters
        $clean = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // Extract first 3 digits (assuming Madagascar format: 03X...)
        if (strlen($clean) >= 3 && strpos($clean, '0') === 0) {
            return substr($clean, 0, 3);
        }
        
        return null;
    }

    /**
     * Check if a phone number belongs to an internal operator
     * @param string $phoneNumber Phone number to check
     * @param string $internalOperatorName Name of internal operator (default: 'M-Money')
     * @return bool True if internal, false if external or unknown
     */
    public static function isInternalOperator($phoneNumber, $internalOperatorName = 'M-Money')
    {
        $prefix = self::extractPrefix($phoneNumber);
        if (!$prefix) {
            return false;
        }

        $db = \Config\Database::connect();
        $config = $db->table('config_prefixes')
            ->where('prefixe', $prefix)
            ->where('type_operateur', 'interne')
            ->get()
            ->getRowArray();

        return $config !== null;
    }

    /**
     * Get operator configuration for a phone number
     * @param string $phoneNumber Phone number to check
     * @return array|null Operator configuration or null
     */
    public static function getOperatorConfig($phoneNumber)
    {
        $prefix = self::extractPrefix($phoneNumber);
        if (!$prefix) {
            return null;
        }

        $db = \Config\Database::connect();
        return $db->table('config_prefixes')
            ->where('prefixe', $prefix)
            ->get()
            ->getRowArray();
    }
}
