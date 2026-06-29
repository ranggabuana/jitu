<?php

namespace App\Http\Controllers\Pemohon;

use App\Http\Controllers\Controller;
use App\Models\Perijinan;
use App\Models\PerijinanFormField;
use App\Models\DataPerijinan;
use App\Models\DataPerijinanValidasi;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\KswpService;

class DashboardController extends Controller
{
    /**
     * Check KSWP status via API.
     */
    public function checkKswp(Request $request, KswpService $kswpService)
    {
        $user = Auth::user();
        
        if ($user->status_pemohon === 'badan_usaha') {
            if (!$user->npwp) {
                return response()->json([
                    'status' => 'ERROR',
                    'message' => 'NPWP Perusahaan Anda belum terdaftar di profil. Silakan lengkapi profil Anda terlebih dahulu.'
                ]);
            }
            // For badan_usaha, check using NPWP
            $result = $kswpService->checkTaxStatus($user->npwp, 'NPWP');
        } else {
            // Default to perorangan, check using NIK (stored in nip column)
            if (!$user->nip) {
                return response()->json([
                    'status' => 'ERROR',
                    'message' => 'NIK Anda belum terdaftar di profil. Silakan lengkapi profil Anda terlebih dahulu.'
                ]);
            }
            $result = $kswpService->checkTaxStatus($user->nip, 'NIK');
        }

        return response()->json($result);
    }

    /**
     * Display the pemohon dashboard.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get user's applications statistics
        $stats = [
            'total' => DataPerijinan::where('user_id', $user->id)->count(),
            'in_progress' => DataPerijinan::where('user_id', $user->id)
                ->whereIn('status', ['submitted', 'in_progress'])->count(),
            'needs_fix' => DataPerijinan::where('user_id', $user->id)
                ->where('status', 'perbaikan')->count(),
            'completed' => DataPerijinan::where('user_id', $user->id)
                ->where('status', 'approved')->count(),
        ];

        // Build query for applications
        $query = DataPerijinan::with(['perijinan'])
            ->where('user_id', $user->id);

        // Searching
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_registrasi', 'like', "%{$search}%")
                    ->orWhereHas('perijinan', function ($q2) use ($search) {
                        $q2->where('nama_perijinan', 'like', "%{$search}%");
                    });
            });
        }

        // Sorting - Always group by family (root_perpanjang_id or id) to support rowspan, ordered from newest family to oldest
        $query->orderByRaw('COALESCE(root_perpanjang_id, id) DESC')
              ->orderBy('created_at', 'asc');

        // Pagination & Per Page
        $perPage = $request->input('per_page', 5);
        $applications = $query->paginate($perPage)->withQueryString();

        return view('pemohon.dashboard.index', compact(
            'user',
            'stats',
            'applications'
        ));
    }

    /**
     * Display perijinan listing for pemohon.
     */
    public function perijinan(Request $request)
    {
        $search = $request->get('search', '');

        // Fetch pencabutan medis items separately
        $pencabutanQuery = Perijinan::where('jenis_perijinan', 'pencabutan_medis');
        if ($search !== '') {
            $pencabutanQuery->where(function ($q) use ($search) {
                $q->where('nama_perijinan', 'like', "%{$search}%")
                    ->orWhere('dasar_hukum', 'like', "%{$search}%")
                    ->orWhere('persyaratan', 'like', "%{$search}%");
            });
        }
        $pencabutanMedisItems = $pencabutanQuery->orderBy('nama_perijinan')->get();

        // Determine if we should show the representative card
        $includePencabutanMedis = false;
        if ($pencabutanMedisItems->count() > 0) {
            if ($search === '') {
                $includePencabutanMedis = true;
            } else {
                $repName = "pencabutan Surat Izin Praktik (SIP) tenaga medis dan tenaga kesehatan";
                if (stripos($repName, $search) !== false || $pencabutanMedisItems->count() > 0) {
                    $includePencabutanMedis = true;
                }
            }
        }

        // Fetch other items
        $query = Perijinan::where('jenis_perijinan', '!=', 'pencabutan_medis');
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('nama_perijinan', 'like', "%{$search}%")
                    ->orWhere('dasar_hukum', 'like', "%{$search}%")
                    ->orWhere('persyaratan', 'like', "%{$search}%");
            });
        }

        $perijinans = $query->orderBy('nama_perijinan')->paginate(12)->withQueryString();

        return view('pemohon.perijinan.index', compact('perijinans', 'includePencabutanMedis', 'pencabutanMedisItems'));
    }

    /**
     * Display perijinan detail as JSON for modal.
     */
    public function perijinanDetail($id)
    {
        try {
            $perijinan = Perijinan::with([
                'activeValidationFlows.assignedUser',
                'activeFormFields' => function ($query) {
                    $query->where('form_type', 'global')
                        ->orderBy('order', 'asc')
                        ->orderBy('id', 'asc');
                }
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $perijinan
            ]);
        } catch (\Exception $e) {
            \Log::error('Error loading perijinan detail: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat detail perizinan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get application detail as JSON for modal (with validation records).
     */
    public function applicationDetail($id)
    {
        try {
            $user = Auth::user();
            
            $application = DataPerijinan::with([
                'perijinan',
                'validasiRecords.validationFlow.assignedUser',
                'validasiRecords.validator'
            ])
                ->where('id', $id)
                ->where('user_id', $user->id)
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $application->id,
                    'no_registrasi' => $application->no_registrasi,
                    'status' => $application->status,
                    'status_label' => $application->status_label,
                    'status_color' => $application->status_color,
                    'created_at' => $application->created_at,
                    'perijinan' => [
                        'id' => $application->perijinan->id,
                        'nama_perijinan' => $application->perijinan->nama_perijinan,
                    ],
                    'validasi_records' => $application->validasiRecords->map(function($validasi) {
                        // Determine validator label and display info
                        $displayRole = $validasi->validationFlow->role_label ?? 'Validator';
                        
                        return [
                            'id' => $validasi->id,
                            'status' => $validasi->status,
                            'status_label' => $validasi->status_label,
                            'status_color' => $validasi->status_color,
                            'catatan' => $validasi->catatan,
                            'validated_at' => $validasi->validated_at,
                            'validation_flow' => [
                                'role' => $validasi->validationFlow->role,
                                'role_label' => $displayRole,
                            ],
                            'validator' => $validasi->validator ? [
                                'role_label' => $displayRole,
                                // We mask the specific name for security/privacy
                                'name' => maskName($validasi->validator->name),
                            ] : null,
                        ];
                    }),
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error loading application detail: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat detail pengajuan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show form pengajuan perizinan.
     */
    public function createPengajuan($perijinanId)
    {
        $user = Auth::user();

        // Generate CAPTCHA
        session([
            'pengajuan_num1' => rand(1, 10),
            'pengajuan_num2' => rand(1, 10),
        ]);

        $perijinan = Perijinan::with([
            'activeFormFields' => function ($query) {
                $query->where('form_type', 'global')
                    ->orderBy('order', 'asc')
                    ->orderBy('id', 'asc'); // Fallback sorting by ID if order is same
            },
            'activeValidationFlows'
        ])->findOrFail($perijinanId);

        $userDokumens = \App\Models\UserDokumen::with('masterDokumen')
            ->where('user_id', $user->id)
            ->get();

        $renewFromApp = null;
        if (request()->filled('renew_from')) {
            $renewFromApp = DataPerijinan::where('id', request()->renew_from)
                ->where('user_id', $user->id)
                ->whereIn('status', ['approved', 'diperbaiki'])
                ->first();
        }

        $pembetulanFromApp = null;
        if (request()->filled('pembetulan_from')) {
            $pembetulanFromApp = DataPerijinan::where('id', request()->pembetulan_from)
                ->where('user_id', $user->id)
                ->whereIn('status', ['approved', 'diperbaiki'])
                ->first();
        }

        return view('pemohon.pengajuan.create', compact('perijinan', 'user', 'userDokumens', 'renewFromApp', 'pembetulanFromApp'));
    }

    /**
     * Refresh CAPTCHA for pengajuan form.
     */
    public function refreshPengajuanCaptcha()
    {
        session([
            'pengajuan_num1' => rand(1, 10),
            'pengajuan_num2' => rand(1, 10),
        ]);

        return response()->json([
            'num1' => session('pengajuan_num1'),
            'num2' => session('pengajuan_num2'),
        ]);
    }

    /**
     * Store pengajuan perizinan.
     */
    public function storePengajuan(Request $request)
    {
        $user = Auth::user();

        // Validate CAPTCHA first
        $request->validate([
            'captcha' => 'required|numeric',
        ], [
            'captcha.required' => 'Silakan masukkan hasil penjumlahan.',
            'captcha.numeric' => 'Hasil penjumlahan harus berupa angka.',
        ]);

        // Check CAPTCHA
        $captchaAnswer = ($request->session()->get('pengajuan_num1', 0) + $request->session()->get('pengajuan_num2', 0));
        if ($request->captcha != $captchaAnswer) {
            return redirect()->back()
                ->withInput()
                ->with('captcha_error', 'Hasil penjumlahan CAPTCHA salah. Silakan coba lagi.');
        }

        // Clear CAPTCHA after verification
        session()->forget(['pengajuan_num1', 'pengajuan_num2']);

        $request->validate([
            'perijinan_id' => 'required|exists:perijinan,id',
            'form_fields' => 'nullable|array',
            'pernyataan' => 'required|accepted',
        ], [
            'pernyataan.required' => 'Anda harus menyetujui pernyataan pertanggungjawaban data.',
            'pernyataan.accepted' => 'Anda harus menyetujui pernyataan pertanggungjawaban data.',
        ]);

        $perijinan = Perijinan::with(['activeFormFields' => function ($query) {
            $query->where('form_type', 'global');
        }])->findOrFail($request->perijinan_id);

        // ===============================
        // 🔹 VALIDASI DINAMIS
        // ===============================
        $validationRules = [];
        $validationMessages = [];

        foreach ($perijinan->activeFormFields as $field) {

            $fieldKey = 'form_fields.' . $field->id;

            if ($field->type !== 'file' && $field->type !== 'pas_foto' && $field->type !== 'gambar') {

                $rules = [];

                if ($field->is_required) {
                    $rules[] = 'required';
                    $validationMessages[$fieldKey . '.required'] = "Field {$field->label} wajib diisi.";
                } else {
                    $rules[] = 'nullable';
                }

                if ($field->type === 'email') {
                    $rules[] = 'email';
                }

                if ($field->type === 'number') {
                    $rules[] = 'numeric';
                }

                $validationRules[$fieldKey] = $rules;
            }
        }

        // ===============================
        // 🔹 VALIDASI FILE DINAMIS & CEK EXISTING FILE
        // ===============================
        $formFiles = $request->file('form_files');
        $existingFiles = $request->input('existing_files', []);
        $oldFiles = $request->input('old_files', []);

        foreach ($perijinan->activeFormFields as $field) {
            if (($field->type === 'file' || $field->type === 'pas_foto' || $field->type === 'gambar') && $field->is_required) {
                // If it's required but NOT in uploaded files, NOT in existing files, and NOT in old files, throw error
                if (empty($formFiles[$field->id]) && empty($existingFiles[$field->id]) && empty($oldFiles[$field->id])) {
                    $validationRules["form_files.{$field->id}"] = 'required';
                    $validationMessages["form_files.{$field->id}.required"] = "Field {$field->label} wajib diisi.";
                }
            }
        }

        if ($formFiles) {
            foreach ($formFiles as $fieldId => $files) {
                $field = $perijinan->activeFormFields->firstWhere('id', $fieldId);

                if ($field) {
                    $allowedTypes = $field->file_types 
                        ? implode(',', array_map('trim', explode(',', $field->file_types))) 
                        : (($field->type === 'pas_foto' || $field->type === 'gambar') ? 'jpg,jpeg,png' : 'pdf,doc,docx,jpg,jpeg,png');
                    $maxSize = $field->max_file_size ? ($field->max_file_size * 1024) : 10240; // in KB

                    foreach ((array) $files as $index => $file) {
                        $ruleKey = "form_files.$fieldId.$index";
                        $rules = ['file', "mimes:{$allowedTypes}", "max:{$maxSize}"];
                        $validationRules[$ruleKey] = $rules;
                        
                        $validationMessages["{$ruleKey}.mimes"] = "Format file {$field->label} harus berupa: {$allowedTypes}.";
                        $validationMessages["{$ruleKey}.max"] = "Ukuran file {$field->label} maksimal " . ($field->max_file_size ?: 10) . " MB.";
                    }
                }
            }
        }

        // ===============================
        // 🔹 VALIDASI EXISTING FILES (DOKUMEN SAYA)
        // ===============================
        if (!empty($existingFiles)) {
            foreach ($existingFiles as $fieldId => $userDokumenId) {
                if ($userDokumenId) {
                    $field = $perijinan->activeFormFields->firstWhere('id', $fieldId);
                    if ($field) {
                        $userDoc = \App\Models\UserDokumen::find($userDokumenId);
                        if ($userDoc) {
                            $originalPath = public_path($userDoc->file_path);
                            if (file_exists($originalPath)) {
                                $extension = strtolower(pathinfo($originalPath, PATHINFO_EXTENSION));
                                $allowedTypesStr = strtolower($field->file_types ?: 'pdf,doc,docx,jpg,jpeg,png');
                                $allowedTypes = array_map('trim', explode(',', $allowedTypesStr));
                                
                                if (!in_array($extension, $allowedTypes)) {
                                    $fieldKey = "existing_files.{$fieldId}";
                                    // Use a closure or standard rules to trigger validation error
                                    $validationRules[$fieldKey] = [
                                        function ($attribute, $value, $fail) use ($field, $allowedTypesStr) {
                                            $fail("Format file pada dokumen saya untuk berkas '{$field->label}' tidak sesuai. Harus berupa: {$allowedTypesStr}.");
                                        }
                                    ];
                                }

                                $fileSizeInKb = filesize($originalPath) / 1024;
                                $maxMB = ($field->max_file_size ?: 10);
                                $maxSize = $maxMB * 1024;
                                if ($fileSizeInKb > $maxSize) {
                                    $fieldKey = "existing_files.{$fieldId}";
                                    $validationRules[$fieldKey][] = function ($attribute, $value, $fail) use ($field, $maxMB) {
                                        $fail("Ukuran berkas pada dokumen saya untuk '{$field->label}' melebihi batas maksimal {$maxMB} MB.");
                                    };
                                }
                            }
                        }
                    }
                }
            }
        }

        $request->validate($validationRules, $validationMessages);

        // ===============================
        // 🔹 PROSES SIMPAN
        // ===============================
        try {
            DB::beginTransaction();

            $uploadedFiles = [];

            // Proses Existing Files (Dokumen Saya)
            if (!empty($existingFiles)) {
                foreach ($existingFiles as $fieldId => $userDokumenId) {
                    if ($userDokumenId) {
                        $userDoc = \App\Models\UserDokumen::find($userDokumenId);
                        if ($userDoc && file_exists(public_path($userDoc->file_path))) {
                            // Cukup simpan path nya atau copy file.
                            // Kita simpan path referensinya saja untuk menghemat space,
                            // tapi disarankan untuk copy file agar independen per pengajuan.
                            $originalPath = public_path($userDoc->file_path);
                            
                            $extension = pathinfo($originalPath, PATHINFO_EXTENSION);
                            $filename = 'doc_saya_' . $fieldId . '_' . time() . '.' . $extension;
                            $uploadPath = public_path('uploads/perijinan/' . $perijinan->id);
                            
                            if (!file_exists($uploadPath)) {
                                mkdir($uploadPath, 0755, true);
                            }
                            
                            copy($originalPath, $uploadPath . '/' . $filename);
                            
                            $uploadedFiles[$fieldId][] = 'uploads/perijinan/' . $perijinan->id . '/' . $filename;
                        }
                    }
                }
            }

            // Proses Uploaded Files
            if ($formFiles) {
                foreach ($formFiles as $fieldId => $files) {
                    $field = $perijinan->activeFormFields->firstWhere('id', $fieldId);
                    if ($field) {
                        foreach ((array) $files as $file) {
                            if ($file && $file->isValid()) {
                                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                                $extension = $file->getClientOriginalExtension();
                                $filename = $originalName . '_' . time() . '.' . $extension;

                                $uploadPath = public_path('uploads/perijinan/' . $perijinan->id);

                                if (!file_exists($uploadPath)) {
                                    mkdir($uploadPath, 0755, true);
                                }

                                $file->move($uploadPath, $filename);

                                $uploadedFiles[$fieldId][] = 'uploads/perijinan/' . $perijinan->id . '/' . $filename;
                            }
                        }
                    }
                }
            }

            // Copy Old Files from previous application (renew/pembetulan) if no new file is uploaded/selected
            if (!empty($oldFiles)) {
                foreach ($oldFiles as $fieldId => $paths) {
                    // Check if we already have files for this field (uploaded or selected from My Documents)
                    if (empty($uploadedFiles[$fieldId])) {
                        foreach ((array) $paths as $oldPath) {
                            $originalPath = public_path($oldPath);
                            if (file_exists($originalPath)) {
                                $extension = pathinfo($originalPath, PATHINFO_EXTENSION);
                                $originalName = pathinfo($originalPath, PATHINFO_FILENAME);
                                $filename = $originalName . '_' . time() . '.' . $extension;
                                $uploadPath = public_path('uploads/perijinan/' . $perijinan->id);

                                if (!file_exists($uploadPath)) {
                                    mkdir($uploadPath, 0755, true);
                                }

                                copy($originalPath, $uploadPath . '/' . $filename);
                                $uploadedFiles[$fieldId][] = 'uploads/perijinan/' . $perijinan->id . '/' . $filename;
                            }
                        }
                    }
                }
            }

            // ===============================
            // 🔹 SIMPAN DATA
            // ===============================
            $perpanjangDariId = null;
            $rootPerpanjangId = null;

            if ($request->filled('renew_from')) {
                $renewFromApp = DataPerijinan::where('id', $request->renew_from)
                    ->where('user_id', $user->id)
                    ->first(); // Get matching app

                if ($renewFromApp) {
                    $perpanjangDariId = $renewFromApp->id;
                    $rootPerpanjangId = $renewFromApp->root_perpanjang_id ?? $renewFromApp->id;

                    // Update status of previous application to 'diperpanjang'
                    $renewFromApp->update([
                        'status' => 'diperpanjang'
                    ]);
                }
            }

            $pembetulanDariId = null;
            $isPembetulan = false;

            if ($request->filled('pembetulan_from')) {
                $pembetulanFromApp = DataPerijinan::where('id', $request->pembetulan_from)
                    ->where('user_id', $user->id)
                    ->first();

                if ($pembetulanFromApp) {
                    $pembetulanDariId = $pembetulanFromApp->id;
                    $isPembetulan = true;
                }
            }

            if ($isPembetulan) {
                $data = $pembetulanFromApp;
                
                // Clear old validation records
                $data->validasiRecords()->delete();

                // Re-evaluate BO data matching global fields
                $boData = $data->bo_data ?? [];
                $allFields = \App\Models\PerijinanFormField::where('perijinan_id', $perijinan->id)
                    ->where('is_active', true)
                    ->get();
                $boFields = $allFields->where('form_type', 'bo');
                
                foreach ($boFields as $field) {
                    $matchingGlobalField = $allFields
                        ->where('form_type', 'global')
                        ->where('name', $field->name)
                        ->first() ?? $allFields
                        ->where('form_type', 'global')
                        ->filter(fn($f) => strtolower($f->label) === strtolower($field->label))
                        ->first();
                        
                    if ($matchingGlobalField) {
                        $isFile = in_array($field->type, ['file', 'pas_foto', 'gambar']);
                        if ($isFile) {
                            $globalFiles = $uploadedFiles[$matchingGlobalField->id] ?? [];
                            $globalFile = is_array($globalFiles) ? ($globalFiles[0] ?? null) : $globalFiles;
                            if (!empty($globalFile)) {
                                $boData[$field->name] = $globalFile;
                            }
                        } else if ($field->type !== 'table') {
                            $val = ($request->form_fields ?? [])[$matchingGlobalField->id] ?? '';
                            $boData[$field->name] = $val;
                        }
                    }
                }

                // Physically delete the old signed files and drafts if they exist
                if ($data->file_izin_tte && file_exists(public_path($data->file_izin_tte))) {
                    @unlink(public_path($data->file_izin_tte));
                }
                if ($data->file_izin && file_exists(public_path($data->file_izin))) {
                    @unlink(public_path($data->file_izin));
                }
                if ($data->file_izin_pembetulan && file_exists(public_path($data->file_izin_pembetulan))) {
                    @unlink(public_path($data->file_izin_pembetulan));
                }

                $updateData = [
                    'status' => 'submitted',
                    'current_step' => 1,
                    'is_pembetulan' => true,
                    'pembetulan_dari_id' => $pembetulanDariId,
                    'form_data' => $request->form_fields ?? [],
                    'form_files' => !empty($uploadedFiles) ? $uploadedFiles : null,
                    'bo_data' => $boData,
                    'data_pemohon' => [
                        'name' => $user->name,
                        'email' => $user->email,
                        'nip' => $user->nip,
                        'no_hp' => $user->no_hp,
                        'status_pemohon' => $user->status_pemohon,
                        'nama_perusahaan' => $user->nama_perusahaan,
                        'npwp' => $user->npwp,
                    ],
                    'submitted_at' => now(),
                    'approved_at' => null,
                    'completed_at' => null,
                    'rejected_at' => null,
                    'file_izin_tte' => null,
                    'file_izin' => null,
                    'file_izin_pembetulan' => null, // Reset the BO manual upload file as well
                ];

                $data->update($updateData);
            } else {
                $perpanjangDariId = null;
                $rootPerpanjangId = null;

                if ($request->filled('renew_from')) {
                    $renewFromApp = DataPerijinan::where('id', $request->renew_from)
                        ->where('user_id', $user->id)
                        ->first(); // Get matching app

                    if ($renewFromApp) {
                        $perpanjangDariId = $renewFromApp->id;
                        $rootPerpanjangId = $renewFromApp->root_perpanjang_id ?? $renewFromApp->id;

                        // Update status of previous application to 'diperpanjang'
                        $renewFromApp->update([
                            'status' => 'diperpanjang'
                        ]);
                    }
                }

                $data = DataPerijinan::create([
                    'user_id' => $user->id,
                    'perijinan_id' => $perijinan->id,
                    'status' => 'submitted',
                    'current_step' => 1,
                    'form_data' => $request->form_fields ?? [],
                    'form_files' => !empty($uploadedFiles) ? $uploadedFiles : null,
                    'data_pemohon' => [
                        'name' => $user->name,
                        'email' => $user->email,
                        'nip' => $user->nip,
                        'no_hp' => $user->no_hp,
                        'status_pemohon' => $user->status_pemohon,
                        'nama_perusahaan' => $user->nama_perusahaan,
                        'npwp' => $user->npwp,
                    ],
                    'submitted_at' => now(),
                    'perpanjang_dari_id' => $perpanjangDariId,
                    'root_perpanjang_id' => $rootPerpanjangId,
                ]);
            }

            // ===============================
            // 🔹 VALIDASI FLOW
            // ===============================
            $validationFlows = $perijinan->activeValidationFlows()->orderBy('order')->get();

            if ($data->pembetulan_dari_id) {
                $validationFlows = $validationFlows->filter(function($flow) {
                    return in_array($flow->role, ['fo', 'bo', 'verifikator', 'kadin']);
                });
            }

            $validationIndex = 1;
            foreach ($validationFlows as $flow) {
                // Untuk role tertentu (FO, BO, Verifikator, Kadin), user_id bisa NULL
                // karena semua user dengan role tersebut bisa validasi
                $assignedUserId = $flow->assigned_user_id;
                
                DataPerijinanValidasi::create([
                    'data_perijinan_id' => $data->id,
                    'validation_flow_id' => $flow->id,
                    'user_id' => $assignedUserId, // NULL untuk FO/BO/Verifikator/Kadin jika tidak di-assign
                    'status' => 'pending',
                    'order' => $validationIndex++,
                ]);
            }

            // ===============================
            // 🔹 GENERATE DOKUMEN SURAT
            // ===============================
            try {
                $data->load(['user.provinsi', 'user.kabupaten', 'user.kecamatan', 'user.kelurahan', 'perijinan']);
                $generatedDocs = \App\Services\DocumentGenerator::generateDocuments($data);
                $data->update([
                    'file_pernyataan' => $generatedDocs['file_pernyataan'] ?? null,
                    'file_permohonan' => $generatedDocs['file_permohonan'] ?? null,
                    'file_keabsahan' => $generatedDocs['file_keabsahan'] ?? null,
                    'file_rekom' => $generatedDocs['file_rekom'] ?? null,
                    'file_rekom_multi' => $generatedDocs['file_rekom_multi'] ?? null,
                    'file_izin' => $generatedDocs['file_izin'] ?? null,
                    'file_rekom_tte' => $data->is_pembetulan ? $data->file_rekom_tte : null,
                    'file_rekom_multi_tte' => $data->is_pembetulan ? $data->file_rekom_multi_tte : null,
                    'file_izin_tte' => null,
                ]);
            } catch (\Exception $docEx) {
                \Log::error('Error generating permit letters in storePengajuan: ' . $docEx->getMessage());
            }

            DB::commit();

            // Log activity
            ActivityLog::log(
                'Mengajukan perizinan baru: ' . $perijinan->nama_perijinan,
                $data,
                'created',
                [
                    'no_registrasi' => $data->no_registrasi,
                    'perijinan' => $perijinan->nama_perijinan,
                ],
                'data_perijinan'
            );

            return redirect()->route('pemohon.pengajuan.success', $data->id)
                ->with('success', 'Pengajuan berhasil dikirim. Nomor registrasi: ' . $data->no_registrasi);
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Error storePengajuan: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat mengirim pengajuan.');
        }
    }

    /**
     * Show success page after submission.
     */
    public function successPengajuan($id)
    {
        $user = Auth::user();
        $data = DataPerijinan::with(['perijinan', 'validasiRecords.validationFlow'])
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        return view('pemohon.pengajuan.success', compact('data'));
    }

    /**
     * Show tracking page for user's applications.
     */
    public function tracking()
    {
        $user = Auth::user();

        $data = DataPerijinan::with(['perijinan', 'validasiRecords.validationFlow'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('pemohon.tracking.index', compact('data'));
    }

    /**
     * Show detail tracking for specific application.
     */
    public function trackingDetail($id)
    {
        $user = Auth::user();

        $data = DataPerijinan::with([
            'perijinan.activeFormFields',
            'validasiRecords.validationFlow.assignedUser.opd',
            'validasiRecords.validator.opd'
        ])
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Fetch SKM questions if needed
        $skmQuestions = [];
        if (in_array($data->status, ['approved', 'diperbaiki']) && !$data->isSkmFilled()) {
            $skmQuestions = \App\Models\DataSkm::aktif()->orderBy('urutan')->get();
            
            // Generate CAPTCHA if not exists
            if (!session()->has('captcha_num1')) {
                session([
                    'captcha_num1' => rand(1, 10),
                    'captcha_num2' => rand(1, 10),
                ]);
            }
        }

        // Fetch renewal history chain
        $rootPerpanjangId = $data->root_perpanjang_id ?? $data->id;
        $renewalHistory = DataPerijinan::where(function($q) use ($rootPerpanjangId) {
            $q->where('id', $rootPerpanjangId)
              ->orWhere('root_perpanjang_id', $rootPerpanjangId);
        })
        ->orderBy('created_at', 'asc')
        ->get();

        return view('pemohon.tracking.detail', compact('data', 'skmQuestions', 'renewalHistory'));
    }

    /**
     * Show edit form for pengajuan that needs revision.
     */
    public function editPengajuan($id)
    {
        $user = Auth::user();

        // Generate CAPTCHA
        session([
            'pengajuan_num1' => rand(1, 10),
            'pengajuan_num2' => rand(1, 10),
        ]);

        $data = DataPerijinan::with([
            'perijinan.activeFormFields' => function ($query) {
                $query->where('form_type', 'global')
                    ->orderBy('order', 'asc')->orderBy('id', 'asc');
            }
        ])
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->where('status', 'perbaikan') // Only allow edit if status is 'perbaikan'
            ->firstOrFail();

        $userDokumens = \App\Models\UserDokumen::with('masterDokumen')
            ->where('user_id', $user->id)
            ->get();

        return view('pemohon.pengajuan.edit', compact('data', 'userDokumens'));
    }

    /**
     * Update pengajuan after revision.
     */
    public function updatePengajuan(Request $request, $id)
    {
        $user = Auth::user();

        \Log::info('updatePengajuan called', [
            'user_id' => $user->id,
            'application_id' => $id,
            'status' => $request->status
        ]);

        $data = DataPerijinan::with([
            'perijinan.activeFormFields' => function ($query) {
                $query->where('form_type', 'global');
            }
        ])
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->where('status', 'perbaikan')
            ->firstOrFail();

        \Log::info('Application found', [
            'application_id' => $id,
            'current_status' => $data->status,
            'perijinan_id' => $data->perijinan_id
        ]);

        // Validate CAPTCHA first
        $request->validate([
            'captcha' => 'required|numeric',
        ], [
            'captcha.required' => 'Silakan masukkan hasil penjumlahan.',
            'captcha.numeric' => 'Hasil penjumlahan harus berupa angka.',
        ]);

        // Check CAPTCHA
        $captchaAnswer = ($request->session()->get('pengajuan_num1', 0) + $request->session()->get('pengajuan_num2', 0));
        if ($request->captcha != $captchaAnswer) {
            return redirect()->back()
                ->withInput()
                ->with('captcha_error', 'Hasil penjumlahan CAPTCHA salah. Silakan coba lagi.');
        }

        // Clear CAPTCHA after verification
        session()->forget(['pengajuan_num1', 'pengajuan_num2']);

        $request->validate([
            'form_fields' => 'nullable|array',
            'pernyataan' => 'required|accepted',
        ], [
            'pernyataan.required' => 'Anda harus menyetujui pernyataan pertanggungjawaban data.',
            'pernyataan.accepted' => 'Anda harus menyetujui pernyataan pertanggungjawaban data.',
        ]);

        $perijinan = $data->perijinan;

        // ===============================
        // 🔹 VALIDASI DINAMIS
        // ===============================
        $validationRules = [];
        $validationMessages = [];

        foreach ($perijinan->activeFormFields as $field) {
            $fieldKey = 'form_fields.' . $field->id;

            if ($field->type !== 'file' && $field->type !== 'pas_foto' && $field->type !== 'gambar') {
                $rules = [];

                // Check if field has value in request
                $hasValue = isset($request->form_fields[$field->id]) && 
                           !empty($request->form_fields[$field->id]);
                
                // Only validate required if field is being submitted
                if ($field->is_required && $hasValue) {
                    $rules[] = 'required';
                    $validationMessages[$fieldKey . '.required'] = "Field {$field->label} wajib diisi.";
                } else {
                    $rules[] = 'nullable';
                }

                if ($field->type === 'email') {
                    $rules[] = 'email';
                }

                if ($field->type === 'number') {
                    $rules[] = 'numeric';
                }

                if ($field->type === 'date') {
                    $rules[] = 'date';
                }

                if ($field->type === 'url') {
                    $rules[] = 'url';
                }

                $validationRules[$fieldKey] = $rules;
            } else {
                // File type - always optional for update (only validate if uploading)
                $validationRules[$fieldKey] = 'nullable|array';
            }
        }

        \Log::info('Validation rules', $validationRules);

        $validatedData = $request->validate($validationRules, $validationMessages);

        \Log::info('Validation passed', $validatedData);

        // ===============================
        // 🔹 VALIDASI FILE UNTUK UPDATE PENGAJUAN
        // ===============================
        $formFiles = $request->file('form_fields');
        if ($formFiles) {
            foreach ($formFiles as $fieldId => $files) {
                $field = $perijinan->activeFormFields->firstWhere('id', $fieldId);
                if ($field) {
                    $allowedTypes = $field->file_types 
                        ? implode(',', array_map('trim', explode(',', $field->file_types))) 
                        : (($field->type === 'pas_foto' || $field->type === 'gambar') ? 'jpg,jpeg,png' : 'pdf,doc,docx,jpg,jpeg,png');
                    $maxSize = $field->max_file_size ? ($field->max_file_size * 1024) : 10240; // in KB

                    foreach ((array) $files as $index => $file) {
                        if ($file) {
                            $ruleKey = "form_fields_files.$fieldId.$index";
                            $rules = ['file', "mimes:{$allowedTypes}", "max:{$maxSize}"];
                            $validationRules[$ruleKey] = $rules;
                            
                            $validationMessages["{$ruleKey}.mimes"] = "Format file {$field->label} harus berupa: {$allowedTypes}.";
                            $validationMessages["{$ruleKey}.max"] = "Ukuran file {$field->label} maksimal " . ($field->max_file_size ?: 10) . " MB.";
                        }
                    }
                }
            }
        }

        if (!empty($existingFiles)) {
            foreach ($existingFiles as $fieldId => $userDokumenId) {
                if ($userDokumenId) {
                    $field = $perijinan->activeFormFields->firstWhere('id', $fieldId);
                    if ($field) {
                        $userDoc = \App\Models\UserDokumen::find($userDokumenId);
                        if ($userDoc) {
                            $originalPath = public_path($userDoc->file_path);
                            if (file_exists($originalPath)) {
                                $extension = strtolower(pathinfo($originalPath, PATHINFO_EXTENSION));
                                $allowedTypesStr = strtolower($field->file_types ?: 'pdf,doc,docx,jpg,jpeg,png');
                                $allowedTypes = array_map('trim', explode(',', $allowedTypesStr));
                                
                                if (!in_array($extension, $allowedTypes)) {
                                    $fieldKey = "existing_files.{$fieldId}";
                                    $validationRules[$fieldKey] = [
                                        function ($attribute, $value, $fail) use ($field, $allowedTypesStr) {
                                            $fail("Format file pada dokumen saya untuk berkas '{$field->label}' tidak sesuai. Harus berupa: {$allowedTypesStr}.");
                                        }
                                    ];
                                }

                                $fileSizeInKb = filesize($originalPath) / 1024;
                                $maxMB = ($field->max_file_size ?: 10);
                                $maxSize = $maxMB * 1024;
                                if ($fileSizeInKb > $maxSize) {
                                    $fieldKey = "existing_files.{$fieldId}";
                                    $validationRules[$fieldKey][] = function ($attribute, $value, $fail) use ($field, $maxMB) {
                                        $fail("Ukuran berkas pada dokumen saya untuk '{$field->label}' melebihi batas maksimal {$maxMB} MB.");
                                    };
                                }
                            }
                        }
                    }
                }
            }
        }

        if (!empty($validationRules)) {
            $request->validate(array_filter($validationRules), $validationMessages);
        }

        // ===============================
        // 🔹 UPLOAD FILES & EXISTING FILES
        // ===============================
        $formFiles = $request->file('form_fields');
        $existingFiles = $request->input('existing_files', []);
        $uploadedFiles = [];

        // Proses Existing Files (Dokumen Saya)
        if (!empty($existingFiles)) {
            foreach ($existingFiles as $fieldId => $userDokumenId) {
                if ($userDokumenId) {
                    $userDoc = \App\Models\UserDokumen::find($userDokumenId);
                    if ($userDoc && file_exists(public_path($userDoc->file_path))) {
                        $originalPath = public_path($userDoc->file_path);
                        $extension = pathinfo($originalPath, PATHINFO_EXTENSION);
                        $filename = 'doc_saya_' . $fieldId . '_' . time() . '.' . $extension;
                        $uploadPath = public_path('uploads/perijinan/' . $perijinan->id);
                        
                        if (!file_exists($uploadPath)) {
                            mkdir($uploadPath, 0755, true);
                        }
                        
                        copy($originalPath, $uploadPath . '/' . $filename);
                        
                        $uploadedFiles[$fieldId][] = 'uploads/perijinan/' . $perijinan->id . '/' . $filename;
                    }
                }
            }
        }

        if ($formFiles) {
            foreach ($formFiles as $fieldId => $files) {
                $field = $perijinan->activeFormFields->firstWhere('id', $fieldId);

                if ($field) {
                    foreach ((array) $files as $file) {
                        if ($file && $file->isValid()) {
                            // Generate nama file unik dengan tetap mempertahankan nama asli
                            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                            $extension = $file->getClientOriginalExtension();
                            $filename = $originalName . '_' . time() . '.' . $extension;

                            // Path upload
                            $uploadPath = public_path('uploads/perijinan/' . $perijinan->id);

                            if (!file_exists($uploadPath)) {
                                mkdir($uploadPath, 0755, true);
                            }

                            // Simpan file
                            $file->move($uploadPath, $filename);

                            $uploadedFiles[$fieldId][] = 'uploads/perijinan/' . $perijinan->id . '/' . $filename;
                        }
                    }
                }
            }
        }

        // ===============================
        // 🔹 UPDATE DATA
        // ===============================
        // Keep existing form data and update only submitted fields
        $formData = $data->form_data ?? [];
        
        // Update form data with new values (only for fields that were submitted)
        if ($request->form_fields) {
            foreach ($request->form_fields as $fieldId => $value) {
                // Handle checkbox arrays
                if (is_array($value)) {
                    $formData[$fieldId] = implode(',', $value);
                } else {
                    $formData[$fieldId] = $value;
                }
            }
        }

        // Merge files - keep existing files and add new files
        $existingFiles = $data->form_files ?? [];
        $mergedFiles = $existingFiles; // Start with existing files
        
        // Add new files for each field (without removing old files)
        foreach ($uploadedFiles as $fieldId => $newFiles) {
            if (!isset($mergedFiles[$fieldId])) {
                $mergedFiles[$fieldId] = [];
            }
            // Merge new files with existing files for this field
            $mergedFiles[$fieldId] = array_merge($mergedFiles[$fieldId], $newFiles);
        }
        
        // Handle deleted files
        if ($request->deleted_files) {
            foreach ($request->deleted_files as $fieldId => $deletedFilesString) {
                if ($deletedFilesString && isset($mergedFiles[$fieldId])) {
                    $deletedFiles = explode(',', $deletedFilesString);
                    // Remove deleted files from merged files
                    $mergedFiles[$fieldId] = array_filter($mergedFiles[$fieldId], function($file) use ($deletedFiles) {
                        return !in_array($file, $deletedFiles);
                    });
                    
                    // Re-index array
                    $mergedFiles[$fieldId] = array_values($mergedFiles[$fieldId]);
                    
                    // Remove field if no files left
                    if (empty($mergedFiles[$fieldId])) {
                        unset($mergedFiles[$fieldId]);
                    }
                }
            }
        }

        \Log::info('Files merged', [
            'existing_count' => count($existingFiles),
            'uploaded_count' => count($uploadedFiles),
            'deleted_count' => $request->deleted_files ? count($request->deleted_files) : 0,
            'final_count' => count($mergedFiles)
        ]);

        $data->update([
            'form_data' => $formData,
            'form_files' => $mergedFiles,
            'status' => 'submitted', // Back to submitted status
            'catatan_perbaikan' => null, // Clear catatan perbaikan
            'catatan_pemohon' => $request->catatan_pemohon, // Save applicant note
            'current_step' => 1, // Reset to first validation step
        ]);

        // ===============================
        // 🔹 RE-GENERATE DOKUMEN SURAT
        // ===============================
        try {
            $data->load(['user.provinsi', 'user.kabupaten', 'user.kecamatan', 'user.kelurahan', 'perijinan']);
            $generatedDocs = \App\Services\DocumentGenerator::generateDocuments($data);
            $data->update([
                'file_pernyataan' => $generatedDocs['file_pernyataan'] ?? null,
                'file_permohonan' => $generatedDocs['file_permohonan'] ?? null,
                'file_keabsahan' => $generatedDocs['file_keabsahan'] ?? null,
                'file_rekom' => $generatedDocs['file_rekom'] ?? null,
                'file_rekom_multi' => $generatedDocs['file_rekom_multi'] ?? null,
                'file_izin' => $generatedDocs['file_izin'] ?? null,
            ]);
        } catch (\Exception $docEx) {
            \Log::error('Error re-generating permit letters in updatePengajuan: ' . $docEx->getMessage());
        }

        \Log::info('Application updated', [
            'application_id' => $id,
            'new_status' => 'submitted',
            'form_data_count' => count($formData),
            'form_files_count' => count($mergedFiles)
        ]);

        // Reset all validation steps to pending
        $data->validasiRecords()->update([
            'status' => 'pending',
            'user_id' => null, // Clear assigned user for collective roles
            'catatan' => null,
            'validated_at' => null,
        ]);

        \Log::info('Validation records reset', [
            'application_id' => $id,
            'validation_count' => $data->validasiRecords()->count()
        ]);

        // Activate first validation step
        $firstValidasi = $data->validasiRecords()->where('order', 1)->first();
        if ($firstValidasi) {
            $firstValidasi->update(['status' => 'pending']);
            \Log::info('First validation step activated', [
                'validation_id' => $firstValidasi->id,
                'order' => 1
            ]);
        }

        // Log activity
        ActivityLog::log(
            'Memperbaiki pengajuan perijinan',
            $data,
            'updated',
            [
                'no_registrasi' => $data->no_registrasi,
                'status' => 'submitted',
            ],
            'data_perijinan'
        );

        \Log::info('Redirecting to tracking detail', [
            'application_id' => $id
        ]);

        return redirect()->route('pemohon.tracking.detail', $id)
            ->with('success', 'Pengajuan berhasil diperbaiki dan dikirim kembali untuk validasi.');
    }
}
