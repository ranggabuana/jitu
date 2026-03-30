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

        $hasilSkm = $query->orderBy($sortBy, $sortOrder)->paginate($perPage);
        $questions = DataSkm::aktif()->get();
        
        $hasilSkm->appends([
            'search' => $search,
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder,
            'per_page' => $perPage,
            'question_filter' => $questionFilter,
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
     * Export results to CSV.
     */
    public function export()
    {
        $hasilSkm = HasilSkm::with(['dataSkm', 'user'])->get();
        
        $filename = 'hasil_skm_' . date('Y-m-d_His') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'Pertanyaan', 'Nama Responden', 'Email', 'NIP', 'Jawaban', 'Nilai', 'Saran', 'Tanggal']);
        
        foreach ($hasilSkm as $response) {
            fputcsv($output, [
                $response->id,
                $response->dataSkm->pertanyaan,
                $response->responden_nama,
                $response->responden_email,
                $response->nip,
                $response->jawaban_label,
                $response->score,
                $response->saran,
                $response->created_at->format('d/m/Y H:i'),
            ]);
        }
        
        fclose($output);
        exit;
    }
}
