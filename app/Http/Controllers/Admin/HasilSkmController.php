<?php

namespace App\Http\Controllers\Admin;

use App\Models\HasilSkm;
use App\Models\DataSkm;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HasilSkmController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $perPage = $request->get('per_page', 10);
        $questionFilter = $request->get('question_filter', 'all');
        $dateFrom = $request->get('date_from', '');
        $dateTo = $request->get('date_to', '');

        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;
        $allowedSorts = ['responden_nama', 'jawaban', 'created_at'];
        $sortBy = in_array($sortBy, $allowedSorts) ? $sortBy : 'created_at';
        $sortOrder = in_array(strtolower($sortOrder), ['asc', 'desc']) ? $sortOrder : 'desc';

        $query = HasilSkm::with(['dataSkm', 'user']);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('responden_nama', 'like', "%{$search}%")
                  ->orWhere('responden_email', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        if ($questionFilter !== 'all') {
            $query->where('data_skm_id', $questionFilter);
        }

        // Apply date range filter
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $hasilSkm = $query->orderBy($sortBy, $sortOrder)->paginate($perPage);
        $questions = DataSkm::aktif()->get();

        $hasilSkm->appends([
            'search' => $search,
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder,
            'per_page' => $perPage,
            'question_filter' => $questionFilter,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
        ]);

        // Calculate overall statistics
        $totalResponses = HasilSkm::count();
        $overallAverage = HasilSkm::selectRaw('AVG(jawaban) as avg')->value('avg');
        $satisfactionPercentage = $overallAverage ? ($overallAverage / 4) * 100 : 0;

        return view('skm.hasil.index', compact(
            'hasilSkm',
            'search',
            'sortBy',
            'sortOrder',
            'perPage',
            'questionFilter',
            'questions',
            'totalResponses',
            'overallAverage',
            'satisfactionPercentage'
        ));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $hasilSkm = HasilSkm::with(['dataSkm', 'user'])->findOrFail($id);
        return view('skm.hasil.show', compact('hasilSkm'));
    }

    /**
     * Display statistics/summary page.
     */
    public function statistics()
    {
        $questions = DataSkm::aktif()->withCount('hasilSkm')->get();
        
        $overallStats = [];
        foreach ($questions as $question) {
            $totalResponses = $question->hasilSkm()->count();
            $averageScore = $question->hasilSkm()->selectRaw('AVG(jawaban) as avg')->value('avg') ?? 0;
            $satisfactionPercentage = $totalResponses > 0 ? ($averageScore / 4) * 100 : 0;
            
            $overallStats[] = [
                'question' => $question,
                'total_responses' => $totalResponses,
                'average_score' => round($averageScore, 2),
                'satisfaction_percentage' => round($satisfactionPercentage, 2),
            ];
        }

        // Overall summary
        $totalAllResponses = HasilSkm::count();
        $overallAverage = HasilSkm::selectRaw('AVG(jawaban) as avg')->value('avg') ?? 0;
        $overallSatisfaction = $totalAllResponses > 0 ? ($overallAverage / 4) * 100 : 0;

        // Monthly trend (last 6 months)
        $monthlyTrend = HasilSkm::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, AVG(jawaban) as avg_score, COUNT(*) as total')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(6)
            ->get()
            ->reverse()
            ->values();

        return view('skm.hasil.statistics', compact(
            'overallStats',
            'totalAllResponses',
            'overallAverage',
            'overallSatisfaction',
            'monthlyTrend'
        ));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $hasilSkm = HasilSkm::findOrFail($id);
        $hasilSkm->delete();

        return redirect()->route('skm.hasil.index')
            ->with('success', 'Jawaban SKM berhasil dihapus.');
    }

    /**
     * Export results to Excel.
     */
    public function export(Request $request)
    {
        $query = HasilSkm::with(['dataSkm', 'user']);

        // Apply same filters as index
        $search = $request->get('search', '');
        $questionFilter = $request->get('question_filter', 'all');
        $dateFrom = $request->get('date_from', '');
        $dateTo = $request->get('date_to', '');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('responden_nama', 'like', "%{$search}%")
                  ->orWhere('responden_email', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        if ($questionFilter !== 'all') {
            $query->where('data_skm_id', $questionFilter);
        }

        // Apply date range filter
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $hasilSkm = $query->orderBy('created_at', 'desc')->get();

        // Generate filename with date range
        $filename = 'hasil_skm';
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
                <Interior ss:Color="#4472C4" ss:Pattern="Solid"/>
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
            <Style ss:ID="center">
                <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
            </Style>
        </Styles>';

        echo '<Worksheet ss:Name="Hasil SKM">';
        echo '<Table>';
        
        // Title row
        echo '<Row ss:Height="30">';
        echo '<Cell ss:MergeAcross="8" ss:StyleID="title"><Data ss:Type="String">LAPORAN HASIL SKM</Data></Cell>';
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
        echo '<Cell ss:StyleID="header"><Data ss:Type="String">Pertanyaan</Data></Cell>';
        echo '<Cell ss:StyleID="header"><Data ss:Type="String">Nama Responden</Data></Cell>';
        echo '<Cell ss:StyleID="header"><Data ss:Type="String">Email</Data></Cell>';
        echo '<Cell ss:StyleID="header"><Data ss:Type="String">NIP</Data></Cell>';
        echo '<Cell ss:StyleID="header"><Data ss:Type="String">Jawaban</Data></Cell>';
        echo '<Cell ss:StyleID="header"><Data ss:Type="String">Nilai</Data></Cell>';
        echo '<Cell ss:StyleID="header"><Data ss:Type="String">Saran</Data></Cell>';
        echo '<Cell ss:StyleID="header"><Data ss:Type="String">Tanggal</Data></Cell>';
        echo '</Row>';

        // Data rows
        $no = 1;
        foreach ($hasilSkm as $response) {
            echo '<Row>';
            echo '<Cell ss:StyleID="center"><Data ss:Type="Number">' . $no++ . '</Data></Cell>';
            echo '<Cell><Data ss:Type="String">' . htmlspecialchars($response->dataSkm->pertanyaan) . '</Data></Cell>';
            echo '<Cell><Data ss:Type="String">' . htmlspecialchars($response->responden_nama ?? 'Anonim') . '</Data></Cell>';
            echo '<Cell><Data ss:Type="String">' . htmlspecialchars($response->responden_email ?? '-') . '</Data></Cell>';
            echo '<Cell><Data ss:Type="String">' . htmlspecialchars($response->nip ?? '-') . '</Data></Cell>';
            echo '<Cell ss:StyleID="center"><Data ss:Type="String">' . htmlspecialchars($response->jawaban_label) . '</Data></Cell>';
            echo '<Cell ss:StyleID="center"><Data ss:Type="Number">' . $response->score . '</Data></Cell>';
            echo '<Cell><Data ss:Type="String">' . htmlspecialchars($response->saran ?? '-') . '</Data></Cell>';
            echo '<Cell ss:StyleID="date"><Data ss:Type="String">' . $response->created_at . '</Data></Cell>';
            echo '</Row>';
        }

        echo '</Table>';
        echo '</Worksheet>';
        echo '</Workbook>';
        
        exit;
    }
}
