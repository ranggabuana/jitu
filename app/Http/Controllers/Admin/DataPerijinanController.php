<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DataPerijinan;
use App\Models\Perijinan;
use App\Models\User;
use App\Models\DataPerijinanValidasi;
use App\Models\PerijinanValidationFlow;
use App\Models\ActivityLog;
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
                $applications = DataPerijinan::where('id', 0)->paginate(15);
                
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

        // Get only in-progress applications (not approved/completed yet)
        $query->whereNotIn('status', ['approved', 'completed']);

        $applications = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Statistics - only count for accessible perijinan
        if ($user->isAdmin()) {
            $totalDalamProses = DataPerijinan::whereNotIn('status', ['approved', 'completed'])->count();
            $totalSubmitted = DataPerijinan::where('status', 'submitted')->count();
            $totalInProgress = DataPerijinan::where('status', 'in_progress')->count();
            $totalPerbaikan = DataPerijinan::where('status', 'perbaikan')->count();
        } else {
            $accessibleIds = $user->getAccessiblePerijinanIds();
            $totalDalamProses = DataPerijinan::whereIn('perijinan_id', $accessibleIds)
                ->whereNotIn('status', ['approved', 'completed'])->count();
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
                $applications = DataPerijinan::where('id', 0)->paginate(15);

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
        $query->where('status', 'approved');

        $applications = $query->orderBy('approved_at', 'desc')->paginate(15)->withQueryString();

        // Statistics - only count for accessible perijinan
        if ($user->isAdmin()) {
            $totalSelesai = DataPerijinan::where('status', 'approved')->count();
            $totalApproved = DataPerijinan::where('status', 'approved')->count();
        } else {
            $accessibleIds = $user->getAccessiblePerijinanIds();
            $totalSelesai = DataPerijinan::whereIn('perijinan_id', $accessibleIds)
                ->where('status', 'approved')->count();
            $totalApproved = DataPerijinan::whereIn('perijinan_id', $accessibleIds)
                ->where('status', 'approved')->count();
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
                $applications = DataPerijinan::where('id', 0)->paginate(15);

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

        $applications = $query->orderBy('rejected_at', 'desc')->paginate(15)->withQueryString();

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
                $applications = DataPerijinan::where('id', 0)->paginate(15);

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

        $applications = $query->orderBy('updated_at', 'desc')->paginate(15)->withQueryString();

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
            'action' => 'required|in:approved,rejected,revision',
            'catatan' => 'nullable|string|max:1000',
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

            \Log::info('Current validation found', [
                'validation_id' => $currentValidasi->id,
                'order' => $currentValidasi->order,
                'status' => $currentValidasi->status,
                'user_id' => $currentValidasi->user_id
            ]);

            $validationFlow = $currentValidasi->validationFlow;
            $userRole = $user->role;

            // Role yang tidak memerlukan assigned_user_id (semua user dengan role ini bisa validasi)
            $rolesWithoutAssignment = ['fo', 'bo', 'verifikator', 'kadin'];

            if (in_array($userRole, $rolesWithoutAssignment)) {
                // Cek apakah role user match dengan role di validation flow
                if ($userRole !== $validationFlow->role) {
                    return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk melakukan validasi pada tahap ini.');
                }
                
                // Cek apakah user sudah pernah validasi di tahap ini
                // Untuk role kolektif, kita track berdasarkan user_id yang validasi
                $existingValidasi = $application->validasiRecords()
                    ->where('order', $application->current_step)
                    ->whereNotNull('user_id')
                    ->where('user_id', $user->id)
                    ->where('status', '!=', 'pending')
                    ->first();
                
                if ($existingValidasi) {
                    return redirect()->back()->with('error', 'Anda telah memvalidasi pengajuan ini sebelumnya. Satu user hanya bisa validasi sekali per tahap.');
                }
                
                // Cek apakah tahap ini sudah ada yang validasi (satu tahap hanya butuh 1 validasi dari role yang sesuai)
                $tahapSudahDivalidasi = $application->validasiRecords()
                    ->where('order', $application->current_step)
                    ->where('status', '!=', 'pending')
                    ->exists();
                
                if ($tahapSudahDivalidasi) {
                    return redirect()->back()->with('error', 'Tahap validasi ini sudah diselesaikan oleh user lain.');
                }
            } else {
                // Role yang memerlukan assigned_user_id (Operator OPD, Kepala OPD, Admin)
                if ($currentValidasi->user_id !== $user->id) {
                    return redirect()->back()->with('error', 'Anda tidak ditugaskan untuk melakukan validasi pada tahap ini.');
                }
                
                // Cek apakah sudah validasi (untuk assigned user)
                if ($currentValidasi->status !== 'pending') {
                    return redirect()->back()->with('error', 'Anda telah memvalidasi pengajuan ini sebelumnya.');
                }
            }

            // Check if already validated by anyone
            if ($currentValidasi->status !== 'pending') {
                return redirect()->back()->with('error', 'Tahap validasi ini sudah diselesaikan.');
            }

            // Update validation status - simpan juga user_id untuk tracking
            $updateData = [
                'status' => $request->action,
                'catatan' => $request->catatan,
                'validated_at' => now(),
            ];
            
            // Untuk role kolektif (FO, BO, Verifikator, Kadin), simpan user_id validator
            if (in_array($userRole, $rolesWithoutAssignment)) {
                $updateData['user_id'] = $user->id;
            }
            
            $currentValidasi->update($updateData);

            // Handle based on action
            if ($request->action === 'approved') {
                // Check if this is the last validation step
                $totalSteps = $application->perijinan->activeValidationFlows()->count();
                
                if ($application->current_step >= $totalSteps) {
                    // All validations complete - approve application
                    $application->update([
                        'status' => 'approved',
                        'approved_at' => now(),
                        'completed_at' => now(),
                    ]);
                } else {
                    // Move to next step
                    $application->update([
                        'current_step' => $application->current_step + 1,
                        'status' => 'in_progress',
                    ]);

                    // Activate next validation step
                    $nextValidasi = $application->validasiRecords()
                        ->where('order', $application->current_step + 1)
                        ->first();
                    
                    if ($nextValidasi) {
                        $nextValidasi->update(['status' => 'pending']);
                    }
                }
            } elseif ($request->action === 'rejected') {
                // Reject application - stop all validations
                $application->update([
                    'status' => 'rejected',
                    'catatan_reject' => $request->catatan,
                    'rejected_at' => now(),
                ]);

                // Mark all remaining validations as rejected
                $application->validasiRecords()
                    ->where('status', 'pending')
                    ->update(['status' => 'rejected']);
            } elseif ($request->action === 'revision') {
                // Send back for revision
                $application->update([
                    'status' => 'perbaikan',
                    'catatan_perbaikan' => $request->catatan,
                ]);
            }

            // Log activity (sebelum commit untuk menghindari masalah session)
            $actionLabel = $request->action === 'approved' ? 'Menyetujui' : ($request->action === 'rejected' ? 'Menolak' : 'Meminta perbaikan');

            try {
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
                    auth()->id() // Explicitly pass user_id
                );
            } catch (\Exception $logException) {
                // Log error tapi jangan gagalkan validasi
                \Log::error('Failed to log activity: ' . $logException->getMessage());
            }

            DB::commit();
            
            \Log::info('Validation committed successfully', [
                'application_id' => $id,
                'user_id' => auth()->id(),
                'action' => $request->action,
                'session_status' => auth()->check() ? 'active' : 'lost'
            ]);

            $successMessage = $request->action === 'approved' ? 'Validasi berhasil disetujui.' : ($request->action === 'rejected' ? 'Pengajuan ditolak.' : 'Pengajuan dikembalikan untuk perbaikan.');

            // Redirect dengan session flash
            return redirect()->route('data-perijinan.show', $id)
                ->with('success', $successMessage);

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
            'perijinan',
            'perijinan.activeFormFields',
            'validasiRecords.validationFlow.assignedUser',
            'validasiRecords.validator'
        ])->findOrFail($id);

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

        return view('admin.data-perijinan.show', compact('application'));
    }

    /**
     * Update the status of an application.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:submitted,in_progress,perbaikan,approved,rejected',
            'catatan' => 'nullable|string',
        ]);

        $application = DataPerijinan::findOrFail($id);
        $oldStatus = $application->status;

        $updateData = [
            'status' => $request->status,
        ];

        if ($request->status === 'approved') {
            $updateData['approved_at'] = now();
            
            // Check if all validations are complete
            $allValidationsComplete = $application->validasiRecords->every(function ($validasi) {
                return $validasi->status === 'approved';
            });

            if ($allValidationsComplete) {
                $updateData['completed_at'] = now();
            }
        } elseif ($request->status === 'perbaikan') {
            $updateData['catatan_perbaikan'] = $request->catatan;
        } elseif ($request->status === 'rejected') {
            $updateData['catatan_reject'] = $request->catatan;
            $updateData['rejected_at'] = now();
        }

        $application->update($updateData);

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

        $query->whereNotIn('status', ['approved', 'completed']);
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

        $query->where('status', 'approved');
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
            'perlu_perbaikan' => 'PERLU PERBAIKAN',
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
            'perbaikan' => 'Perlu Perbaikan',
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
            if ($app->status === 'approved' && $app->approved_at) {
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
     * Download uploaded file from application.
     */
    public function downloadFile($filepath)
    {
        // Decode URL-encoded path
        $filepath = urldecode($filepath);
        
        // Security: Prevent directory traversal attacks
        $filepath = str_replace('..', '', $filepath);
        
        // Get the full path relative to public folder
        $relativePath = 'uploads/perijinan/' . $filepath;
        $path = public_path($relativePath);

        // Debug logging
        \Log::info('Download file attempt', [
            'filepath' => $filepath,
            'relativePath' => $relativePath,
            'fullPath' => $path,
            'fileExists' => file_exists($path),
            'realPath' => realpath($path)
        ]);

        // Verify the file exists and is within the expected directory
        $realPath = realpath($path);
        $publicPath = realpath(public_path('uploads/perijinan'));
        
        if ($realPath && $publicPath && strpos($realPath, $publicPath) === 0 && file_exists($path)) {
            return response()->download($path);
        }

        \Log::error('File not found or invalid path', [
            'filepath' => $filepath,
            'path' => $path,
            'realPath' => $realPath,
            'publicPath' => $publicPath
        ]);

        return redirect()->back()
            ->with('error', 'File tidak ditemukan.');
    }
}
