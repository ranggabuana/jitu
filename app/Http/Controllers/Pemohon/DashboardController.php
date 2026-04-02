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
    public function index()
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

        // Get recent applications
        $recentApplications = DataPerijinan::with(['perijinan'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('pemohon.dashboard.index', compact(
            'user',
            'stats',
            'recentApplications'
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
                'activeValidationFlows',
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

        return view('pemohon.pengajuan.create', compact('perijinan', 'user'));
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
        // 🔹 VALIDASI FILE DINAMIS
        // ===============================
        $formFiles = $request->file('form_files');

        if ($formFiles) {
            foreach ($formFiles as $fieldId => $files) {

                $field = $perijinan->activeFormFields->firstWhere('id', $fieldId);

                if ($field) {
                    foreach ((array) $files as $index => $file) {

                        $ruleKey = "form_files.$fieldId.$index";

                        $rules = ['file', 'max:10240']; // max 10MB

                        if ($field->is_required) {
                            $rules[] = 'required';
                        }

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
            'validasiRecords.validationFlow',
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

        return view('pemohon.pengajuan.edit', compact('data'));
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
        // 🔹 UPLOAD FILES
        // ===============================
        $formFiles = $request->file('form_fields');
        $uploadedFiles = [];

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
            'current_step' => 1, // Reset to first validation step
        ]);

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

    /**
     * Show application details.
     */
    public function showApplication($id)
    {
        return response()->json([
            'success' => false,
            'message' => 'Fitur ini akan tersedia segera. Tabel aplikasi belum dibuat.'
        ]);
    }
}
