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

        // Apply access control for non-admin users (e.g. operator_opd)
        $user = auth()->user();
        if (!$user->isAdmin()) {
            $accessibleIds = $user->getAccessiblePerijinanIds();
            $query->whereIn('id', $accessibleIds);
        }

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
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('perijinan.index')->with('error', 'Hanya Admin yang dapat menambah data perijinan.');
        }
        return view('perijinan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('perijinan.index')->with('error', 'Hanya Admin yang dapat menambah data perijinan.');
        }
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
            ->with('success', 'Jenis perijinan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && !in_array($id, $user->getAccessiblePerijinanIds())) {
            return redirect()->route('perijinan.index')->with('error', 'Anda tidak memiliki akses ke data perijinan ini.');
        }

        $perijinan = Perijinan::with(['activeFormFields', 'validationFlows.assignedUser'])->findOrFail($id);
        return view('perijinan.show', compact('perijinan'));
    }

    /**
     * Show form builder page for managing form fields.
     */
    public function formBuilder(string $id)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && !in_array($id, $user->getAccessiblePerijinanIds())) {
            return redirect()->route('perijinan.index')->with('error', 'Anda tidak memiliki akses ke form builder perijinan ini.');
        }

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

        // Use real values from request (unsaved) or database (saved)
        if ($templateType === 'izin') {
            $realNumber = $request->input('next_nomor_izin') ?? $perijinan->next_nomor_izin ?? 1;
        } else {
            $realNumber = $request->input('next_nomor_rekom') ?? $perijinan->next_nomor_rekom ?? 1;
        }

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
            '[NOMOR URUT]' => $realNumber,
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

        $replacements['[NOMOR SURAT]'] = ($perijinan->kode_perijinan ?? 'KODE') . '/' . $realNumber . '/' . $kodeOpd . '/' . date('Y');

        // [GAMBAR TTE]
        $gambarTte = \App\Models\Setting::get('gambar_tte');
        $tteHtml = '<div style="width: 100px; height: 100px; border: 1px dashed #ccc; display: inline-flex; align-items: center; justify-content: center; font-size: 10px; color: #999;">[QR CODE TTE]</div>';
        if ($gambarTte && \Illuminate\Support\Facades\File::exists(public_path($gambarTte))) {
            $imageData = base64_encode(\Illuminate\Support\Facades\File::get(public_path($gambarTte)));
            $tteHtml = '<img src="data:image/png;base64,' . $imageData . '" style="width: 100px; height: 100px; object-fit: contain;">';
        }
        $replacements['[GAMBAR TTE]'] = $tteHtml;

        // [LOGO KABUPATEN]
        $logoKab = \App\Models\Setting::get('logo_kabupaten');
        $logoHtml = '<div style="width: 80px; height: 100px; border: 1px dashed #ccc; display: inline-flex; align-items: center; justify-content: center; font-size: 10px; color: #999;">[LOGO KAB]</div>';
        if ($logoKab && \Illuminate\Support\Facades\File::exists(public_path($logoKab))) {
            $imageData = base64_encode(\Illuminate\Support\Facades\File::get(public_path($logoKab)));
            $logoHtml = '<img src="data:image/png;base64,' . $imageData . '" style="width: 80px; height: auto; object-fit: contain;">';
        }
        $replacements['[LOGO KABUPATEN]'] = $logoHtml;

        // Add dynamic form field placeholders
        $formFields = $perijinan->activeFormFields()->where('form_type', $templateType)->get();
        foreach ($formFields as $field) {
            $placeholder = '[' . strtoupper($field->name) . ']';
            $replacements[$placeholder] = 'DUMMY_' . strtoupper($field->name);
        }

        // Global form fields also available
        $globalFields = $perijinan->activeFormFields()->where('form_type', 'global')->get();
        foreach ($globalFields as $field) {
            $placeholder = '[' . strtoupper($field->name) . ']';
            if (!isset($replacements[$placeholder])) {
                $replacements[$placeholder] = 'DUMMY_GLOBAL_' . strtoupper($field->name);
            }
        }

        // Replace placeholders
        $previewHtml = str_replace(
            array_keys($replacements),
            array_values($replacements),
            $htmlContent
        );

        // Fix for Tinymce pagebreaks in preview
        $previewHtml = str_replace('<!-- pagebreak -->', '<div style="page-break-after: always; border-top: 1px dashed #ccc; margin: 20px 0; position: relative;"><span style="position: absolute; top: -10px; left: 50%; transform: translateX(-50%); background: #eee; padding: 0 10px; font-size: 10px; color: #666;">PAGE BREAK</span></div>', $previewHtml);

        // Generate PDF for preview
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($previewHtml);
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('preview.pdf');
    }

    /**
     * Store a new form field.
     */
    public function storeFormField(Request $request, string $id)
    {
        $perijinan = Perijinan::findOrFail($id);
        $user = auth()->user();

        // Access Control: Operator OPD can only add 'rekom' fields
        if (!$user->isAdmin()) {
            if ($request->input('form_type') !== 'rekom') {
                return redirect()->back()->with('error', 'Anda hanya memiliki akses untuk mengelola field Formulir Rekomendasi.');
            }
        }

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
                'form_type' => $validated['form_type']
            ],
            'perijinan_form'
        );

        return redirect()->route('perijinan.form-builder', $id)
            ->with('success', 'Field formulir berhasil ditambahkan.')
            ->with('active_tab', $validated['form_type']);
    }

    /**
     * Update a form field.
     */
    public function updateFormField(Request $request, string $perijinanId, string $fieldId)
    {
        $perijinan = Perijinan::findOrFail($perijinanId);
        $field = PerijinanFormField::where('perijinan_id', $perijinan->id)->findOrFail($fieldId);
        $user = auth()->user();

        // Access Control: Operator OPD can only update 'rekom' fields
        if (!$user->isAdmin()) {
            if ($field->form_type !== 'rekom' || $request->input('form_type') !== 'rekom') {
                return redirect()->back()->with('error', 'Anda hanya memiliki akses untuk mengelola field Formulir Rekomendasi.');
            }
        }

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
            ->with('success', 'Field formulir berhasil diperbarui.')
            ->with('active_tab', $field->form_type);
    }

    /**
     * Delete a form field.
     */
    public function deleteFormField(string $perijinanId, string $fieldId)
    {
        $perijinan = Perijinan::findOrFail($perijinanId);
        $field = PerijinanFormField::where('perijinan_id', $perijinan->id)->findOrFail($fieldId);
        $user = auth()->user();

        // Access Control: Operator OPD can only delete 'rekom' fields    
        if (!$user->isAdmin()) {
            if ($field->form_type !== 'rekom') {
                return redirect()->back()->with('error', 'Anda hanya memiliki akses untuk menghapus field Formulir Rekomendasi.');
            }
        }

        // Log activity before delete
        ActivityLog::log(
            'Menghapus field formulir',
            $perijinan,
            'deleted',
            ['field' => $field->toArray()],
            'perijinan_form'
        );

        $formType = $field->form_type;
        $field->delete();

        return redirect()->route('perijinan.form-builder', $perijinanId)
            ->with('success', 'Field formulir berhasil dihapus.')
            ->with('active_tab', $formType);
    }

    /**
     * Reorder form fields via AJAX.
     */
    public function reorderFormFields(Request $request, string $id)
    {
        $perijinan = Perijinan::findOrFail($id);

        $validated = $request->validate([
            'field_ids' => 'required|array',
            'field_ids.*' => 'required|exists:perijinan_form_fields,id'
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
        $user = auth()->user();
        if (!$user->isAdmin()) {
            return redirect()->route('perijinan.index')->with('error', 'Hanya Admin yang dapat mengelola alur validasi.');
        }
        
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

        return view('perijinan.alur-validasi', compact(
            'perijinan',
            'availableRoles',
            'foUsers',
            'boUsers',
            'operatorOpdUsers',
            'kepalaOpdUsers',
            'verifikatorUsers',
            'kadinUsers'
        ));
    }

    /**
     * Store validation flow.
     */
    public function storeValidationFlow(Request $request, string $id)
    {
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('perijinan.index')->with('error', 'Hanya Admin yang dapat mengelola alur validasi.');
        }
        $perijinan = Perijinan::findOrFail($id);

        $validated = $request->validate([
            'role' => 'required|string|in:fo,bo,operator_opd,kepala_opd,verifikator,kadin',
            'assigned_user_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
            'sla_hours' => 'nullable|integer|min:1',
            'order' => 'integer'
        ]);

        $validated['perijinan_id'] = $perijinan->id;
        $validated['order'] = $request->input('order', $perijinan->validationFlows()->count() + 1);

        PerijinanValidationFlow::create($validated);

        // Log activity
        ActivityLog::log(
            'Menambah alur validasi baru',
            $perijinan,
            'created',
            ['role' => $validated['role']],
            'perijinan_validation'
        );

        return redirect()->route('perijinan.alur-validasi', $id)
            ->with('success', 'Alur validasi berhasil ditambahkan.');
    }

    /**
     * Update validation flow.
     */
    public function updateValidationFlow(Request $request, string $perijinanId, string $flowId)
    {
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('perijinan.index')->with('error', 'Hanya Admin yang dapat mengelola alur validasi.');
        }
        $perijinan = Perijinan::findOrFail($perijinanId);
        $flow = PerijinanValidationFlow::where('perijinan_id', $perijinan->id)->findOrFail($flowId);

        $validated = $request->validate([
            'role' => 'required|string|in:fo,bo,operator_opd,kepala_opd,verifikator,kadin',
            'assigned_user_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
            'sla_hours' => 'nullable|integer|min:1',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');
        $flow->update($validated);

        // Log activity
        ActivityLog::log(
            'Mengupdate alur validasi',
            $perijinan,
            'updated',
            ['role' => $validated['role']],
            'perijinan_validation'
        );

        return redirect()->route('perijinan.alur-validasi', $perijinanId)
            ->with('success', 'Alur validasi berhasil diperbarui.');
    }

    /**
     * Delete validation flow.
     */
    public function deleteValidationFlow(string $perijinanId, string $flowId)
    {
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('perijinan.index')->with('error', 'Hanya Admin yang dapat mengelola alur validasi.');
        }
        $perijinan = Perijinan::findOrFail($perijinanId);
        $flow = PerijinanValidationFlow::where('perijinan_id', $perijinan->id)->findOrFail($flowId);

        // Log activity before delete
        ActivityLog::log(
            'Menghapus alur validasi',
            $perijinan,
            'deleted',
            ['role' => $flow->role],
            'perijinan_validation'
        );

        $flow->delete();

        return redirect()->route('perijinan.alur-validasi', $perijinanId)
            ->with('success', 'Alur validasi berhasil dihapus.');
    }

    /**
     * Reorder validation flows via AJAX.
     */
    public function reorderValidationFlows(Request $request, string $id)
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Hanya Admin yang dapat mengelola alur validasi.'], 403);
        }
        $perijinan = Perijinan::findOrFail($id);

        $validated = $request->validate([
            'flow_ids' => 'required|array',
            'flow_ids.*' => 'required|exists:perijinan_validation_flows,id'
        ]);

        DB::transaction(function () use ($perijinan, $validated) {
            foreach ($validated['flow_ids'] as $index => $flowId) {
                PerijinanValidationFlow::where('id', $flowId)
                    ->where('perijinan_id', $perijinan->id)
                    ->update(['order' => $index + 1]);
            }
        });

        // Log activity
        ActivityLog::log(
            'Mengurutkan ulang alur validasi',
            $perijinan,
            'updated',
            ['total_flows' => count($validated['flow_ids'])],
            'perijinan_validation'
        );

        return response()->json(['success' => true]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('perijinan.index')->with('error', 'Hanya Admin yang dapat mengubah data perijinan.');
        }
        $perijinan = Perijinan::findOrFail($id);
        return view('perijinan.edit', compact('perijinan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('perijinan.index')->with('error', 'Hanya Admin yang dapat mengubah data perijinan.');
        }
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
        $data = $request->all();

        // Handle gambar_alur upload
        if ($request->hasFile('gambar_alur')) {
            $gambarAlur = $request->file('gambar_alur');
            $gambarAlurName = time() . '_alur_' . str_replace(' ', '_', $request->nama_perijinan) . '.' . $gambarAlur->getClientOriginalExtension();
            
            $uploadPath = public_path('uploads/data-perijinan');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            
            // Delete old image if exists
            if ($perijinan->gambar_alur && file_exists(public_path($perijinan->gambar_alur))) {
                @unlink(public_path($perijinan->gambar_alur));
            }
            
            $gambarAlur->move($uploadPath, $gambarAlurName);
            $data['gambar_alur'] = 'uploads/data-perijinan/' . $gambarAlurName;
        }

        $perijinan->update($data);

        // Log activity
        ActivityLog::log(
            'Mengupdate jenis perijinan',
            $perijinan,
            'updated',
            ['data' => $request->all()],
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
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('perijinan.index')->with('error', 'Hanya Admin yang dapat menghapus data perijinan.');
        }
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
            ->with('success', 'Jenis perijinan berhasil dihapus.');
    }

    /**
     * Update the templates for letters.
     */
    public function updateTemplates(Request $request, string $id)
    {
        $perijinan = Perijinan::findOrFail($id);
        $user = auth()->user();

        $request->validate([
            'template_pernyataan' => 'nullable|string',
            'template_permohonan' => 'nullable|string',
            'template_keabsahan' => 'nullable|string',
            'template_surat_rekom' => 'nullable|string',
            'template_surat_izin' => 'nullable|string',
            'next_nomor_rekom' => 'nullable|integer',
            'next_nomor_izin' => 'nullable|integer',
        ]);

        $updateData = [];

        // Check and prepare data based on permissions
        if ($user->isAdmin()) {
            // Admin can update everything
            if ($request->has('template_pernyataan')) $updateData['template_pernyataan'] = $request->template_pernyataan;
            if ($request->has('template_permohonan')) $updateData['template_permohonan'] = $request->template_permohonan;
            if ($request->has('template_keabsahan')) $updateData['template_keabsahan'] = $request->template_keabsahan;
            if ($request->has('template_surat_rekom')) $updateData['template_surat_rekom'] = $request->template_surat_rekom;
            if ($request->has('template_surat_izin')) $updateData['template_surat_izin'] = $request->template_surat_izin;
            if ($request->has('next_nomor_rekom')) $updateData['next_nomor_rekom'] = $request->next_nomor_rekom;
            if ($request->has('next_nomor_izin')) $updateData['next_nomor_izin'] = $request->next_nomor_izin;
        } else {
            // Operator OPD restricted to Rekom only
            // If they try to change other things, block it
            $restrictedFields = ['template_surat_izin', 'next_nomor_izin', 'template_pernyataan', 'template_permohonan', 'template_keabsahan'];
            foreach ($restrictedFields as $field) {
                if ($request->has($field) && $request->get($field) !== null && $request->get($field) != $perijinan->$field) {
                    return redirect()->back()->with('error', 'Anda hanya memiliki akses untuk memperbarui Template Rekomendasi.');
                }
            }

            if ($request->has('template_surat_rekom')) $updateData['template_surat_rekom'] = $request->template_surat_rekom;
            if ($request->has('next_nomor_rekom')) $updateData['next_nomor_rekom'] = $request->next_nomor_rekom;
        }

        if (!empty($updateData)) {
            $perijinan->update($updateData);

            // Log activity
            ActivityLog::log(
                'Memperbarui template surat perijinan',
                $perijinan,
                'updated',
                array_map(fn($val) => !empty($val) ? 'Updated' : 'Empty', $updateData),
                'perijinan'
            );
        }

        return back()
            ->with('success', 'Template surat berhasil diperbarui.')
            ->with('active_tab', 'templates');
    }
}
