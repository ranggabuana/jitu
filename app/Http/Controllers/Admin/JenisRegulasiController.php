<?php

namespace App\Http\Controllers\Admin;

use App\Models\JenisRegulasi;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class JenisRegulasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $statusFilter = $request->get('status_filter', 'all');

        $query = JenisRegulasi::query();

        if ($search) {
            $query->where('nama_jenis', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
        }

        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        $jenisRegulasi = $query->orderBy('created_at', 'desc')->paginate(10);
        $jenisRegulasi->appends([
            'search' => $search,
            'status_filter' => $statusFilter,
        ]);

        return view('jenis-regulasi.index', compact('jenisRegulasi', 'search', 'statusFilter'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('jenis-regulasi.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_jenis' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:jenis_regulasi,slug',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:aktif,tidak_aktif',
        ]);

        $data = $request->except('slug');

        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($request->nama_jenis);
        }

        // Ensure unique slug
        $originalSlug = $data['slug'];
        $count = 1;
        while (JenisRegulasi::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $originalSlug . '-' . $count;
            $count++;
        }

        $jenisRegulasi = JenisRegulasi::create($data);

        // Log activity
        ActivityLog::log(
            'Menambah jenis regulasi baru',
            $jenisRegulasi,
            'created',
            ['data' => $data],
            'jenis_regulasi'
        );

        return redirect()->route('jenis-regulasi.index')
            ->with('success', 'Jenis regulasi berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $jenisRegulasi = JenisRegulasi::findOrFail($id);
        return view('jenis-regulasi.edit', compact('jenisRegulasi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $jenisRegulasi = JenisRegulasi::findOrFail($id);
        $oldData = $jenisRegulasi->toArray();

        $request->validate([
            'nama_jenis' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:jenis_regulasi,slug,' . $id,
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:aktif,tidak_aktif',
        ]);

        $data = $request->except('slug');

        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($request->nama_jenis);
        }

        // Ensure unique slug
        $originalSlug = $data['slug'];
        $count = 1;
        while (JenisRegulasi::where('slug', $data['slug'])->where('id', '!=', $id)->exists()) {
            $data['slug'] = $originalSlug . '-' . $count;
            $count++;
        }

        $jenisRegulasi->update($data);

        // Log activity
        ActivityLog::log(
            'Mengupdate jenis regulasi',
            $jenisRegulasi,
            'updated',
            [
                'old' => $oldData,
                'new' => $data
            ],
            'jenis_regulasi'
        );

        return redirect()->route('jenis-regulasi.index')
            ->with('success', 'Jenis regulasi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $jenisRegulasi = JenisRegulasi::findOrFail($id);

        // Check if has regulasi
        if ($jenisRegulasi->regulasi()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Tidak dapat menghapus jenis regulasi yang masih memiliki data regulasi.');
        }

        // Log activity
        ActivityLog::log(
            'Menghapus jenis regulasi',
            $jenisRegulasi,
            'deleted',
            ['data' => $jenisRegulasi->toArray()],
            'jenis_regulasi'
        );

        $jenisRegulasi->delete();

        return redirect()->route('jenis-regulasi.index')
            ->with('success', 'Jenis regulasi berhasil dihapus.');
    }
}
