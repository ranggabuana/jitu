<?php

namespace App\Services;

use App\Models\Perijinan;
use App\Models\DataPerijinan;
use App\Models\PerijinanOpdConfig;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DocumentGenerator
{
    /**
     * Generate the three required documents for an application.
     *
     * @param DataPerijinan $application
     * @return array Array of paths for [pernyataan, permohonan, keabsahan]
     */
    public static function generateDocuments(DataPerijinan $application, $targetOpdId = null, bool $forceOfficial = false): array
    {
        $tempFilesToDelete = [];
        $perijinan = $application->perijinan;
        $user = $application->user;

        if (!$perijinan || !$user) {
            \Log::error('DocumentGenerator: Application is missing perijinan or user relations.', ['id' => $application->id]);
            return [
                'file_pernyataan' => null,
                'file_permohonan' => null,
                'file_keabsahan' => null,
            ];
        }

        // 1. Identify/Extract Pekerjaan from form data
        $pekerjaan = self::extractPekerjaan($application, $perijinan);

        // 1.5 Construct full address
        $userAddress = $user->alamat_ktp ?? $user->alamat_lengkap ?? $user->alamat_domisili ?? '';
        $addressParts = [];
        if ($user->kelurahan) $addressParts[] = 'Kel/Desa ' . $user->kelurahan->name;
        if ($user->kecamatan) $addressParts[] = 'Kec. ' . $user->kecamatan->name;
        if ($user->kabupaten) $addressParts[] = 'Kab/Kota ' . $user->kabupaten->name;
        if ($user->provinsi) $addressParts[] = 'Provinsi ' . $user->provinsi->name;
        
        $fullAlamat = $userAddress;
        if (!empty($addressParts)) {
            $fullAlamat .= ', ' . implode(', ', $addressParts);
        }

        // 2. Prepare Replacements Map
        $baseReplacements = [
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
            '${PROVINSI}' => $user->provinsi->name ?? '-',
            '${KABUPATEN}' => $user->kabupaten->name ?? '-',
            '${KECAMATAN}' => $user->kecamatan->name ?? '-',
            '${KELURAHAN}' => $user->kelurahan->name ?? '-',
            '${ALAMAT_LENGKAP}' => $fullAlamat ?: '-',
            '${STATUS_PEMOHON}' => $user->status_pemohon ?? '-',
            '${ROLE}' => $user->role_label ?? $user->role ?? '-',
            '${STATUS_USER}' => $user->status ?? '-',
            '${OPD_USER}' => $user->opd->nama_opd ?? '-',
            '${NAMA_IZIN}' => $perijinan->nama_perijinan ?? '-',
            '${TANGGAL}' => self::formatDateIndonesian($application->created_at ?? now()),
            '${TANGGAL_HARI_INI}' => self::formatDateIndonesian($application->created_at ?? now()),
            '${NO_REGISTRASI}' => $application->no_registrasi ?? '-',
            '${MASA_AKTIF}' => $application->masa_aktif ? self::formatDateIndonesian($application->masa_aktif) : '-',
        ];

        // 3. Define output directory
        $safeNoRegistrasi = str_replace('-', '_', $application->no_registrasi);
        $folderPath = 'uploads/perijinan/generated_' . $safeNoRegistrasi;
        $absoluteFolder = public_path($folderPath);

        if (!File::exists($absoluteFolder)) {
            File::makeDirectory($absoluteFolder, 0755, true);
        }

        $generatedPaths = [];

        // 4. Generate each document type
        $documentTypes = [
            'pernyataan' => [
                'template_field' => 'template_pernyataan',
                'default_method' => 'getDefaultPernyataanTemplate',
                'filename' => 'Surat_Pernyataan_' . $safeNoRegistrasi,
            ],
            'permohonan' => [
                'template_field' => 'template_permohonan',
                'default_method' => 'getDefaultPermohonanTemplate',
                'filename' => 'Surat_Permohonan_' . $safeNoRegistrasi,
            ],
            'keabsahan' => [
                'template_field' => 'template_keabsahan',
                'default_method' => 'getDefaultKeabsahanTemplate',
                'filename' => 'Surat_Keabsahan_' . $safeNoRegistrasi,
            ],
            'izin' => [
                'template_field' => 'template_surat_izin',
                'default_method' => 'getDefaultSuratIzinTemplate',
                'filename' => 'Surat_Izin_' . $safeNoRegistrasi,
            ],
        ];

        // 4.5. Add ${GAMBAR_TTE} and ${LOGO_KABUPATEN} to replacements
        $gambarTte = \App\Models\Setting::get('gambar_tte');
        $tteHtml = '';
        if ($gambarTte) {
            $ttePath = public_path($gambarTte);
            if (!File::exists($ttePath) && \Illuminate\Support\Facades\Storage::disk('public')->exists($gambarTte)) {
                $ttePath = \Illuminate\Support\Facades\Storage::disk('public')->path($gambarTte);
            }
            if (File::exists($ttePath)) {
                $imageData = base64_encode(File::get($ttePath));
                $mime = File::mimeType($ttePath);
                $src = 'data:' . $mime . ';base64,' . $imageData;
                $tteHtml = '<img src="' . $src . '" style="max-width: 250px; max-height: 95px; width: auto; height: auto;" alt="TTE" />';
            }
        }
        $baseReplacements['${GAMBAR_TTE}'] = $tteHtml;

        $logoKabupaten = \App\Models\Setting::get('logo_kabupaten');
        $logoKabHtml = '';
        if ($logoKabupaten) {
            $logoPath = public_path($logoKabupaten);
            if (!File::exists($logoPath) && \Illuminate\Support\Facades\Storage::disk('public')->exists($logoKabupaten)) {
                $logoPath = \Illuminate\Support\Facades\Storage::disk('public')->path($logoKabupaten);
            }
            if (File::exists($logoPath)) {
                $imageData = base64_encode(File::get($logoPath));
                $mime = File::mimeType($logoPath);
                $src = 'data:' . $mime . ';base64,' . $imageData;
                $logoKabHtml = '<img src="' . $src . '" style="max-height: 110px; width: auto;" alt="Logo Kabupaten" />';
            }
        }
        $baseReplacements['${LOGO_KABUPATEN}'] = $logoKabHtml;

        // 4.6. Add ${QRCODE} to replacements (default/fallback is black)
        $scanUrl = route('front.perizinan.scan', $application->no_registrasi);
        $tempQrPath = self::generateQrCodeFile($scanUrl, false);
        if ($tempQrPath && File::exists($tempQrPath)) {
            $tempFilesToDelete[] = $tempQrPath;
            $qrCodeBase64 = base64_encode(File::get($tempQrPath));
            $qrHtml = '<img src="data:image/png;base64,' . $qrCodeBase64 . '" style="width: 60px; height: 60px;" alt="Scan QR Code" />';
            $baseReplacements['${QRCODE}'] = $qrHtml;
            $baseReplacements['${_IMG_PATH_QRCODE}'] = $tempQrPath;
        } else {
            $baseReplacements['${QRCODE}'] = '[Gagal Generate QR Code]';
        }
        
        // 5. Build Applicant Data Map (Global Form)
        $applicantReplacements = [];
        if (!empty($application->form_data) && is_array($application->form_data)) {
            foreach ($application->form_data as $fieldId => $value) {
                $valStr = is_array($value) ? implode(', ', $value) : (string)$value;
                $applicantReplacements['${' . strtoupper(str_replace(' ', '_', $fieldId)) . '}'] = $valStr;
                $field = $perijinan->activeFormFields->firstWhere('id', $fieldId);
                if ($field) {
                    $applicantReplacements['${' . strtoupper(str_replace(' ', '_', $field->label)) . '}'] = $valStr;
                }
            }
        }

        // Add pas_foto and gambar field replacements
        if ($perijinan->activeFormFields) {
            foreach ($perijinan->activeFormFields->where('form_type', 'global') as $field) {
                if ($field->type === 'pas_foto' || $field->type === 'gambar') {
                    $files = $application->form_files[$field->id] ?? null;
                    $file = is_array($files) ? ($files[0] ?? null) : $files;
                    
                    if ($file) {
                        $absolutePath = public_path($file);
                        if (!File::exists($absolutePath) && \Illuminate\Support\Facades\Storage::disk('public')->exists($file)) {
                            $absolutePath = \Illuminate\Support\Facades\Storage::disk('public')->path($file);
                        }
                        if (File::exists($absolutePath)) {
                            $imageData = base64_encode(File::get($absolutePath));
                            $mime = File::mimeType($absolutePath);
                            $src = 'data:' . $mime . ';base64,' . $imageData;
                            
                            if ($field->type === 'pas_foto') {
                                $htmlImg = '<img src="' . $src . '" style="width: 2.79cm; height: 3.81cm; object-fit: cover;" alt="Pas Foto" />';
                                $imgValType = 'PASFOTO_';
                            } else {
                                $htmlImg = '<img src="' . $src . '" style="max-width: 100%; max-height: 250px; width: auto; height: auto; object-fit: contain;" alt="Gambar" />';
                                $imgValType = 'GAMBAR_';
                            }
                            
                            $applicantReplacements['${' . strtoupper(str_replace(' ', '_', $field->name)) . '}'] = $htmlImg;
                            $applicantReplacements['${' . strtoupper(str_replace(' ', '_', $field->label)) . '}'] = $htmlImg;
                            $applicantReplacements['${' . strtoupper(str_replace(' ', '_', $field->id)) . '}'] = $htmlImg;
                            $applicantReplacements['${_IMG_VAL_' . $imgValType . strtoupper(str_replace(' ', '_', $field->name)) . '}'] = $absolutePath;
                            $applicantReplacements['${_IMG_VAL_' . $imgValType . strtoupper(str_replace(' ', '_', $field->label)) . '}'] = $absolutePath;
                        }
                    } else {
                        $applicantReplacements['${' . strtoupper(str_replace(' ', '_', $field->name)) . '}'] = '';
                        $applicantReplacements['${' . strtoupper(str_replace(' ', '_', $field->label)) . '}'] = '';
                    }
                }
            }
        }

        // 5.5. Build BO Data Map (BO Form)
        $boReplacements = [];
        if ($perijinan->has_bo_form && !empty($application->bo_data) && is_array($application->bo_data)) {
            foreach ($application->bo_data as $key => $value) {
                $field = $perijinan->activeFormFields->where('form_type', 'bo')->firstWhere('name', $key);
                if ($field && ($field->type === 'pas_foto' || $field->type === 'gambar')) {
                    if ($value) {
                        $absolutePath = public_path($value);
                        if (!File::exists($absolutePath) && \Illuminate\Support\Facades\Storage::disk('public')->exists($value)) {
                            $absolutePath = \Illuminate\Support\Facades\Storage::disk('public')->path($value);
                        }
                        if (File::exists($absolutePath)) {
                            $imageData = base64_encode(File::get($absolutePath));
                            $mime = File::mimeType($absolutePath);
                            $src = 'data:' . $mime . ';base64,' . $imageData;
                            
                            if ($field->type === 'pas_foto') {
                                $htmlImg = '<img src="' . $src . '" style="width: 2.79cm; height: 3.81cm; object-fit: cover;" alt="Pas Foto" />';
                                $imgValType = 'PASFOTO_';
                            } else {
                                $htmlImg = '<img src="' . $src . '" style="max-width: 100%; max-height: 250px; width: auto; height: auto; object-fit: contain;" alt="Gambar" />';
                                $imgValType = 'GAMBAR_';
                            }
                            
                            $boReplacements['${' . strtoupper(str_replace(' ', '_', $key)) . '}'] = $htmlImg;
                            if ($field) {
                                $boReplacements['${' . strtoupper(str_replace(' ', '_', $field->label)) . '}'] = $htmlImg;
                            }
                            $boReplacements['${_IMG_VAL_' . $imgValType . strtoupper(str_replace(' ', '_', $key)) . '}'] = $absolutePath;
                            if ($field) {
                                $boReplacements['${_IMG_VAL_' . $imgValType . strtoupper(str_replace(' ', '_', $field->label)) . '}'] = $absolutePath;
                            }
                        } else {
                            $boReplacements['${' . strtoupper(str_replace(' ', '_', $key)) . '}'] = '';
                            if ($field) {
                                $boReplacements['${' . strtoupper(str_replace(' ', '_', $field->label)) . '}'] = '';
                            }
                        }
                    } else {
                        $boReplacements['${' . strtoupper(str_replace(' ', '_', $key)) . '}'] = '';
                        if ($field) {
                            $boReplacements['${' . strtoupper(str_replace(' ', '_', $field->label)) . '}'] = '';
                        }
                    }
                } else {
                    $valStr = is_array($value) ? implode(', ', $value) : (string)$value;
                    $boReplacements['${' . strtoupper(str_replace(' ', '_', $key)) . '}'] = $valStr;
                    if ($field) {
                        $boReplacements['${' . strtoupper(str_replace(' ', '_', $field->label)) . '}'] = $valStr;
                    }
                }
            }
        }

        // 6. Handle Recommendation Documents (The Complex Part)
        $rekomList = [];
        if ($perijinan->is_multi_opd) {
            // Multi OPD: Get all OPDs involved in the validation flow
            $involvedOpds = $perijinan->activeValidationFlows()
                ->whereIn('role', ['operator_opd', 'kepala_opd'])
                ->whereNotNull('assigned_user_id')
                ->with('assignedUser.opd')
                ->get()
                ->pluck('assignedUser.opd')
                ->filter()
                ->unique('id');

            foreach ($involvedOpds as $opd) {
                // Only generate for target OPD if specified, otherwise generate all that have data
                if ($targetOpdId && $opd->id != $targetOpdId) continue;
                
                $opdRekomData = $application->rekom_data_multi[$opd->id] ?? null;
                if (!$opdRekomData && !$targetOpdId) continue;

                $rekomList[] = [
                    'opd' => $opd,
                    'data' => $opdRekomData ?? [],
                    'filename' => 'Surat_Rekomendasi_' . Str::slug($opd->nama_opd, '_') . '_' . $safeNoRegistrasi,
                ];
            }
        } else {
            // Single OPD: Just standard rekom
            $opd = null;
            $flowWithOpd = $perijinan->activeValidationFlows()
                ->whereIn('role', ['operator_opd', 'kepala_opd'])
                ->whereNotNull('assigned_user_id')
                ->with('assignedUser.opd')
                ->get()
                ->pluck('assignedUser.opd')
                ->filter()
                ->first();
            if ($flowWithOpd) {
                $opd = $flowWithOpd;
            }
            if (!$opd && auth()->check() && auth()->user()->role === 'operator_opd' && auth()->user()->opd) {
                $opd = auth()->user()->opd;
            }

            $rekomList[] = [
                'opd' => $opd,
                'data' => $application->rekom_data ?? [],
                'filename' => 'Surat_Rekomendasi_' . $safeNoRegistrasi,
            ];
        }

        // Generate Recommendation Documents
        $fileRekomMulti = $application->file_rekom_multi ?? [];
        foreach ($rekomList as $rekomItem) {
            $opd = $rekomItem['opd'];
            $rekomData = $rekomItem['data'];
            $filename = $rekomItem['filename'];

            $isRekomDraft = true;
            if ($forceOfficial) {
                $isRekomDraft = false;
            } else {
                if ($perijinan->is_multi_opd && $opd) {
                    $isRekomDraft = empty($application->file_rekom_multi_tte[$opd->id]);
                } else {
                    $isRekomDraft = empty($application->file_rekom_tte);
                }
            }

            $rekomScanParams = [
                'no_registrasi' => $application->no_registrasi,
                'type' => 'rekom',
                'opd_id' => $opd ? $opd->id : null
            ];
            if ($isRekomDraft) {
                $rekomScanParams['is_draft'] = 1;
            }
            $rekomScanUrl = route('front.perizinan.scan', $rekomScanParams);
            $rekomQrPath = self::generateQrCodeFile($rekomScanUrl, $isRekomDraft);
            $rekomQrHtml = '';
            if ($rekomQrPath && File::exists($rekomQrPath)) {
                $tempFilesToDelete[] = $rekomQrPath;
                $rekomQrBase64 = base64_encode(File::get($rekomQrPath));
                $rekomQrHtml = '<img src="data:image/png;base64,' . $rekomQrBase64 . '" style="width: 60px; height: 60px;" alt="Scan QR Code" />';
            } else {
                $rekomQrHtml = '[Gagal Generate QR Code]';
            }

            $rekomReplacements = [];
            foreach ($rekomData as $key => $value) {
                $field = $perijinan->activeFormFields->where('form_type', 'rekom')->firstWhere('name', $key);
                if ($field && ($field->type === 'pas_foto' || $field->type === 'gambar')) {
                    if ($value) {
                        $absolutePath = public_path($value);
                        if (!File::exists($absolutePath) && \Illuminate\Support\Facades\Storage::disk('public')->exists($value)) {
                            $absolutePath = \Illuminate\Support\Facades\Storage::disk('public')->path($value);
                        }
                        if (File::exists($absolutePath)) {
                            $imageData = base64_encode(File::get($absolutePath));
                            $mime = File::mimeType($absolutePath);
                            $src = 'data:' . $mime . ';base64,' . $imageData;
                            
                            if ($field->type === 'pas_foto') {
                                $htmlImg = '<img src="' . $src . '" style="width: 2.79cm; height: 3.81cm; object-fit: cover;" alt="Pas Foto" />';
                                $imgValType = 'PASFOTO_';
                            } else {
                                $htmlImg = '<img src="' . $src . '" style="max-width: 100%; max-height: 250px; width: auto; height: auto; object-fit: contain;" alt="Gambar" />';
                                $imgValType = 'GAMBAR_';
                            }
                            
                            $rekomReplacements['${' . strtoupper(str_replace(' ', '_', $key)) . '}'] = $htmlImg;
                            if ($field) {
                                $rekomReplacements['${' . strtoupper(str_replace(' ', '_', $field->label)) . '}'] = $htmlImg;
                            }
                            $rekomReplacements['${_IMG_VAL_' . $imgValType . strtoupper(str_replace(' ', '_', $key)) . '}'] = $absolutePath;
                            if ($field) {
                                $rekomReplacements['${_IMG_VAL_' . $imgValType . strtoupper(str_replace(' ', '_', $field->label)) . '}'] = $absolutePath;
                            }
                        } else {
                            $rekomReplacements['${' . strtoupper(str_replace(' ', '_', $key)) . '}'] = '';
                            if ($field) {
                                $rekomReplacements['${' . strtoupper(str_replace(' ', '_', $field->label)) . '}'] = '';
                            }
                        }
                    } else {
                        $rekomReplacements['${' . strtoupper(str_replace(' ', '_', $key)) . '}'] = '';
                        if ($field) {
                            $rekomReplacements['${' . strtoupper(str_replace(' ', '_', $field->label)) . '}'] = '';
                        }
                    }
                } else {
                    $valStr = is_array($value) ? implode(', ', $value) : (string)$value;
                    $rekomReplacements['${' . strtoupper(str_replace(' ', '_', $key)) . '}'] = $valStr;
                    if ($field) {
                        $rekomReplacements['${' . strtoupper(str_replace(' ', '_', $field->label)) . '}'] = $valStr;
                    }
                }
            }

            $finalReplacements = array_merge($baseReplacements, $applicantReplacements, $boReplacements, $rekomReplacements);
            
            // Override QR code replacements for Rekomendasi
            $finalReplacements['${QRCODE}'] = $rekomQrHtml;
            if ($rekomQrPath) {
                $finalReplacements['${_IMG_PATH_QRCODE}'] = $rekomQrPath;
            }
            $kodePerijinan = $perijinan->kode_perijinan ?? 'PER';
            $noUrut = $application->no_rekom ?? $perijinan->next_nomor_rekom ?? '-';
            $kodeOpd = $opd ? ($opd->kode_opd ?? 'OPD') : ($application->no_rekom_kode ?? 'OPD');
            $tahun = now()->year;

            $finalReplacements['${NOMOR_URUT}'] = $noUrut;
            $finalReplacements['${NOMOR_SURAT}'] = "{$kodePerijinan}/{$noUrut}/{$kodeOpd}/{$tahun}";

            // Override ${GAMBAR_TTE} for specific OPD
            if ($opd && $opd->gambar_tte) {
                $opdTtePath = public_path($opd->gambar_tte);
                if (!File::exists($opdTtePath) && \Illuminate\Support\Facades\Storage::disk('public')->exists($opd->gambar_tte)) {
                    $opdTtePath = \Illuminate\Support\Facades\Storage::disk('public')->path($opd->gambar_tte);
                }
                if (File::exists($opdTtePath)) {
                    $imageData = base64_encode(File::get($opdTtePath));
                    $mime = File::mimeType($opdTtePath);
                    $src = 'data:' . $mime . ';base64,' . $imageData;
                    $finalReplacements['${GAMBAR_TTE}'] = '<img src="' . $src . '" style="max-width: 250px; max-height: 95px; width: auto; height: auto;" alt="TTE OPD" />';
                    $finalReplacements['${_IMG_PATH_TTE}'] = $opd->gambar_tte; // For Word template
                }
            }

            // Fetch Template (OPD Specific > Perijinan > Setting)
            $rawTemplate = null;
            if ($opd) {
                $opdConfig = PerijinanOpdConfig::where('perijinan_id', $perijinan->id)
                    ->where('opd_id', $opd->id)
                    ->first();
                if ($opdConfig && $opdConfig->template_surat_rekom) {
                    $rawTemplate = $opdConfig->template_surat_rekom;
                }
            }
            
            if (!$rawTemplate) {
                $rawTemplate = $perijinan->template_surat_rekom ?? \App\Models\Setting::get('template_rekom');
            }

            if (empty(trim(strip_tags($rawTemplate ?? '')))) {
                $rawTemplate = self::getDefaultSuratRekomTemplate();
            }

            if (Str::endsWith($rawTemplate, '.docx')) {
                $path = self::generateFromWord($rawTemplate, $finalReplacements, $filename, $folderPath, $absoluteFolder);
            } else {
                $path = self::renderAndSave($rawTemplate, $finalReplacements, $filename, $folderPath, $absoluteFolder, $application->no_registrasi);
            }
            
            if ($opd) {
                $fileRekomMulti[$opd->id] = $path;
                // If it's multi-OPD, we also set the main file_rekom to the latest one generated
                $generatedPaths['file_rekom'] = $path;
            } else {
                $generatedPaths['file_rekom'] = $path;
            }
        }
        if ($perijinan->is_multi_opd) {
            $generatedPaths['file_rekom_multi'] = $fileRekomMulti;
        }

        // 7. Generate Other Documents (Pernyataan, Permohonan, Keabsahan, Izin)
        foreach ($documentTypes as $type => $config) {
            $rawTemplate = $perijinan->{$config['template_field']} ?? \App\Models\Setting::get('template_' . $type);
            if (empty(trim(strip_tags($rawTemplate ?? '')))) {
                $rawTemplate = self::{$config['default_method']}();
            }

            $dataReplacements = [];
            if ($type === 'izin') {
                if (!empty($application->izin_data) && is_array($application->izin_data)) {
                    foreach ($application->izin_data as $key => $value) {
                        $field = $perijinan->activeFormFields->where('form_type', 'izin')->firstWhere('name', $key);
                        if ($field && ($field->type === 'pas_foto' || $field->type === 'gambar')) {
                            if ($value) {
                                $absolutePath = public_path($value);
                                if (!File::exists($absolutePath) && \Illuminate\Support\Facades\Storage::disk('public')->exists($value)) {
                                    $absolutePath = \Illuminate\Support\Facades\Storage::disk('public')->path($value);
                                }
                                if (File::exists($absolutePath)) {
                                    $imageData = base64_encode(File::get($absolutePath));
                                    $mime = File::mimeType($absolutePath);
                                    $src = 'data:' . $mime . ';base64,' . $imageData;
                                    
                                    if ($field->type === 'pas_foto') {
                                        $htmlImg = '<img src="' . $src . '" style="width: 2.79cm; height: 3.81cm; object-fit: cover;" alt="Pas Foto" />';
                                        $imgValType = 'PASFOTO_';
                                    } else {
                                        $htmlImg = '<img src="' . $src . '" style="max-width: 100%; max-height: 250px; width: auto; height: auto; object-fit: contain;" alt="Gambar" />';
                                        $imgValType = 'GAMBAR_';
                                    }
                                    
                                    $dataReplacements['${' . strtoupper(str_replace(' ', '_', $key)) . '}'] = $htmlImg;
                                    if ($field) {
                                        $dataReplacements['${' . strtoupper(str_replace(' ', '_', $field->label)) . '}'] = $htmlImg;
                                    }
                                    $dataReplacements['${_IMG_VAL_' . $imgValType . strtoupper(str_replace(' ', '_', $key)) . '}'] = $absolutePath;
                                    if ($field) {
                                        $dataReplacements['${_IMG_VAL_' . $imgValType . strtoupper(str_replace(' ', '_', $field->label)) . '}'] = $absolutePath;
                                    }
                                } else {
                                    $dataReplacements['${' . strtoupper(str_replace(' ', '_', $key)) . '}'] = '';
                                    if ($field) {
                                        $dataReplacements['${' . strtoupper(str_replace(' ', '_', $field->label)) . '}'] = '';
                                    }
                                }
                            } else {
                                $dataReplacements['${' . strtoupper(str_replace(' ', '_', $key)) . '}'] = '';
                                if ($field) {
                                    $dataReplacements['${' . strtoupper(str_replace(' ', '_', $field->label)) . '}'] = '';
                                }
                            }
                        } else {
                            $valStr = is_array($value) ? implode(', ', $value) : (string)$value;
                            $dataReplacements['${' . strtoupper(str_replace(' ', '_', $key)) . '}'] = $valStr;
                            if ($field) {
                                $dataReplacements['${' . strtoupper(str_replace(' ', '_', $field->label)) . '}'] = $valStr;
                            }
                        }
                    }
                }
            }

            $finalReplacements = array_merge($baseReplacements, $applicantReplacements, $boReplacements, $dataReplacements);
            
            if ($type === 'izin') {
                $isIzinDraft = $forceOfficial ? false : empty($application->file_izin_tte);
                $izinScanParams = [
                    'no_registrasi' => $application->no_registrasi,
                    'type' => 'izin'
                ];
                if ($isIzinDraft) {
                    $izinScanParams['is_draft'] = 1;
                }
                $izinScanUrl = route('front.perizinan.scan', $izinScanParams);
                $izinQrPath = self::generateQrCodeFile($izinScanUrl, $isIzinDraft);
                $izinQrHtml = '';
                if ($izinQrPath && File::exists($izinQrPath)) {
                    $tempFilesToDelete[] = $izinQrPath;
                    $izinQrBase64 = base64_encode(File::get($izinQrPath));
                    $izinQrHtml = '<img src="data:image/png;base64,' . $izinQrBase64 . '" style="width: 60px; height: 60px;" alt="Scan QR Code" />';
                } else {
                    $izinQrHtml = '[Gagal Generate QR Code]';
                }

                $finalReplacements['${QRCODE}'] = $izinQrHtml;
                if ($izinQrPath) {
                    $finalReplacements['${_IMG_PATH_QRCODE}'] = $izinQrPath;
                }

                $kodePerijinan = $perijinan->kode_perijinan ?? 'PER';
                $noUrut = $application->no_izin ?? $perijinan->next_nomor_izin ?? '-';
                $kodeOpd = $application->no_izin_kode ?? 'DPMPTSP';
                $tahun = now()->year;
                $finalReplacements['${NOMOR_URUT}'] = $noUrut;
                $finalReplacements['${NOMOR_SURAT}'] = "{$kodePerijinan}/{$noUrut}/{$kodeOpd}/{$tahun}";
            }

            if (Str::endsWith($rawTemplate, '.docx')) {
                $path = self::generateFromWord($rawTemplate, $finalReplacements, $config['filename'], $folderPath, $absoluteFolder);
            } else {
                $path = self::renderAndSave($rawTemplate, $finalReplacements, $config['filename'], $folderPath, $absoluteFolder, $application->no_registrasi);
            }
            $generatedPaths['file_' . $type] = $path;
        }

        foreach ($tempFilesToDelete as $tempFile) {
            if ($tempFile && File::exists($tempFile)) {
                @unlink($tempFile);
            }
        }

        return $generatedPaths;
    }

    /**
     * Helper to render template and save as PDF.
     */
    private static function renderAndSave($template, $replacements, $filename, $folderPath, $absoluteFolder, $noRegistrasi): string
    {
        $checkmarkHtml = '<span class="checkmark">&#10003;</span>';
        $template = str_replace(['[x]', '[v]', '[V]', '✓'], $checkmarkHtml, $template);
        $template = str_replace('<!-- pagebreak -->', '<div class="page-break"></div>', $template);

        $htmlContent = str_replace(
            array_keys($replacements),
            array_values($replacements),
            $template
        );

        $fullHtml = self::wrapHtmlTemplate($htmlContent, $filename, $noRegistrasi);
        $relativePath = $folderPath . '/' . $filename;

        try {
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
                
                $absolutePdfPath = $absoluteFolder . '/' . $filename . '.pdf';
                File::put($absolutePdfPath, $pdf->output());
                return $relativePath . '.pdf';
            }
        } catch (\Exception $e) {
            \Log::error("DocumentGenerator: Failed to generate PDF $filename: " . $e->getMessage());
        }

        // Fallback to HTML
        $absoluteHtmlPath = $absoluteFolder . '/' . $filename . '.html';
        File::put($absoluteHtmlPath, $fullHtml);
        return $relativePath . '.html';
    }

    /**
     * Generate PDF from Word (.docx) template using LibreOffice
     */
    private static function generateFromWord($templatePathDB, $replacements, $filename, $folderPath, $absoluteFolder)
    {
        // Templates are now stored in public/uploads/templates
        $realTemplatePath = public_path($templatePathDB);
        
        // Backward compatibility fallback for old files in storage/app/private/ or storage/app/
        if (!File::exists($realTemplatePath)) {
            $realTemplatePath = storage_path('app/private/' . $templatePathDB);
            if (!File::exists($realTemplatePath)) {
                $realTemplatePath = storage_path('app/' . $templatePathDB);
            }
        }

        if (!File::exists($realTemplatePath)) {
            \Log::error("Template Word tidak ditemukan: " . $realTemplatePath);
            return null;
        }

        try {
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($realTemplatePath);

            // Replace Data Teks (Supports both [VAR] and ${VAR} formats)
            foreach ($replacements as $key => $value) {
                $cleanKey = str_replace(['[', ']', '${', '}'], '', $key);
                // Bypass if value is HTML (like img tag)
                if (is_string($value) && !str_contains($value, '<img')) {
                    $val = htmlspecialchars(strip_tags($value));
                    
                    // Pass 1: Replace [VAR]
                    $templateProcessor->setMacroChars('[', ']');
                    $templateProcessor->setValue($cleanKey, $val);
                    
                    // Pass 2: Replace ${VAR}
                    $templateProcessor->setMacroChars('${', '}');
                    $templateProcessor->setValue($cleanKey, $val);
                }
            }

            // Replace Gambar (TTE/Kop)
            // Use specific paths if provided in replacements (internal keys)
            $gambarTte = $replacements['${_IMG_PATH_TTE}'] ?? \App\Models\Setting::get('gambar_tte');
            $logoKab = \App\Models\Setting::get('logo_kabupaten');
            $qrCode = $replacements['${_IMG_PATH_QRCODE}'] ?? null;

            // Handle Image Placeholders for both formats (spaces and underscores)
            $macros = [
                'GAMBAR TTE' => $gambarTte,
                'GAMBAR_TTE' => $gambarTte,
                'LOGO KABUPATEN' => $logoKab,
                'LOGO_KABUPATEN' => $logoKab,
                'QRCODE' => $qrCode,
                'QR_CODE' => $qrCode,
            ];
            foreach ($macros as $macro => $path) {
                if ($path && (strpos($path, '/') === 0 || strpos($path, ':\\') !== false || strpos($path, ':/') !== false)) {
                    $actualPath = $path;
                } else {
                    $actualPath = $path ? public_path($path) : null;
                    if ($path && !File::exists($actualPath) && \Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                        $actualPath = \Illuminate\Support\Facades\Storage::disk('public')->path($path);
                    }
                }

                if ($path && $actualPath && File::exists($actualPath)) {
                    $isTte = ($macro === 'GAMBAR TTE' || $macro === 'GAMBAR_TTE');
                    $isQr = ($macro === 'QRCODE' || $macro === 'QR_CODE');
                    $imgConfig = [
                        'path' => $actualPath,
                        'width' => $isTte ? 250 : ($isQr ? 60 : 80), 
                        'height' => $isTte ? 95 : ($isQr ? 60 : 100), 
                        'ratio' => true
                    ];

                    try {
                        // Replace [IMAGE_VAR]
                        $templateProcessor->setMacroChars('[', ']');
                        $templateProcessor->setImageValue($macro, $imgConfig);

                        // Replace ${IMAGE_VAR}
                        $templateProcessor->setMacroChars('${', '}');
                        $templateProcessor->setImageValue($macro, $imgConfig);
                    } catch (\Exception $e) {}
                } else {
                    try {
                        $templateProcessor->setMacroChars('[', ']');
                        $templateProcessor->setValue($macro, '');
                        $templateProcessor->setMacroChars('${', '}');
                        $templateProcessor->setValue($macro, '');
                    } catch (\Exception $e) {}
                }
            }

            // Handle dynamic image replacements (pas_foto and gambar)
            foreach ($replacements as $key => $path) {
                if (str_contains($key, '_IMG_VAL_')) {
                    $isGambar = str_contains($key, '_IMG_VAL_GAMBAR_');
                    
                    if ($isGambar) {
                        $cleanMacro = str_replace(['[', ']', '${', '}', '_IMG_VAL_GAMBAR_'], '', $key);
                        $imgConfig = [
                            'path' => $path,
                            'width' => 350,   // Max width bounding box
                            'height' => 250,  // Max height bounding box
                            'ratio' => true   // Conserves original aspect ratio
                        ];
                    } else {
                        $cleanMacro = str_replace(['[', ']', '${', '}', '_IMG_VAL_PASFOTO_', '_IMG_VAL_'], '', $key);
                        $imgConfig = [
                            'path' => $path,
                            'width' => 105,   // ~2.79 cm at 96 DPI
                            'height' => 144,  // ~3.81 cm at 96 DPI
                            'ratio' => false  // Forces aspect ratio to 3x4 portrait
                        ];
                    }
                    
                    if ($path && File::exists($path)) {
                        try {
                            $templateProcessor->setMacroChars('[', ']');
                            $templateProcessor->setImageValue($cleanMacro, $imgConfig);
                            $templateProcessor->setMacroChars('${', '}');
                            $templateProcessor->setImageValue($cleanMacro, $imgConfig);
                        } catch (\Exception $e) {
                            \Log::error("Failed to replace dynamic word image {$cleanMacro}: " . $e->getMessage());
                        }
                    } else {
                        try {
                            $templateProcessor->setMacroChars('[', ']');
                            $templateProcessor->setValue($cleanMacro, '');
                            $templateProcessor->setMacroChars('${', '}');
                            $templateProcessor->setValue($cleanMacro, '');
                        } catch (\Exception $e) {}
                    }
                }
            }

            $tempDocxPath = $absoluteFolder . '/' . $filename . '.docx';
            $templateProcessor->saveAs($tempDocxPath);

            $librePath = env('LIBREOFFICE_PATH', 'libreoffice');
            $profilePath = storage_path('app/libreoffice_profile_' . uniqid());
            
            if (!File::exists($profilePath)) {
                File::makeDirectory($profilePath, 0755, true);
            }

            // Adjust command for Windows vs Linux compatibility
            if (str_contains(strtoupper(PHP_OS), 'WIN')) {
                $librePathWin = str_replace('/', DIRECTORY_SEPARATOR, $librePath);
                $profilePathUrl = str_replace(DIRECTORY_SEPARATOR, '/', $profilePath);
                $command = "\"{$librePathWin}\" \"-env:UserInstallation=file:///{$profilePathUrl}\" --headless --convert-to pdf \"{$tempDocxPath}\" --outdir \"{$absoluteFolder}\"";
            } else {
                $command = "\"{$librePath}\" -env:UserInstallation=file://\"{$profilePath}\" --headless --convert-to pdf \"{$tempDocxPath}\" --outdir \"{$absoluteFolder}\"";
            }
            
            exec($command . ' 2>&1', $output, $returnVar);

            // Clean up unique profile path
            if (File::exists($profilePath)) {
                File::deleteDirectory($profilePath);
            }

            if ($returnVar === 0) {
                @unlink($tempDocxPath);
                return $folderPath . '/' . $filename . '.pdf';
            } else {
                \Log::error("LibreOffice Error: " . implode("\n", $output));
                return null;
            }

        } catch (\Exception $e) {
            \Log::error("Error Generate Word: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Helper to generate QR code image.
     *
     * @param string $url
     * @param bool $isDraft
     * @return string|null Path to temporary QR code image
     */
    private static function generateQrCodeFile(string $url, bool $isDraft): ?string
    {
        try {
            $qrCode = \BaconQrCode\Encoder\Encoder::encode($url, \BaconQrCode\Common\ErrorCorrectionLevel::H());
            $matrix = $qrCode->getMatrix();
            $width = $matrix->getWidth();
            $height = $matrix->getHeight();
            $margin = 4;
            $moduleSize = 6;
            
            $imgWidth = ($width + 2 * $margin) * $moduleSize;
            $imgHeight = ($height + 2 * $margin) * $moduleSize;
            
            $image = imagecreatetruecolor($imgWidth, $imgHeight);
            $white = imagecolorallocate($image, 255, 255, 255);
            if ($isDraft) {
                $fgColor = imagecolorallocate($image, 239, 68, 68); // Red
            } else {
                $fgColor = imagecolorallocate($image, 0, 0, 0); // Black
            }
            imagefill($image, 0, 0, $white);
            
            for ($y = 0; $y < $height; $y++) {
                for ($x = 0; $x < $width; $x++) {
                    if ($matrix->get($x, $y) === 1) {
                        $x1 = ($x + $margin) * $moduleSize;
                        $y1 = ($y + $margin) * $moduleSize;
                        $x2 = $x1 + $moduleSize - 1;
                        $y2 = $y1 + $moduleSize - 1;
                        imagefilledrectangle($image, $x1, $y1, $x2, $y2, $fgColor);
                    }
                }
            }
            
            $tempPath = storage_path('app/temp_qr_' . md5($url) . '_' . ($isDraft ? 'draft' : 'official') . '_' . uniqid() . '.png');
            imagepng($image, $tempPath);
            imagedestroy($image);
            
            return $tempPath;
        } catch (\Exception $e) {
            \Log::error('QR Code generation helper failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Extract pekerjaan (occupation) from application form fields dynamically.
     */
    private static function extractPekerjaan(DataPerijinan $application, Perijinan $perijinan): string
    {
        $formData = $application->form_data;
        if (empty($formData) || !is_array($formData)) {
            return '-';
        }

        $activeFields = $perijinan->activeFormFields;
        if ($activeFields->isEmpty()) {
            return '-';
        }

        foreach ($formData as $fieldId => $value) {
            $field = $activeFields->firstWhere('id', $fieldId);
            if ($field) {
                $labelLower = strtolower($field->label);
                $nameLower = strtolower($field->name);
                if (str_contains($labelLower, 'pekerjaan') || str_contains($nameLower, 'pekerjaan')) {
                    if (!empty($value)) {
                        return is_array($value) ? implode(', ', $value) : (string)$value;
                    }
                }
            }
        }

        return '-';
    }

    /**
     * Format a date into Indonesian format (e.g. 20 Mei 2026).
     */
    private static function formatDateIndonesian($date): string
    {
        if (!$date) {
            $date = now();
        }

        if (is_string($date)) {
            $date = Carbon::parse($date);
        }

        $months = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        return $date->format('j') . ' ' . $months[$date->format('n')] . ' ' . $date->format('Y');
    }

    /**
     * Wrap the inner template body in a printable HTML format with styling.
     */
    public static function wrapHtmlTemplate(string $content, string $type, string $noRegistrasi = '-'): string
    {
        return '<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>' . Str::title($type) . '</title>
    <style>
        @page {
            margin: 1cm 1.5cm 2cm 1.5cm;
        }
        body {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #000;
            margin: 0;
            padding: 0;
        }
        footer {
            position: fixed;
            bottom: -1cm;
            left: 0;
            right: 0;
            height: 1.5cm;
            text-align: center;
            border-top: 0.5pt solid #ccc;
            padding-top: 5pt;
        }
        .footer-text {
            font-size: 8pt;
            color: #666;
            font-style: italic;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table td {
            vertical-align: top;
        }
        /* Respect TinyMCE alignments */
        p, div, h1, h2, h3, h4, h5, h6 {
            margin-top: 0;
            margin-bottom: 8pt;
        }
        .text-center { text-align: center !important; }
        .text-right { text-align: right !important; }
        .text-left { text-align: left !important; }
        .text-justify { text-align: justify !important; }
        
        .checkmark {
            font-family: "DejaVu Sans", sans-serif !important;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <footer>
        <div class="footer-text">
            Dokumen ini dibuat secara elektronik melalui Sistem Perizinan Online "Dawet Ayu" Banjarnegara.<br/>
            Nomor Dokumen: ' . $noRegistrasi . '
        </div>
    </footer>
    <main>
        ' . $content . '
    </main>
</body>
</html>';
    }

    /**
     * Default Template for Surat Pernyataan.
     */
    public static function getDefaultPernyataanTemplate(): string
    {
        return '<h2>SURAT PERNYATAAN</h2>
<p>Yang bertanda tangan di bawah ini:</p>
<table>
    <tbody>
        <tr>
            <td style="width: 25%;">Nama</td>
            <td style="width: 2%;">:</td>
            <td>${NAMA_PEMOHON}</td>
        </tr>
        <tr>
            <td>NIK</td>
            <td>:</td>
            <td>${NIK}</td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td>:</td>
            <td>${ALAMAT_LENGKAP}</td>
        </tr>
        <tr>
            <td>No. HP</td>
            <td>:</td>
            <td>${NO_HP}</td>
        </tr>
        <tr>
            <td>Pekerjaan</td>
            <td>:</td>
            <td>${PEKERJAAN}</td>
        </tr>
        <tr>
            <td>Email</td>
            <td>:</td>
            <td>${EMAIL}</td>
        </tr>
    </tbody>
</table>
<p>Dengan ini saya menyatakan bersedia mentaati dan tidak melanggar ketentuan peraturan perundang-undangan.</p>
<p>Demikian surat pernyataan ini saya buat dengan sebenarnya untuk dipergunakan sebagaimana mestinya.</p>
<table class="signature-table">
    <tbody>
        <tr>
            <td style="width: 60%;">&nbsp;</td>
            <td>
                <p>Banjarnegara, ${TANGGAL}<br />Pemohon,</p>
                <br /><br />
                <p><strong>${NAMA_PEMOHON}</strong></p>
            </td>
        </tr>
    </tbody>
</table>';
    }

    /**
     * Default Template for Surat Permohonan.
     */
    public static function getDefaultPermohonanTemplate(): string
    {
        return '<h2>SURAT PERMOHONAN IZIN</h2>
<p>Kepada Yth.<br /><strong>Kepala Dinas Penanaman Modal dan Pelayanan Terpadu Satu Pintu Kabupaten Banjarnegara</strong><br />di - <strong>Tempat</strong></p>
<p>Saya yang bertanda tangan di bawah ini:</p>
<table>
    <tbody>
        <tr>
            <td style="width: 25%;">Nama</td>
            <td style="width: 2%;">:</td>
            <td>${NAMA_PEMOHON}</td>
        </tr>
        <tr>
            <td>NIK</td>
            <td>:</td>
            <td>${NIK}</td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td>:</td>
            <td>${ALAMAT_LENGKAP}</td>
        </tr>
        <tr>
            <td>No. HP</td>
            <td>:</td>
            <td>${NO_HP}</td>
        </tr>
        <tr>
            <td>Email</td>
            <td>:</td>
            <td>${EMAIL}</td>
        </tr>
    </tbody>
</table>
<p>Dengan ini mengajukan permohonan untuk memperoleh :<br />Perizinan: <strong>${NAMA_IZIN}</strong></p>
<p>Sebagai bahan pertimbangan, bersama ini kami sampaikan kelengkapan persyaratan melalui Sistem Perizinan Online "Dawet Ayu" Banjarnegara.</p>
<p>Demikian permohonan ini disampaikan, atas perhatian dan perkenannya diucapkan terima kasih.</p>
<table class="signature-table">
    <tbody>
        <tr>
            <td style="width: 60%;">
                <p><strong>Pernyataan Pemohon:</strong><br />[x] Data yang disampaikan adalah benar.<br />[x] Bersedia bertanggung jawab atas data yang diberikan.</p>
            </td>
            <td>
                <p>Banjarnegara, ${TANGGAL}<br />Pemohon,</p>
                <br /><br />
                <p><strong>${NAMA_PEMOHON}</strong></p>
            </td>
        </tr>
    </tbody>
</table>';
    }

    /**
     * Default Template for Surat Keabsahan.
     */
    public static function getDefaultKeabsahanTemplate(): string
    {
        return '<h2>SURAT PERNYATAAN</h2>
<p>Yang bertanda tangan di bawah ini:</p>
<table>
    <tbody>
        <tr>
            <td style="width: 25%;">Nama</td>
            <td style="width: 2%;">:</td>
            <td>${NAMA_PEMOHON}</td>
        </tr>
        <tr>
            <td>NIK</td>
            <td>:</td>
            <td>${NIK}</td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td>:</td>
            <td>${ALAMAT_LENGKAP}</td>
        </tr>
        <tr>
            <td>No. HP</td>
            <td>:</td>
            <td>${NO_HP}</td>
        </tr>
        <tr>
            <td>Pekerjaan</td>
            <td>:</td>
            <td>${PEKERJAAN}</td>
        </tr>
        <tr>
            <td>Email</td>
            <td>:</td>
            <td>${EMAIL}</td>
        </tr>
    </tbody>
</table>
<p>Dengan ini kami menyatakan dengan sesungguhnya bahwa semua informasi yang disampaikan dalam seluruh dokumen serta lampiran-lampirannya yang kami upload ini adalah benar dan kesatuan yang tidak dapat dipisahkan. Apabila diketemukan dan/atau dibuktikan adanya penipuan/pemalsuan atas informasi yang kami sampaikan, maka kami bersedia dikenakan dan menerima penerapan sanksi.</p>
<p>Demikian surat pernyataan kebenaran dan keabsahan data ini kami buat untuk digunakan secara semestinya dan atas perhatiannya diucapkan terima kasih.</p>
<table class="signature-table">
    <tbody>
        <tr>
            <td style="width: 60%;">&nbsp;</td>
            <td>
                <p>Banjarnegara, ${TANGGAL}<br />Pemohon,</p>
                <br /><br />
                <p><strong>${NAMA_PEMOHON}</strong></p>
            </td>
        </tr>
    </tbody>
</table>';
    }

    /**
     * Default Template for Surat Rekomendasi.
     */
    public static function getDefaultSuratRekomTemplate(): string
    {
        return '<div style="font-family: \'Bookman Old Style\', serif; line-height: 1.5; color: #000;">
    <table style="width: 100%; border-collapse: collapse; border-bottom: 2px solid black; margin-bottom: 15px;">
        <tbody>
            <tr>
                <td style="width: 18%; text-align: center; vertical-align: middle; padding-bottom: 5px;">
                    ${LOGO_KABUPATEN}
                </td>
                <td style="width: 82%; text-align: center; vertical-align: middle; padding-bottom: 5px;">
                    <h3 style="margin: 0; font-size: 14pt; font-weight: bold; font-family: \'Bookman Old Style\', serif; text-align: center;">PEMERINTAH KABUPATEN BANJARNEGARA</h3>
                    <h2 style="margin: 2px 0; font-size: 14pt; font-weight: bold; font-family: \'Bookman Old Style\', serif; text-align: center;">DINAS PENANAMAN MODAL DAN PELAYANAN TERPADU SATU PINTU</h2>
                    <p style="margin: 0; font-size: 10pt; font-family: \'Bookman Old Style\', serif; text-align: center;">Jl. Letjend Suprapto No. 1, Banjarnegara, Jawa Tengah 53414</p>
                </td>
            </tr>
        </tbody>
    </table>
    <div style="text-align: center; margin-bottom: 15px;">
        <h4 style="margin: 0; font-size: 11pt; font-weight: bold; text-decoration: underline; font-family: \'Bookman Old Style\', serif;">SURAT REKOMENDASI IZIN OPERASIONAL</h4>
        <p style="margin: 2px 0 0 0; font-size: 10pt; font-family: \'Bookman Old Style\', serif;">Nomor: ${NOMOR_SURAT}</p>
    </div>
    <p style="font-size: 10pt; text-align: justify; margin-bottom: 10px;">Berdasarkan surat permohonan dari <strong>${NAMA_PEMOHON}</strong> pada tanggal ${TANGGAL}. Setelah dilakukan verifikasi kelayakan terhadap persyaratan administrasi dan persyaratan teknis pada entitas yang diusulkan, dengan ini Instansi/Dinas Terkait Kabupaten Banjarnegara menyatakan <strong>LAYAK</strong> dan <strong>MEMBERIKAN REKOMENDASI IZIN OPERASIONAL</strong> kepada:</p>
    <table style="width: 100%; font-size: 10pt; margin-bottom: 15px;" border="0">
        <tbody>
            <tr>
                <td style="width: 30%; padding: 2px 0;">Nama Pemohon</td>
                <td style="width: 2%; padding: 2px 0;">:</td>
                <td style="padding: 2px 0;"><strong>${NAMA_PEMOHON}</strong></td>
            </tr>
            <tr>
                <td style="padding: 2px 0;">Alamat</td>
                <td style="padding: 2px 0;">:</td>
                <td style="padding: 2px 0;">${ALAMAT_LENGKAP}</td>
            </tr>
            <tr>
                <td style="padding: 2px 0;">Jenis Izin</td>
                <td style="padding: 2px 0;">:</td>
                <td style="padding: 2px 0;">${NAMA_IZIN}</td>
            </tr>
        </tbody>
    </table>
    <p style="font-size: 10pt; text-align: justify; margin-bottom: 20px;">Demikian surat rekomendasi ini dibuat untuk dipergunakan sebagaimana mestinya.</p>
    <table style="width: 100%; font-size: 10pt;">
        <tbody>
            <tr>
                <td style="width: 55%;"></td>
                <td style="width: 45%; text-align: center;">
                    Banjarnegara, ${TANGGAL}<br />
                    Kepala Instansi Terkait,<br />
                    <br />
                    ${GAMBAR_TTE}<br />
                    <br />
                    <strong><u>Nama Kepala Instansi</u></strong><br />
                    NIP. .........................
                </td>
            </tr>
        </tbody>
    </table>
</div>';
    }

    /**
     * Default Template for Surat Izin.
     */
    public static function getDefaultSuratIzinTemplate(): string
    {
        return '<div style="font-family: \'Bookman Old Style\', serif; line-height: 1.5; color: #000;">
    <table style="width: 100%; border-collapse: collapse; border-bottom: 2px solid black; margin-bottom: 15px;">
        <tbody>
            <tr>
                <td style="width: 18%; text-align: center; vertical-align: middle; padding-bottom: 5px;">
                    ${LOGO_KABUPATEN}
                </td>
                <td style="width: 82%; text-align: center; vertical-align: middle; padding-bottom: 5px;">
                    <h3 style="margin: 0; font-size: 14pt; font-weight: bold; font-family: \'Bookman Old Style\', serif; text-align: center;">PEMERINTAH KABUPATEN BANJARNEGARA</h3>
                    <h2 style="margin: 2px 0; font-size: 14pt; font-weight: bold; font-family: \'Bookman Old Style\', serif; text-align: center;">DINAS PENANAMAN MODAL DAN PELAYANAN TERPADU SATU PINTU</h2>
                    <p style="margin: 0; font-size: 10pt; font-family: \'Bookman Old Style\', serif; text-align: center;">Jl. Letjend Suprapto No. 1, Banjarnegara, Jawa Tengah 53414</p>
                </td>
            </tr>
        </tbody>
    </table>
    <div style="text-align: center; margin-bottom: 15px;">
        <h4 style="margin: 0; font-size: 11pt; font-weight: bold; text-decoration: underline; font-family: \'Bookman Old Style\', serif;">SURAT IZIN PENDIRIAN / OPERASIONAL</h4>
        <p style="margin: 2px 0 0 0; font-size: 10pt; font-family: \'Bookman Old Style\', serif;">Nomor: ${NOMOR_SURAT}</p>
    </div>
    <p style="font-size: 10pt; text-align: justify; margin-bottom: 10px;">Membaca surat permohonan dari <strong>${NAMA_PEMOHON}</strong> tanggal ${TANGGAL} dan berdasarkan Surat Rekomendasi Nomor: ${NO_REGISTRASI}, dengan ini Kepala Dinas Penanaman Modal dan Pelayanan Terpadu Satu Pintu Kabupaten Banjarnegara <strong>MEMBERIKAN IZIN</strong> kepada:</p>
    <table style="width: 100%; font-size: 10pt; margin-bottom: 15px;" border="0">
        <tbody>
            <tr>
                <td style="width: 30%; padding: 2px 0;">Nama Pemohon</td>
                <td style="width: 2%; padding: 2px 0;">:</td>
                <td style="padding: 2px 0;"><strong>${NAMA_PEMOHON}</strong></td>
            </tr>
            <tr>
                <td style="padding: 2px 0;">Alamat</td>
                <td style="padding: 2px 0;">:</td>
                <td style="padding: 2px 0;">${ALAMAT_LENGKAP}</td>
            </tr>
            <tr>
                <td style="padding: 2px 0;">Jenis Izin</td>
                <td style="padding: 2px 0;">:</td>
                <td style="padding: 2px 0;">${NAMA_IZIN}</td>
            </tr>
            <tr>
                <td style="padding: 2px 0;">Masa Berlaku s/d</td>
                <td style="padding: 2px 0;">:</td>
                <td style="padding: 2px 0;"><strong>${MASA_AKTIF}</strong></td>
            </tr>
        </tbody>
    </table>
    <p style="font-size: 10pt; text-align: justify; margin-bottom: 20px;">Keputusan izin ini berlaku sejak tanggal ditetapkan dengan ketentuan wajib memenuhi semua peraturan perundang-undangan yang berlaku. Apabila di kemudian hari terdapat kekeliruan dalam keputusan ini, akan diadakan perbaikan sebagaimana mestinya.</p>
    <table style="width: 100%; font-size: 10pt;">
        <tbody>
            <tr>
                <td style="width: 55%;"></td>
                <td style="width: 45%; text-align: center;">
                    Ditetapkan di Banjarnegara<br />
                    pada tanggal ${TANGGAL}<br />
                    Kepala Dinas,<br />
                    <br />
                    ${GAMBAR_TTE}<br />
                    <br />
                    <strong><u>Nama Kepala Dinas</u></strong><br />
                    NIP. .........................
                </td>
            </tr>
        </tbody>
    </table>
</div>';
    }
}
