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
        $request->validate([
            'action' => 'required|in:approved,rejected,revision',
            'catatan' => 'nullable|string|max:1000',
        ]);

        $user = auth()->user();
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
            $userRole = $user->role;
            
            // Role yang tidak memerlukan assigned_user_id (semua user dengan role ini bisa validasi)
            $rolesWithoutAssignment = ['fo', 'bo', 'verifikator', 'kadin'];
            
            if (in_array($userRole, $rolesWithoutAssignment)) {
                // Cek apakah role user match dengan role di validation flow
                if ($userRole !== $validationFlow->role) {
                    return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk melakukan validasi pada tahap ini.');
                }
            } else {
                // Role yang memerlukan assigned_user_id (Operator OPD, Kepala OPD, Admin)
                if ($currentValidasi->user_id !== $user->id) {
                    return redirect()->back()->with('error', 'Anda tidak ditugaskan untuk melakukan validasi pada tahap ini.');
                }
            }

            // Check if already validated
            if ($currentValidasi->status !== 'pending') {
                return redirect()->back()->with('error', 'Tahap validasi ini sudah diselesaikan.');
            }

            // Update validation status
            $currentValidasi->update([
                'status' => $request->action,
                'catatan' => $request->catatan,
                'validated_at' => now(),
            ]);

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

            // Log activity
            $actionLabel = $request->action === 'approved' ? 'Menyetujui' : ($request->action === 'rejected' ? 'Menolak' : 'Meminta perbaikan');
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
                'data_perijinan'
            );

            DB::commit();

            $successMessage = $request->action === 'approved' ? 'Validasi berhasil disetujui.' : ($request->action === 'rejected' ? 'Pengajuan ditolak.' : 'Pengajuan dikembalikan untuk perbaikan.');
            return redirect()->route('data-perijinan.show', $id)->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error processing validation: ' . $e->getMessage());
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
            'validasiRecords.validationFlow',
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
