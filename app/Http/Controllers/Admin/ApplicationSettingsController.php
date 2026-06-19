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
        
        // Load global templates
        $templateSettings = Setting::where('group', 'templates')->get()->pluck('value', 'key');
        $templatePernyataan = $templateSettings['template_pernyataan'] ?? \App\Services\DocumentGenerator::getDefaultPernyataanTemplate();
        $templatePermohonan = $templateSettings['template_permohonan'] ?? \App\Services\DocumentGenerator::getDefaultPermohonanTemplate();
        $templateKeabsahan = $templateSettings['template_keabsahan'] ?? \App\Services\DocumentGenerator::getDefaultKeabsahanTemplate();

        $holidays = Holiday::orderBy('date', 'asc')->get();
        $masterDokumens = \App\Models\MasterDokumenPemohon::orderBy('nama_dokumen', 'asc')->get();

        return view('settings.application', compact(
            'generalSettings',
            'contactSettings',
            'socialMediaSettings',
            'workingHours',
            'holidays',
            'masterDokumens',
            'templatePernyataan',
            'templatePermohonan',
            'templateKeabsahan'
        ));
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
            'logo_kabupaten' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
            'gambar_tte' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',

            // Contact Settings
            'whatsapp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',

            // Social Media Settings
            'facebook' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'youtube' => 'nullable|url|max:255',
            'tiktok' => 'nullable|url|max:255',
            'twitter' => 'nullable|url|max:255',

            // Working Hours
            'work_hours' => 'nullable|array',

            // Templates
            'template_pernyataan' => 'nullable|string',
            'template_permohonan' => 'nullable|string',
            'template_keabsahan' => 'nullable|string',
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
            'templates' => Setting::where('group', 'templates')->get()->pluck('value', 'key')->toArray(),
        ];

        // ... (logo, tte, panduan uploads remain the same) ...
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

        // Handle Logo Kabupaten upload
        if ($request->hasFile('logo_kabupaten')) {
            $logoKabupaten = $request->file('logo_kabupaten');
            $logoKabupatenName = time() . '_logo_kabupaten.' . $logoKabupaten->getClientOriginalExtension();
            $logoKabupaten->move(public_path('assets/images'), $logoKabupatenName);

            // Delete old logo kabupaten if exists
            $oldLogoKabupaten = Setting::where('key', 'logo_kabupaten')->first();
            if ($oldLogoKabupaten && $oldLogoKabupaten->value && file_exists(public_path($oldLogoKabupaten->value))) {
                unlink(public_path($oldLogoKabupaten->value));
            }

            Setting::set('logo_kabupaten', 'assets/images/' . $logoKabupatenName, 'file', 'general', 'Logo Kabupaten');
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

        // Working Hours Settings (New JSON structure)
        $workHoursData = $request->input('work_hours', []);
        Setting::set('work_hours', json_encode($workHoursData), 'text', 'working_hours', 'Detail Jam Kerja Harian');

        // Template Settings
        Setting::set('template_pernyataan', $request->template_pernyataan, 'textarea', 'templates', 'Template Surat Pernyataan');
        Setting::set('template_permohonan', $request->template_permohonan, 'textarea', 'templates', 'Template Surat Permohonan');
        Setting::set('template_keabsahan', $request->template_keabsahan, 'textarea', 'templates', 'Template Surat Keabsahan');

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
                'work_hours' => $workHoursData,
            ],
            'templates' => [
                'template_pernyataan' => $request->template_pernyataan,
                'template_permohonan' => $request->template_permohonan,
                'template_keabsahan' => $request->template_keabsahan,
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

        return response()->json(['success' => true, 'message' => 'Hari libur berhasil dihapus.']);
    }

    /**
     * Preview the template as PDF.
     */
    public function previewTemplate(Request $request)
    {
        $type = $request->input('template_type', 'pernyataan');
        $htmlContent = $request->input('template_content', '');

        // Dummy Data Replacements
        $replacements = [
            '[NAMA PEMOHON]' => 'Budi Santoso',
            '[NIK]' => '3304123456789001',
            '[ALAMAT LENGKAP]' => 'Jl. Pemuda No. 45, Kel/Desa Krandegan, Kec. Banjarnegara, Kab/Kota Banjarnegara, Provinsi Jawa Tengah',
            '[NO HP]' => '081234567890',
            '[EMAIL]' => 'budi.santoso@email.com',
            '[PEKERJAAN]' => 'Wiraswasta',
            '[NAMA IZIN]' => 'Izin Apotek',
            '[TANGGAL]' => \Carbon\Carbon::now()->translatedFormat('d F Y'),
            '[TANGGAL_HARI_INI]' => \Carbon\Carbon::now()->translatedFormat('d F Y'),
            '[NO REGISTRASI]' => 'REG-' . date('Ymd') . '-001',
        ];

        // [GAMBAR TTE]
        $gambarTte = \App\Models\Setting::get('gambar_tte');
        $tteHtml = '<div style="width: 100px; height: 100px; border: 1px dashed #ccc; display: inline-flex; align-items: center; justify-content: center; font-size: 10px; color: #999;">[QR CODE TTE]</div>';
        if ($gambarTte && \Illuminate\Support\Facades\File::exists(public_path($gambarTte))) {
            $imageData = base64_encode(\Illuminate\Support\Facades\File::get(public_path($gambarTte)));
            $mime = \Illuminate\Support\Facades\File::mimeType(public_path($gambarTte));
            $src = 'data:' . $mime . ';base64,' . $imageData;
            $tteHtml = '<img src="' . $src . '" style="max-width: 150px; max-height: 150px;" alt="TTE" />';
        }
        $replacements['[GAMBAR TTE]'] = $tteHtml;

        // [LOGO KABUPATEN]
        $logoKabupaten = \App\Models\Setting::get('logo_kabupaten');
        $logoKabHtml = '<div style="width: 80px; height: 80px; border: 1px dashed #ccc; display: inline-flex; align-items: center; justify-content: center; font-size: 10px; color: #999;">[LOGO KAB]</div>';
        if ($logoKabupaten && \Illuminate\Support\Facades\File::exists(public_path($logoKabupaten))) {
            $imageData = base64_encode(\Illuminate\Support\Facades\File::get(public_path($logoKabupaten)));
            $mime = \Illuminate\Support\Facades\File::mimeType(public_path($logoKabupaten));
            $src = 'data:' . $mime . ';base64,' . $imageData;
            $logoKabHtml = '<img src="' . $src . '" style="max-height: 110px; width: auto;" alt="Logo Kabupaten" />';
        }
        $replacements['[LOGO KABUPATEN]'] = $logoKabHtml;

        // Handle Page Breaks
        $htmlContent = str_replace('<!-- pagebreak -->', '<div class="page-break"></div>', $htmlContent);

        // Replace placeholders
        $htmlContent = str_replace(
            array_keys($replacements),
            array_values($replacements),
            $htmlContent
        );

        $fullHtml = \App\Services\DocumentGenerator::wrapHtmlTemplate($htmlContent, $type, 'PREVIEW');

        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($fullHtml)
                ->setPaper('a4', 'portrait')
                ->setWarnings(false)
                ->setOptions([
                    'isRemoteEnabled' => true,
                    'isHtml5ParserEnabled' => true,
                    'isFontSubsettingEnabled' => true,
                    'defaultFont' => 'DejaVu Sans',
                ]);
            return $pdf->stream('Pratinjau_' . ucfirst($type) . '.pdf');
        }

        return response($fullHtml);
    }

    /**
     * Get setting value helper.
     */
    private function getSetting($key, $default = '')
    {
        return Setting::get($key, $default);
    }
}
