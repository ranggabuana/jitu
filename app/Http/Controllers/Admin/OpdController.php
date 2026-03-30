<?php

namespace App\Http\Controllers\Admin;

use App\Models\Opd;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OpdController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');
        $perPage = $request->get('per_page', 10);

        // Validate per_page to prevent abuse
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;

        // Validate sort_by to prevent SQL injection
        $allowedSorts = ['nama_opd', 'id', 'created_at', 'updated_at'];
        $sortBy = in_array($sortBy, $allowedSorts) ? $sortBy : 'id';

        // Validate sort_order
        $sortOrder = in_array(strtolower($sortOrder), ['asc', 'desc']) ? $sortOrder : 'desc';

        $query = Opd::query();

        // Apply search filter
        if ($search) {
            $query->where('nama_opd', 'like', "%{$search}%");
        }

        // Apply sorting
        $opds = $query->orderBy($sortBy, $sortOrder)->paginate($perPage);

        // Append query parameters to pagination links
        $opds->appends([
            'search' => $search,
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder,
            'per_page' => $perPage,
        ]);

        return view('opd.index', compact('opds', 'search', 'sortBy', 'sortOrder', 'perPage'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('opd.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_opd' => 'required|string|max:255',
        ]);

        $opd = Opd::create($request->all());

        // Log activity
        ActivityLog::log(
            'Menambah OPD baru',
            $opd,
            'created',
            ['data' => $request->all()],
            'opd'
        );

        return redirect()->route('opd.index')
            ->with('success', 'OPD berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $opd = Opd::findOrFail($id);
        return view('opd.edit', compact('opd'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama_opd' => 'required|string|max:255',
        ]);

        $opd = Opd::findOrFail($id);
        $oldData = $opd->toArray();
        
        $opd->update($request->all());

        // Log activity
        ActivityLog::log(
            'Mengupdate data OPD',
            $opd,
            'updated',
            [
                'old' => $oldData,
                'new' => $request->all()
            ],
            'opd'
        );

        return redirect()->route('opd.index')
            ->with('success', 'OPD berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $opd = Opd::findOrFail($id);

        // Log activity
        ActivityLog::log(
            'Menghapus OPD',
            $opd,
            'deleted',
            ['data' => $opd->toArray()],
            'opd'
        );

        $opd->delete();

        return redirect()->route('opd.index')
            ->with('success', 'OPD berhasil dihapus.');
    }
}
