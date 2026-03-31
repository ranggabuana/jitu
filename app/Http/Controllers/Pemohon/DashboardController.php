<?php

namespace App\Http\Controllers\Pemohon;

use App\Http\Controllers\Controller;
use App\Models\Perijinan;
use App\Models\PerijinanFormField;
use App\Models\DataPerijinan;
use App\Models\DataPerijinanValidasi;
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

        // Sample messages (placeholder)
        $messages = [];

        return view('pemohon.dashboard.index', compact(
            'user',
            'stats',
            'recentApplications',
            'messages'
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
            $query->where(function($q) use ($search) {
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
            'activeFormFields' => function($query) {
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

        $perijinan = Perijinan::findOrFail($request->perijinan_id);
        
        // Build validation rules based on perijinan's form fields
        $validationRules = [];
        $validationMessages = [];
        
        if ($perijinan->activeFormFields->count() > 0) {
            foreach ($perijinan->activeFormFields as $field) {
                $fieldKey = 'form_fields.' . $field->id;
                $rules = [];

                if ($field->is_required) {
                    $rules[] = 'required';
                    $validationMessages[$fieldKey . '.required'] = "Field {$field->label} wajib diisi.";
                } else {
                    $rules[] = 'nullable';
                }

                // Add type-specific validation
                if ($field->type === 'email') {
                    $rules[] = 'email';
                } elseif ($field->type === 'number') {
                    $rules[] = 'numeric';
                } elseif ($field->type === 'file') {
                    // File validation handled separately below
                    continue;
                }

                $validationRules[$fieldKey] = $rules;
            }
        }

        // Validate files
        if ($request->hasFile('form_files')) {
            foreach ($request->form_files as $fieldId => $file) {
                $field = $perijinan->activeFormFields->firstWhere('id', $fieldId);
                if ($field) {
                    if ($field->is_required) {
                        $validationRules['form_files.' . $fieldId] = 'required|file|max:10240';
                        $validationMessages['form_files.' . $fieldId . '.required'] = "File {$field->label} wajib diunggah.";
                    } else {
                        $validationRules['form_files.' . $fieldId] = 'nullable|file|max:10240';
                    }
                }
            }
        } else {
            // Check for required files that weren't uploaded
            foreach ($perijinan->activeFormFields as $field) {
                if ($field->type === 'file' && $field->is_required) {
                    $validationRules['form_files.' . $field->id] = 'required';
                    $validationMessages['form_files.' . $field->id . '.required'] = "File {$field->label} wajib diunggah.";
                }
            }
        }
        
        $request->validate($validationRules, $validationMessages);

        try {
            DB::beginTransaction();

            // Create application with unique registration number
            $data = DataPerijinan::create([
                'user_id' => $user->id,
                'perijinan_id' => $perijinan->id,
                'status' => 'submitted',
                'current_step' => 1,
                'form_data' => $request->form_fields ?? [],
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

            // Create validation records based on perijinan validation flows
            $validationFlows = $perijinan->activeValidationFlows()->orderBy('order')->get();
            
            foreach ($validationFlows as $index => $flow) {
                DataPerijinanValidasi::create([
                    'data_perijinan_id' => $data->id,
                    'validation_flow_id' => $flow->id,
                    'user_id' => $flow->user_id, // Assigned validator
                    'status' => 'pending',
                    'order' => $index + 1,
                ]);
            }

            DB::commit();

            return redirect()->route('pemohon.pengajuan.success', $data->id)
                ->with('success', 'Pengajuan berhasil dikirim. Nomor registrasi: ' . $data->no_registrasi);
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error storing pengajuan: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengirim pengajuan. Silakan coba lagi.');
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
