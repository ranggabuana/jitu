<?php

use App\Models\Setting;
use App\Models\Holiday;
use Carbon\Carbon;

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

if (!function_exists('isWorkDay')) {
    /**
     * Check if a given date is a working day and not a holiday.
     *
     * @param mixed $date
     * @return bool
     */
    function isWorkDay($date): bool
    {
        $carbonDate = Carbon::parse($date);
        $dayName = $carbonDate->format('l'); // e.g., Monday
        
        // 1. Check if it's a holiday in the database
        $isHoliday = Holiday::whereDate('date', $carbonDate->format('Y-m-d'))->exists();
        if ($isHoliday) {
            return false;
        }

        // 2. Check if the day is in the configured work hours (New JSON structure)
        $workHoursJson = Setting::get('work_hours');
        if ($workHoursJson) {
            $workHours = json_decode($workHoursJson, true);
            if (is_array($workHours) && isset($workHours[$dayName])) {
                return isset($workHours[$dayName]['active']) && $workHours[$dayName]['active'] == '1';
            }
        }

        // 3. Fallback to old structure if exists (work_days)
        $workDaysJson = Setting::get('work_days');
        if ($workDaysJson) {
            $workDays = json_decode($workDaysJson, true);
            if (is_array($workDays) && !empty($workDays)) {
                return in_array($dayName, $workDays);
            }
        }

        // Fallback to default (Monday to Friday)
        return !in_array($dayName, ['Saturday', 'Sunday']);
    }
}

if (!function_exists('getNextWorkDay')) {
    /**
     * Get the next available working day from a given date.
     *
     * @param mixed $date
     * @param int $daysToAdd
     * @return Carbon
     */
    function getNextWorkDay($date, int $daysToAdd = 1): Carbon
    {
        $currentDate = Carbon::parse($date);
        $addedDays = 0;

        while ($addedDays < $daysToAdd) {
            $currentDate->addDay();
            if (isWorkDay($currentDate)) {
                $addedDays++;
            }
        }

        return $currentDate;
    }
}

if (!function_exists('maskName')) {
    /**
     * Mask name for privacy (e.g., "John Doe" -> "J**n D*e")
     *
     * @param string|null $name
     * @return string
     */
    function maskName(?string $name): string
    {
        if (!$name) return '-';
        $parts = explode(' ', $name);
        $maskedParts = array_map(function($part) {
            if (strlen($part) <= 2) return $part;
            return substr($part, 0, 1) . str_repeat('*', strlen($part) - 2) . substr($part, -1);
        }, $parts);
        return implode(' ', $maskedParts);
    }
}

if (!function_exists('maskEmail')) {
    /**
     * Mask email for privacy (e.g., "john.doe@example.com" -> "j******e@example.com")
     *
     * @param string|null $email
     * @return string
     */
    function maskEmail(?string $email): string
    {
        if (!$email) return '-';
        $parts = explode('@', $email);
        if (count($parts) < 2) return $email;
        
        $name = $parts[0];
        $domain = $parts[1];
        
        if (strlen($name) <= 2) {
            $maskedName = str_repeat('*', strlen($name));
        } else {
            $maskedName = substr($name, 0, 1) . str_repeat('*', strlen($name) - 2) . substr($name, -1);
        }
        
        return $maskedName . '@' . $domain;
    }
}

if (!function_exists('formatDuration')) {
    /**
     * Format duration in seconds to human readable string
     *
     * @param int $seconds
     * @return string
     */
    function formatDuration($seconds): string
    {
        if ($seconds < 1) return '0 detik';
        
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;
        
        $parts = [];
        if ($hours > 0) $parts[] = $hours . ' jam';
        if ($minutes > 0) $parts[] = $minutes . ' menit';
        if ($secs > 0 || empty($parts)) $parts[] = $secs . ' detik';
        
        return implode(' ', $parts);
    }
}
