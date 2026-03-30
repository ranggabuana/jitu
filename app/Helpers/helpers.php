<?php

use App\Models\Setting;

if (!function_exists('setting')) {
    /**
     * Get setting value by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function setting(string $key, $default = null)
    {
        return Setting::get($key, $default);
    }
}

if (!function_exists('formatWhatsAppNumber')) {
    /**
     * Format WhatsApp phone number to international format (62xxx)
     *
     * @param string|null $number
     * @return string
     */
    function formatWhatsAppNumber(?string $number): string
    {
        if (empty($number)) {
            return '6281234567890'; // Default fallback
        }

        // Remove all non-numeric characters
        $number = preg_replace('/[^0-9]/', '', $number);

        // Remove leading zeros
        $number = ltrim($number, '0');

        // If starts with 8, prepend 62
        if (str_starts_with($number, '8')) {
            $number = '62' . $number;
        }
        // If starts with 62, keep as is
        elseif (!str_starts_with($number, '62')) {
            // Assume it's missing the country code, prepend 62
            $number = '62' . $number;
        }

        return $number;
    }
}
