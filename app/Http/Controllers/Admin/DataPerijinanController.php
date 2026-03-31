<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DataPerijinan;
use App\Models\Perijinan;
use App\Models\User;
use App\Models\DataPerijinanValidasi;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataPerijinanController extends Controller
{
    /**
     * Display in-progress applications (dalam proses).
     */
    public function dalamProses(Request $request)
    {
        $query = DataPerijinan::with(['user', 'perijinan']);

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

        // Statistics
        $totalDalamProses = DataPerijinan::whereNotIn('status', ['approved', 'completed'])->count();
        $totalSubmitted = DataPerijinan::where('status', 'submitted')->count();
        $totalInProgress = DataPerijinan::where('status', 'in_progress')->count();
        $totalPerbaikan = DataPerijinan::where('status', 'perbaikan')->count();

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
        $query = DataPerijinan::with(['user', 'perijinan']);

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

        // Statistics
        $totalSelesai = DataPerijinan::where('status', 'approved')->count();
        $totalApproved = DataPerijinan::where('status', 'approved')->count();

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
     * Display detail of an application.
     */
    public function show($id)
    {
        $application = DataPerijinan::with([
            'user',
            'perijinan',
            'perijinan.activeFormFields',
            'validasiRecords.validationFlow',
            'validasiRecords.validator'
        ])->findOrFail($id);

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
}
