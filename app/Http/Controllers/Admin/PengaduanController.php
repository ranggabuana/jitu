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

    /**
     * Export pengaduan to Excel.
     */
    public function export(Request $request)
    {
        $this->authorizePengaduanAccess();

        $query = Pengaduan::with('user');

        // Apply same filters as index
        $search = $request->get('search', '');
        $statusFilter = $request->get('status_filter', 'all');
        $dateFrom = $request->get('date_from', '');
        $dateTo = $request->get('date_to', '');

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

        // Apply date range filter
        if ($dateFrom) {
            $query->whereDate('tanggal_pengaduan', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('tanggal_pengaduan', '<=', $dateTo);
        }

        $pengaduan = $query->orderBy('tanggal_pengaduan', 'desc')->get();

        // Generate filename with date range
        $filename = 'data_pengaduan';
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
                <Interior ss:Color="#8B5CF6" ss:Pattern="Solid"/>
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

        echo '<Worksheet ss:Name="Data Pengaduan">';
        echo '<Table>';
        echo '<Column ss:Width="40"/>';
        echo '<Column ss:Width="120"/>';
        echo '<Column ss:Width="150"/>';
        echo '<Column ss:Width="120"/>';
        echo '<Column ss:Width="100"/>';
        echo '<Column ss:Width="120"/>';
        echo '<Column ss:Width="100"/>';
        echo '<Column ss:Width="200"/>';
        echo '<Column ss:Width="200"/>';
        
        // Title row
        echo '<Row ss:Height="30">';
        echo '<Cell ss:MergeAcross="8" ss:StyleID="title"><Data ss:Type="String">DATA PENGADUAN MASYARAKAT</Data></Cell>';
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
        echo '<Cell ss:MergeAcross="8" ss:StyleID="subtitle"><Data ss:Type="String">' . $dateRangeText . '</Data></Cell>';
        echo '</Row>';
        
        // Empty row
        echo '<Row></Row>';
        
        // Header row
        echo '<Row ss:Height="25">';
        echo '<Cell ss:StyleID="header"><Data ss:Type="String">No</Data></Cell>';
        echo '<Cell ss:StyleID="header"><Data ss:Type="String">No. Pengaduan</Data></Cell>';
        echo '<Cell ss:StyleID="header"><Data ss:Type="String">Nama Pelapor</Data></Cell>';
        echo '<Cell ss:StyleID="header"><Data ss:Type="String">Email</Data></Cell>';
        echo '<Cell ss:StyleID="header"><Data ss:Type="String">No. HP</Data></Cell>';
        echo '<Cell ss:StyleID="header"><Data ss:Type="String">Kategori</Data></Cell>';
        echo '<Cell ss:StyleID="header"><Data ss:Type="String">Status</Data></Cell>';
        echo '<Cell ss:StyleID="header"><Data ss:Type="String">Isi Pengaduan</Data></Cell>';
        echo '<Cell ss:StyleID="header"><Data ss:Type="String">Tanggal Pengaduan</Data></Cell>';
        echo '</Row>';

        // Data rows
        $no = 1;
        $statusLabelsMap = [
            'pending' => 'Pending',
            'proses' => 'Dalam Proses',
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak',
        ];

        foreach ($pengaduan as $item) {
            echo '<Row>';
            echo '<Cell ss:StyleID="center"><Data ss:Type="Number">' . $no++ . '</Data></Cell>';
            echo '<Cell ss:StyleID="wrap"><Data ss:Type="String">' . htmlspecialchars($item->no_pengaduan) . '</Data></Cell>';
            echo '<Cell ss:StyleID="wrap"><Data ss:Type="String">' . htmlspecialchars($item->nama) . '</Data></Cell>';
            echo '<Cell ss:StyleID="wrap"><Data ss:Type="String">' . htmlspecialchars($item->email ?? '-') . '</Data></Cell>';
            echo '<Cell ss:StyleID="wrap"><Data ss:Type="String">' . htmlspecialchars($item->no_hp ?? '-') . '</Data></Cell>';
            echo '<Cell ss:StyleID="center"><Data ss:Type="String">' . htmlspecialchars(ucfirst($item->kategori)) . '</Data></Cell>';
            echo '<Cell ss:StyleID="center"><Data ss:Type="String">' . htmlspecialchars($statusLabelsMap[$item->status] ?? $item->status) . '</Data></Cell>';
            echo '<Cell ss:StyleID="wrap"><Data ss:Type="String">' . htmlspecialchars($item->isi_pengaduan ?? '-') . '</Data></Cell>';
            echo '<Cell ss:StyleID="date"><Data ss:Type="String">' . $item->tanggal_pengaduan . '</Data></Cell>';
            echo '</Row>';
        }

        echo '</Table>';
        echo '</Worksheet>';
        echo '</Workbook>';
        
        exit;
    }
}
