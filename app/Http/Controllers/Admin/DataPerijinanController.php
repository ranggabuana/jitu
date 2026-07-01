<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DataPerijinan;
use App\Models\Perijinan;
use App\Models\User;
use App\Models\DataPerijinanValidasi;
use App\Models\PerijinanValidationFlow;
use App\Models\ActivityLog;
use App\Mail\ApplicationStatusNotification;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DataPerijinanController extends Controller
{
    /**
     * Display in-progress applications (dalam proses).
     */
    public function dalamProses(Request $request)
    {
        $user = auth()->user();
        
        $query = DataPerijinan::with(['user', 'perijinan']);

        // Filter data based on user's assigned validation flows
        // Admin can see all, other users only see their assigned perijinan
        if (!$user->isAdmin()) {
            $accessiblePerijinanIds = $user->getAccessiblePerijinanIds();
            
            // Debug: Log accessible IDs
            \Log::info('User ID: ' . $user->id . ', Role: ' . $user->role . ', Accessible Perijinan IDs: ' . json_encode($accessiblePerijinanIds));
            
            // If user has assigned perijinan, filter by those IDs
            if (!empty($accessiblePerijinanIds)) {
                // Filter by perijinan IDs
                $query->whereIn('perijinan_id', $accessiblePerijinanIds);
                
                // For collective roles (FO, BO, Verifikator, Kadin), also filter by their role
                $collectiveRoles = ['fo', 'bo', 'verifikator', 'kadin'];
                if (in_array($user->role, $collectiveRoles)) {
                    // Get validation flow IDs for this role
                    $validationFlowIds = PerijinanValidationFlow::whereIn('role', $collectiveRoles)
                        ->where('is_active', true)
                        ->pluck('id');
                    
                    // Filter applications that have validation flow for this role
                    $query->whereHas('validasiRecords', function($q) use ($validationFlowIds) {
                        $q->whereIn('validation_flow_id', $validationFlowIds);
                    });
                }
            } else {
                // User has no assigned perijinan yet, show empty result with pagination
                $applications = DataPerijinan::where('id', 0)->paginate(10);
                
                return view('admin.data-perijinan.dalam-proses', [
                    'applications' => $applications,
                    'totalDalamProses' => 0,
                    'totalSubmitted' => 0,
                    'totalInProgress' => 0,
                    'totalPerbaikan' => 0,
                    'perijinanTypes' => collect([])
                ]);
            }
        }

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_registrasi', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('perijinan', function ($q) use ($search) {
                        $q->where('nama_perijinan', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by perijinan type
        if ($request->filled('perijinan_id')) {
            $query->where('perijinan_id', $request->perijinan_id);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Filter by current validation stage (role)
        if ($request->filled('step')) {
            $query->whereHas('validasiRecords', function($q) use ($request) {
                $q->where('order', DB::raw('data_perijinan.current_step'))
                  ->whereHas('validationFlow', function($q2) use ($request) {
                      $q2->where('role', $request->step);
                  });
            });
        }

        // Get only in-progress applications (not approved/completed, and not rejected)
        $query->whereNotIn('status', ['approved', 'diperbaiki', 'diperpanjang', 'completed', 'rejected', 'perbaikan']);

        $applications = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        // Statistics - only count for accessible perijinan
        if ($user->isAdmin()) {
            $totalDalamProses = DataPerijinan::whereNotIn('status', ['approved', 'diperbaiki', 'diperpanjang', 'completed', 'rejected', 'perbaikan'])->count();
            $totalSubmitted = DataPerijinan::where('status', 'submitted')->count();
            $totalInProgress = DataPerijinan::where('status', 'in_progress')->count();
            $totalPerbaikan = DataPerijinan::where('status', 'perbaikan')->count();
        } else {
            $accessibleIds = $user->getAccessiblePerijinanIds();
            $totalDalamProses = DataPerijinan::whereIn('perijinan_id', $accessibleIds)
                ->whereNotIn('status', ['approved', 'diperbaiki', 'diperpanjang', 'completed', 'rejected', 'perbaikan'])->count();
            $totalSubmitted = DataPerijinan::whereIn('perijinan_id', $accessibleIds)
                ->where('status', 'submitted')->count();
            $totalInProgress = DataPerijinan::whereIn('perijinan_id', $accessibleIds)
                ->where('status', 'in_progress')->count();
            $totalPerbaikan = DataPerijinan::whereIn('perijinan_id', $accessibleIds)
                ->where('status', 'perbaikan')->count();
        }

        // Perijinan types for filter
        $perijinanTypes = Perijinan::orderBy('nama_perijinan')->get();

        // Log activity
        ActivityLog::log(
            'Melihat daftar perijinan dalam proses',
            null,
            'viewed',
            [
                'search' => $request->search,
                'status_filter' => $request->status,
            ],
            'data_perijinan'
        );

        return view('admin.data-perijinan.dalam-proses', compact(
            'applications',
            'totalDalamProses',
            'totalSubmitted',
            'totalInProgress',
            'totalPerbaikan',
            'perijinanTypes'
        ));
    }

    /**
     * Display completed applications (selesai).
     */
    public function selesai(Request $request)
    {
        $user = auth()->user();

        $query = DataPerijinan::with(['user', 'perijinan']);

        // Filter data based on user's assigned validation flows
        // Admin can see all, other users only see their assigned perijinan
        if (!$user->isAdmin()) {
            $accessiblePerijinanIds = $user->getAccessiblePerijinanIds();

            // If user has assigned perijinan, filter by those IDs
            if (!empty($accessiblePerijinanIds)) {
                // Filter by perijinan IDs
                $query->whereIn('perijinan_id', $accessiblePerijinanIds);

                // For collective roles (FO, BO, Verifikator, Kadin), also filter by their role
                $collectiveRoles = ['fo', 'bo', 'verifikator', 'kadin'];
                if (in_array($user->role, $collectiveRoles)) {
                    // Get validation flow IDs for this role
                    $validationFlowIds = PerijinanValidationFlow::whereIn('role', $collectiveRoles)
                        ->where('is_active', true)
                        ->pluck('id');

                    // Filter applications that have validation flow for this role
                    $query->whereHas('validasiRecords', function($q) use ($validationFlowIds) {
                        $q->whereIn('validation_flow_id', $validationFlowIds);
                    });
                }
            } else {
                // User has no assigned perijinan yet, show empty result with pagination
                $applications = DataPerijinan::where('id', 0)->paginate(10);

                return view('admin.data-perijinan.selesai', [
                    'applications' => $applications,
                    'totalSelesai' => 0,
                    'totalApproved' => 0,
                    'perijinanTypes' => collect([])
                ]);
            }
        }

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_registrasi', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('perijinan', function ($q) use ($search) {
                        $q->where('nama_perijinan', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('approved_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('approved_at', '<=', $request->end_date);
        }

        // Get only completed/approved applications
        $query->whereIn('status', ['approved', 'diperbaiki']);

        $applications = $query->orderBy('approved_at', 'desc')->paginate(10)->withQueryString();

        // Statistics - only count for accessible perijinan
        if ($user->isAdmin()) {
            $totalSelesai = DataPerijinan::whereIn('status', ['approved', 'diperbaiki'])->count();
            $totalApproved = DataPerijinan::whereIn('status', ['approved', 'diperbaiki'])->count();
        } else {
            $accessibleIds = $user->getAccessiblePerijinanIds();
            $totalSelesai = DataPerijinan::whereIn('perijinan_id', $accessibleIds)
                ->whereIn('status', ['approved', 'diperbaiki'])->count();
            $totalApproved = DataPerijinan::whereIn('perijinan_id', $accessibleIds)
                ->whereIn('status', ['approved', 'diperbaiki'])->count();
        }

        // Perijinan types for filter
        $perijinanTypes = Perijinan::orderBy('nama_perijinan')->get();

        // Log activity
        ActivityLog::log(
            'Melihat daftar perijinan selesai',
            null,
            'viewed',
            [
                'search' => $request->search,
            ],
            'data_perijinan'
        );

        return view('admin.data-perijinan.selesai', compact(
            'applications',
            'totalSelesai',
            'totalApproved',
            'perijinanTypes'
        ));
    }

    /**
     * Display rejected applications (ditolak).
     */
    public function ditolak(Request $request)
    {
        $user = auth()->user();

        $query = DataPerijinan::with(['user', 'perijinan', 'validasiRecords']);

        // Filter data based on user's assigned validation flows
        if (!$user->isAdmin()) {
            $accessiblePerijinanIds = $user->getAccessiblePerijinanIds();

            if (!empty($accessiblePerijinanIds)) {
                $query->whereIn('perijinan_id', $accessiblePerijinanIds);
            } else {
                $applications = DataPerijinan::where('id', 0)->paginate(10);

                return view('admin.data-perijinan.ditolak', [
                    'applications' => $applications,
                    'totalDitolak' => 0,
                    'perijinanTypes' => collect([])
                ]);
            }
        }

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_registrasi', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('perijinan', function ($q) use ($search) {
                        $q->where('nama_perijinan', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by perijinan type
        if ($request->filled('perijinan_id')) {
            $query->where('perijinan_id', $request->perijinan_id);
        }

        // Get only rejected applications
        $query->where('status', 'rejected');

        $applications = $query->orderBy('rejected_at', 'desc')->paginate(10)->withQueryString();

        // Statistics
        if ($user->isAdmin()) {
            $totalDitolak = DataPerijinan::where('status', 'rejected')->count();
        } else {
            $accessibleIds = $user->getAccessiblePerijinanIds();
            $totalDitolak = DataPerijinan::whereIn('perijinan_id', $accessibleIds)
                ->where('status', 'rejected')->count();
        }

        // Perijinan types for filter
        $perijinanTypes = Perijinan::orderBy('nama_perijinan')->get();

        // Log activity
        ActivityLog::log(
            'Melihat daftar perijinan ditolak',
            null,
            'viewed',
            [
                'search' => $request->search,
            ],
            'data_perijinan'
        );

        return view('admin.data-perijinan.ditolak', compact(
            'applications',
            'totalDitolak',
            'perijinanTypes'
        ));
    }

    /**
     * Display applications that need revision (perlu perbaikan).
     */
    public function perluPerbaikan(Request $request)
    {
        $user = auth()->user();

        $query = DataPerijinan::with(['user', 'perijinan', 'validasiRecords']);

        // Filter data based on user's assigned validation flows
        if (!$user->isAdmin()) {
            $accessiblePerijinanIds = $user->getAccessiblePerijinanIds();

            if (!empty($accessiblePerijinanIds)) {
                $query->whereIn('perijinan_id', $accessiblePerijinanIds);
            } else {
                $applications = DataPerijinan::where('id', 0)->paginate(10);

                return view('admin.data-perijinan.perlu-perbaikan', [
                    'applications' => $applications,
                    'totalPerluPerbaikan' => 0,
                    'perijinanTypes' => collect([])
                ]);
            }
        }

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_registrasi', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('perijinan', function ($q) use ($search) {
                        $q->where('nama_perijinan', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by perijinan type
        if ($request->filled('perijinan_id')) {
            $query->where('perijinan_id', $request->perijinan_id);
        }

        // Get only applications that need revision
        $query->where('status', 'perbaikan');

        $applications = $query->orderBy('updated_at', 'desc')->paginate(10)->withQueryString();

        // Statistics
        if ($user->isAdmin()) {
            $totalPerluPerbaikan = DataPerijinan::where('status', 'perbaikan')->count();
        } else {
            $accessibleIds = $user->getAccessiblePerijinanIds();
            $totalPerluPerbaikan = DataPerijinan::whereIn('perijinan_id', $accessibleIds)
                ->where('status', 'perbaikan')->count();
        }

        // Perijinan types for filter
        $perijinanTypes = Perijinan::orderBy('nama_perijinan')->get();

        // Log activity
        ActivityLog::log(
            'Melihat daftar perijinan perlu perbaikan',
            null,
            'viewed',
            [
                'search' => $request->search,
            ],
            'data_perijinan'
        );

        return view('admin.data-perijinan.perlu-perbaikan', compact(
            'applications',
            'totalPerluPerbaikan',
            'perijinanTypes'
        ));
    }

    /**
     * Show SLA Report for a specific application.
     */
    public function slaReport($id)
    {
        $application = DataPerijinan::with([
            'perijinan',
            'user',
            'validasiRecords.validationFlow', 
            'validasiRecords.validator.opd',
            'returnLogs.fromUser.opd'
        ])->findOrFail($id);
        
        $records = $application->validasiRecords->sortBy('order');
        $returnLogs = $application->returnLogs->sortByDesc('created_at');
        
        return view('admin.data-perijinan.sla-report', compact('application', 'records', 'returnLogs'));
    }

    /**
     * Process validation (approve/reject) for current step.
     */
    public function processValidation(Request $request, $id)
    {
        \Log::info('processValidation called', [
            'user_id' => auth()->id(),
            'user_role' => auth()->user()->role,
            'application_id' => $id,
            'action' => $request->action
        ]);
        
        $request->validate([
            'action' => 'required|in:approved,rejected,revision,return_to_bo,return_to_operator_opd,return_to_kepala_opd,return_to_verifikator',
            'catatan' => 'nullable|string|max:1000',
            'passphrase' => 'nullable|string',
            'target_opd_id' => 'nullable|exists:opd,id',
        ]);

        $user = auth()->user();
        \Log::info('User retrieved', ['user_id' => $user->id, 'role' => $user->role]);
        
        $application = DataPerijinan::with([
            'perijinan.activeValidationFlows',
            'validasiRecords.validationFlow'
        ])->findOrFail($id);

        DB::beginTransaction();
        try {
            // Admin hanya bisa memantau, tidak bisa validasi
            if ($user->isAdmin()) {
                return redirect()->back()->with('error', 'Admin tidak dapat melakukan validasi. Hanya user yang ditugaskan di alur validasi yang dapat melakukan validasi.');
            }

            // Cek jika status aplikasi adalah 'perbaikan' - tidak bisa divalidasi sebelum disubmit ulang
            if ($application->status === 'perbaikan') {
                return redirect()->back()->with('error', 'Pengajuan sedang dalam tahap perbaikan oleh pemohon. Tidak dapat divalidasi sebelum pemohon submit ulang.');
            }

            // Get current validation step
            $currentValidasi = $application->validasiRecords()
                ->where('order', $application->current_step)
                ->first();

            if (!$currentValidasi) {
                return redirect()->back()->with('error', 'Tahap validasi saat ini tidak ditemukan.');
            }

            $validationFlow = $currentValidasi->validationFlow;
            $isMultiOpd = $application->perijinan->is_multi_opd;
            $userRole = $user->role;

            // Target record for this user
            $myValidasi = null;
            
            // Logic for parallel validation in Multi-OPD
            if ($isMultiOpd && in_array($userRole, ['operator_opd', 'kepala_opd'])) {
                // Find all OPD steps
                $opdSteps = $application->validasiRecords()
                    ->whereHas('validationFlow', function($q) {
                        $q->whereIn('role', ['operator_opd', 'kepala_opd']);
                    })
                    ->get();
                
                $minOpdOrder = $opdSteps->min('order');
                $maxOpdOrder = $opdSteps->max('order');

                // Check if we are currently in the OPD validation phase
                if ($application->current_step < $minOpdOrder) {
                    return redirect()->back()->with('error', 'Permohonan belum mencapai tahap validasi OPD.');
                }
                
                // Find specific record assigned to this user
                $myValidasi = $application->validasiRecords()
                    ->whereHas('validationFlow', function($q) use ($user) {
                        $q->where('assigned_user_id', $user->id);
                    })
                    ->where('status', 'pending')
                    ->first();
                
                if (!$myValidasi) {
                    return redirect()->back()->with('error', 'Anda tidak memiliki tugas validasi yang tertunda untuk permohonan ini.');
                }
                
                // If Kepala OPD, ensure their Operator OPD has finished
                if ($userRole === 'kepala_opd') {
                    $operatorFlow = $application->perijinan->activeValidationFlows()
                        ->where('role', 'operator_opd')
                        ->whereHas('assignedUser', function($q) use ($user) {
                            $q->where('opd_id', $user->opd_id);
                        })
                        ->first();
                    
                    if ($operatorFlow) {
                        $operatorRecord = $application->validasiRecords()
                            ->where('validation_flow_id', $operatorFlow->id)
                            ->first();
                        
                        if ($operatorRecord && $operatorRecord->status !== 'approved') {
                            return redirect()->back()->with('error', 'Menunggu validasi dari Operator OPD Anda terlebih dahulu.');
                        }
                    }
                }
            } else {
                // Standard sequential check
                if ($validationFlow->assigned_user_id !== $user->id) {
                    return redirect()->back()->with('error', 'Anda tidak ditugaskan untuk melakukan validasi pada tahap saat ini (sedang dalam tahapan ' . ($validationFlow->role_label ?? 'Lainnya') . ').');
                }
                $myValidasi = $currentValidasi;
            }

            // Check if already validated (failsafe)
            if ($myValidasi->status !== 'pending') {
                return redirect()->back()->with('error', 'Tahap validasi ini sudah diselesaikan.');
            }

            // Guard: on pembetulan, BO must upload the izin PDF before approving
            if ($request->action === 'approved' && $application->is_pembetulan && $userRole === 'bo') {
                if (empty($application->file_izin_pembetulan) || !file_exists(public_path($application->file_izin_pembetulan))) {
                    return redirect()->back()->with('error', 'Harap unggah file PDF surat izin yang siap TTE terlebih dahulu sebelum menyetujui tahap ini.');
                }
            }

            // Enforce role restriction for revision action
            if ($request->action === 'revision' && in_array($userRole, ['verifikator', 'kadin'])) {
                return redirect()->back()->with('error', 'Verifikator dan Kadin tidak diperbolehkan meminta perbaikan ke pemohon.');
            }

            // Update validation status
            $updateData = [
                'user_id' => $user->id,
                'catatan' => $request->catatan,
                'validated_at' => now(),
            ];

            // Accumulate SLA Duration correctly
            if ($request->has('elapsed_seconds')) {
                $myValidasi->increment('duration_seconds', intval($request->elapsed_seconds));
                $updateData['sla_start_at'] = now(); // Reset timer anchor to prevent compounding
            }
            
            $isReturnAction = in_array($request->action, ['return_to_bo', 'return_to_operator_opd', 'return_to_kepala_opd', 'return_to_verifikator']);
            
            // Update status
            if (!$isReturnAction) {
                $updateData['status'] = $request->action;
            }
            $myValidasi->update($updateData);

            // Handle based on action
            if ($request->action === 'approved') {
                $perijinan = $application->perijinan;
                $applicationUpdateData = [];

                // Contextual OPD code assignment
                if ($user->role === 'operator_opd' || $user->role === 'kepala_opd') {
                    if ($application->no_rekom_kode === null && $user->opd) {
                        $applicationUpdateData['no_rekom_kode'] = $user->opd->kode_opd ?? 'OPD';
                    }
                } else if ($user->role === 'verifikator' || $user->role === 'kadin' || $user->role === 'admin') {
                    if ($application->no_izin_kode === null) {
                        $applicationUpdateData['no_izin_kode'] = 'DPMPTSP';
                    }
                }

                // Check overall progress
                $totalSteps = $application->perijinan->activeValidationFlows()->count();
                
                // Find next incomplete step in the sequence
                $nextPendingRecord = $application->validasiRecords()
                    ->where('status', 'pending')
                    ->orderBy('order', 'asc')
                    ->first();

                if (!$nextPendingRecord) {
                    // All validations complete - approve application
                    $applicationUpdateData['status'] = $application->is_pembetulan ? 'diperbaiki' : 'approved';
                    $applicationUpdateData['approved_at'] = now();
                    $applicationUpdateData['completed_at'] = now();

                    if ($application->is_pembetulan) {
                        $application->hasilSkm()->delete();
                    }

                    if ($application->no_rekom === null) {
                        $applicationUpdateData['no_rekom'] = $perijinan->next_nomor_rekom;
                        $perijinan->increment('next_nomor_rekom');
                    }
                    if ($application->no_izin === null) {
                        $applicationUpdateData['no_izin'] = $perijinan->next_nomor_izin;
                        $perijinan->increment('next_nomor_izin');
                    }

                    if ($application->user && $application->user->email) {
                        EmailService::send($application->user->email, $application->user->name, new ApplicationStatusNotification($application, 'approved'));
                    }
                } else {
                    // Update current_step to the next pending one
                    $applicationUpdateData['current_step'] = $nextPendingRecord->order;
                    $applicationUpdateData['status'] = 'in_progress';
                }

                if (!empty($applicationUpdateData)) {
                    $application->update($applicationUpdateData);

                    // Regenerate documents to reflect assigned codes/numbers
                    try {
                        $generatedDocs = \App\Services\DocumentGenerator::generateDocuments($application->fresh());
                        $application->update([
                            'file_rekom' => $generatedDocs['file_rekom'] ?? $application->file_rekom,
                            'file_izin' => $generatedDocs['file_izin'] ?? $application->file_izin,
                        ]);
                    } catch (\Exception $e) {
                        \Log::error('Gagal meregenerasi dokumen saat validation step: ' . $e->getMessage());
                    }
                }
            } else if ($request->action === 'rejected') {
                // Reject application - stop all validations
                $application->update([
                    'status' => 'rejected',
                    'catatan_reject' => $request->catatan,
                    'rejected_at' => now(),
                ]);

                // Send Email Notification: Rejected
                if ($application->user && $application->user->email) {
                        EmailService::send(
                        $application->user->email,
                        $application->user->name,
                        new ApplicationStatusNotification($application, 'rejected', $request->catatan)
                    );
                }

                // Mark all remaining validations as rejected
                $application->validasiRecords()
                    ->where('status', 'pending')
                    ->update(['status' => 'rejected']);
            } elseif ($request->action === 'revision') {
                // Send back for revision - RESET SLA
                
                // Delete generated recommendation/permit files from disk
                if ($application->file_rekom && file_exists(public_path($application->file_rekom))) {
                    @unlink(public_path($application->file_rekom));
                }
                if ($application->file_rekom_tte && file_exists(public_path($application->file_rekom_tte))) {
                    @unlink(public_path($application->file_rekom_tte));
                }
                if (!empty($application->file_rekom_multi) && is_array($application->file_rekom_multi)) {
                    foreach ($application->file_rekom_multi as $path) {
                        if ($path && file_exists(public_path($path))) {
                            @unlink(public_path($path));
                        }
                    }
                }
                if (!empty($application->file_rekom_multi_tte) && is_array($application->file_rekom_multi_tte)) {
                    foreach ($application->file_rekom_multi_tte as $path) {
                        if ($path && file_exists(public_path($path))) {
                            @unlink(public_path($path));
                        }
                    }
                }
                if ($application->file_izin && file_exists(public_path($application->file_izin))) {
                    @unlink(public_path($application->file_izin));
                }
                if ($application->file_izin_tte && file_exists(public_path($application->file_izin_tte))) {
                    @unlink(public_path($application->file_izin_tte));
                }

                $application->update([
                    'status' => 'perbaikan',
                    'catatan_perbaikan' => $request->catatan,
                    'current_step' => 1,
                    'bo_data' => null,
                    'rekom_data' => null,
                    'rekom_data_multi' => null,
                    'izin_data' => null,
                    'no_rekom' => null,
                    'no_izin' => null,
                    'no_rekom_kode' => null,
                    'no_izin_kode' => null,
                    'file_rekom' => null,
                    'file_rekom_tte' => null,
                    'file_rekom_multi' => null,
                    'file_rekom_multi_tte' => null,
                    'file_izin' => null,
                    'file_izin_tte' => null,
                ]);

                // Reset all validation records (SLA and status)
                $application->validasiRecords()->update([
                    'status' => 'pending',
                    'user_id' => null,
                    'catatan' => null,
                    'validated_at' => null,
                    'duration_seconds' => 0,
                    'sla_start_at' => null,
                ]);

                // Send Email Notification: Returned/Revision
                if ($application->user && $application->user->email) {
                        EmailService::send(
                        $application->user->email,
                        $application->user->name,
                        new ApplicationStatusNotification($application, 'returned', $request->catatan)
                    );
                }
            } elseif ($isReturnAction) {
                $targetRole = '';
                $roleLabel = '';
                if ($request->action === 'return_to_bo') {
                    $targetRole = 'bo';
                    $roleLabel = 'Back Office (BO)';
                } elseif ($request->action === 'return_to_operator_opd') {
                    $targetRole = 'operator_opd';
                    $roleLabel = 'Operator OPD';
                } elseif ($request->action === 'return_to_kepala_opd') {
                    $targetRole = 'kepala_opd';
                    $roleLabel = 'Kepala OPD';
                } elseif ($request->action === 'return_to_verifikator') {
                    $targetRole = 'verifikator';
                    $roleLabel = 'Verifikator';
                }

                $isMultiOpd = $application->perijinan->is_multi_opd;
                $userOpdId = $user->opd_id;

                // Find the target record in the validation steps
                $targetRecordQuery = $application->validasiRecords()
                    ->whereHas('validationFlow', function($q) use ($targetRole) {
                        $q->where('role', $targetRole);
                    });

                if ($isMultiOpd && $targetRole === 'kepala_opd' && $request->filled('target_opd_id')) {
                    $targetRecordQuery->whereHas('validationFlow.assignedUser', function($q) use ($request) {
                        $q->where('opd_id', $request->target_opd_id);
                    });
                } elseif ($isMultiOpd && $userOpdId && in_array($targetRole, ['operator_opd', 'kepala_opd'])) {
                    $targetRecordQuery->whereHas('validationFlow.assignedUser', function($q) use ($userOpdId) {
                        $q->where('opd_id', $userOpdId);
                    });
                }

                $targetRecord = $targetRecordQuery->orderBy('order', 'asc')->first();
                
                if ($targetRecord) {
                    $targetOrder = $targetRecord->order;
                    
                    \Log::info("Returning application {$application->no_registrasi} to {$roleLabel} (Order: {$targetOrder})");

                    // Log the return for SLA Report
                    \App\Models\DataPerijinanReturnLog::create([
                        'data_perijinan_id' => $application->id,
                        'from_user_id' => $user->id,
                        'from_role_label' => $user->role_label,
                        'to_role_label' => $roleLabel,
                        'catatan' => $request->catatan,
                    ]);

                    // Reset records from targetOrder onwards
                    $recordsQuery = $application->validasiRecords()->where('order', '>=', $targetOrder);

                    // If multi OPD, only reset records belonging to the targeted OPD or non-OPD roles (verifikator/kadin)
                    if ($isMultiOpd && $targetRole === 'kepala_opd' && $request->filled('target_opd_id')) {
                        $targetOpdId = $request->target_opd_id;
                        $recordsQuery->where(function($query) use ($targetOpdId) {
                            $query->whereHas('validationFlow.assignedUser', function($q) use ($targetOpdId) {
                                $q->where('opd_id', $targetOpdId);
                            })
                            ->orWhereHas('validationFlow', function($q) {
                                $q->whereNotIn('role', ['operator_opd', 'kepala_opd']);
                            });
                        });
                    } elseif ($isMultiOpd && $userOpdId && in_array($targetRole, ['operator_opd', 'kepala_opd'])) {
                        $recordsQuery->where(function($query) use ($userOpdId) {
                            $query->whereHas('validationFlow.assignedUser', function($q) use ($userOpdId) {
                                $q->where('opd_id', $userOpdId);
                            })
                            ->orWhereHas('validationFlow', function($q) {
                                $q->whereNotIn('role', ['operator_opd', 'kepala_opd']);
                            });
                        });
                    }

                    $recordsToReset = $recordsQuery->get();

                    foreach ($recordsToReset as $record) {
                        $record->status = 'pending';
                        $record->validated_at = null;
                        $record->catatan = null;
                        
                        // Clear user_id only if it's a collective role
                        if ($record->validationFlow && in_array($record->validationFlow->role, ['verifikator', 'kadin'])) {
                            $record->user_id = null;
                        }
                        
                        $record->save();

                        // Clear TTE files automatically when their steps are reset
                        if ($record->validationFlow) {
                            if ($record->validationFlow->role === 'kepala_opd') {
                                $opdId = $record->validationFlow->assignedUser->opd_id ?? null;
                                if ($isMultiOpd && $opdId) {
                                    $multiTte = $application->file_rekom_multi_tte ?? [];
                                    if (isset($multiTte[$opdId])) {
                                        if (file_exists(public_path($multiTte[$opdId]))) {
                                            @unlink(public_path($multiTte[$opdId]));
                                        }
                                        unset($multiTte[$opdId]);
                                        $application->file_rekom_multi_tte = $multiTte;
                                    }
                                } else {
                                    if ($application->file_rekom_tte && file_exists(public_path($application->file_rekom_tte))) {
                                        @unlink(public_path($application->file_rekom_tte));
                                    }
                                    $application->file_rekom_tte = null;
                                }
                            } elseif ($record->validationFlow->role === 'kadin') {
                                if ($application->file_izin_tte && file_exists(public_path($application->file_izin_tte))) {
                                    @unlink(public_path($application->file_izin_tte));
                                }
                                $application->file_izin_tte = null;
                            }
                        }
                    }

                    // Update application state to the first pending step
                    $firstPendingRecord = $application->validasiRecords()
                        ->where('status', 'pending')
                        ->orderBy('order', 'asc')
                        ->first();

                    if ($firstPendingRecord) {
                        $application->current_step = $firstPendingRecord->order;
                    } else {
                        $application->current_step = $targetOrder;
                    }
                    $application->status = 'in_progress';
                    $application->save();
                    
                } else {
                    DB::rollBack();
                    return redirect()->back()->with('error', "Tahap {$roleLabel} tidak ditemukan dalam alur validasi.");
                }
            }

            // Log activity
            $actionLabel = [
                'approved' => 'Menyetujui',
                'rejected' => 'Menolak',
                'revision' => 'Meminta perbaikan',
                'return_to_bo' => 'Mengembalikan ke BO',
                'return_to_operator_opd' => 'Mengembalikan ke Operator OPD',
                'return_to_kepala_opd' => 'Mengembalikan ke Kepala OPD',
                'return_to_verifikator' => 'Mengembalikan ke Verifikator'
            ][$request->action] ?? $request->action;

            ActivityLog::log(
                "{$actionLabel} validasi perijinan",
                $application,
                'updated',
                [
                    'action' => $request->action,
                    'catatan' => $request->catatan,
                    'current_step' => $application->current_step,
                    'no_registrasi' => $application->no_registrasi,
                ],
                'data_perijinan',
                $user->id
            );

            DB::commit();

            $msg = ($request->action === 'approved') ? 'Validasi berhasil disetujui.' :
                  (($request->action === 'rejected') ? 'Pengajuan ditolak.' :
                  (($request->action === 'revision') ? 'Pengajuan berhasil dikembalikan kepada pemohon.' :
                  'Pengajuan berhasil dikembalikan ke tahap sebelumnya.'));

            return redirect()->route('data-perijinan.show', $id)->with('success', $msg);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error processing validation: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses validasi.');
        }
    }

    /**
     * Display detail of an application.
     */
    public function show($id)
    {
        $user = auth()->user();

        $application = DataPerijinan::with([
            'user',
            'user.provinsi',
            'user.kabupaten',
            'user.kecamatan',
            'user.kelurahan',
            'perijinan',
            'perijinan.activeFormFields',
            'validasiRecords.validationFlow.assignedUser.opd',
            'validasiRecords.validator.opd'
        ])->findOrFail($id);

        // Find the specific validation record for the current user
        $userRole = auth()->user()->role;
        $isParallelOpdTurn = ($application->perijinan->is_multi_opd && in_array($userRole, ['operator_opd', 'kepala_opd']));
        $userAssignedRecord = null;
        if ($isParallelOpdTurn) {
            $userAssignedRecord = $application->validasiRecords->where('validationFlow.assigned_user_id', auth()->id())->where('status', 'pending')->first();
        }
        $cv = $application->validasiRecords->where('order', $application->current_step)->first();
        $activeTask = ($isParallelOpdTurn && $userAssignedRecord) ? $userAssignedRecord : $cv;

        // Initialize SLA start time if not set
        if ($activeTask && $activeTask->status === 'pending' && !$activeTask->sla_start_at) {
            // Check if this task is actually available for the user
            $canVal = false;
            if ($isParallelOpdTurn && $userAssignedRecord) {
                // In parallel multi-opd, check if operator can start or kepala opd can start after operator approved
                if ($userRole === 'operator_opd') {
                    $canVal = true; 
                } else if ($userRole === 'kepala_opd') {
                    $myOperator = $application->validasiRecords->first(fn($vr) => $vr->validationFlow->role === 'operator_opd' && $vr->validationFlow->assignedUser->opd_id == auth()->user()->opd_id);
                    $canVal = $myOperator && $myOperator->status === 'approved';
                }
            } else if ($cv && $cv->status === 'pending') {
                // sequential task
                if ($cv->validationFlow->role === $userRole && (in_array($userRole, ['verifikator', 'kadin']) || $cv->validationFlow->assigned_user_id === auth()->id())) {
                    $canVal = true;
                }
            }

            if ($canVal) {
                $activeTask->update(['sla_start_at' => now()]);
            }
        }

        // Ensure documents are generated if missing
        if (!$application->file_pernyataan || !$application->file_permohonan || !$application->file_keabsahan || !$application->file_rekom || !$application->file_izin) {
            try {
                $generatedDocs = \App\Services\DocumentGenerator::generateDocuments($application);
                $updateData = [];
                if (!$application->file_pernyataan && isset($generatedDocs['file_pernyataan'])) {
                    $updateData['file_pernyataan'] = $generatedDocs['file_pernyataan'];
                }
                if (!$application->file_permohonan && isset($generatedDocs['file_permohonan'])) {
                    $updateData['file_permohonan'] = $generatedDocs['file_permohonan'];
                }
                if (!$application->file_keabsahan && isset($generatedDocs['file_keabsahan'])) {
                    $updateData['file_keabsahan'] = $generatedDocs['file_keabsahan'];
                }
                if (!$application->file_rekom && isset($generatedDocs['file_rekom'])) {
                    $updateData['file_rekom'] = $generatedDocs['file_rekom'];
                }
                if (isset($generatedDocs['file_rekom_multi'])) {
                    $updateData['file_rekom_multi'] = $generatedDocs['file_rekom_multi'];
                }
                if (!$application->file_izin && isset($generatedDocs['file_izin'])) {
                    $updateData['file_izin'] = $generatedDocs['file_izin'];
                }
                
                if (!empty($updateData)) {
                    $application->update($updateData);
                }
            } catch (\Exception $e) {
                \Log::error('Auto-generation of documents failed in show: ' . $e->getMessage());
            }
        }

        // Check if user has access to this application
        if (!$user->isAdmin()) {
            $accessibleIds = $user->getAccessiblePerijinanIds();
            if (!empty($accessibleIds) && !in_array($application->perijinan_id, $accessibleIds)) {
                abort(403, 'Unauthorized access to this application');
            }
        }

        // Log activity
        ActivityLog::log(
            'Melihat detail perijinan',
            $application,
            'viewed',
            [
                'no_registrasi' => $application->no_registrasi,
            ],
            'data_perijinan'
        );

        // Fetch renewal history chain
        $rootPerpanjangId = $application->root_perpanjang_id ?? $application->id;
        $renewalHistory = DataPerijinan::where(function($q) use ($rootPerpanjangId) {
            $q->where('id', $rootPerpanjangId)
              ->orWhere('root_perpanjang_id', $rootPerpanjangId);
        })
        ->orderBy('created_at', 'asc')
        ->get();

        return view('admin.data-perijinan.show', compact('application', 'renewalHistory'));
    }

    /**
     * Save recommendation form data filled by Operator OPD.
     */
    public function saveRekomData(Request $request, $id)
    {
        $user = auth()->user();
        if ($user->isAdmin()) {
            abort(403, 'Admin hanya memiliki akses memantau (read-only).');
        }
        if ($user->role !== 'operator_opd') {
            abort(403, 'Hanya Operator OPD yang dapat mengisi data rekomendasi.');
        }

        $application = DataPerijinan::with(['perijinan.activeFormFields', 'validasiRecords.validationFlow'])->findOrFail($id);
        $perijinan = $application->perijinan;
        
        // Strict check for Multi-OPD Parallelism
        if (!$user->isAdmin()) {
            $isParallelAllowed = false;
            if ($perijinan->is_multi_opd && $user->role === 'operator_opd') {
                $opdSteps = $application->validasiRecords->filter(function($v) {
                    return $v->validationFlow && in_array($v->validationFlow->role, ['operator_opd', 'kepala_opd']);
                });
                $minOpdOrder = $opdSteps->min('order');
                if ($application->current_step >= $minOpdOrder) {
                    $isParallelAllowed = true;
                }
            }

            if (!$isParallelAllowed) {
                $cv = $application->validasiRecords->where('order', $application->current_step)->first();
                if (!$cv || !$cv->validationFlow || $cv->validationFlow->assigned_user_id !== $user->id) {
                    return redirect()->back()->with('error', 'Gagal menyimpan. Anda belum dapat mengisi data pada tahap ini (sedang tahapan ' . ($cv->validationFlow->role_label ?? 'Lainnya') . ').');
                }
            }
        }
        
        $opdId = null;
        if ($perijinan->is_multi_opd) {
            $opdId = $user->role === 'operator_opd' ? $user->opd_id : ($request->opd_id ?? 0);
        }

        $rekomFieldsQuery = $perijinan->activeFormFields()->where('form_type', 'rekom');
        
        // Filter fields by OPD if operator or if multi OPD request
        if ($perijinan->is_multi_opd && $opdId) {
            $rekomFieldsQuery->where(function($q) use ($opdId) {
                $q->where('opd_id', $opdId)->orWhereNull('opd_id');
            });
        } elseif ($user->role === 'operator_opd' && $user->opd_id) {
            $rekomFieldsQuery->where(function($q) use ($user) {
                $q->where('opd_id', $user->opd_id)->orWhereNull('opd_id');
            });
        }
        $rekomFields = $rekomFieldsQuery->get();
        $existingRekomData = $perijinan->is_multi_opd 
            ? ($application->rekom_data_multi[$opdId] ?? []) 
            : ($application->rekom_data ?? []);
            
        $rules = [];
        $messages = [];
        foreach ($rekomFields as $field) {
            $isFile = in_array($field->type, ['file', 'pas_foto', 'gambar']);
            $hasExistingFile = $isFile && !empty($existingRekomData[$field->name]);
            if ($isFile && !$hasExistingFile) {
                // Cek referensi BO atau global
                $refFile = null;
                if ($perijinan->has_bo_form) {
                    $matchingBoField = $perijinan->activeFormFields
                        ->where('form_type', 'bo')
                        ->where('name', $field->name)
                        ->first() ?? $perijinan->activeFormFields
                        ->where('form_type', 'bo')
                        ->filter(fn($f) => strtolower($f->label) === strtolower($field->label))
                        ->first();
                    if ($matchingBoField) {
                        $refFile = $application->bo_data[$matchingBoField->name] ?? null;
                    }
                }
                if (empty($refFile)) {
                    $matchingGlobalField = $perijinan->activeFormFields
                        ->where('form_type', 'global')
                        ->where('name', $field->name)
                        ->first() ?? $perijinan->activeFormFields
                        ->where('form_type', 'global')
                        ->filter(fn($f) => strtolower($f->label) === strtolower($field->label))
                        ->first();
                    if ($matchingGlobalField) {
                        $globalFiles = $application->form_files[$matchingGlobalField->id] ?? [];
                        $refFile = is_array($globalFiles) ? ($globalFiles[0] ?? null) : $globalFiles;
                    }
                }
                if (!empty($refFile)) {
                    $hasExistingFile = true;
                }
            }
            
            if ($field->is_required && !$hasExistingFile) {
                $fieldRules = ['required'];
                $messages[$field->name . '.required'] = "Formulir {$field->label} wajib diisi.";
            } else {
                $fieldRules = ['nullable'];
            }
            
            if ($field->type === 'email') $fieldRules[] = 'email';
            if ($field->type === 'number') $fieldRules[] = 'numeric';
            if ($field->type === 'file') {
                $fieldRules[] = 'file';
                $fieldRules[] = 'max:5120';
            }
            if ($field->type === 'pas_foto' || $field->type === 'gambar') {
                $fieldRules[] = 'file';
                $fieldRules[] = 'mimes:jpeg,jpg,png';
                $fieldRules[] = 'max:5120';
            }
            if ($field->type === 'table') {
                // Table fields are submitted as arrays; skip standard validation
                continue;
            }
            $rules[$field->name] = $fieldRules;
        }
        
        $rules['masa_aktif_rekom'] = 'required|date';
        $messages['masa_aktif_rekom.required'] = 'Masa aktif rekomendasi wajib diisi.';
        $messages['masa_aktif_rekom.date'] = 'Masa aktif rekomendasi harus berupa tanggal yang valid.';

        $validated = $request->validate($rules, $messages);
        
        if ($perijinan->is_multi_opd) {

            $multiData = $application->rekom_data_multi ?? [];
            $currentData = $multiData[$opdId] ?? [];

            foreach ($rekomFields as $field) {
                $isFile = in_array($field->type, ['file', 'pas_foto', 'gambar']);
                if ($isFile) {
                    if ($request->hasFile($field->name)) {
                        $file = $request->file($field->name);
                        $filename = 'rekom_' . $opdId . '_' . $field->name . '_' . time() . '.' . $file->getClientOriginalExtension();
                        $path = 'uploads/perijinan/' . $application->perijinan_id;
                        $file->move(public_path($path), $filename);
                        $currentData[$field->name] = $path . '/' . $filename;
                    } elseif (empty($currentData[$field->name])) {
                        // Auto-fill dari BO atau global
                        $refFile = null;
                        if ($perijinan->has_bo_form) {
                            $matchingBoField = $perijinan->activeFormFields
                                ->where('form_type', 'bo')
                                ->where('name', $field->name)
                                ->first() ?? $perijinan->activeFormFields
                                ->where('form_type', 'bo')
                                ->filter(fn($f) => strtolower($f->label) === strtolower($field->label))
                                ->first();
                            if ($matchingBoField) {
                                $refFile = $application->bo_data[$matchingBoField->name] ?? null;
                            }
                        }
                        if (empty($refFile)) {
                            $matchingGlobalField = $perijinan->activeFormFields
                                ->where('form_type', 'global')
                                ->where('name', $field->name)
                                ->first() ?? $perijinan->activeFormFields
                                ->where('form_type', 'global')
                                ->filter(fn($f) => strtolower($f->label) === strtolower($field->label))
                                ->first();
                            if ($matchingGlobalField) {
                                $globalFiles = $application->form_files[$matchingGlobalField->id] ?? [];
                                $refFile = is_array($globalFiles) ? ($globalFiles[0] ?? null) : $globalFiles;
                            }
                        }
                        if (!empty($refFile)) {
                            $currentData[$field->name] = $refFile;
                        }
                    }
                } elseif ($field->type === 'table') {
                    $tableVal = $request->input($field->name, []);
                    if (is_array($tableVal) && count($tableVal) > 0) {
                        $currentData[$field->name] = $tableVal;
                    }
                } elseif (!$isFile) {
                    if (array_key_exists($field->name, $validated)) {
                        $currentData[$field->name] = $validated[$field->name];
                    } else {
                        $isAlreadySaved = array_key_exists($field->name, $currentData);
                        if (!$isAlreadySaved) {
                            $val = null;
                            if ($perijinan->has_bo_form) {
                                $matchingBoField = $perijinan->activeFormFields
                                    ->where('form_type', 'bo')
                                    ->where('name', $field->name)
                                    ->first() ?? $perijinan->activeFormFields
                                    ->where('form_type', 'bo')
                                    ->filter(fn($f) => strtolower($f->label) === strtolower($field->label))
                                    ->first();

                                if ($matchingBoField) {
                                    $val = $application->bo_data[$matchingBoField->name] ?? null;
                                }
                            } else {
                                $matchingGlobalField = $perijinan->activeFormFields
                                    ->where('form_type', 'global')
                                    ->where('name', $field->name)
                                    ->first() ?? $perijinan->activeFormFields
                                    ->where('form_type', 'global')
                                    ->filter(fn($f) => strtolower($f->label) === strtolower($field->label))
                                    ->first();

                                if ($matchingGlobalField) {
                                    if ($field->type === 'file' || $field->type === 'pas_foto' || $field->type === 'gambar') {
                                        $globalFiles = $application->form_files[$matchingGlobalField->id] ?? [];
                                        $val = is_array($globalFiles) ? ($globalFiles[0] ?? null) : $globalFiles;
                                    } else {
                                        $val = $application->form_data[$matchingGlobalField->id] ?? '';
                                        if (is_array($val)) $val = implode(', ', $val);
                                    }
                                }
                            }
                            if ($val !== null) {
                                $currentData[$field->name] = $val;
                            }
                        }
                    }
                }
            }
            
            if (array_key_exists('masa_aktif_rekom', $validated)) {
                $currentData['masa_aktif_rekom'] = $validated['masa_aktif_rekom'];
            }

            $multiData[$opdId] = $currentData;
            $application->rekom_data_multi = $multiData;
            
            // Accumulate SLA Duration correctly for the specific step assigned to this user
            if ($request->has('elapsed_seconds')) {
                $targetRecord = null;
                $targetRecord = $application->validasiRecords->first(function($vr) use ($user) {
                    return $vr->validationFlow && $vr->validationFlow->role === 'operator_opd' && $vr->validationFlow->assignedUser && $vr->validationFlow->assignedUser->opd_id === $user->opd_id && $vr->status === 'pending';
                });
                
                if (!$targetRecord) {
                    $targetRecord = $application->validasiRecords->where('order', $application->current_step)->where('status', 'pending')->first();
                }
                
                if ($targetRecord) {
                    $targetRecord->increment('duration_seconds', intval($request->elapsed_seconds));
                    $targetRecord->update(['sla_start_at' => now()]);
                }
            }

            $application->save();
        } else {
            // Standard Single OPD Logic
            $rekomData = $application->rekom_data ?? [];
            foreach ($rekomFields as $field) {
                $isFile = in_array($field->type, ['file', 'pas_foto', 'gambar']);
                if ($isFile) {
                    if ($request->hasFile($field->name)) {
                        $file = $request->file($field->name);
                        $filename = 'rekom_' . $field->name . '_' . time() . '.' . $file->getClientOriginalExtension();
                        $path = 'uploads/perijinan/' . $application->perijinan_id;
                        $file->move(public_path($path), $filename);
                        $rekomData[$field->name] = $path . '/' . $filename;
                    } elseif (empty($rekomData[$field->name])) {
                        // Auto-fill dari BO atau global
                        $refFile = null;
                        if ($perijinan->has_bo_form) {
                            $matchingBoField = $perijinan->activeFormFields
                                ->where('form_type', 'bo')
                                ->where('name', $field->name)
                                ->first() ?? $perijinan->activeFormFields
                                ->where('form_type', 'bo')
                                ->filter(fn($f) => strtolower($f->label) === strtolower($field->label))
                                ->first();
                            if ($matchingBoField) {
                                $refFile = $application->bo_data[$matchingBoField->name] ?? null;
                            }
                        }
                        if (empty($refFile)) {
                            $matchingGlobalField = $perijinan->activeFormFields
                                ->where('form_type', 'global')
                                ->where('name', $field->name)
                                ->first() ?? $perijinan->activeFormFields
                                ->where('form_type', 'global')
                                ->filter(fn($f) => strtolower($f->label) === strtolower($field->label))
                                ->first();
                            if ($matchingGlobalField) {
                                $globalFiles = $application->form_files[$matchingGlobalField->id] ?? [];
                                $refFile = is_array($globalFiles) ? ($globalFiles[0] ?? null) : $globalFiles;
                            }
                        }
                        if (!empty($refFile)) {
                            $rekomData[$field->name] = $refFile;
                        }
                    }
                } elseif ($field->type === 'table') {
                    $tableVal = $request->input($field->name, []);
                    if (is_array($tableVal) && count($tableVal) > 0) {
                        $rekomData[$field->name] = $tableVal;
                    }
                } elseif (!$isFile) {
                    if (array_key_exists($field->name, $validated)) {
                        $rekomData[$field->name] = $validated[$field->name];
                    } else {
                        $isAlreadySaved = array_key_exists($field->name, $rekomData);
                        if (!$isAlreadySaved) {
                            $val = null;
                            if ($perijinan->has_bo_form) {
                                $matchingBoField = $perijinan->activeFormFields
                                    ->where('form_type', 'bo')
                                    ->where('name', $field->name)
                                    ->first() ?? $perijinan->activeFormFields
                                    ->where('form_type', 'bo')
                                    ->filter(fn($f) => strtolower($f->label) === strtolower($field->label))
                                    ->first();

                                if ($matchingBoField) {
                                    $val = $application->bo_data[$matchingBoField->name] ?? null;
                                }
                            } else {
                                $matchingGlobalField = $perijinan->activeFormFields
                                    ->where('form_type', 'global')
                                    ->where('name', $field->name)
                                    ->first() ?? $perijinan->activeFormFields
                                    ->where('form_type', 'global')
                                    ->filter(fn($f) => strtolower($f->label) === strtolower($field->label))
                                    ->first();

                                if ($matchingGlobalField) {
                                    if ($field->type === 'file' || $field->type === 'pas_foto' || $field->type === 'gambar') {
                                        $globalFiles = $application->form_files[$matchingGlobalField->id] ?? [];
                                        $val = is_array($globalFiles) ? ($globalFiles[0] ?? null) : $globalFiles;
                                    } else {
                                        $val = $application->form_data[$matchingGlobalField->id] ?? '';
                                        if (is_array($val)) $val = implode(', ', $val);
                                    }
                                }
                            }
                            if ($val !== null) {
                                $rekomData[$field->name] = $val;
                            }
                        }
                    }
                }
            }
            
            if (array_key_exists('masa_aktif_rekom', $validated)) {
                $rekomData['masa_aktif_rekom'] = $validated['masa_aktif_rekom'];
            }
            
            $application->rekom_data = $rekomData;

            // Accumulate SLA Duration correctly for the specific step assigned to this user
            if ($request->has('elapsed_seconds')) {
                $targetRecord = $application->validasiRecords->where('validationFlow.assigned_user_id', $user->id)->where('status', 'pending')->first()
                                ?? $application->validasiRecords->where('order', $application->current_step)->first();
                if ($targetRecord) {
                    $targetRecord->increment('duration_seconds', intval($request->elapsed_seconds));
                    $targetRecord->update(['sla_start_at' => now()]);
                }
            }

            $application->save();
        }

        // Reload and generate documents
        $application->load(['perijinan.activeFormFields', 'user']);
        try {
            $targetOpdId = $perijinan->is_multi_opd ? ($user->opd_id ?? $request->opd_id) : null;
            $generatedDocs = \App\Services\DocumentGenerator::generateDocuments($application, $targetOpdId);
            
            if (!empty($generatedDocs)) {
                $updateData = [];
                if (isset($generatedDocs['file_rekom'])) $updateData['file_rekom'] = $generatedDocs['file_rekom'];
                if (isset($generatedDocs['file_rekom_multi'])) $updateData['file_rekom_multi'] = $generatedDocs['file_rekom_multi'];
                if (isset($generatedDocs['file_izin'])) $updateData['file_izin'] = $generatedDocs['file_izin'];
                
                if (!empty($updateData)) {
                    $application->update($updateData);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Failed to regenerate documents after saving rekom data: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Data rekomendasi berhasil disimpan.');
    }

    /**
     * Save permit form data filled by officials.
     */
    public function saveIzinData(Request $request, $id)
    {
        $user = auth()->user();
        if ($user->isAdmin()) {
            abort(403, 'Admin hanya memiliki akses memantau (read-only).');
        }
        if ($user->role !== 'verifikator') {
            abort(403, 'Hanya Verifikator yang dapat mengisi data izin.');
        }

        $application = DataPerijinan::with(['perijinan.activeFormFields', 'validasiRecords.validationFlow'])->findOrFail($id);
        
        // Strict check: current step must belong to this user if not admin
        if (!$user->isAdmin()) {
            $cv = $application->validasiRecords->where('order', $application->current_step)->first();
            if (!$cv || !$cv->validationFlow || $cv->validationFlow->assigned_user_id !== $user->id) {
                return redirect()->back()->with('error', 'Gagal menyimpan. Anda belum dapat mengisi data pada tahap ini (sedang tahapan ' . ($cv->validationFlow->role_label ?? 'Lainnya') . ').');
            }
        }
        
        $izinFields = $application->perijinan->activeFormFields
            ->where('form_type', 'izin');
            
        $rules = [];
        $messages = [];
        $existingIzinData = $application->izin_data ?? [];
        foreach ($izinFields as $field) {
            $isFile = in_array($field->type, ['file', 'pas_foto', 'gambar']);
            $hasExistingFile = $isFile && !empty($existingIzinData[$field->name]);
            if ($isFile && !$hasExistingFile) {
                // Cek referensi BO, rekom, atau global
                $refFile = null;
                if ($application->perijinan->validasi_tanpa_opd && $application->perijinan->has_bo_form) {
                    $matchingBoFileField = $application->perijinan->activeFormFields
                        ->where('form_type', 'bo')
                        ->where('name', $field->name)
                        ->first() ?? $application->perijinan->activeFormFields
                        ->where('form_type', 'bo')
                        ->filter(fn($f) => strtolower($f->label) === strtolower($field->label))
                        ->first();
                    if ($matchingBoFileField) {
                        $refFile = $application->bo_data[$matchingBoFileField->name] ?? null;
                    }
                } else {
                    $matchingRekomFileField = $application->perijinan->activeFormFields
                        ->where('form_type', 'rekom')
                        ->where('name', $field->name)
                        ->first() ?? $application->perijinan->activeFormFields
                        ->where('form_type', 'rekom')
                        ->filter(fn($f) => strtolower($f->label) === strtolower($field->label))
                        ->first();
                    if ($matchingRekomFileField) {
                        $refFile = $application->rekom_data[$matchingRekomFileField->name] ?? null;
                    }
                }
                if (empty($refFile)) {
                    $matchingGlobalFileField = $application->perijinan->activeFormFields
                        ->where('form_type', 'global')
                        ->where('name', $field->name)
                        ->first() ?? $application->perijinan->activeFormFields
                        ->where('form_type', 'global')
                        ->filter(fn($f) => strtolower($f->label) === strtolower($field->label))
                        ->first();
                    if ($matchingGlobalFileField) {
                        $globalFiles = $application->form_files[$matchingGlobalFileField->id] ?? [];
                        $refFile = is_array($globalFiles) ? ($globalFiles[0] ?? null) : $globalFiles;
                    }
                }
                if (!empty($refFile)) {
                    $hasExistingFile = true;
                }
            }
            
            if ($field->is_required && !$hasExistingFile) {
                $fieldRules = ['required'];
                $messages[$field->name . '.required'] = "Formulir {$field->label} wajib diisi.";
            } else {
                $fieldRules = ['nullable'];
            }
            
            if ($field->type === 'email') $fieldRules[] = 'email';
            if ($field->type === 'number') $fieldRules[] = 'numeric';
            if ($field->type === 'file') {
                $fieldRules[] = 'file';
                $fieldRules[] = 'max:5120';
            }
            if ($field->type === 'pas_foto' || $field->type === 'gambar') {
                $fieldRules[] = 'file';
                $fieldRules[] = 'mimes:jpeg,jpg,png';
                $fieldRules[] = 'max:5120';
            }
            if ($field->type === 'table') {
                continue;
            }
            
            $rules[$field->name] = $fieldRules;
        }

        $rules['masa_aktif'] = 'required|date';
        $messages['masa_aktif.required'] = 'Masa aktif izin wajib diisi.';
        $messages['masa_aktif.date'] = 'Masa aktif izin harus berupa tanggal yang valid.';

        $validated = $request->validate($rules, $messages);
        
        $izinData = $application->izin_data ?? [];

            foreach ($izinFields as $field) {
                $isFile = in_array($field->type, ['file', 'pas_foto', 'gambar']);
                if ($isFile) {
                    if ($request->hasFile($field->name)) {
                        $file = $request->file($field->name);
                        if ($file->isValid()) {
                            $filename = 'izin_' . $field->name . '_' . time() . '.' . $file->getClientOriginalExtension();
                            $path = 'uploads/perijinan/' . $application->perijinan_id;
                            $file->move(public_path($path), $filename);
                            $izinData[$field->name] = $path . '/' . $filename;
                        }
                    } elseif (empty($izinData[$field->name])) {
                        // Auto-fill berkas referensi
                        $refFile = null;
                        if ($application->perijinan->validasi_tanpa_opd && $application->perijinan->has_bo_form) {
                            $matchingBoFileField = $application->perijinan->activeFormFields
                                ->where('form_type', 'bo')
                                ->where('name', $field->name)
                                ->first() ?? $application->perijinan->activeFormFields
                                ->where('form_type', 'bo')
                                ->filter(fn($f) => strtolower($f->label) === strtolower($field->label))
                                ->first();
                            if ($matchingBoFileField) {
                                $refFile = $application->bo_data[$matchingBoFileField->name] ?? null;
                            }
                        } else {
                            $matchingRekomFileField = $application->perijinan->activeFormFields
                                ->where('form_type', 'rekom')
                                ->where('name', $field->name)
                                ->first() ?? $application->perijinan->activeFormFields
                                ->where('form_type', 'rekom')
                                ->filter(fn($f) => strtolower($f->label) === strtolower($field->label))
                                ->first();
                            if ($matchingRekomFileField) {
                                $refFile = $application->rekom_data[$matchingRekomFileField->name] ?? null;
                            }
                        }
                        if (empty($refFile)) {
                            $matchingGlobalFileField = $application->perijinan->activeFormFields
                                ->where('form_type', 'global')
                                ->where('name', $field->name)
                                ->first() ?? $application->perijinan->activeFormFields
                                ->where('form_type', 'global')
                                ->filter(fn($f) => strtolower($f->label) === strtolower($field->label))
                                ->first();
                            if ($matchingGlobalFileField) {
                                $globalFiles = $application->form_files[$matchingGlobalFileField->id] ?? [];
                                $refFile = is_array($globalFiles) ? ($globalFiles[0] ?? null) : $globalFiles;
                            }
                        }
                        if (!empty($refFile)) {
                            $izinData[$field->name] = $refFile;
                        }
                    }
                } elseif ($field->type === 'table') {
                    $tableVal = $request->input($field->name, []);
                    if (is_array($tableVal) && count($tableVal) > 0) {
                        $izinData[$field->name] = $tableVal;
                    }
                } elseif (!$isFile) {
                if (array_key_exists($field->name, $validated)) {
                    $izinData[$field->name] = $validated[$field->name];
                }
            }
        }

        $application = DataPerijinan::with(['perijinan.activeFormFields', 'validasiRecords.validationFlow'])->findOrFail($id);
        
        $application->forceFill([
            'izin_data' => $izinData,
            'masa_aktif' => array_key_exists('masa_aktif', $validated) ? $validated['masa_aktif'] : $application->masa_aktif,
        ])->save();

        // Accumulate SLA Duration correctly for the specific step assigned to this user
        if ($request->has('elapsed_seconds')) {
            $targetRecord = $application->validasiRecords->where('validationFlow.assigned_user_id', $user->id)->where('status', 'pending')->first()
                            ?? $application->validasiRecords->where('order', $application->current_step)->first();
            if ($targetRecord) {
                $targetRecord->increment('duration_seconds', intval($request->elapsed_seconds));
                $targetRecord->update(['sla_start_at' => now()]);
            }
        }

        // Reload to ensure everything is fresh for document generation
        $application->load(['perijinan.activeFormFields', 'user']);

        try {
            $generatedDocs = \App\Services\DocumentGenerator::generateDocuments($application);
            if (!empty($generatedDocs)) {
                $application->update($generatedDocs);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to regenerate documents after saving izin data: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Data izin berhasil disimpan.');
    }

    /**
     * Save Back Office (BO) special form data.
     */
    public function saveBoData(Request $request, $id)
    {
        $user = auth()->user();
        if ($user->isAdmin()) {
            abort(403, 'Admin hanya memiliki akses memantau (read-only).');
        }
        if ($user->role !== 'bo') {
            abort(403, 'Hanya Back Office (BO) yang dapat mengisi data BO.');
        }

        $application = DataPerijinan::with(['perijinan.activeFormFields', 'validasiRecords.validationFlow'])->findOrFail($id);
        
        // Strict check: current step must belong to this user if not admin
        if (!$user->isAdmin()) {
            $cv = $application->validasiRecords->where('order', $application->current_step)->first();
            if (!$cv || !$cv->validationFlow || $cv->validationFlow->assigned_user_id !== $user->id) {
                return redirect()->back()->with('error', 'Gagal menyimpan. Anda belum dapat mengisi data pada tahap ini (sedang tahapan ' . ($cv->validationFlow->role_label ?? 'Lainnya') . ').');
            }
        }
        
        $boFields = $application->perijinan->activeFormFields
            ->where('form_type', 'bo');
            
        $rules = [];
        $messages = [];
        $existingBoData = $application->bo_data ?? [];
        foreach ($boFields as $field) {
            $isFile = in_array($field->type, ['file', 'pas_foto', 'gambar']);
            $hasExistingFile = $isFile && !empty($existingBoData[$field->name]);
            if ($isFile && !$hasExistingFile) {
                // Cek apakah ada berkas global pemohon yang di-auto-fill
                $matchingGlobalField = $application->perijinan->activeFormFields
                    ->where('form_type', 'global')
                    ->where('name', $field->name)
                    ->first() ?? $application->perijinan->activeFormFields
                    ->where('form_type', 'global')
                    ->filter(fn($f) => strtolower($f->label) === strtolower($field->label))
                    ->first();
                if ($matchingGlobalField) {
                    $globalFiles = $application->form_files[$matchingGlobalField->id] ?? [];
                    $globalFile = is_array($globalFiles) ? ($globalFiles[0] ?? null) : $globalFiles;
                    if (!empty($globalFile)) {
                        $hasExistingFile = true;
                    }
                }
            }
            
            if ($field->is_required && !$hasExistingFile) {
                $fieldRules = ['required'];
                $messages[$field->name . '.required'] = "Formulir {$field->label} wajib diisi.";
            } else {
                $fieldRules = ['nullable'];
            }
            
            if ($field->type === 'email') $fieldRules[] = 'email';
            if ($field->type === 'number') $fieldRules[] = 'numeric';
            if ($field->type === 'file') {
                $fieldRules[] = 'file';
                $fieldRules[] = 'max:5120';
            }
            if ($field->type === 'pas_foto' || $field->type === 'gambar') {
                $fieldRules[] = 'file';
                $fieldRules[] = 'mimes:jpeg,jpg,png';
                $fieldRules[] = 'max:5120';
            }
            if ($field->type === 'table') {
                continue;
            }
            
            $rules[$field->name] = $fieldRules;
        }

        // Pembetulan: validate the uploaded izin DOCX template
        if ($application->is_pembetulan) {
            $hasPembetulanFile = !empty($application->file_izin_pembetulan) && file_exists(public_path($application->file_izin_pembetulan));
            $rules['file_izin_pembetulan'] = $hasPembetulanFile ? ['nullable', 'file', 'mimes:docx', 'max:10240'] : ['required', 'file', 'mimes:docx', 'max:10240'];
            $messages['file_izin_pembetulan.required'] = 'Harap unggah file template DOCX surat izin.';
            $messages['file_izin_pembetulan.mimes'] = 'File surat izin harus berformat DOCX.';
        }

        $validated = $request->validate($rules, $messages);
        
        $boData = $application->bo_data ?? [];

        foreach ($boFields as $field) {
            $isFile = in_array($field->type, ['file', 'pas_foto', 'gambar']);
            if ($isFile) {
                if ($request->hasFile($field->name)) {
                    $file = $request->file($field->name);
                    if ($file->isValid()) {
                        $filename = 'bo_' . $field->name . '_' . time() . '.' . $file->getClientOriginalExtension();
                        $path = 'uploads/perijinan/' . $application->perijinan_id;
                        $file->move(public_path($path), $filename);
                        $boData[$field->name] = $path . '/' . $filename;
                    }
                } elseif (empty($boData[$field->name])) {
                    // Simpan berkas referensi global jika user tidak upload file baru
                    $matchingGlobalField = $application->perijinan->activeFormFields
                        ->where('form_type', 'global')
                        ->where('name', $field->name)
                        ->first() ?? $application->perijinan->activeFormFields
                        ->where('form_type', 'global')
                        ->filter(fn($f) => strtolower($f->label) === strtolower($field->label))
                        ->first();
                    if ($matchingGlobalField) {
                        $globalFiles = $application->form_files[$matchingGlobalField->id] ?? [];
                        $globalFile = is_array($globalFiles) ? ($globalFiles[0] ?? null) : $globalFiles;
                        if (!empty($globalFile)) {
                            $boData[$field->name] = $globalFile;
                        }
                    }
                }
            } elseif ($field->type === 'table') {
                $tableVal = $request->input($field->name, []);
                if (is_array($tableVal) && count($tableVal) > 0) {
                    $boData[$field->name] = $tableVal;
                }
            } elseif (!$isFile) {
                if (array_key_exists($field->name, $validated)) {
                    $boData[$field->name] = $validated[$field->name];
                } else {
                    $isAlreadySaved = array_key_exists($field->name, $boData);
                    if (!$isAlreadySaved) {
                        $matchingGlobalField = $application->perijinan->activeFormFields
                            ->where('form_type', 'global')
                            ->where('name', $field->name)
                            ->first() ?? $application->perijinan->activeFormFields
                            ->where('form_type', 'global')
                            ->filter(fn($f) => strtolower($f->label) === strtolower($field->label))
                            ->first();

                        if ($matchingGlobalField) {
                            $val = $application->form_data[$matchingGlobalField->id] ?? '';
                            if (is_array($val)) $val = implode(', ', $val);
                            if ($val !== null) {
                                $boData[$field->name] = $val;
                            }
                        }
                    }
                }
            }
        }

        $application = DataPerijinan::with(['perijinan.activeFormFields', 'validasiRecords.validationFlow'])->findOrFail($id);
        
        $application->forceFill([
            'bo_data' => $boData,
        ])->save();

        // Accumulate SLA Duration correctly for the specific step assigned to this user
        if ($request->has('elapsed_seconds')) {
            $targetRecord = $application->validasiRecords->where('validationFlow.assigned_user_id', $user->id)->where('status', 'pending')->first()
                            ?? $application->validasiRecords->where('order', $application->current_step)->first();
            if ($targetRecord) {
                $targetRecord->increment('duration_seconds', intval($request->elapsed_seconds));
                $targetRecord->update(['sla_start_at' => now()]);
            }
        }

        // Handle pembetulan DOCX upload for izin
        if ($application->is_pembetulan && $request->hasFile('file_izin_pembetulan')) {
            $docxFile = $request->file('file_izin_pembetulan');
            if ($docxFile->isValid()) {
                // If there's an existing PDF, clean it up
                if ($application->file_izin_pembetulan && file_exists(public_path($application->file_izin_pembetulan))) {
                    @unlink(public_path($application->file_izin_pembetulan));
                    // Also clean up its matching template docx if any
                    $oldDocxTemplate = str_replace('.pdf', '_template.docx', $application->file_izin_pembetulan);
                    if (file_exists(public_path($oldDocxTemplate))) {
                        @unlink(public_path($oldDocxTemplate));
                    }
                }
                
                $time = time();
                $destPath = 'uploads/perijinan/' . $application->perijinan_id;
                
                // Save the uploaded DOCX template file on disk
                $docxFilename = 'izin_pembetulan_' . $application->id . '_' . $time . '_template.docx';
                $docxFile->move(public_path($destPath), $docxFilename);
                $docxAbsPath = public_path($destPath . '/' . $docxFilename);
                
                // Also reset TTE so Kadin must sign the new file
                $application->file_izin_tte = null;
                
                // Process dynamic variable placeholders in the uploaded DOCX template
                // and compile it to a PDF using LibreOffice!
                try {
                    $application->load(['perijinan', 'user']);
                    $generatedPdfPath = \App\Services\DocumentGenerator::processPembetulanDocx($docxAbsPath, $application);
                    
                    // Save the processed PDF path in database
                    $application->file_izin_pembetulan = $generatedPdfPath;
                    $application->save();
                } catch (\Exception $e) {
                    \Log::error('processPembetulanDocx failed after BO upload: ' . $e->getMessage(), [
                        'application_id' => $application->id,
                        'trace' => $e->getTraceAsString(),
                    ]);
                    // Clean up and redirect back with error
                    if (file_exists($docxAbsPath)) {
                        @unlink($docxAbsPath);
                    }
                    return redirect()->back()->with('error', 'Gagal memproses file template DOCX: ' . $e->getMessage());
                }
            }
        }


        // Reload to ensure everything is fresh for document generation
        // Skip auto-generation for pembetulan: BO provides the PDF manually
        if (!$application->is_pembetulan) {
            $application->load(['perijinan.activeFormFields', 'user']);
            try {
                $generatedDocs = \App\Services\DocumentGenerator::generateDocuments($application);
                if (!empty($generatedDocs)) {
                    $application->update($generatedDocs);
                }
            } catch (\Exception $e) {
                \Log::error('Failed to regenerate documents after saving bo data: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'Data BO Form berhasil disimpan.');
    }

    /**
     * Update status of an application.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:submitted,in_progress,perbaikan,approved,rejected',
            'catatan' => 'nullable|string',
        ]);

        $application = DataPerijinan::findOrFail($id);
        $oldStatus = $application->status;

        $targetStatus = $request->status;
        if ($request->status === 'approved' && $application->is_pembetulan) {
            $targetStatus = 'diperbaiki';
        }

        $updateData = [
            'status' => $targetStatus,
        ];

        if ($request->status === 'approved') {
            $updateData['approved_at'] = now();
            if ($application->is_pembetulan) {
                $application->hasilSkm()->delete();
            }
            
            // Determine Kode OPD from current validator
            $user = auth()->user();
            if ($user->role === 'operator_opd' || $user->role === 'kepala_opd') {
                if ($application->no_rekom_kode === null && $user->opd) {
                    $updateData['no_rekom_kode'] = $user->opd->kode_opd ?? 'OPD';
                }
            } else if ($user->role === 'verifikator' || $user->role === 'kadin' || $user->role === 'admin') {
                if ($application->no_izin_kode === null) {
                    $updateData['no_izin_kode'] = 'DPMPTSP';
                }
            }

            // Assign Rekom & Izin Numbers if not already assigned
            $perijinan = $application->perijinan;
            if ($application->no_rekom === null) {
                $updateData['no_rekom'] = $perijinan->next_nomor_rekom;
                $perijinan->increment('next_nomor_rekom');
            }
            if ($application->no_izin === null) {
                $updateData['no_izin'] = $perijinan->next_nomor_izin;
                $perijinan->increment('next_nomor_izin');
            }

            // Check if all validations are complete
            $allValidationsComplete = $application->validasiRecords->every(function ($validasi) {
                return $validasi->status === 'approved';
            });

            if ($allValidationsComplete) {
                $updateData['completed_at'] = now();
            }

            // Update application first so freshest data is used for documents
            $application->update($updateData);
            
            // Regenerate documents
            try {
                $generatedDocs = \App\Services\DocumentGenerator::generateDocuments($application->fresh());
                $application->update([
                    'file_rekom' => $generatedDocs['file_rekom'] ?? $application->file_rekom,
                    'file_izin' => $generatedDocs['file_izin'] ?? $application->file_izin,
                ]);
            } catch (\Exception $e) {
                \Log::error('Gagal meregenerasi dokumen saat update status ke approved: ' . $e->getMessage());
            }
        } elseif ($request->status === 'perbaikan') {
            $updateData['catatan_perbaikan'] = $request->catatan;
        } elseif ($request->status === 'rejected') {
            $updateData['catatan_reject'] = $request->catatan;
            $updateData['rejected_at'] = now();
        }

        // Only update if not already handled in 'approved' block to avoid redundant calls
        if ($request->status !== 'approved') {
            $application->update($updateData);
        }

        // Log activity
        ActivityLog::log(
            'Mengupdate status perijinan',
            $application,
            'updated',
            [
                'old_status' => $oldStatus,
                'new_status' => $request->status,
                'no_registrasi' => $application->no_registrasi,
            ],
            'data_perijinan'
        );

        return redirect()->back()->with('success', 'Status perijinan berhasil diperbarui.');
    }

    /**
     * Export dalam proses applications to Excel.
     */
    public function exportDalamProses(Request $request)
    {
        $user = auth()->user();
        $query = DataPerijinan::with(['user', 'perijinan']);

        // Apply same access filters as index
        if (!$user->isAdmin()) {
            $accessiblePerijinanIds = $user->getAccessiblePerijinanIds();
            if (!empty($accessiblePerijinanIds)) {
                $query->whereIn('perijinan_id', $accessiblePerijinanIds);
            } else {
                $query->where('id', 0);
            }
        }

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_registrasi', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('perijinan', function ($q) use ($search) {
                        $q->where('nama_perijinan', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by perijinan type
        if ($request->filled('perijinan_id')) {
            $query->where('perijinan_id', $request->perijinan_id);
        }

        // Filter by date range
        $dateFrom = $request->get('date_from', '');
        $dateTo = $request->get('date_to', '');
        
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $query->whereNotIn('status', ['approved', 'diperbaiki', 'diperpanjang', 'completed', 'rejected', 'perbaikan']);
        $applications = $query->orderBy('created_at', 'desc')->get();

        return $this->exportToExcel($applications, 'dalam_proses', $dateFrom, $dateTo);
    }

    /**
     * Export perlu perbaikan applications to Excel.
     */
    public function exportPerluPerbaikan(Request $request)
    {
        $user = auth()->user();
        $query = DataPerijinan::with(['user', 'perijinan']);

        if (!$user->isAdmin()) {
            $accessiblePerijinanIds = $user->getAccessiblePerijinanIds();
            if (!empty($accessiblePerijinanIds)) {
                $query->whereIn('perijinan_id', $accessiblePerijinanIds);
            } else {
                $query->where('id', 0);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_registrasi', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('perijinan', function ($q) use ($search) {
                        $q->where('nama_perijinan', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('perijinan_id')) {
            $query->where('perijinan_id', $request->perijinan_id);
        }

        $dateFrom = $request->get('date_from', '');
        $dateTo = $request->get('date_to', '');
        
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $query->where('status', 'perbaikan');
        $applications = $query->orderBy('updated_at', 'desc')->get();

        return $this->exportToExcel($applications, 'perlu_perbaikan', $dateFrom, $dateTo);
    }

    /**
     * Export SLA data to Excel.
     */
    public function exportSla(Request $request)
    {
        $user = auth()->user();
        $query = DataPerijinan::with(['user', 'perijinan', 'validasiRecords.validationFlow', 'validasiRecords.validator.opd']);

        if (!$user->isAdmin()) {
            $accessiblePerijinanIds = $user->getAccessiblePerijinanIds();
            if (!empty($accessiblePerijinanIds)) {
                $query->whereIn('perijinan_id', $accessiblePerijinanIds);
            } else {
                $query->where('id', 0);
            }
        }

        $dateFrom = $request->get('date_from', '');
        $dateTo = $request->get('date_to', '');
        
        if ($dateFrom) {
            $query->whereDate('approved_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('approved_at', '<=', $dateTo);
        }

        $query->whereIn('status', ['approved', 'diperbaiki']);
        $applications = $query->orderBy('approved_at', 'desc')->get();

        // XML-based Excel Export (matching existing pattern)
        $filename = 'report_sla_' . date('Y-m-d_His') . '.xls';

        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<?mso-application progid="Excel.Sheet"?>';
        echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" xmlns:html="http://www.w3.org/TR/REC-html40">';
        
        echo '<Styles>
            <Style ss:ID="header"><Font ss:Bold="1" ss:Color="#FFFFFF"/><Interior ss:Color="#2563EB" ss:Pattern="Solid"/><Borders><Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/></Borders></Style>
            <Style ss:ID="title"><Font ss:Size="14" ss:Bold="1"/><Alignment ss:Horizontal="Center"/></Style>
            <Style ss:ID="wrap"><Alignment ss:Vertical="Top" ss:WrapText="1"/></Style>
        </Styles>';

        echo '<Worksheet ss:Name="Laporan SLA">';
        echo '<Table>';
        echo '<Column ss:Width="100"/>'; // No Reg
        echo '<Column ss:Width="150"/>'; // Jenis
        echo '<Column ss:Width="120"/>'; // Pemohon
        echo '<Column ss:Width="300"/>'; // Detail SLA
        echo '<Column ss:Width="100"/>'; // Total SLA
        
        echo '<Row ss:Height="25"><Cell ss:MergeAcross="4" ss:StyleID="title"><Data ss:Type="String">LAPORAN KINERJA SLA VALIDASI (SELESAI)</Data></Cell></Row>';
        echo '<Row><Cell ss:MergeAcross="4" ss:StyleID="title"><Data ss:Type="String">Periode: ' . ($dateFrom ?: 'Semua') . ' s/d ' . ($dateTo ?: 'Sekarang') . '</Data></Cell></Row>';
        echo '<Row ss:Index="4">
            <Cell ss:StyleID="header"><Data ss:Type="String">No. Registrasi</Data></Cell>
            <Cell ss:StyleID="header"><Data ss:Type="String">Jenis Perijinan</Data></Cell>
            <Cell ss:StyleID="header"><Data ss:Type="String">Pemohon</Data></Cell>
            <Cell ss:StyleID="header"><Data ss:Type="String">Detail Tahapan (Petugas | Durasi)</Data></Cell>
            <Cell ss:StyleID="header"><Data ss:Type="String">Total Net SLA</Data></Cell>
        </Row>';

        foreach ($applications as $app) {
            $slaDetails = [];
            $totalSeconds = 0;
            foreach ($app->validasiRecords as $v) {
                $duration = $v->duration_seconds ?? 0;
                $totalSeconds += $duration;
                $opd = ($v->validator->opd ?? ($v->validationFlow->assignedUser->opd ?? null))->nama_opd ?? 'N/A';
                $user = $v->validator->name ?? ($v->validationFlow->assignedUser->name ?? '-');
                $role = $v->validationFlow->role_label ?? 'Tahapan';
                $slaDetails[] = "{$role} ({$opd}) | {$user} | " . formatDuration($duration);
            }

            echo '<Row ss:AutoHeight="1">
                <Cell><Data ss:Type="String">' . $app->no_registrasi . '</Data></Cell>
                <Cell><Data ss:Type="String">' . $app->perijinan->nama_perijinan . '</Data></Cell>
                <Cell><Data ss:Type="String">' . $app->user->name . '</Data></Cell>
                <Cell ss:StyleID="wrap"><Data ss:Type="String">' . implode("&#10;", $slaDetails) . '</Data></Cell>
                <Cell><Data ss:Type="String">' . formatDuration($totalSeconds) . '</Data></Cell>
            </Row>';
        }

        echo '</Table></Worksheet></Workbook>';
        exit;
    }

    /**
     * Export selesai applications to Excel.
     */
    public function exportSelesai(Request $request)
    {
        $user = auth()->user();
        $query = DataPerijinan::with(['user', 'perijinan']);

        if (!$user->isAdmin()) {
            $accessiblePerijinanIds = $user->getAccessiblePerijinanIds();
            if (!empty($accessiblePerijinanIds)) {
                $query->whereIn('perijinan_id', $accessiblePerijinanIds);
            } else {
                $query->where('id', 0);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_registrasi', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('perijinan', function ($q) use ($search) {
                        $q->where('nama_perijinan', 'like', "%{$search}%");
                    });
            });
        }

        $dateFrom = $request->get('date_from', '');
        $dateTo = $request->get('date_to', '');
        
        if ($dateFrom) {
            $query->whereDate('approved_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('approved_at', '<=', $dateTo);
        }

        $query->whereIn('status', ['approved', 'diperbaiki']);
        $applications = $query->orderBy('approved_at', 'desc')->get();

        return $this->exportToExcel($applications, 'selesai', $dateFrom, $dateTo);
    }

    /**
     * Export ditolak applications to Excel.
     */
    public function exportDitolak(Request $request)
    {
        $user = auth()->user();
        $query = DataPerijinan::with(['user', 'perijinan']);

        if (!$user->isAdmin()) {
            $accessiblePerijinanIds = $user->getAccessiblePerijinanIds();
            if (!empty($accessiblePerijinanIds)) {
                $query->whereIn('perijinan_id', $accessiblePerijinanIds);
            } else {
                $query->where('id', 0);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_registrasi', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('perijinan', function ($q) use ($search) {
                        $q->where('nama_perijinan', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('perijinan_id')) {
            $query->where('perijinan_id', $request->perijinan_id);
        }

        $dateFrom = $request->get('date_from', '');
        $dateTo = $request->get('date_to', '');
        
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $query->where('status', 'rejected');
        $applications = $query->orderBy('rejected_at', 'desc')->get();

        return $this->exportToExcel($applications, 'ditolak', $dateFrom, $dateTo);
    }

    /**
     * Generate Excel export file.
     */
    private function exportToExcel($applications, $statusLabel, $dateFrom, $dateTo)
    {
        // Generate filename with date range
        $filename = 'data_perijinan_' . $statusLabel;
        if ($dateFrom || $dateTo) {
            $filename .= '_';
            if ($dateFrom) {
                $filename .= $dateFrom;
            }
            $filename .= '_sd_';
            if ($dateTo) {
                $filename .= $dateTo;
            }
        }
        $filename .= '_' . date('Y-m-d_His') . '.xls';

        // Set headers for Excel
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<?mso-application progid="Excel.Sheet"?>';
        echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">';
        
        echo '<Styles>
            <Style ss:ID="Default" ss:Name="Normal">
                <Alignment ss:Vertical="Bottom"/>
                <Borders/>
                <Font ss:FontName="Calibri" ss:Size="11"/>
                <Interior/>
                <NumberFormat/>
                <Protection/>
            </Style>
            <Style ss:ID="header">
                <Font ss:FontName="Calibri" ss:Size="12" ss:Bold="1" ss:Color="#FFFFFF"/>
                <Interior ss:Color="#2563EB" ss:Pattern="Solid"/>
                <Borders>
                    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
                </Borders>
            </Style>
            <Style ss:ID="title">
                <Font ss:FontName="Calibri" ss:Size="14" ss:Bold="1" ss:Color="#1F4E79"/>
                <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
            </Style>
            <Style ss:ID="subtitle">
                <Font ss:FontName="Calibri" ss:Size="11" ss:Color="#595959"/>
                <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
            </Style>
            <Style ss:ID="date">
                <NumberFormat ss:Format="dd/mm/yyyy\ hh:mm"/>
            </Style>
            <Style ss:ID="wrap">
                <Alignment ss:Vertical="Center" ss:WrapText="1"/>
            </Style>
            <Style ss:ID="center">
                <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
            </Style>
        </Styles>';

        echo '<Worksheet ss:Name="Data Perijinan">';
        echo '<Table>';
        echo '<Column ss:Width="40"/>';
        echo '<Column ss:Width="120"/>';
        echo '<Column ss:Width="150"/>';
        echo '<Column ss:Width="180"/>';
        echo '<Column ss:Width="120"/>';
        echo '<Column ss:Width="120"/>';
        echo '<Column ss:Width="120"/>';
        
        // Title row
        $statusLabels = [
            'dalam_proses' => 'DALAM PROSES',
            'perlu_perbaikan' => 'PERLU PERBAIKAN PEMOHON',
            'selesai' => 'SELESAI',
            'ditolak' => 'DITOLAK'
        ];
        
        echo '<Row ss:Height="30">';
        echo '<Cell ss:MergeAcross="6" ss:StyleID="title"><Data ss:Type="String">DATA PERIJINAN - ' . strtoupper($statusLabels[$statusLabel]) . '</Data></Cell>';
        echo '</Row>';
        
        // Date range row
        echo '<Row ss:Height="20">';
        $dateRangeText = 'Periode: ';
        if ($dateFrom && $dateTo) {
            $dateRangeText .= date('d/m/Y', strtotime($dateFrom)) . ' s/d ' . date('d/m/Y', strtotime($dateTo));
        } elseif ($dateFrom) {
            $dateRangeText .= 'Dari tanggal ' . date('d/m/Y', strtotime($dateFrom)) . ' s/d sekarang';
        } elseif ($dateTo) {
            $dateRangeText .= 'Sampai tanggal ' . date('d/m/Y', strtotime($dateTo));
        } else {
            $dateRangeText .= 'Semua tanggal';
        }
        echo '<Cell ss:MergeAcross="6" ss:StyleID="subtitle"><Data ss:Type="String">' . $dateRangeText . '</Data></Cell>';
        echo '</Row>';
        
        // Empty row
        echo '<Row></Row>';
        
        // Header row
        echo '<Row ss:Height="25">';
        echo '<Cell ss:StyleID="header"><Data ss:Type="String">No</Data></Cell>';
        echo '<Cell ss:StyleID="header"><Data ss:Type="String">No. Registrasi</Data></Cell>';
        echo '<Cell ss:StyleID="header"><Data ss:Type="String">Pemohon</Data></Cell>';
        echo '<Cell ss:StyleID="header"><Data ss:Type="String">Jenis Perijinan</Data></Cell>';
        echo '<Cell ss:StyleID="header"><Data ss:Type="String">Tanggal Pengajuan</Data></Cell>';
        echo '<Cell ss:StyleID="header"><Data ss:Type="String">Status</Data></Cell>';
        echo '<Cell ss:StyleID="header"><Data ss:Type="String">Tanggal Approval/Rejection</Data></Cell>';
        echo '</Row>';

        // Data rows
        $no = 1;
        $statusLabelsMap = [
            'submitted' => 'Submitted',
            'in_progress' => 'Dalam Proses',
            'perbaikan' => 'Perlu Perbaikan Pemohon',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
        ];

        foreach ($applications as $app) {
            echo '<Row>';
            echo '<Cell ss:StyleID="center"><Data ss:Type="Number">' . $no++ . '</Data></Cell>';
            echo '<Cell ss:StyleID="wrap"><Data ss:Type="String">' . htmlspecialchars($app->no_registrasi) . '</Data></Cell>';
            echo '<Cell ss:StyleID="wrap"><Data ss:Type="String">' . htmlspecialchars($app->user->name ?? '-') . '</Data></Cell>';
            echo '<Cell ss:StyleID="wrap"><Data ss:Type="String">' . htmlspecialchars($app->perijinan->nama_perijinan ?? '-') . '</Data></Cell>';
            echo '<Cell ss:StyleID="date"><Data ss:Type="String">' . $app->created_at . '</Data></Cell>';
            echo '<Cell ss:StyleID="center"><Data ss:Type="String">' . htmlspecialchars($statusLabelsMap[$app->status] ?? $app->status) . '</Data></Cell>';
            
            $approvalDate = '';
            if (in_array($app->status, ['approved', 'diperbaiki']) && $app->approved_at) {
                $approvalDate = $app->approved_at;
            } elseif ($app->status === 'rejected' && $app->rejected_at) {
                $approvalDate = $app->rejected_at;
            }
            
            if ($approvalDate) {
                echo '<Cell ss:StyleID="date"><Data ss:Type="String">' . $approvalDate . '</Data></Cell>';
            } else {
                echo '<Cell ss:StyleID="wrap"><Data ss:Type="String">-</Data></Cell>';
            }
            echo '</Row>';
        }

        echo '</Table>';
        echo '</Worksheet>';
        echo '</Workbook>';
        
        exit;
    }

    /**
     * Download or preview uploaded file from application.
     */
    public function downloadFile(Request $request, $filepath)
    {
        $user = auth()->user();

        // Decode URL-encoded path naturally
        $filepath = urldecode($filepath);
        
        // Security: Prevent directory traversal attacks
        $filepath = str_replace('..', '', $filepath);
        
        // Extract perijinan_id from path (e.g., "1/filename.pdf" -> "1")
        $pathParts = explode('/', ltrim($filepath, '/'));
        $perijinanIdFromPath = $pathParts[0] ?? null;

        // Access Control Check
        if (!$user->isAdmin() && $perijinanIdFromPath) {
            $accessibleIds = $user->getAccessiblePerijinanIds();
            if (!empty($accessibleIds) && !in_array($perijinanIdFromPath, $accessibleIds)) {
                \Log::warning('Unauthorized file access attempt', [
                    'user_id' => $user->id,
                    'role' => $user->role,
                    'perijinan_id' => $perijinanIdFromPath,
                    'filepath' => $filepath
                ]);
                abort(403, 'Anda tidak memiliki akses ke berkas ini.');
            }
        }

        // Get the full path relative to public folder
        $relativePath = 'uploads/perijinan/' . ltrim($filepath, '/');
        $path = public_path($relativePath);
        
        // Fallback for hosting environments using unique public folder mappings
        if (!file_exists($path)) {
            $path = base_path('public/' . $relativePath);
        }

        // Debug logging
        \Log::info('File access attempt details:', [
            'filepath_input' => $filepath,
            'resolved_relative' => $relativePath,
            'computed_public_path' => $path,
            'file_exists_status' => file_exists($path) ? 'YES' : 'NO',
            'base_path' => base_path()
        ]);

        // Verify the file exists
        if (file_exists($path)) {
            // For images/PDFs in preview mode, return as inline stream
            if ($request->has('preview')) {
                return response()->file($path);
            }
            return response()->download($path);
        }

        \Log::error('File not found', [
            'filepath' => $filepath,
            'path' => $path
        ]);

        return redirect()->back()
            ->with('error', 'File tidak ditemukan di server.');
    }

    /**
     * Regenerate permit documents manually.
     */
    public function regenerateDocuments($id)
    {
        $user = auth()->user();
        if ($user->isAdmin()) {
            abort(403, 'Admin hanya memiliki akses memantau (read-only).');
        }

        $application = DataPerijinan::with(['user.provinsi', 'user.kabupaten', 'user.kecamatan', 'user.kelurahan', 'perijinan'])->findOrFail($id);

        try {
            $generatedDocs = \App\Services\DocumentGenerator::generateDocuments($application);
            if (!empty($generatedDocs)) {
                $application->update([
                    'file_pernyataan' => $generatedDocs['file_pernyataan'] ?? null,
                    'file_permohonan' => $generatedDocs['file_permohonan'] ?? null,
                    'file_keabsahan' => $generatedDocs['file_keabsahan'] ?? null,
                    'file_rekom' => $generatedDocs['file_rekom'] ?? null,
                    'file_rekom_multi' => $generatedDocs['file_rekom_multi'] ?? $application->file_rekom_multi,
                    'file_izin' => $generatedDocs['file_izin'] ?? null,
                ]);
            }
            // Log activity
            ActivityLog::log(
                'Mengenerasi ulang dokumen perijinan',
                $application,
                'updated',
                [
                    'no_registrasi' => $application->no_registrasi,
                ],
                'data_perijinan'
            );

            return redirect()->back()
                ->with('success', 'Dokumen perijinan berhasil digenerasi ulang.');
        } catch (\Exception $e) {
            \Log::error('Error regenerating documents manually: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal mengenerasi dokumen perijinan: ' . $e->getMessage());
        }
    }

    /**
     * Refresh/regenerate the PDF of a correction permit from its uploaded DOCX template.
     */
    public function refreshPembetulanPdf($id)
    {
        $user = auth()->user();
        if ($user->isAdmin()) {
            abort(403, 'Admin hanya memiliki akses memantau (read-only).');
        }
        
        if (!in_array($user->role, ['verifikator', 'bo'])) {
            abort(403, 'Hanya Verifikator atau BO yang dapat memperbarui dokumen pembetulan.');
        }

        $application = DataPerijinan::with(['user.provinsi', 'user.kabupaten', 'user.kecamatan', 'user.kelurahan', 'perijinan'])->findOrFail($id);

        if (!$application->is_pembetulan) {
            return redirect()->back()->with('error', 'Aplikasi ini bukan pengajuan pembetulan izin.');
        }

        if (empty($application->file_izin_pembetulan)) {
            return redirect()->back()->with('error', 'Belum ada berkas surat izin yang diunggah oleh BO.');
        }

        $docxTemplatePath = str_replace('.pdf', '_template.docx', $application->file_izin_pembetulan);
        $docxAbsPath = public_path($docxTemplatePath);

        if (!file_exists($docxAbsPath)) {
            return redirect()->back()->with('error', 'File template DOCX asli tidak ditemukan di server.');
        }

        try {
            // Process the template docx again using updated data and overwrite/update the PDF
            $generatedPdfPath = \App\Services\DocumentGenerator::processPembetulanDocx($docxAbsPath, $application);

            // Update path in database (should remain same but resets TTE so it has to be signed again)
            $application->update([
                'file_izin_pembetulan' => $generatedPdfPath,
                'file_izin_tte' => null, // reset TTE if any since contents changed
            ]);

            // Log activity
            ActivityLog::log(
                'Memperbarui/regenerasi PDF pembetulan dari template DOCX',
                $application,
                'updated',
                [
                    'no_registrasi' => $application->no_registrasi,
                ],
                'data_perijinan'
            );

            return redirect()->back()->with('success', 'PDF surat izin pembetulan berhasil diperbarui berdasarkan data perizinan terbaru.');

        } catch (\Exception $e) {
            \Log::error('refreshPembetulanPdf failed: ' . $e->getMessage(), [
                'application_id' => $application->id,
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('error', 'Gagal memperbarui PDF: ' . $e->getMessage());
        }
    }

    /**
     * Apply TTE (Digital Signature) to a document.
     */
    public function applyTte(Request $request, $id)
    {
        $request->validate([
            'passphrase' => 'required|string',
            'doc_type' => 'required|in:rekom,izin',
        ]);

        $user = auth()->user();
        if ($user->isAdmin()) {
            abort(403, 'Admin hanya memiliki akses memantau (read-only).');
        }
        $application = DataPerijinan::findOrFail($id);
        $isMultiOpd = $application->perijinan->is_multi_opd;

        try {
            $nik = $user->nip;
            if (!$nik) {
                throw new \Exception("NIK (NIP) Anda belum terdaftar di sistem. Silakan hubungi Admin.");
            }

            $ttelog = new \App\Models\EsignLog();
            $ttelog->user_id = $user->id;
            $ttelog->data_perijinan_id = $application->id;

            if ($request->doc_type === 'rekom') {
                if ($user->role !== 'kepala_opd') {
                    abort(403, 'Hanya Kepala OPD yang dapat menandatangani Rekomendasi.');
                }

                // Force regenerate document with official black QR code before TTE signing
                $filePath = null;
                try {
                    $generatedDocs = \App\Services\DocumentGenerator::generateDocuments($application, $isMultiOpd ? $user->opd_id : null, true);
                    $filePath = $isMultiOpd ? ($generatedDocs['file_rekom_multi'][$user->opd_id] ?? null) : ($generatedDocs['file_rekom'] ?? null);
                } catch (\Exception $e) {
                    \Log::error('Gagal meregenerasi rekom sebelum TTE: ' . $e->getMessage());
                }

                if (!$filePath || !file_exists(public_path($filePath))) {
                    throw new \Exception("Dokumen draft rekomendasi tidak ditemukan.");
                }

                // Process TTE
                $signedPdfData = \App\Services\EsignService::signPdf($nik, $request->passphrase, public_path($filePath));

                // Save to new signed file
                $pathInfo = pathinfo($filePath);
                $newFilename = $pathInfo['filename'] . '_signed_' . time() . '.' . $pathInfo['extension'];
                $newFilePath = $pathInfo['dirname'] . '/' . $newFilename;

                file_put_contents(public_path($newFilePath), $signedPdfData);

                // Update DB path in the new TTE column
                if ($isMultiOpd) {
                    $multiData = $application->file_rekom_multi_tte ?? [];
                    $multiData[$user->opd_id] = $newFilePath;
                    $application->file_rekom_multi_tte = $multiData;
                    $application->save();
                } else {
                    $application->file_rekom_tte = $newFilePath;
                    $application->save();
                }
                $ttelog->document_type = 'rekomendasi';

            } else if ($request->doc_type === 'izin') {
                if ($user->role !== 'kadin') {
                    abort(403, 'Hanya Kadin yang dapat menandatangani Surat Izin.');
                }

                $filePath = null;

                if ($application->is_pembetulan) {
                    // Pembetulan: use the template file to regenerate the PDF but with isDraft = false (black QR code)
                    $docxTemplatePath = str_replace('.pdf', '_template.docx', $application->file_izin_pembetulan);
                    $docxAbsPath = public_path($docxTemplatePath);
                    if (file_exists($docxAbsPath)) {
                        try {
                            $generatedPdfPath = \App\Services\DocumentGenerator::processPembetulanDocx($docxAbsPath, $application, false);
                            $application->file_izin_pembetulan = $generatedPdfPath;
                            $application->save();
                        } catch (\Exception $e) {
                            \Log::error('Gagal meregenerasi izin pembetulan sebelum TTE: ' . $e->getMessage());
                        }
                    }
                    
                    $filePath = $application->file_izin_pembetulan ?? null;
                    if (!$filePath || !file_exists(public_path($filePath))) {
                        throw new \Exception("File PDF surat izin yang diunggah BO tidak ditemukan. Minta BO untuk mengunggah ulang.");
                    }
                } else {
                    // Normal flow: force regenerate document with official black QR code before TTE signing
                    try {
                        $generatedDocs = \App\Services\DocumentGenerator::generateDocuments($application, null, true);
                        $filePath = $generatedDocs['file_izin'] ?? null;
                    } catch (\Exception $e) {
                        \Log::error('Gagal meregenerasi izin sebelum TTE: ' . $e->getMessage());
                    }

                    if (!$filePath || !file_exists(public_path($filePath))) {
                        throw new \Exception("Dokumen draft izin tidak ditemukan.");
                    }
                }

                // Process TTE
                $signedPdfData = \App\Services\EsignService::signPdf($nik, $request->passphrase, public_path($filePath));

                $pathInfo = pathinfo($filePath);
                $newFilename = $pathInfo['filename'] . '_signed_' . time() . '.' . $pathInfo['extension'];
                $newFilePath = $pathInfo['dirname'] . '/' . $newFilename;

                file_put_contents(public_path($newFilePath), $signedPdfData);
                $application->file_izin_tte = $newFilePath;
                $application->save();

                $ttelog->document_type = 'izin';
            }

            $ttelog->status = 'success';
            $ttelog->save();

            // Accumulate SLA Duration correctly for the specific step assigned to this user
            if ($request->has('elapsed_seconds')) {
                $targetRecord = null;
                if ($application->perijinan->is_multi_opd && in_array($user->role, ['operator_opd', 'kepala_opd'])) {
                    $targetRecord = $application->validasiRecords->first(function($vr) use ($user) {
                        return $vr->validationFlow && $vr->validationFlow->role === $user->role && $vr->validationFlow->assignedUser && $vr->validationFlow->assignedUser->opd_id === $user->opd_id && $vr->status === 'pending';
                    });
                }
                
                if (!$targetRecord) {
                    $targetRecord = $application->validasiRecords->where('order', $application->current_step)->where('status', 'pending')->first();
                }
                
                if ($targetRecord) {
                    $targetRecord->increment('duration_seconds', intval($request->elapsed_seconds));
                    $targetRecord->update(['sla_start_at' => now()]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Proses TTE Berhasil. Dokumen resmi telah diterbitkan.'
            ]);

        } catch (\Exception $e) {
            // Log failure
            $ttelogFail = new \App\Models\EsignLog();
            $ttelogFail->user_id = $user->id;
            $ttelogFail->data_perijinan_id = $application->id;
            $ttelogFail->document_type = $request->doc_type == 'rekom' ? 'rekomendasi' : 'izin';
            $ttelogFail->status = 'failed';
            $ttelogFail->error_message = substr($e->getMessage(), 0, 500);
            $ttelogFail->save();

            return response()->json([
                'error' => true,
                'message' => 'Gagal melakukan TTE: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Verify PDF endpoint
     */
    public function verifyPdf(Request $request, $id)
    {
        $request->validate([
            'doc_type' => 'required|in:rekom,izin',
            'opd_id' => 'nullable|integer'
        ]);

        try {
            $application = DataPerijinan::findOrFail($id);
            $filePath = null;

            if ($request->doc_type === 'rekom') {
                if ($application->perijinan->is_multi_opd && $request->opd_id) {
                    $filePath = $application->file_rekom_multi[$request->opd_id] ?? null;
                } else {
                    $filePath = $application->file_rekom;
                }
            } else {
                $filePath = $application->file_izin;
            }

            if (!$filePath || !file_exists(public_path($filePath))) {
                return response()->json([
                    'error' => true,
                    'message' => 'Dokumen fisik tidak ditemukan di server.'
                ]);
            }

            $result = \App\Services\EsignService::verifyPdf(public_path($filePath));
            
            return response()->json($result);

        } catch (\Exception $e) {
            \Log::error('PDF Verification failed: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Terjadi kesalahan sistem saat memverifikasi PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Deactivate permit.
     */
    public function deactivate($id)
    {
        $user = auth()->user();
        if (!$user->isAdmin()) {
            abort(403, 'Hanya Admin yang dapat menonaktifkan izin.');
        }

        $application = DataPerijinan::findOrFail($id);
        $application->update(['is_deactivated' => true]);

        ActivityLog::log(
            'Menonaktifkan izin permohonan ' . $application->no_registrasi,
            $application,
            'updated',
            [
                'no_registrasi' => $application->no_registrasi,
                'is_deactivated' => true,
            ],
            'data_perijinan'
        );

        return redirect()->back()->with('success', 'Izin permohonan berhasil dinonaktifkan.');
    }

    /**
     * Activate permit.
     */
    public function activate($id)
    {
        $user = auth()->user();
        if (!$user->isAdmin()) {
            abort(403, 'Hanya Admin yang dapat mengaktifkan kembali izin.');
        }

        $application = DataPerijinan::findOrFail($id);
        $application->update(['is_deactivated' => false]);

        ActivityLog::log(
            'Mengaktifkan kembali izin permohonan ' . $application->no_registrasi,
            $application,
            'updated',
            [
                'no_registrasi' => $application->no_registrasi,
                'is_deactivated' => false,
            ],
            'data_perijinan'
        );

        return redirect()->back()->with('success', 'Izin permohonan berhasil diaktifkan kembali.');
    }
}
