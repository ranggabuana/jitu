<?php

namespace App\Http\Controllers\Admin;

use App\Models\Pengaduan;
use App\Models\ActivityLog;
use App\Models\PengaduanHandler;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PengaduanController extends Controller
{
    /**
     * Check if user can access pengaduan.
     */
    private function authorizePengaduanAccess()
    {
        if (!PengaduanHandler::canAccessPengaduan()) {
            abort(403, 'Anda tidak memiliki akses ke menu pengaduan. Hubungi admin untuk penunjukan.');
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorizePengaduanAccess();

        $search = $request->get('search', '');
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');
        $perPage = $request->get('per_page', 10);
        $statusFilter = $request->get('status_filter', 'all');

        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;
        $allowedSorts = ['no_pengaduan', 'nama', 'kategori', 'status', 'tanggal_pengaduan'];
        $sortBy = in_array($sortBy, $allowedSorts) ? $sortBy : 'id';
        $sortOrder = in_array(strtolower($sortOrder), ['asc', 'desc']) ? $sortOrder : 'desc';

        $query = Pengaduan::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('no_pengaduan', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%");
            });
        }

        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        $pengaduan = $query->with('user')->orderBy($sortBy, $sortOrder)->paginate($perPage);
        $pengaduan->appends([
            'search' => $search,
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder,
            'per_page' => $perPage,
            'status_filter' => $statusFilter,
        ]);

        // Statistics
        $totalPengaduan = Pengaduan::count();
        $pendingCount = Pengaduan::pending()->count();
        $prosesCount = Pengaduan::proses()->count();
        $selesaiCount = Pengaduan::selesai()->count();
        $ditolakCount = Pengaduan::ditolak()->count();

        return view('pengaduan.index', compact(
            'pengaduan',
            'search',
            'sortBy',
            'sortOrder',
            'perPage',
            'statusFilter',
            'totalPengaduan',
            'pendingCount',
            'prosesCount',
            'selesaiCount',
            'ditolakCount'
        ));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $this->authorizePengaduanAccess();

        $pengaduan = Pengaduan::with('user')->findOrFail($id);
        
        // Log activity
        ActivityLog::log(
            'Melihat detail pengaduan',
            $pengaduan,
            'viewed',
            ['no_pengaduan' => $pengaduan->no_pengaduan],
            'pengaduan'
        );

        return view('pengaduan.show', compact('pengaduan'));
    }

    /**
     * Update the status and response of the specified resource.
     */
    public function updateStatus(Request $request, string $id)
    {
        $this->authorizePengaduanAccess();

        $pengaduan = Pengaduan::findOrFail($id);
        
        // Store old data for logging
        $oldData = $pengaduan->toArray();
        $oldStatus = $pengaduan->status;
        $oldRespon = $pengaduan->respon;

        $request->validate([
            'status' => 'required|in:pending,proses,selesai,ditolak',
            'respon' => 'nullable|string',
        ]);

        $data = [
            'status' => $request->status,
            'user_id' => Auth::id(),
        ];

        // Set tanggal_respon when status changes to selesai or ditolak
        if (in_array($request->status, ['selesai', 'ditolak']) && !$pengaduan->tanggal_respon) {
            $data['tanggal_respon'] = now();
        }

        // Update response if provided
        if ($request->filled('respon')) {
            $data['respon'] = $request->respon;
        }

        $pengaduan->update($data);

        // Log activity with detailed changes
        try {
            $logId = ActivityLog::log(
                'Mengupdate status pengaduan',
                $pengaduan,
                'updated',
                [
                    'no_pengaduan' => $pengaduan->no_pengaduan,
                    'nama' => $pengaduan->nama,
                    'old_status' => $oldStatus,
                    'new_status' => $request->status,
                    'old_respon' => $oldRespon,
                    'new_respon' => $request->respon,
                    'changes' => $data,
                    'updated_by' => Auth::user()->name,
                    'updated_by_id' => Auth::id(),
                ],
                'pengaduan'
            );

            // Debug logging to ensure it's working
            \Log::info('Pengaduan status updated', [
                'pengaduan_id' => $pengaduan->id,
                'no_pengaduan' => $pengaduan->no_pengaduan,
                'old_status' => $oldStatus,
                'new_status' => $request->status,
                'activity_log_id' => $logId->id,
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to log pengaduan update', [
                'error' => $e->getMessage(),
                'pengaduan_id' => $pengaduan->id,
            ]);
        }

        return redirect()->back()->with('success', 'Status pengaduan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->authorizePengaduanAccess();

        $pengaduan = Pengaduan::findOrFail($id);

        // Delete associated file
        if ($pengaduan->lampiran && file_exists(public_path($pengaduan->lampiran))) {
            unlink(public_path($pengaduan->lampiran));
        }

        // Log activity
        ActivityLog::log(
            'Menghapus pengaduan',
            $pengaduan,
            'deleted',
            [
                'no_pengaduan' => $pengaduan->no_pengaduan,
                'data' => $pengaduan->toArray()
            ],
            'pengaduan'
        );

        $pengaduan->delete();

        return redirect()->route('pengaduan.index')
            ->with('success', 'Pengaduan berhasil dihapus.');
    }

    /**
     * Download the complaint attachment.
     */
    public function download(string $id)
    {
        $this->authorizePengaduanAccess();

        $pengaduan = Pengaduan::findOrFail($id);

        if (!$pengaduan->lampiran || !file_exists(public_path($pengaduan->lampiran))) {
            return redirect()->back()->with('error', 'File tidak ditemukan.');
        }

        // Log activity
        ActivityLog::log(
            'Mengunduh lampiran pengaduan',
            $pengaduan,
            'viewed',
            [
                'no_pengaduan' => $pengaduan->no_pengaduan,
                'file' => $pengaduan->lampiran
            ],
            'pengaduan'
        );

        return response()->download(
            public_path($pengaduan->lampiran),
            'Lampiran_' . $pengaduan->no_pengaduan . '.' . pathinfo($pengaduan->lampiran, PATHINFO_EXTENSION)
        );
    }
}
