<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Perijinan;
use App\Models\User;
use App\Models\Opd;
use App\Models\Berita;
use App\Models\DataSkm;
use App\Models\HasilSkm;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Statistics
        $totalPerijinan = Perijinan::count();
        $totalPemohon = User::where('role', 'pemohon')->count();
        $totalPemohonAktif = User::where('role', 'pemohon')->where('status', 'aktif')->count();
        $totalOpd = Opd::count();
        $totalBerita = Berita::count();
        $totalBeritaAktif = Berita::where('status', 'aktif')->count();
        $totalPengguna = User::where('role', '!=', 'pemohon')->count();
        
        // SKM Statistics
        $totalPertanyaanSkm = DataSkm::count();
        $totalPertanyaanAktif = DataSkm::where('status', 'aktif')->count();
        $totalResponsSkm = HasilSkm::count();
        $averageSkm = HasilSkm::selectRaw('AVG(jawaban) as avg')->value('avg') ?? 0;
        $satisfactionPercentage = $totalResponsSkm > 0 ? ($averageSkm / 4) * 100 : 0;

        // Users by role
        $usersByRole = User::select('role', DB::raw('count(*) as total'))
            ->where('role', '!=', 'pemohon')
            ->groupBy('role')
            ->get()
            ->pluck('total', 'role')
            ->toArray();

        // Recent activity logs
        $recentActivities = ActivityLog::with('user')
            ->latest()
            ->limit(10)
            ->get();

        // Recent perijinan
        $recentPerijinan = Perijinan::latest()->limit(5)->get();

        // Recent pemohon
        $recentPemohon = User::where('role', 'pemohon')
            ->latest()
            ->limit(5)
            ->get();

        // Monthly perijinan trend (last 6 months)
        $monthlyPerijinan = Perijinan::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total")
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(6)
            ->get()
            ->reverse()
            ->values();

        // Monthly SKM response trend (last 6 months)
        $monthlySkm = HasilSkm::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, AVG(jawaban) as avg_score, COUNT(*) as total")
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(6)
            ->get()
            ->reverse()
            ->values();

        return view('admin.dashboard', [
            'title' => 'Dashboard | Sistem Perijinan',
            'totalPerijinan' => $totalPerijinan,
            'totalPemohon' => $totalPemohon,
            'totalPemohonAktif' => $totalPemohonAktif,
            'totalOpd' => $totalOpd,
            'totalBerita' => $totalBerita,
            'totalBeritaAktif' => $totalBeritaAktif,
            'totalPengguna' => $totalPengguna,
            'totalPertanyaanSkm' => $totalPertanyaanSkm,
            'totalPertanyaanAktif' => $totalPertanyaanAktif,
            'totalResponsSkm' => $totalResponsSkm,
            'satisfactionPercentage' => round($satisfactionPercentage, 2),
            'usersByRole' => $usersByRole,
            'recentActivities' => $recentActivities,
            'recentPerijinan' => $recentPerijinan,
            'recentPemohon' => $recentPemohon,
            'monthlyPerijinan' => $monthlyPerijinan,
            'monthlySkm' => $monthlySkm,
        ]);
    }
}
