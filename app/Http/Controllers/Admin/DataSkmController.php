<?php

namespace App\Http\Controllers\Admin;

use App\Models\DataSkm;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DataSkmController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $sortBy = $request->get('sort_by', 'urutan');
        $sortOrder = $request->get('sort_order', 'asc');
        $perPage = $request->get('per_page', 10);
        $statusFilter = $request->get('status_filter', 'all');

        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;
        $allowedSorts = ['pertanyaan', 'tipe', 'status', 'urutan', 'id', 'created_at'];
        $sortBy = in_array($sortBy, $allowedSorts) ? $sortBy : 'urutan';
        $sortOrder = in_array(strtolower($sortOrder), ['asc', 'desc']) ? $sortOrder : 'asc';

        $query = DataSkm::query();

        if ($search) {
            $query->where('pertanyaan', 'like', "%{$search}%");
        }

        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        $dataSkm = $query->orderBy($sortBy, $sortOrder)->paginate($perPage);
        $dataSkm->appends([
            'search' => $search,
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder,
            'per_page' => $perPage,
            'status_filter' => $statusFilter,
        ]);

        return view('skm.data.index', compact('dataSkm', 'search', 'sortBy', 'sortOrder', 'perPage', 'statusFilter'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('skm.data.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pertanyaan' => 'required|string|max:500',
            'urutan' => 'required|integer|min:0',
            'status' => 'required|in:aktif,tidak_aktif',
        ]);

        $data = $request->except('bobot_max');
        $data['user_id'] = Auth::id();
        $data['bobot_max'] = 4; // Fixed bobot max

        $dataSkm = DataSkm::create($data);

        // Log activity
        ActivityLog::log(
            'Menambah pertanyaan SKM baru',
            $dataSkm,
            'created',
            ['data' => $data],
            'skm'
        );

        return redirect()->route('skm.data.index')
            ->with('success', 'Pertanyaan SKM berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $dataSkm = DataSkm::with(['user', 'hasilSkm' => function($query) {
            $query->with('user')->latest()->take(10);
        }])->findOrFail($id);

        // Calculate statistics
        $totalResponses = $dataSkm->hasilSkm()->count();
        $averageScore = $dataSkm->hasilSkm()->selectRaw('AVG(jawaban) as avg')->value('avg');
        
        $scoreDistribution = [];
        for ($i = 1; $i <= 4; $i++) {
            $scoreDistribution[$i] = $dataSkm->hasilSkm()->where('jawaban', $i)->count();
        }

        return view('skm.data.show', compact('dataSkm', 'totalResponses', 'averageScore', 'scoreDistribution'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $dataSkm = DataSkm::findOrFail($id);
        return view('skm.data.edit', compact('dataSkm'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $dataSkm = DataSkm::findOrFail($id);
        $oldData = $dataSkm->toArray();

        $request->validate([
            'pertanyaan' => 'required|string|max:500',
            'urutan' => 'required|integer|min:0',
            'status' => 'required|in:aktif,tidak_aktif',
        ]);

        $data = $request->except('bobot_max');
        $data['bobot_max'] = 4; // Fixed bobot max

        $dataSkm->update($data);

        // Log activity
        ActivityLog::log(
            'Mengupdate pertanyaan SKM',
            $dataSkm,
            'updated',
            [
                'old' => $oldData,
                'new' => $data
            ],
            'skm'
        );

        return redirect()->route('skm.data.index')
            ->with('success', 'Pertanyaan SKM berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $dataSkm = DataSkm::findOrFail($id);

        // Log activity
        ActivityLog::log(
            'Menghapus pertanyaan SKM',
            $dataSkm,
            'deleted',
            ['data' => $dataSkm->toArray()],
            'skm'
        );

        $dataSkm->delete();

        return redirect()->route('skm.data.index')
            ->with('success', 'Pertanyaan SKM berhasil dihapus.');
    }
}
