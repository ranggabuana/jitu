<?php

namespace App\Http\Controllers\Admin;

use App\Models\Perijinan;
use App\Models\PerijinanFormField;
use App\Models\PerijinanValidationFlow;
use App\Models\PerijinanOpdConfig;
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

        // Apply access control for non-admin users
        $user = auth()->user();
        if ($user->role === 'operator_opd') {
            $accessibleIds = $user->getAccessiblePerijinanIds();
            $query->whereIn('id', $accessibleIds);
        }
        // Admin and Verifikator can access all, so no filter needed for them

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
            'kode_perijinan' => 'nullable|string|max:50',
            'nama_perijinan' => 'required|string|max:255',
            'jenis_perijinan' => 'required|in:umum,pencabutan_medis',
            'is_multi_opd' => 'nullable|boolean',
            'has_bo_form' => 'nullable|boolean',
            'validasi_tanpa_opd' => 'nullable|boolean',
            'opsi_perpanjangan' => 'nullable|in:setelah_habis,sebelum_habis,keduanya',
            'dasar_hukum' => 'required|string',
            'persyaratan' => 'required|string',
            'prosedur' => 'required|string',
            'informasi_biaya' => 'nullable|string',
            'lama_waktu_proses' => 'nullable|string|max:255',
            'gambar_alur' => 'nullable|file|mimes:png,jpg,jpeg|max:2048',
        ]);

        $data = $request->all();
        $data['is_multi_opd'] = $request->has('is_multi_opd');
        $data['has_bo_form'] = $request->has('has_bo_form');
        $data['validasi_tanpa_opd'] = $request->has('validasi_tanpa_opd');

        if ($data['validasi_tanpa_opd']) {
            $data['is_multi_opd'] = false;
        }

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
            ['data' => $data],
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
        if ($user->role === 'operator_opd' && !in_array($id, $user->getAccessiblePerijinanIds())) {
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
        if ($user->role === 'operator_opd' && !in_array($id, $user->getAccessiblePerijinanIds())) {
            return redirect()->route('perijinan.index')->with('error', 'Anda tidak memiliki akses ke form builder perijinan ini.');
        }

        $perijinan = Perijinan::with(['formFields' => function($q) use ($user) {
            $q->with('opd');
            // If operator OPD, only show their fields or global fields as reference
            if ($user->role === 'operator_opd') {
                $q->where(function($query) use ($user) {
                    $query->where('opd_id', $user->opd_id)
                          ->orWhereNull('opd_id');
                });
            }
        }, 'opdConfigs.opd', 'activeValidationFlows.assignedUser.opd'])->findOrFail($id);

        // Fetch OPD config if user is operator_opd
        $opdConfig = null;
        if ($user->role === 'operator_opd' && $user->opd_id) {
            $opdConfig = PerijinanOpdConfig::firstOrCreate([
                'perijinan_id' => $id,
                'opd_id' => $user->opd_id
            ]);
        }

        return view('perijinan.form-builder', compact('perijinan', 'opdConfig'));
    }

    /**
     * Preview the template by replacing placeholders with dummy data.
     */
    public function previewTemplate(Request $request, string $id)
    {
        $perijinan = Perijinan::findOrFail($id);
        $user = auth()->user();
        $templateType = $request->input('template_type', 'rekom'); // 'rekom' or 'izin'
        $htmlContent = $request->input('template_content', '');

        // Fetch OPD config if needed
        $opdConfig = null;
        if ($templateType === 'rekom' && $user->role === 'operator_opd' && $user->opd_id) {
            $opdConfig = PerijinanOpdConfig::where('perijinan_id', $id)
                ->where('opd_id', $user->opd_id)
                ->first();
        }

        // Use real values from request (unsaved) or database (saved)
        if ($templateType === 'izin') {
            $realNumber = $request->input('next_nomor_izin') ?? $perijinan->next_nomor_izin ?? 1;
        } else {
            $realNumber = $request->input('next_nomor_rekom') ?? $opdConfig->next_nomor_rekom ?? $perijinan->next_nomor_rekom ?? 1;
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
            '[TANGGAL_HARI_INI]' => \Carbon\Carbon::now()->translatedFormat('d F Y'),
            '[TANGGAL REKOM TTE]' => \Carbon\Carbon::now()->translatedFormat('d F Y'),
            '${TANGGAL_REKOM_TTE}' => \Carbon\Carbon::now()->translatedFormat('d F Y'),
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
        $replacements['${NOMOR_SURAT}'] = $replacements['[NOMOR SURAT]'];

        // Determine preview NOMOR_REKOM
        $noRekomUrutPreview = $request->input('next_nomor_rekom') ?? $perijinan->next_nomor_rekom ?? 1;
        $kodePerijinanPreview = $perijinan->kode_perijinan ?? 'KODE';
        $tahunPreview = date('Y');

        if ($perijinan->is_multi_opd) {
            $involvedOpdsPreview = $perijinan->activeValidationFlows()
                ->whereIn('role', ['operator_opd', 'kepala_opd'])
                ->whereNotNull('assigned_user_id')
                ->with('assignedUser.opd')
                ->get()
                ->pluck('assignedUser.opd')
                ->filter()
                ->unique('id');

            if ($involvedOpdsPreview->count() > 0) {
                $rekomNumsPreview = [];
                foreach ($involvedOpdsPreview as $opdPreview) {
                    $opdCodePreview = $opdPreview->kode_opd ?? 'OPD';
                    $rekomNumsPreview[] = "{$opdPreview->nama_opd}: {$kodePerijinanPreview}/{$noRekomUrutPreview}/{$opdCodePreview}/{$tahunPreview}";
                }
                $nomorRekomPreview = implode(', ', $rekomNumsPreview);
            } else {
                $nomorRekomPreview = "{$kodePerijinanPreview}/{$noRekomUrutPreview}/OPD/{$tahunPreview}";
            }
        } else {
            $nomorRekomPreview = "{$kodePerijinanPreview}/{$noRekomUrutPreview}/{$kodeOpd}/{$tahunPreview}";
        }

        // Determine preview NOMOR_IZIN
        $noIzinUrutPreview = $request->input('next_nomor_izin') ?? $perijinan->next_nomor_izin ?? 1;
        $nomorIzinPreview = "{$kodePerijinanPreview}/{$noIzinUrutPreview}/DPMPTSP/{$tahunPreview}";

        $replacements['${NOMOR_REKOM}'] = $nomorRekomPreview;
        $replacements['[NOMOR REKOM]'] = $nomorRekomPreview;
        $replacements['${NOMOR_IZIN}'] = $nomorIzinPreview;
        $replacements['[NOMOR IZIN]'] = $nomorIzinPreview;


        // [GAMBAR TTE]
        $gambarTte = \App\Models\Setting::get('gambar_tte');
        
        // If operator OPD, use their OPD's TTE if available
        if ($user->role === 'operator_opd' && $user->opd_id && $user->opd && $user->opd->gambar_tte) {
            $gambarTte = $user->opd->gambar_tte;
        }

        $tteHtml = '<div style="width: 100px; height: 100px; border: 1px dashed #ccc; display: inline-flex; align-items: center; justify-content: center; font-size: 10px; color: #999;">[QR CODE TTE]</div>';
        if ($gambarTte) {
            $ttePath = public_path($gambarTte);
            if (!\Illuminate\Support\Facades\File::exists($ttePath) && \Illuminate\Support\Facades\Storage::disk('public')->exists($gambarTte)) {
                $ttePath = \Illuminate\Support\Facades\Storage::disk('public')->path($gambarTte);
            }
            if (\Illuminate\Support\Facades\File::exists($ttePath)) {
                $imageData = base64_encode(\Illuminate\Support\Facades\File::get($ttePath));
                $mime = \Illuminate\Support\Facades\File::mimeType($ttePath);
                $tteHtml = '<img src="data:' . $mime . ';base64,' . $imageData . '" style="width: 100px; height: 100px; object-fit: contain;">';
            }
        }
        $replacements['[GAMBAR TTE]'] = $tteHtml;

        // [LOGO KABUPATEN]
        $logoKab = \App\Models\Setting::get('logo_kabupaten');
        $logoHtml = '<div style="width: 80px; height: 100px; border: 1px dashed #ccc; display: inline-flex; align-items: center; justify-content: center; font-size: 10px; color: #999;">[LOGO KAB]</div>';
        if ($logoKab) {
            $logoPath = public_path($logoKab);
            if (!\Illuminate\Support\Facades\File::exists($logoPath) && \Illuminate\Support\Facades\Storage::disk('public')->exists($logoKab)) {
                $logoPath = \Illuminate\Support\Facades\Storage::disk('public')->path($logoKab);
            }
            if (\Illuminate\Support\Facades\File::exists($logoPath)) {
                $imageData = base64_encode(\Illuminate\Support\Facades\File::get($logoPath));
                $mime = \Illuminate\Support\Facades\File::mimeType($logoPath);
                $logoHtml = '<img src="data:' . $mime . ';base64,' . $imageData . '" style="width: 80px; height: auto; object-fit: contain;">';
            }
        }
        $replacements['[LOGO KABUPATEN]'] = $logoHtml;

        // Add dynamic form field placeholders
        $formFieldsQuery = $perijinan->formFields()->where('is_active', true)->where('form_type', $templateType);
        
        // If it's rekom and we have OPD context, filter by OPD
        if ($templateType === 'rekom' && $user->role === 'operator_opd' && $user->opd_id) {
            $formFieldsQuery->where('opd_id', $user->opd_id);
        } else {
            // Default/Admin fields
            $formFieldsQuery->whereNull('opd_id');
        }

        $formFields = $formFieldsQuery->get();
        foreach ($formFields as $field) {
            $placeholder = '[' . strtoupper($field->name) . ']';
            $replacements[$placeholder] = 'DUMMY_' . strtoupper($field->name);
        }

        // Global form fields also available
        $globalFieldsQuery = $perijinan->activeFormFields()->where('form_type', 'global');
        if ($user->role === 'operator_opd' && $user->opd_id) {
            $globalFieldsQuery->where(function($q) use ($user) {
                $q->where('opd_id', $user->opd_id)->orWhereNull('opd_id');
            });
        } else {
            $globalFieldsQuery->whereNull('opd_id');
        }
        
        $globalFields = $globalFieldsQuery->get();
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
        // Access Control: Role based field restrictions
        if ($user->role === 'operator_opd') {
            if ($request->input('form_type') !== 'rekom') {
                return redirect()->back()->with('error', 'Anda hanya memiliki akses untuk mengelola field Formulir Rekomendasi.');
            }
        } elseif ($user->role === 'verifikator') {
            if ($request->input('form_type') !== 'izin') {
                return redirect()->back()->with('error', 'Anda hanya memiliki akses untuk mengelola field Formulir Izin.');
            }
        } elseif ($user->role === 'bo') {
            if ($request->input('form_type') !== 'bo') {
                return redirect()->back()->with('error', 'Anda hanya memiliki akses untuk mengelola field Formulir BO.');
            }
        }

        $validated = $request->validate([
            'form_type' => 'nullable|in:global,rekom,izin,bo',
            'label' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'type' => 'required|in:text,textarea,number,date,email,phone,select,radio,checkbox,file,pas_foto,gambar,table',
            'is_required' => 'boolean',
            'placeholder' => 'nullable|string',
            'help_text' => 'nullable|string',
            'options' => 'nullable|array',
            'order' => 'integer',
            'is_active' => 'boolean',
            'file_types' => 'nullable|string|max:255',
            'max_file_size' => 'nullable|integer|min:1',
        ]);

        if (!empty($validated['file_types'])) {
            $validated['file_types'] = implode(',', array_map('trim', explode(',', $validated['file_types'])));
        }

        $validated['perijinan_id'] = $perijinan->id;
        $validated['form_type'] = $validated['form_type'] ?? 'global';
        $validated['is_required'] = $request->has('is_required');
        $validated['is_active'] = $request->has('is_active');

        // If operator OPD, associate with their OPD
        if ($user->role === 'operator_opd' && $user->opd_id) {
            $validated['opd_id'] = $user->opd_id;
        }

        $validated['order'] = $request->input('order', $perijinan->formFields()->where('form_type', $validated['form_type'])->max('order') + 1);

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
        // Access Control: Role based field restrictions
        if ($user->role === 'operator_opd') {
            if ($field->form_type !== 'rekom' || $request->input('form_type') !== 'rekom') {
                return redirect()->back()->with('error', 'Anda hanya memiliki akses untuk mengelola field Formulir Rekomendasi.');
            }
            if ($field->opd_id !== $user->opd_id) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk memperbarui field ini.');
            }
        } elseif ($user->role === 'verifikator') {
            if ($field->form_type !== 'izin' || $request->input('form_type') !== 'izin') {
                return redirect()->back()->with('error', 'Anda hanya memiliki akses untuk mengelola field Formulir Izin.');
            }
        } elseif ($user->role === 'bo') {
            if ($field->form_type !== 'bo' || $request->input('form_type') !== 'bo') {
                return redirect()->back()->with('error', 'Anda hanya memiliki akses untuk mengelola field Formulir BO.');
            }
        }

        $validated = $request->validate([
            'form_type' => 'nullable|in:global,rekom,izin,bo',
            'label' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'type' => 'required|in:text,textarea,number,date,email,phone,select,radio,checkbox,file,pas_foto,gambar,table',
            'is_required' => 'boolean',
            'placeholder' => 'nullable|string',
            'help_text' => 'nullable|string',
            'options' => 'nullable|array',
            'order' => 'integer',
            'is_active' => 'boolean',
            'file_types' => 'nullable|string|max:255',
            'max_file_size' => 'nullable|integer|min:1',
        ]);

        if (!empty($validated['file_types'])) {
            $validated['file_types'] = implode(',', array_map('trim', explode(',', $validated['file_types'])));
        }

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

        // Access Control: Role based field restrictions
        if ($user->role === 'operator_opd') {
            if ($field->form_type !== 'rekom') {
                return redirect()->back()->with('error', 'Anda hanya memiliki akses untuk menghapus field Formulir Rekomendasi.');
            }
            if ($field->opd_id !== $user->opd_id) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menghapus field ini.');
            }
        } elseif ($user->role === 'verifikator') {
            if ($field->form_type !== 'izin') {
                return redirect()->back()->with('error', 'Anda hanya memiliki akses untuk menghapus field Formulir Izin.');
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
        if ($perijinan->validasi_tanpa_opd) {
            unset($availableRoles['operator_opd']);
            unset($availableRoles['kepala_opd']);
        }
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

        if ($perijinan->validasi_tanpa_opd && in_array($request->role, ['operator_opd', 'kepala_opd'])) {
            return redirect()->back()->with('error', 'Perizinan ini diset validasi tanpa OPD, tidak dapat menambahkan role Operator OPD atau Kepala OPD.');
        }

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

        if ($perijinan->validasi_tanpa_opd && in_array($request->role, ['operator_opd', 'kepala_opd'])) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Perizinan ini diset validasi tanpa OPD, tidak dapat memperbarui role ke Operator OPD atau Kepala OPD.'
                ], 422);
            }
            return redirect()->back()->with('error', 'Perizinan ini diset validasi tanpa OPD, tidak dapat memperbarui role ke Operator OPD atau Kepala OPD.');
        }

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

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Alur validasi berhasil diperbarui.'
            ]);
        }

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
        $perijinan = Perijinan::findOrFail($id);

        $request->validate([
            'kode_perijinan' => 'nullable|string|max:50',
            'nama_perijinan' => 'required|string|max:255',
            'jenis_perijinan' => 'required|in:umum,pencabutan_medis',
            'is_multi_opd' => 'nullable|boolean',
            'has_bo_form' => 'nullable|boolean',
            'validasi_tanpa_opd' => 'nullable|boolean',
            'opsi_perpanjangan' => 'nullable|in:setelah_habis,sebelum_habis,keduanya',
            'dasar_hukum' => 'required|string',
            'persyaratan' => 'required|string',
            'prosedur' => 'required|string',
            'informasi_biaya' => 'nullable|string',
            'lama_waktu_proses' => 'nullable|string|max:255',
            'gambar_alur' => 'nullable|file|mimes:png,jpg,jpeg|max:2048',
        ]);

        $data = $request->all();
        $data['is_multi_opd'] = $request->has('is_multi_opd');
        $data['has_bo_form'] = $request->has('has_bo_form');
        $data['validasi_tanpa_opd'] = $request->has('validasi_tanpa_opd');

        if ($data['validasi_tanpa_opd']) {
            $data['is_multi_opd'] = false;
        }

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

        if ($perijinan->validasi_tanpa_opd) {
            $perijinan->validationFlows()->whereIn('role', ['operator_opd', 'kepala_opd'])->delete();
        }

        // Log activity
        ActivityLog::log(
            'Mengupdate jenis perijinan',
            $perijinan,
            'updated',
            ['data' => $data],
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

        // Access Control: Role based template restrictions
        if ($user->role === 'operator_opd') {
            if ($request->has('template_surat_izin') || $request->has('next_nomor_izin') || $request->has('template_pernyataan')) {
                return redirect()->back()->with('error', 'Anda hanya memiliki akses untuk memperbarui Template Rekomendasi.');
            }
        } elseif ($user->role === 'verifikator') {
            if ($request->has('template_surat_rekom') || $request->has('next_nomor_rekom') || $request->has('template_pernyataan')) {
                return redirect()->back()->with('error', 'Anda hanya memiliki akses untuk memperbarui Template Izin.');
            }
        }

        $request->validate([
            'template_pernyataan' => 'nullable|string',
            'template_permohonan' => 'nullable|string',
            'template_keabsahan' => 'nullable|string',
            'template_surat_rekom' => 'nullable',
            'template_surat_izin' => 'nullable',
            'file_template_rekom' => 'nullable|file|mimes:docx|max:10240',
            'file_template_izin' => 'nullable|file|mimes:docx|max:10240',
            'keterangan_rekom' => 'nullable|string',
            'keterangan_izin' => 'nullable|string',
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
            if ($request->has('keterangan_rekom')) $updateData['keterangan_rekom'] = $request->keterangan_rekom;
            if ($request->has('keterangan_izin')) $updateData['keterangan_izin'] = $request->keterangan_izin;
            if ($request->has('next_nomor_rekom')) $updateData['next_nomor_rekom'] = $request->next_nomor_rekom;
            if ($request->has('next_nomor_izin')) $updateData['next_nomor_izin'] = $request->next_nomor_izin;

            // Handle file uploads
            if ($request->hasFile('file_template_rekom')) {
                // Delete old file if exists
                if ($perijinan->template_surat_rekom && file_exists(public_path($perijinan->template_surat_rekom))) {
                    @unlink(public_path($perijinan->template_surat_rekom));
                }

                $file = $request->file('file_template_rekom');
                $filename = 'template_rekom_' . $perijinan->id . '_' . time() . '.docx';
                $uploadPath = public_path('uploads/templates');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                $file->move($uploadPath, $filename);
                $path = 'uploads/templates/' . $filename;
                $updateData['template_surat_rekom'] = $path;
            }
            if ($request->hasFile('file_template_izin')) {
                // Delete old file if exists
                if ($perijinan->template_surat_izin && file_exists(public_path($perijinan->template_surat_izin))) {
                    @unlink(public_path($perijinan->template_surat_izin));
                }

                $file = $request->file('file_template_izin');
                $filename = 'template_izin_' . $perijinan->id . '_' . time() . '.docx';
                $uploadPath = public_path('uploads/templates');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                $file->move($uploadPath, $filename);
                $path = 'uploads/templates/' . $filename;
                $updateData['template_surat_izin'] = $path;
            }
        } else {
            // Role-based restrictions logic
            $restrictedFields = [];
            $roleLabel = '';
            
            if ($user->role === 'operator_opd') {
                $restrictedFields = ['template_surat_izin', 'file_template_izin', 'keterangan_izin', 'next_nomor_izin', 'template_pernyataan', 'template_permohonan', 'template_keabsahan'];
                $roleLabel = 'Rekomendasi';
            } elseif ($user->role === 'verifikator') {
                $restrictedFields = ['template_surat_rekom', 'file_template_rekom', 'keterangan_rekom', 'next_nomor_rekom', 'template_pernyataan', 'template_permohonan', 'template_keabsahan'];
                $roleLabel = 'Izin';
            }

            foreach ($restrictedFields as $field) {
                if ($request->has($field) && $request->get($field) !== null && $request->get($field) != $perijinan->$field) {
                    return redirect()->back()->with('error', "Anda hanya memiliki akses untuk memperbarui Template {$roleLabel}.");
                }
            }

            // Assign allowed data
            if ($user->role === 'operator_opd' && $user->opd_id) {
                // Fetch/Create OPD config
                $opdConfig = PerijinanOpdConfig::firstOrCreate([
                    'perijinan_id' => $perijinan->id,
                    'opd_id' => $user->opd_id
                ]);

                $opdUpdateData = [];
                if ($request->has('template_surat_rekom')) $opdUpdateData['template_surat_rekom'] = $request->template_surat_rekom;
                if ($request->has('keterangan_rekom')) $opdUpdateData['keterangan_rekom'] = $request->keterangan_rekom;
                if ($request->has('next_nomor_rekom')) $opdUpdateData['next_nomor_rekom'] = $request->next_nomor_rekom;
                
                if ($request->hasFile('file_template_rekom')) {
                    // Delete old file if exists
                    if ($opdConfig->template_surat_rekom && file_exists(public_path($opdConfig->template_surat_rekom))) {
                        @unlink(public_path($opdConfig->template_surat_rekom));
                    }

                    $file = $request->file('file_template_rekom');
                    $filename = 'template_rekom_' . $perijinan->id . '_opd_' . $user->opd_id . '_' . time() . '.docx';
                    $uploadPath = public_path('uploads/templates');
                    if (!file_exists($uploadPath)) {
                        mkdir($uploadPath, 0755, true);
                    }
                    $file->move($uploadPath, $filename);
                    $path = 'uploads/templates/' . $filename;
                    $opdUpdateData['template_surat_rekom'] = $path;
                }

                if (!empty($opdUpdateData)) {
                    $opdConfig->update($opdUpdateData);
                    
                    ActivityLog::log(
                        'Memperbarui template rekom OPD',
                        $perijinan,
                        'updated',
                        ['opd_id' => $user->opd_id, 'data' => $opdUpdateData],
                        'perijinan_opd'
                    );

                    return back()->with('success', 'Template rekomendasi OPD berhasil diperbarui.')->with('active_tab', 'rekom');
                }
            } elseif ($user->role === 'verifikator') {
                if ($request->has('template_surat_izin')) $updateData['template_surat_izin'] = $request->template_surat_izin;
                if ($request->has('keterangan_izin')) $updateData['keterangan_izin'] = $request->keterangan_izin;
                if ($request->has('next_nomor_izin')) $updateData['next_nomor_izin'] = $request->next_nomor_izin;
                if ($request->hasFile('file_template_izin')) {
                    // Delete old file if exists
                    if ($perijinan->template_surat_izin && file_exists(public_path($perijinan->template_surat_izin))) {
                        @unlink(public_path($perijinan->template_surat_izin));
                    }

                    $file = $request->file('file_template_izin');
                    $filename = 'template_izin_' . $perijinan->id . '_' . time() . '.docx';
                    $uploadPath = public_path('uploads/templates');
                    if (!file_exists($uploadPath)) {
                        mkdir($uploadPath, 0755, true);
                    }
                    $file->move($uploadPath, $filename);
                    $path = 'uploads/templates/' . $filename;
                    $updateData['template_surat_izin'] = $path;
                }
            }
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

    /**
     * Download the uploaded template document.
     */
    public function downloadTemplate(Request $request, string $id, string $type)
    {
        $perijinan = Perijinan::findOrFail($id);
        $user = auth()->user();

        // Access Control: Role based template restrictions
        if ($user->role === 'operator_opd' && $type !== 'rekom') {
            return redirect()->back()->with('error', 'Anda hanya memiliki akses untuk mengunduh Template Rekomendasi.');
        } elseif ($user->role === 'verifikator' && $type !== 'izin') {
            return redirect()->back()->with('error', 'Anda hanya memiliki akses untuk mengunduh Template Izin.');
        }

        $path = '';
        $filename = '';

        if ($type === 'rekom') {
            $requestedOpdId = $request->input('opd_id');

            // 1. Admin requesting a specific OPD's template
            if ($user->isAdmin() && $requestedOpdId) {
                $opdConfig = PerijinanOpdConfig::with('opd')->where('perijinan_id', $id)->where('opd_id', $requestedOpdId)->first();
                if ($opdConfig && $opdConfig->template_surat_rekom) {
                    $path = $opdConfig->template_surat_rekom;
                    $filename = 'Template_Rekomendasi_' . str_replace(' ', '_', $opdConfig->opd->nama_opd ?? 'OPD') . '.docx';
                }
            }
            // 2. OPD User downloading their own template (unless forced global)
            elseif ($user->role === 'operator_opd' && $user->opd_id) {
                if ($request->has('force_global') && $request->input('force_global') == '1') {
                    // Explicitly requesting the global admin template
                    $path = $perijinan->template_surat_rekom;
                    $filename = 'Template_Acuan_Admin_' . str_replace(' ', '_', $perijinan->nama_perijinan) . '.docx';
                } else {
                    // Requesting their own custom template
                    $opdConfig = PerijinanOpdConfig::where('perijinan_id', $id)->where('opd_id', $user->opd_id)->first();
                    if ($opdConfig && $opdConfig->template_surat_rekom) {
                        $path = $opdConfig->template_surat_rekom;
                        $filename = 'Template_Kustom_OPD_' . str_replace(' ', '_', $perijinan->nama_perijinan) . '.docx';
                    }
                }
            }
            
            // Fallback to global if path is still empty
            if (empty($path)) {
                $path = $perijinan->template_surat_rekom;
            }
            if (empty($filename)) {
                $filename = 'Template_Rekomendasi_' . str_replace(' ', '_', $perijinan->nama_perijinan) . '.docx';
            }
        } elseif ($type === 'izin') {
            $path = $perijinan->template_surat_izin;
            $filename = 'Template_Izin_' . str_replace(' ', '_', $perijinan->nama_perijinan) . '.docx';
        }

        if (empty($path)) {
            return redirect()->back()->with('error', 'File template tidak ditemukan di database.');
        }

        // Files are now stored in public/uploads/templates
        $absolutePath = public_path($path);

        if (!file_exists($absolutePath)) {
            return redirect()->back()->with('error', 'File fisik template tidak ditemukan di server.');
        }

        return response()->download($absolutePath, $filename);
    }
}
