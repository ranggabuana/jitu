<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EsignLog;
use Illuminate\Http\Request;

class EsignLogController extends Controller
{
    public function index(Request $request)
    {
        $query = EsignLog::with(['user', 'dataPerijinan.perijinan'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhereHas('dataPerijinan', function($q) use ($search) {
                $q->where('no_registrasi', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(15)->withQueryString();

        return view('admin.settings.log-tte', compact('logs'));
    }

    public function export(Request $request)
    {
        $query = EsignLog::with(['user', 'dataPerijinan.perijinan'])->latest();

        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhereHas('dataPerijinan', function($q) use ($search) {
                $q->where('no_registrasi', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->get();

        $filename = 'log_tte_' . date('Y-m-d_His') . '.xls';

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
                <Interior ss:Color="#10B981" ss:Pattern="Solid"/>
                <Borders>
                    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
                </Borders>
            </Style>
            <Style ss:ID="title">
                <Font ss:FontName="Calibri" ss:Size="14" ss:Bold="1" ss:Color="#047857"/>
                <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
            </Style>
            <Style ss:ID="subtitle">
                <Font ss:FontName="Calibri" ss:Size="11" ss:Color="#595959"/>
                <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
            </Style>
            <Style ss:ID="date">
                <NumberFormat ss:Format="dd/mm/yyyy\ hh:mm:ss"/>
            </Style>
            <Style ss:ID="wrap">
                <Alignment ss:Vertical="Center" ss:WrapText="1"/>
            </Style>
            <Style ss:ID="center">
                <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
            </Style>
        </Styles>';

        echo '<Worksheet ss:Name="Log TTE">';
        echo '<Table>';
        echo '<Column ss:Width="40"/>';
        echo '<Column ss:Width="140"/>';
        echo '<Column ss:Width="150"/>';
        echo '<Column ss:Width="100"/>';
        echo '<Column ss:Width="150"/>';
        echo '<Column ss:Width="250"/>';
        echo '<Column ss:Width="100"/>';
        echo '<Column ss:Width="80"/>';
        echo '<Column ss:Width="250"/>';
        
        // Title row
        echo '<Row ss:Height="30">';
        echo '<Cell ss:MergeAcross="8" ss:StyleID="title"><Data ss:Type="String">LAPORAN LOG TANDA TANGAN ELEKTRONIK (TTE)</Data></Cell>';
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
        echo '<Cell ss:StyleID="header"><Data ss:Type="String">Waktu TTE</Data></Cell>';
        echo '<Cell ss:StyleID="header"><Data ss:Type="String">Nama User</Data></Cell>';
        echo '<Cell ss:StyleID="header"><Data ss:Type="String">Role User</Data></Cell>';
        echo '<Cell ss:StyleID="header"><Data ss:Type="String">No. Registrasi</Data></Cell>';
        echo '<Cell ss:StyleID="header"><Data ss:Type="String">Nama Perizinan</Data></Cell>';
        echo '<Cell ss:StyleID="header"><Data ss:Type="String">Jenis Dokumen</Data></Cell>';
        echo '<Cell ss:StyleID="header"><Data ss:Type="String">Status</Data></Cell>';
        echo '<Cell ss:StyleID="header"><Data ss:Type="String">Pesan / Error</Data></Cell>';
        echo '</Row>';

        // Data rows
        $no = 1;
        foreach ($logs as $log) {
            $formattedDate = $log->created_at->format('Y-m-d\TH:i:s\.000');
            $noRegistrasi = $log->dataPerijinan->no_registrasi ?? '-';
            $namaPerijinan = $log->dataPerijinan->perijinan->nama_perijinan ?? '-';
            $statusText = $log->status === 'success' ? 'Berhasil' : 'Gagal';
            $errorMessage = $log->error_message ?? ($log->status === 'success' ? 'TTE berhasil dilakukan.' : '-');
            
            echo '<Row>';
            echo '<Cell ss:StyleID="center"><Data ss:Type="Number">' . $no++ . '</Data></Cell>';
            echo '<Cell ss:StyleID="date"><Data ss:Type="DateTime">' . $formattedDate . '</Data></Cell>';
            echo '<Cell ss:StyleID="wrap"><Data ss:Type="String">' . htmlspecialchars($log->user->name ?? '-') . '</Data></Cell>';
            echo '<Cell ss:StyleID="center"><Data ss:Type="String">' . htmlspecialchars(ucfirst($log->user->role ?? '-')) . '</Data></Cell>';
            echo '<Cell ss:StyleID="wrap"><Data ss:Type="String">' . htmlspecialchars($noRegistrasi) . '</Data></Cell>';
            echo '<Cell ss:StyleID="wrap"><Data ss:Type="String">' . htmlspecialchars($namaPerijinan) . '</Data></Cell>';
            echo '<Cell ss:StyleID="center"><Data ss:Type="String">' . htmlspecialchars(ucfirst($log->document_type)) . '</Data></Cell>';
            echo '<Cell ss:StyleID="center"><Data ss:Type="String">' . htmlspecialchars($statusText) . '</Data></Cell>';
            echo '<Cell ss:StyleID="wrap"><Data ss:Type="String">' . htmlspecialchars($errorMessage) . '</Data></Cell>';
            echo '</Row>';
        }

        echo '</Table>';
        echo '</Worksheet>';
        echo '</Workbook>';
        exit;
    }
}
