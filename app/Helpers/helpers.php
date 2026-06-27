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

if (!function_exists('resolveDynamicVariable')) {
    /**
     * Resolve a dynamic variable placeholder to its actual value from a DataPerijinan application.
     * Variable format: ${VARIABLE_NAME}
     * Consistent with DocumentGenerator variable mapping.
     *
     * @param \App\Models\DataPerijinan $application
     * @param string $variable  e.g. '${NAMA_PEMOHON}'
     * @return string
     */
    function resolveDynamicVariable(\App\Models\DataPerijinan $application, string $variable): string
    {
        $user = $application->user;
        $perijinan = $application->perijinan;

        // Build full address
        $userAddress = $user->alamat_ktp ?? $user->alamat_domisili ?? '';
        $addressParts = [];
        if ($user->kelurahan) $addressParts[] = 'Kel/Desa ' . $user->kelurahan->name;
        if ($user->kecamatan) $addressParts[] = 'Kec. ' . $user->kecamatan->name;
        if ($user->kabupaten) $addressParts[] = 'Kab/Kota ' . $user->kabupaten->name;
        if ($user->provinsi) $addressParts[] = 'Provinsi ' . $user->provinsi->name;
        $fullAlamat = $userAddress;
        if (!empty($addressParts)) {
            $fullAlamat .= ', ' . implode(', ', $addressParts);
        }

        // Extract pekerjaan from form data
        $pekerjaan = '-';
        if (!empty($application->form_data) && is_array($application->form_data)) {
            foreach ($application->form_data as $fieldId => $value) {
                $field = $perijinan->activeFormFields->firstWhere('id', $fieldId);
                if ($field && in_array(strtolower($field->name), ['pekerjaan', 'jenis_pekerjaan'])) {
                    $pekerjaan = is_array($value) ? implode(', ', $value) : (string) $value;
                    break;
                }
            }
        }

        // System variable map
        $variableMap = [
            '${NAMA_PEMOHON}' => $user->name ?? '-',
            '${NIK}' => $user->nip ?? '-',
            '${USERNAME}' => $user->username ?? '-',
            '${EMAIL}' => $user->email ?? '-',
            '${NO_HP}' => $user->no_hp ?? '-',
            '${PEKERJAAN}' => $pekerjaan,
            '${NAMA_PERUSAHAAN}' => $user->nama_perusahaan ?? '-',
            '${NPWP}' => $user->npwp ?? '-',
            '${ALAMAT_KTP}' => $user->alamat_ktp ?? '-',
            '${ALAMAT_DOMISILI}' => $user->alamat_domisili ?? '-',
            '${ALAMAT_LENGKAP}' => $fullAlamat ?: '-',
            '${PROVINSI}' => $user->provinsi->name ?? '-',
            '${KABUPATEN}' => $user->kabupaten->name ?? '-',
            '${KECAMATAN}' => $user->kecamatan->name ?? '-',
            '${KELURAHAN}' => $user->kelurahan->name ?? '-',
            '${STATUS_PEMOHON}' => $user->status_pemohon ?? '-',
            '${NAMA_IZIN}' => $perijinan->nama_perijinan ?? '-',
            '${NO_REGISTRASI}' => $application->no_registrasi ?? '-',
            '${TANGGAL}' => $application->created_at ? Carbon::parse($application->created_at)->translatedFormat('d F Y') : '-',
            '${TANGGAL_HARI_INI}' => Carbon::now()->translatedFormat('d F Y'),
            '${MASA_AKTIF}' => $application->masa_aktif ? Carbon::parse($application->masa_aktif)->translatedFormat('d F Y') : '-',
            '${NOMOR_SURAT}' => $application->no_registrasi ?? '-',
        ];

        // Direct match from map
        $varUpper = strtoupper($variable);
        if (isset($variableMap[$varUpper])) {
            return $variableMap[$varUpper];
        }

        // Try to resolve from form fields (global/rekom/izin/bo data)
        // Variable format: ${field_name}
        $fieldName = trim($variable, '${}');
        $fieldNameLower = strtolower($fieldName);

        // Search in form_data (global form - keyed by field ID)
        if (!empty($application->form_data) && is_array($application->form_data)) {
            foreach ($application->form_data as $fieldId => $value) {
                $field = $perijinan->activeFormFields->firstWhere('id', $fieldId);
                if ($field && strtolower($field->name) === $fieldNameLower) {
                    return is_array($value) ? implode(', ', $value) : (string) $value;
                }
            }
        }

        // Search in rekom_data
        if (!empty($application->rekom_data) && is_array($application->rekom_data)) {
            if (isset($application->rekom_data[$fieldNameLower]) || isset($application->rekom_data[$fieldName])) {
                $val = $application->rekom_data[$fieldNameLower] ?? $application->rekom_data[$fieldName] ?? '';
                return is_array($val) ? implode(', ', $val) : (string) $val;
            }
        }

        // Search in multi-OPD rekom data
        if (!empty($application->rekom_data_opd) && is_array($application->rekom_data_opd)) {
            foreach ($application->rekom_data_opd as $opdId => $opdData) {
                if (is_array($opdData) && (isset($opdData[$fieldNameLower]) || isset($opdData[$fieldName]))) {
                    $val = $opdData[$fieldNameLower] ?? $opdData[$fieldName] ?? '';
                    return is_array($val) ? implode(', ', $val) : (string) $val;
                }
            }
        }

        // Search in izin_data
        if (!empty($application->izin_data) && is_array($application->izin_data)) {
            if (isset($application->izin_data[$fieldNameLower]) || isset($application->izin_data[$fieldName])) {
                $val = $application->izin_data[$fieldNameLower] ?? $application->izin_data[$fieldName] ?? '';
                return is_array($val) ? implode(', ', $val) : (string) $val;
            }
        }

        // Search in bo_data
        if (!empty($application->bo_data) && is_array($application->bo_data)) {
            if (isset($application->bo_data[$fieldNameLower]) || isset($application->bo_data[$fieldName])) {
                $val = $application->bo_data[$fieldNameLower] ?? $application->bo_data[$fieldName] ?? '';
                return is_array($val) ? implode(', ', $val) : (string) $val;
            }
        }

        return '';
    }
}
