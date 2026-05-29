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

class DashboardController extends Controller
{
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

        // Sorting
        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');
        $allowedSorts = ['created_at', 'no_registrasi', 'status'];
        
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }

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
        $query = Perijinan::query();

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_perijinan', 'like', "%{$search}%")
                    ->orWhere('dasar_hukum', 'like', "%{$search}%")
                    ->orWhere('persyaratan', 'like', "%{$search}%");
            });
        }

        $perijinans = $query->orderBy('nama_perijinan')->paginate(12)->withQueryString();

        return view('pemohon.perijinan.index', compact('perijinans'));
    }

    /**
     * Display perijinan detail as JSON for modal.
     */
    public function perijinanDetail($id)
    {
        try {
            $perijinan = Perijinan::with([
                'activeValidationFlows.assignedUser',
                'activeFormFields'
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

        $perijinan = Perijinan::with([
            'activeFormFields' => function ($query) {
                $query->orderBy('order', 'asc')
                    ->orderBy('id', 'asc'); // Fallback sorting by ID if order is same
            },
            'activeValidationFlows'
        ])->findOrFail($perijinanId);

        $userDokumens = \App\Models\UserDokumen::with('masterDokumen')
            ->where('user_id', $user->id)
            ->get();

        return view('pemohon.pengajuan.create', compact('perijinan', 'user', 'userDokumens'));
    }

    /**
     * Store pengajuan perizinan.
     */
    public function storePengajuan(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'perijinan_id' => 'required|exists:perijinan,id',
            'form_fields' => 'nullable|array',
            'pernyataan' => 'required|accepted',
        ], [
            'pernyataan.required' => 'Anda harus menyetujui pernyataan pertanggungjawaban data.',
            'pernyataan.accepted' => 'Anda harus menyetujui pernyataan pertanggungjawaban data.',
        ]);

        $perijinan = Perijinan::with('activeFormFields')->findOrFail($request->perijinan_id);

        // ===============================
        // 🔹 VALIDASI DINAMIS
        // ===============================
        $validationRules = [];
        $validationMessages = [];

        foreach ($perijinan->activeFormFields as $field) {

            $fieldKey = 'form_fields.' . $field->id;

            if ($field->type !== 'file') {

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

        foreach ($perijinan->activeFormFields as $field) {
            if ($field->type === 'file' && $field->is_required) {
                // If it's required but NOT in uploaded files and NOT in existing files, throw error
                if (empty($formFiles[$field->id]) && empty($existingFiles[$field->id])) {
                    $validationRules["form_files.{$field->id}"] = 'required';
                    $validationMessages["form_files.{$field->id}.required"] = "Field {$field->label} wajib diisi.";
                }
            }
        }

        if ($formFiles) {
            foreach ($formFiles as $fieldId => $files) {
                $field = $perijinan->activeFormFields->firstWhere('id', $fieldId);

                if ($field) {
                    foreach ((array) $files as $index => $file) {
                        $ruleKey = "form_fields.$fieldId.$index";
                        $rules = ['file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240'];
                        $validationRules[$ruleKey] = $rules;
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

            // ===============================
            // 🔹 SIMPAN DATA
            // ===============================
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
            ]);

            // ===============================
            // 🔹 VALIDASI FLOW
            // ===============================
            $validationFlows = $perijinan->activeValidationFlows()->orderBy('order')->get();

            foreach ($validationFlows as $index => $flow) {
                // Untuk role tertentu (FO, BO, Verifikator, Kadin), user_id bisa NULL
                // karena semua user dengan role tersebut bisa validasi
                $assignedUserId = $flow->assigned_user_id;
                
                DataPerijinanValidasi::create([
                    'data_perijinan_id' => $data->id,
                    'validation_flow_id' => $flow->id,
                    'user_id' => $assignedUserId, // NULL untuk FO/BO/Verifikator/Kadin jika tidak di-assign
                    'status' => 'pending',
                    'order' => $index + 1,
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
                    'file_izin' => $generatedDocs['file_izin'] ?? null,
                ]);
            } catch (\Exception $docEx) {
                \Log::error('Error generating permit letters in storePengajuan: ' . $docEx->getMessage());
            }

            DB::commit();

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
            'perijinan',
            'validasiRecords.validationFlow.assignedUser',
            'validasiRecords.validator'
        ])
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        return view('pemohon.tracking.detail', compact('data'));
    }

    /**
     * Show edit form for pengajuan that needs revision.
     */
    public function editPengajuan($id)
    {
        $user = Auth::user();

        $data = DataPerijinan::with([
            'perijinan.activeFormFields' => function ($query) {
                $query->orderBy('order', 'asc')->orderBy('id', 'asc');
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
            'perijinan.activeFormFields'
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

            if ($field->type !== 'file') {
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
