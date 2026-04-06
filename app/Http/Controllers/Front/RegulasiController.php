<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Regulasi;
use App\Models\JenisRegulasi;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class RegulasiController extends Controller
{
    /**
     * Display public regulasi list.
     */
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $perPage = $request->get('per_page', 9);
        $jenisFilter = $request->get('jenis_filter', 'all');

        $perPage = in_array($perPage, [9, 18, 27]) ? $perPage : 9;
        $allowedSorts = ['nama_regulasi', 'created_at', 'updated_at'];
        $sortBy = in_array($sortBy, $allowedSorts) ? $sortBy : 'created_at';
        $sortOrder = in_array(strtolower($sortOrder), ['asc', 'desc']) ? $sortOrder : 'desc';

        $query = Regulasi::where('status', 'aktif');

        // Apply search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_regulasi', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        // Apply jenis regulasi filter
        if ($jenisFilter !== 'all') {
            $query->where('jenis_regulasi_id', $jenisFilter);
        }

        $regulasi = $query->with(['user', 'jenisRegulasi'])
            ->orderBy($sortBy, $sortOrder)
            ->paginate($perPage);

        $regulasi->appends([
            'search' => $search,
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder,
            'per_page' => $perPage,
            'jenis_filter' => $jenisFilter,
        ]);

        // Get active jenis regulasi for dropdown
        $jenisList = JenisRegulasi::where('status', 'aktif')
            ->orderBy('nama_jenis')
            ->get();

        // Statistics
        $totalRegulasi = Regulasi::where('status', 'aktif')->count();
        $recentCount = Regulasi::where('status', 'aktif')
            ->where('created_at', '>=', now()->subMonth())
            ->count();

        return view('front.regulasi', compact(
            'regulasi',
            'search',
            'sortBy',
            'sortOrder',
            'perPage',
            'jenisFilter',
            'jenisList',
            'totalRegulasi',
            'recentCount'
        ));
    }

    /**
     * Download regulasi file.
     */
    public function download(string $id)
    {
        $regulasi = Regulasi::findOrFail($id);

        // Only allow download if status is aktif
        if ($regulasi->status !== 'aktif') {
            return redirect()->back()->with('error', 'Regulasi ini tidak tersedia untuk umum.');
        }

        if (!$regulasi->file_regulasi || !file_exists(public_path($regulasi->file_regulasi))) {
            return redirect()->back()->with('error', 'File tidak ditemukan.');
        }

        // Log activity
        ActivityLog::log(
            'Mengunduh regulasi (public)',
            $regulasi,
            'viewed',
            [
                'no_regulasi' => $regulasi->nama_regulasi,
                'file' => $regulasi->file_regulasi,
                'downloaded_by' => 'public',
            ],
            'regulasi'
        );

        return response()->download(
            public_path($regulasi->file_regulasi),
            $regulasi->nama_regulasi . '.' . pathinfo($regulasi->file_regulasi, PATHINFO_EXTENSION)
        );
    }
}
