<?php

namespace App\Http\Controllers\Admin;

use App\Models\Perijinan;
use App\Models\PerijinanFormField;
use App\Models\PerijinanValidationFlow;
use App\Models\User;
use App\Models\Opd;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class PerijinanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');
        $perPage = $request->get('per_page', 10);

        // Validate per_page to prevent abuse
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;

        // Validate sort_by to prevent SQL injection
        $allowedSorts = ['nama_perijinan', 'id', 'created_at', 'updated_at'];
        $sortBy = in_array($sortBy, $allowedSorts) ? $sortBy : 'id';

        // Validate sort_order
        $sortOrder = in_array(strtolower($sortOrder), ['asc', 'desc']) ? $sortOrder : 'desc';

        $query = Perijinan::query();

        // Apply search filter
        if ($search) {
            $query->where('nama_perijinan', 'like', "%{$search}%")
                ->orWhere('dasar_hukum', 'like', "%{$search}%");
        }

        // Apply sorting
        $perijinans = $query->orderBy($sortBy, $sortOrder)->paginate($perPage);

        // Append query parameters to pagination links
        $perijinans->appends([
            'search' => $search,
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder,
            'per_page' => $perPage,
        ]);

        return view('perijinan.index', compact('perijinans', 'search', 'sortBy', 'sortOrder', 'perPage'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('perijinan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_perijinan' => 'required|string|max:255',
            'opsi_perpanjangan' => 'nullable|in:setelah_habis,sebelum_habis,keduanya',
            'dasar_hukum' => 'required|string',
            'persyaratan' => 'required|string',
            'prosedur' => 'required|string',
            'informasi_biaya' => 'nullable|string',
            'lama_waktu_proses' => 'nullable|string|max:255',
            'gambar_alur' => 'nullable|file|mimes:png,jpg,jpeg|max:2048',
        ]);

        $data = $request->all();

        // Handle gambar_alur upload
        if ($request->hasFile('gambar_alur')) {
            $gambarAlur = $request->file('gambar_alur');
            $gambarAlurName = time() . '_alur_' . str_replace(' ', '_', $request->nama_perijinan) . '.' . $gambarAlur->getClientOriginalExtension();
            
            // Ensure directory exists
            $uploadPath = public_path('uploads/data-perijinan');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            
            $gambarAlur->move($uploadPath, $gambarAlurName);
            $data['gambar_alur'] = 'uploads/data-perijinan/' . $gambarAlurName;
        }

        $perijinan = Perijinan::create($data);

        // Log activity
        ActivityLog::log(
            'Menambah jenis perijinan baru',
            $perijinan,
            'created',
            ['data' => $request->all()],
            'perijinan'
        );

        return redirect()->route('perijinan.index')
            ->with('success', 'Jenis Perijinan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $perijinan = Perijinan::with(['activeFormFields', 'validationFlows.assignedUser'])->findOrFail($id);
        return view('perijinan.show', compact('perijinan'));
    }

    /**
     * Show form builder page for managing form fields.
     */
    public function formBuilder(string $id)
    {
        $perijinan = Perijinan::with('formFields')->findOrFail($id);
        return view('perijinan.form-builder', compact('perijinan'));
    }

    /**
     * Preview the template by replacing placeholders with dummy data.
     */
    public function previewTemplate(Request $request, string $id)
    {
        $perijinan = Perijinan::findOrFail($id);
        $templateType = $request->input('template_type', 'rekom'); // 'rekom' or 'izin'
        $htmlContent = $request->input('template_content', '');

        // Dummy Data Replacements
        $replacements = [
            '[NAMA PEMOHON]' => 'Joko Susilo',
            '[NIK]' => '3304123456789012',
            '[ALAMAT LENGKAP]' => 'Jl. Pemuda No. 45, Kel/Desa Krandegan, Kec. Banjarnegara, Kab/Kota Banjarnegara, Provinsi Jawa Tengah',
            '[NO HP]' => '081234567890',
            '[EMAIL]' => 'joko.susilo@example.com',
            '[PEKERJAAN]' => 'Wiraswasta',
            '[NAMA IZIN]' => $perijinan->nama_perijinan,
            '[TANGGAL]' => \Carbon\Carbon::now()->translatedFormat('d F Y'),
            '[NO REGISTRASI]' => 'REG-' . date('Ymd') . '-12345',
            '[NOMOR URUT]' => '123',
        ];

        // Derive Kode OPD from validation flow for preview
        $kodeOpd = 'OPD';
        if ($templateType === 'izin') {
            $kodeOpd = 'DPMPTSP';
        } else {
            $validationFlowWithOpd = $perijinan->activeValidationFlows()      
                ->whereIn('role', ['operator_opd', 'kepala_opd'])
                ->whereNotNull('assigned_user_id')
                ->with('assignedUser.opd')
                ->get()
                ->first(function($flow) {
                    return $flow->assignedUser && $flow->assignedUser->opd;   
                });

            if ($validationFlowWithOpd) {
                $kodeOpd = $validationFlowWithOpd->assignedUser->opd->kode_opd ?? 'OPD';
            } else {
                $anyFlowWithOpd = $perijinan->activeValidationFlows()
                    ->whereNotNull('assigned_user_id')
                    ->with('assignedUser.opd')
                    ->get()
                    ->first(function($flow) {
                        return $flow->assignedUser && $flow->assignedUser->opd;
                    });

                if ($anyFlowWithOpd) {
                    $kodeOpd = $anyFlowWithOpd->assignedUser->opd->kode_opd ?? 'OPD';
                }
            }
        }

        $replacements['[NOMOR SURAT]'] = ($perijinan->kode_perijinan ?? 'KODE') . '/123/' . $kodeOpd . '/' . date('Y');

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
            $logoKabHtml = '<img src="' . $src . '" style="max-height: 90px; width: auto;" alt="Logo Kabupaten" />';
        }
        $replacements['[LOGO KABUPATEN]'] = $logoKabHtml;

        // Dynamic Form Fields Dummy
        foreach ($perijinan->formFields as $field) {
            $replacements['[' . strtoupper($field->name) . ']'] = '[Contoh ' . $field->label . ']';
        }

        // Global fix for checkmarks [x] or [v] or ✓
        $checkmarkHtml = '<span class="checkmark">&#10003;</span>';
        $htmlContent = str_replace(['[x]', '[v]', '[V]', '✓'], $checkmarkHtml, $htmlContent);

        // Handle Page Breaks
        $htmlContent = str_replace('<!-- pagebreak -->', '<div class="page-break"></div>', $htmlContent);

        // Replace placeholders
        $htmlContent = str_replace(
            array_keys($replacements),
            array_values($replacements),
            $htmlContent
        );

        $fullHtml = \App\Services\DocumentGenerator::wrapHtmlTemplate($htmlContent, $templateType, 'PREVIEW');

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
            return $pdf->stream('Pratinjau_' . ucfirst($templateType) . '.pdf');
        }

        return response($fullHtml);
    }

    /**
     * Store a new form field.
     */
    public function storeFormField(Request $request, string $id)
    {
        $perijinan = Perijinan::findOrFail($id);

        $validated = $request->validate([
            'form_type' => 'nullable|in:global,rekom,izin',
            'label' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'type' => 'required|in:text,textarea,number,date,email,phone,select,radio,checkbox,file',
            'is_required' => 'boolean',
            'placeholder' => 'nullable|string',
            'help_text' => 'nullable|string',
            'options' => 'nullable|array',
            'order' => 'integer',
            'is_active' => 'boolean',
            'file_types' => 'nullable|string|max:255',
            'max_file_size' => 'nullable|integer|min:1',
        ]);

        $validated['perijinan_id'] = $perijinan->id;
        $validated['form_type'] = $validated['form_type'] ?? 'global';
        $validated['is_required'] = $request->has('is_required');
        $validated['is_active'] = $request->has('is_active');
        $validated['order'] = $request->input('order', $perijinan->formFields()->where('form_type', $validated['form_type'])->count() + 1);

        // Encode options as JSON if it's an array
        if (isset($validated['options']) && is_array($validated['options'])) {
            // Filter out empty options
            $validated['options'] = array_filter($validated['options'], fn($opt) => !empty($opt));
        }

        PerijinanFormField::create($validated);

        // Log activity
        ActivityLog::log(
            'Menambah field formulir baru',
            $perijinan,
            'created',
            [
                'field_label' => $validated['label'],
                'field_type' => $validated['type'],
                'field_name' => $validated['name']
            ],
            'perijinan_form'
        );

        return redirect()->route('perijinan.form-builder', $id)
            ->with('success', 'Field formulir berhasil ditambahkan.');
    }

    /**
     * Update a form field.
     */
    public function updateFormField(Request $request, string $perijinanId, string $fieldId)
    {
        $perijinan = Perijinan::findOrFail($perijinanId);
        $field = PerijinanFormField::where('perijinan_id', $perijinan->id)->findOrFail($fieldId);

        $validated = $request->validate([
            'form_type' => 'nullable|in:global,rekom,izin',
            'label' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'type' => 'required|in:text,textarea,number,date,email,phone,select,radio,checkbox,file',
            'is_required' => 'boolean',
            'placeholder' => 'nullable|string',
            'help_text' => 'nullable|string',
            'options' => 'nullable|array',
            'order' => 'integer',
            'is_active' => 'boolean',
            'file_types' => 'nullable|string|max:255',
            'max_file_size' => 'nullable|integer|min:1',
        ]);

        $validated['is_required'] = $request->has('is_required');
        $validated['is_active'] = $request->has('is_active');

        // Encode options as JSON if it's an array
        if (isset($validated['options']) && is_array($validated['options'])) {
            // Filter out empty options
            $validated['options'] = array_filter($validated['options'], fn($opt) => !empty($opt));
        }

        $field->update($validated);

        // Log activity
        ActivityLog::log(
            'Mengupdate field formulir',
            $perijinan,
            'updated',
            [
                'field_id' => $field->id,
                'field_label' => $validated['label'],
                'field_type' => $validated['type'],
                'old' => $field->toArray(),
                'new' => $validated
            ],
            'perijinan_form'
        );

        return redirect()->route('perijinan.form-builder', $perijinanId)
            ->with('success', 'Field formulir berhasil diperbarui.');
    }

    /**
     * Delete a form field.
     */
    public function deleteFormField(string $perijinanId, string $fieldId)
    {
        $perijinan = Perijinan::findOrFail($perijinanId);
        $field = PerijinanFormField::where('perijinan_id', $perijinan->id)->findOrFail($fieldId);
        
        // Log activity before delete
        ActivityLog::log(
            'Menghapus field formulir',
            $perijinan,
            'deleted',
            [
                'field_id' => $field->id,
                'field_label' => $field->label,
                'field_type' => $field->type,
                'data' => $field->toArray()
            ],
            'perijinan_form'
        );
        
        $field->delete();

        return redirect()->route('perijinan.form-builder', $perijinanId)
            ->with('success', 'Field formulir berhasil dihapus.');
    }

    /**
     * Reorder form fields.
     */
    public function reorderFormFields(Request $request, string $id)
    {
        $perijinan = Perijinan::findOrFail($id);

        $validated = $request->validate([
            'field_ids' => 'required|array',
            'field_ids.*' => 'exists:perijinan_form_fields,id',
        ]);

        DB::transaction(function () use ($perijinan, $validated) {
            foreach ($validated['field_ids'] as $index => $fieldId) {
                PerijinanFormField::where('id', $fieldId)
                    ->where('perijinan_id', $perijinan->id)
                    ->update(['order' => $index + 1]);
            }
        });

        // Log activity
        ActivityLog::log(
            'Mengurutkan ulang field formulir',
            $perijinan,
            'updated',
            [
                'field_ids' => $validated['field_ids'],
                'total_fields' => count($validated['field_ids'])
            ],
            'perijinan_form'
        );

        return response()->json(['success' => true]);
    }

    /**
     * Show alur validasi page.
     */
    public function alurValidasi(string $id)
    {
        $perijinan = Perijinan::with([
            'validationFlows' => function ($q) {
                $q->orderBy('order');
            },
            'validationFlows.assignedUser'
        ])->findOrFail($id);
        $availableRoles = PerijinanValidationFlow::getAvailableRoles();
        $foUsers = PerijinanValidationFlow::getUsersByRole('fo');
        $boUsers = PerijinanValidationFlow::getUsersByRole('bo');
        $operatorOpdUsers = PerijinanValidationFlow::getUsersByRole('operator_opd');
        $kepalaOpdUsers = PerijinanValidationFlow::getUsersByRole('kepala_opd');
        $verifikatorUsers = PerijinanValidationFlow::getUsersByRole('verifikator');
        $kadinUsers = PerijinanValidationFlow::getUsersByRole('kadin');
        return view('perijinan.alur-validasi', compact('perijinan', 'availableRoles', 'foUsers', 'boUsers', 'operatorOpdUsers', 'kepalaOpdUsers', 'verifikatorUsers', 'kadinUsers'));
    }

    /**
     * Store a validation flow step.
     */
    public function storeValidationFlow(Request $request, string $id)
    {
        $perijinan = Perijinan::findOrFail($id);

        $validated = $request->validate([
            'role' => 'required|string|in:fo,bo,operator_opd,kepala_opd,verifikator,kadin',
            'assigned_user_id' => 'nullable|exists:users,id',
            'order' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'description' => 'nullable|string|max:500',
            'sla_hours' => 'nullable|integer|min:1|max:720',
        ]);

        $validated['perijinan_id'] = $perijinan->id;
        $validated['is_active'] = $request->has('is_active');

        // Validate assigned user for OPD roles
        if (PerijinanValidationFlow::requiresUserAssignment($validated['role'])) {
            if (empty($validated['assigned_user_id'])) {
                return redirect()->route('perijinan.alur-validasi', $id)
                    ->with('error', 'Harap pilih user untuk role ini.');
            }

            // Verify user has the correct role
            $user = User::find($validated['assigned_user_id']);
            if ($user && $user->role !== $validated['role']) {
                return redirect()->route('perijinan.alur-validasi', $id)
                    ->with('error', 'User yang dipilih tidak memiliki role yang sesuai.');
            }
        } else {
            $validated['assigned_user_id'] = null;
        }

        // Allow multiple instances of the same role for different users
        // No duplicate check needed - multiple users can have the same role

        PerijinanValidationFlow::create($validated);

        // Log activity
        ActivityLog::log(
            'Menambah tahap validasi baru',
            $perijinan,
            'created',
            [
                'role' => $validated['role'],
                'order' => $validated['order'],
                'assigned_user_id' => $validated['assigned_user_id'],
                'sla_hours' => $validated['sla_hours']
            ],
            'perijinan_validation'
        );

        return redirect()->route('perijinan.alur-validasi', $id)
            ->with('success', 'Tahap validasi berhasil ditambahkan.');
    }

    /**
     * Update a validation flow step.
     */
    public function updateValidationFlow(Request $request, string $perijinanId, string $flowId)
    {
        try {
            $perijinan = Perijinan::findOrFail($perijinanId);
            $flow = PerijinanValidationFlow::where('perijinan_id', $perijinan->id)->findOrFail($flowId);

            $validated = $request->validate([
                'role' => 'required|string|in:fo,bo,operator_opd,kepala_opd,verifikator,kadin',
                'assigned_user_id' => 'nullable|exists:users,id',
                'order' => 'nullable|integer|min:1',
                'is_active' => 'boolean',
                'description' => 'nullable|string|max:500',
                'sla_hours' => 'nullable|integer|min:1|max:720',
            ]);

            $validated['is_active'] = $request->has('is_active');

            // Keep existing order if not provided
            if (!isset($validated['order'])) {
                $validated['order'] = $flow->order;
            }

            // Validate assigned user for OPD roles
            if (PerijinanValidationFlow::requiresUserAssignment($validated['role'])) {
                if (empty($validated['assigned_user_id'])) {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Harap pilih user untuk role ini.'
                        ], 422);
                    }
                    return redirect()->route('perijinan.alur-validasi', $perijinanId)
                        ->with('error', 'Harap pilih user untuk role ini.');
                }

                // Verify user has the correct role
                $user = User::find($validated['assigned_user_id']);
                if ($user && $user->role !== $validated['role']) {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'User yang dipilih tidak memiliki role yang sesuai.'
                        ], 422);
                    }
                    return redirect()->route('perijinan.alur-validasi', $perijinanId)
                        ->with('error', 'User yang dipilih tidak memiliki role yang sesuai.');
                }
            } else {
                $validated['assigned_user_id'] = null;
            }

            $flow->update($validated);

            // Log activity
            ActivityLog::log(
                'Mengupdate tahap validasi',
                $perijinan,
                'updated',
                [
                    'flow_id' => $flow->id,
                    'role' => $validated['role'],
                    'order' => $validated['order'],
                    'old' => $flow->toArray(),
                    'new' => $validated
                ],
                'perijinan_validation'
            );

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tahap validasi berhasil diperbarui.'
                ]);
            }

            return redirect()->route('perijinan.alur-validasi', $perijinanId)
                ->with('success', 'Tahap validasi berhasil diperbarui.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
            throw $e;
        }
    }

    /**
     * Delete a validation flow step.
     */
    public function deleteValidationFlow(string $perijinanId, string $flowId)
    {
        $perijinan = Perijinan::findOrFail($perijinanId);
        $flow = PerijinanValidationFlow::where('perijinan_id', $perijinan->id)->findOrFail($flowId);
        
        // Log activity before delete
        ActivityLog::log(
            'Menghapus tahap validasi',
            $perijinan,
            'deleted',
            [
                'flow_id' => $flow->id,
                'role' => $flow->role,
                'order' => $flow->order,
                'data' => $flow->toArray()
            ],
            'perijinan_validation'
        );
        
        $flow->delete();

        return redirect()->route('perijinan.alur-validasi', $perijinanId)
            ->with('success', 'Tahap validasi berhasil dihapus.');
    }

    /**
     * Reorder validation flows.
     */
    public function reorderValidationFlows(Request $request, string $id)
    {
        $perijinan = Perijinan::findOrFail($id);

        $validated = $request->validate([
            'flow_ids' => 'required|array',
            'flow_ids.*' => 'exists:perijinan_validation_flows,id',
        ]);

        DB::transaction(function () use ($validated, $perijinan) {
            foreach ($validated['flow_ids'] as $index => $flowId) {
                PerijinanValidationFlow::where('id', $flowId)
                    ->where('perijinan_id', $perijinan->id)
                    ->update(['order' => $index + 1]);
            }
        });

        // Log activity
        ActivityLog::log(
            'Mengurutkan ulang tahap validasi',
            $perijinan,
            'updated',
            [
                'flow_ids' => $validated['flow_ids'],
                'total_flows' => count($validated['flow_ids'])
            ],
            'perijinan_validation'
        );

        return response()->json(['success' => true]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $perijinan = Perijinan::findOrFail($id);
        return view('perijinan.edit', compact('perijinan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'kode_perijinan' => 'nullable|string|max:50|unique:perijinan,kode_perijinan,' . $id,
            'nama_perijinan' => 'required|string|max:255',
            'opsi_perpanjangan' => 'nullable|in:setelah_habis,sebelum_habis,keduanya',
            'dasar_hukum' => 'required|string',
            'persyaratan' => 'required|string',
            'prosedur' => 'required|string',
            'informasi_biaya' => 'nullable|string',
            'lama_waktu_proses' => 'nullable|string|max:255',
            'gambar_alur' => 'nullable|file|mimes:png,jpg,jpeg|max:2048',
        ]);

        $perijinan = Perijinan::findOrFail($id);
        $oldData = $perijinan->toArray();

        $data = $request->all();

        // Handle gambar_alur upload
        if ($request->hasFile('gambar_alur')) {
            $gambarAlur = $request->file('gambar_alur');
            $gambarAlurName = time() . '_alur_' . str_replace(' ', '_', $request->nama_perijinan) . '.' . $gambarAlur->getClientOriginalExtension();
            
            // Ensure directory exists
            $uploadPath = public_path('uploads/data-perijinan');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            
            // Delete old image if exists
            if ($perijinan->gambar_alur && file_exists(public_path($perijinan->gambar_alur))) {
                unlink(public_path($perijinan->gambar_alur));
            }
            
            $gambarAlur->move($uploadPath, $gambarAlurName);
            $data['gambar_alur'] = 'uploads/data-perijinan/' . $gambarAlurName;
        }

        $perijinan->update($data);

        // Log activity
        ActivityLog::log(
            'Mengupdate data jenis perijinan',
            $perijinan,
            'updated',
            [
                'old' => $oldData,
                'new' => $request->all()
            ],
            'perijinan'
        );

        return redirect()->route('perijinan.index')
            ->with('success', 'Jenis Perijinan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $perijinan = Perijinan::findOrFail($id);

        // Log activity
        ActivityLog::log(
            'Menghapus jenis perijinan',
            $perijinan,
            'deleted',
            ['data' => $perijinan->toArray()],
            'perijinan'
        );

        $perijinan->delete();

        return redirect()->route('perijinan.index')
            ->with('success', 'Jenis Perijinan berhasil dihapus.');
    }

    /**
     * Update the templates for letters.
     */
    public function updateTemplates(Request $request, string $id)
    {
        $perijinan = Perijinan::findOrFail($id);

        $request->validate([
            'template_pernyataan' => 'nullable|string',
            'template_permohonan' => 'nullable|string',
            'template_keabsahan' => 'nullable|string',
            'template_surat_rekom' => 'nullable|string',
            'template_surat_izin' => 'nullable|string',
            'next_nomor_rekom' => 'nullable|integer',
            'next_nomor_izin' => 'nullable|integer',
        ]);

        $perijinan->update([
            'template_pernyataan' => $request->template_pernyataan ?? $perijinan->template_pernyataan,
            'template_permohonan' => $request->template_permohonan ?? $perijinan->template_permohonan,
            'template_keabsahan' => $request->template_keabsahan ?? $perijinan->template_keabsahan,
            'template_surat_rekom' => $request->has('template_surat_rekom') ? $request->template_surat_rekom : $perijinan->template_surat_rekom,
            'template_surat_izin' => $request->has('template_surat_izin') ? $request->template_surat_izin : $perijinan->template_surat_izin,
            'next_nomor_rekom' => $request->next_nomor_rekom ?? $perijinan->next_nomor_rekom,
            'next_nomor_izin' => $request->next_nomor_izin ?? $perijinan->next_nomor_izin,
        ]);

        // Log activity
        \App\Models\ActivityLog::log(
            'Memperbarui template surat perijinan',
            $perijinan,
            'updated',
            [
                'template_pernyataan' => !empty($request->template_pernyataan) ? 'Updated' : 'Empty',
                'template_permohonan' => !empty($request->template_permohonan) ? 'Updated' : 'Empty',
                'template_keabsahan' => !empty($request->template_keabsahan) ? 'Updated' : 'Empty',
                'template_surat_rekom' => !empty($request->template_surat_rekom) ? 'Updated' : 'Empty',
                'template_surat_izin' => !empty($request->template_surat_izin) ? 'Updated' : 'Empty',
            ],
            'perijinan'
        );

        return back()
            ->with('success', 'Template surat berhasil diperbarui.')
            ->with('active_tab', 'templates');
    }
}
