<?php

namespace App\Http\Controllers\Admin;

use App\Models\Perijinan;
use App\Models\PerijinanFormField;
use App\Models\PerijinanValidationFlow;
use App\Models\User;
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
            'dasar_hukum' => 'required|string',
            'persyaratan' => 'required|string',
            'prosedur' => 'required|string',
            'informasi_biaya' => 'nullable|string',
        ]);

        $perijinan = Perijinan::create($request->all());

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
     * Store a new form field.
     */
    public function storeFormField(Request $request, string $id)
    {
        $perijinan = Perijinan::findOrFail($id);

        $validated = $request->validate([
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
        $validated['is_required'] = $request->has('is_required');
        $validated['is_active'] = $request->has('is_active');
        $validated['order'] = $request->input('order', $perijinan->formFields()->count() + 1);

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
        $operatorOpdUsers = PerijinanValidationFlow::getUsersByRole('operator_opd');
        $kepalaOpdUsers = PerijinanValidationFlow::getUsersByRole('kepala_opd');
        return view('perijinan.alur-validasi', compact('perijinan', 'availableRoles', 'operatorOpdUsers', 'kepalaOpdUsers'));
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
            'nama_perijinan' => 'required|string|max:255',
            'dasar_hukum' => 'required|string',
            'persyaratan' => 'required|string',
            'prosedur' => 'required|string',
            'informasi_biaya' => 'nullable|string',
        ]);

        $perijinan = Perijinan::findOrFail($id);
        $oldData = $perijinan->toArray();
        
        $perijinan->update($request->all());

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
}
