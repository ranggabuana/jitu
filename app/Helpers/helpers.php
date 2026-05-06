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
        
        // 1. Check if it's a holiday in the database
        $isHoliday = Holiday::whereDate('date', $carbonDate->format('Y-m-d'))->exists();
        if ($isHoliday) {
            return false;
        }

        // 2. Check if the day is in the configured work days
        $workDaysJson = Setting::get('work_days');
        if ($workDaysJson) {
            $workDays = json_decode($workDaysJson, true);
            if (is_array($workDays) && !empty($workDays)) {
                return in_array($carbonDate->format('l'), $workDays);
            }
        }

        // Fallback to default (Monday to Friday)
        return !in_array($carbonDate->format('l'), ['Saturday', 'Sunday']);
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
