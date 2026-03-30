<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\ActivityLog;
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

        return view('settings.application', compact('generalSettings', 'contactSettings', 'socialMediaSettings'));
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
     * Get setting value helper.
     */
    private function getSetting($key, $default = '')
    {
        return Setting::get($key, $default);
    }
}
