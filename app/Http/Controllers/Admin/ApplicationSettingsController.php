<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\ActivityLog;
use App\Models\Holiday;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ApplicationSettingsController extends Controller
{
    /**
     * Display application settings form.
     */
    public function index()
    {
        // Get all settings grouped
        $generalSettings = Setting::where('group', 'general')->get()->pluck('value', 'key');
        $contactSettings = Setting::where('group', 'contact')->get()->pluck('value', 'key');
        $socialMediaSettings = Setting::where('group', 'social_media')->get()->pluck('value', 'key');
        $workingHours = Setting::where('group', 'working_hours')->get()->pluck('value', 'key');
        
        $holidays = Holiday::orderBy('date', 'asc')->get();

        return view('settings.application', compact('generalSettings', 'contactSettings', 'socialMediaSettings', 'workingHours', 'holidays'));
    }

    /**
     * Update application settings.
     */
    public function update(Request $request)
    {
        $request->validate([
            // General Settings
            'app_name' => 'required|string|max:255',
            'app_description' => 'nullable|string',
            'app_logo' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
            'gambar_tte' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'panduan_pendaftaran' => 'nullable|mimes:png,jpg,jpeg,pdf|max:5120',

            // Contact Settings
            'whatsapp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',

            // Social Media Settings
            'facebook' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'youtube' => 'nullable|url|max:255',
            'tiktok' => 'nullable|url|max:255',
            'twitter' => 'nullable|url|max:255',

            // Working Hours
            'work_days' => 'nullable|array',
            'work_start_time' => 'nullable|string',
            'work_end_time' => 'nullable|string',
        ], [
            'app_name.required' => 'Nama aplikasi wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'facebook.url' => 'Format URL Facebook tidak valid.',
            'instagram.url' => 'Format URL Instagram tidak valid.',
            'youtube.url' => 'Format URL YouTube tidak valid.',
            'tiktok.url' => 'Format URL TikTok tidak valid.',
            'twitter.url' => 'Format URL Twitter tidak valid.',
        ]);

        // Capture old data before updating
        $oldData = [
            'general' => Setting::where('group', 'general')->get()->pluck('value', 'key')->toArray(),
            'contact' => Setting::where('group', 'contact')->get()->pluck('value', 'key')->toArray(),
            'social_media' => Setting::where('group', 'social_media')->get()->pluck('value', 'key')->toArray(),
            'working_hours' => Setting::where('group', 'working_hours')->get()->pluck('value', 'key')->toArray(),
        ];

        // Handle logo upload
        if ($request->hasFile('app_logo')) {
            $logo = $request->file('app_logo');
            $logoName = time() . '_logo.' . $logo->getClientOriginalExtension();
            $logo->move(public_path('assets/images'), $logoName);

            // Delete old logo if exists
            $oldLogo = Setting::where('key', 'app_logo')->first();
            if ($oldLogo && $oldLogo->value && file_exists(public_path($oldLogo->value))) {
                unlink(public_path($oldLogo->value));
            }

            Setting::set('app_logo', 'assets/images/' . $logoName, 'file', 'general', 'Logo Aplikasi');
        }

        // Handle Gambar TTE upload
        if ($request->hasFile('gambar_tte')) {
            $tteImage = $request->file('gambar_tte');
            $tteName = time() . '_tte.' . $tteImage->getClientOriginalExtension();
            
            // Ensure directory exists
            $ttePath = public_path('uploads/tte');
            if (!file_exists($ttePath)) {
                mkdir($ttePath, 0755, true);
            }
            
            $tteImage->move($ttePath, $tteName);

            // Delete old TTE image if exists
            $oldTte = Setting::where('key', 'gambar_tte')->first();
            if ($oldTte && $oldTte->value && file_exists(public_path($oldTte->value))) {
                unlink(public_path($oldTte->value));
            }

            Setting::set('gambar_tte', 'uploads/tte/' . $tteName, 'file', 'general', 'Gambar TTE');
        }

        // Handle Panduan Pendaftaran upload
        if ($request->hasFile('panduan_pendaftaran')) {
            $panduanFile = $request->file('panduan_pendaftaran');
            $panduanName = time() . '_panduan.' . $panduanFile->getClientOriginalExtension();
            
            // Ensure directory exists
            $panduanPath = public_path('uploads/informasi');
            if (!file_exists($panduanPath)) {
                mkdir($panduanPath, 0755, true);
            }
            
            $panduanFile->move($panduanPath, $panduanName);

            // Delete old Panduan file if exists
            $oldPanduan = Setting::where('key', 'panduan_pendaftaran')->first();
            if ($oldPanduan && $oldPanduan->value && file_exists(public_path($oldPanduan->value))) {
                unlink(public_path($oldPanduan->value));
            }

            Setting::set('panduan_pendaftaran', 'uploads/informasi/' . $panduanName, 'file', 'general', 'Informasi Panduan Pendaftaran');
        }

        // General Settings
        Setting::set('app_name', $request->app_name, 'text', 'general', 'Nama Aplikasi');
        Setting::set('app_description', $request->app_description, 'textarea', 'general', 'Deskripsi Aplikasi');

        // Contact Settings
        Setting::set('whatsapp', $request->whatsapp, 'text', 'contact', 'WhatsApp');
        Setting::set('email', $request->email, 'text', 'contact', 'Email');
        Setting::set('address', $request->address, 'textarea', 'contact', 'Alamat');
        Setting::set('phone', $request->phone, 'text', 'contact', 'Telepon');

        // Social Media Settings
        Setting::set('facebook', $request->facebook, 'text', 'social_media', 'Facebook');
        Setting::set('instagram', $request->instagram, 'text', 'social_media', 'Instagram');
        Setting::set('youtube', $request->youtube, 'text', 'social_media', 'YouTube');
        Setting::set('tiktok', $request->tiktok, 'text', 'social_media', 'TikTok');
        Setting::set('twitter', $request->twitter, 'text', 'social_media', 'Twitter');

        // Working Hours Settings
        Setting::set('work_days', json_encode($request->work_days ?? []), 'text', 'working_hours', 'Hari Kerja');
        Setting::set('work_start_time', $request->work_start_time, 'text', 'working_hours', 'Jam Mulai Kerja');
        Setting::set('work_end_time', $request->work_end_time, 'text', 'working_hours', 'Jam Selesai Kerja');

        // Capture new data after updating
        $newData = [
            'general' => [
                'app_name' => $request->app_name,
                'app_description' => $request->app_description,
                'app_logo' => $oldData['general']['app_logo'] ?? null,
            ],
            'contact' => [
                'whatsapp' => $request->whatsapp,
                'email' => $request->email,
                'address' => $request->address,
                'phone' => $request->phone,
            ],
            'social_media' => [
                'facebook' => $request->facebook,
                'instagram' => $request->instagram,
                'youtube' => $request->youtube,
                'tiktok' => $request->tiktok,
                'twitter' => $request->twitter,
            ],
            'working_hours' => [
                'work_days' => $request->work_days ?? [],
                'work_start_time' => $request->work_start_time,
                'work_end_time' => $request->work_end_time,
            ],
        ];

        // If logo was uploaded, update the new data
        if ($request->hasFile('app_logo')) {
            $newData['general']['app_logo'] = 'assets/images/' . time() . '_logo.' . $request->file('app_logo')->getClientOriginalExtension();
        }

        // Get a reference setting for the log subject
        $referenceSetting = Setting::where('key', 'app_name')->first();
        if (!$referenceSetting) {
            $referenceSetting = Setting::first();
        }

        // Log activity with old and new data
        ActivityLog::log(
            'Mengupdate pengaturan aplikasi',
            $referenceSetting,
            'updated',
            [
                'old' => $oldData,
                'new' => $newData,
            ],
            'settings'
        );

        return redirect()->route('settings.application')
            ->with('success', 'Pengaturan aplikasi berhasil disimpan.');
    }

    /**
     * Add a new holiday.
     */
    public function addHoliday(Request $request)
    {
        $request->validate([
            'date' => 'required|date|unique:holidays,date',
            'description' => 'nullable|string|max:255',
        ], [
            'date.unique' => 'Tanggal ini sudah ada dalam daftar hari libur.',
        ]);

        $holiday = Holiday::create($request->all());

        ActivityLog::log(
            "Menambah hari libur: {$holiday->date->format('d M Y')} - {$holiday->description}",
            $holiday,
            'created',
            $holiday->toArray(),
            'settings'
        );

        return response()->json([
            'success' => true,
            'message' => 'Hari libur berhasil ditambahkan.',
            'holiday' => [
                'id' => $holiday->id,
                'date' => $holiday->date->format('d M Y'),
                'description' => $holiday->description ?? '-',
            ]
        ]);
    }

    /**
     * Delete a holiday.
     */
    public function deleteHoliday($id)
    {
        $holiday = Holiday::findOrFail($id);
        $oldData = $holiday->toArray();
        $holiday->delete();

        ActivityLog::log(
            "Menghapus hari libur: {$oldData['date']}",
            null,
            'deleted',
            $oldData,
            'settings'
        );

        return response()->json([
            'success' => true,
            'message' => 'Hari libur berhasil dihapus.'
        ]);
    }

    /**
     * Get setting value helper.
     */
    private function getSetting($key, $default = '')
    {
        return Setting::get($key, $default);
    }
}
