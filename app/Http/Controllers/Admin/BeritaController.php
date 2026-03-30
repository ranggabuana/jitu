<?php

namespace App\Http\Controllers\Admin;

use App\Models\Berita;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class BeritaController extends Controller
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
        $statusFilter = $request->get('status_filter', 'all');

        // Validate per_page to prevent abuse
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;

        // Validate sort_by to prevent SQL injection
        $allowedSorts = ['judul', 'id', 'created_at', 'updated_at', 'status'];
        $sortBy = in_array($sortBy, $allowedSorts) ? $sortBy : 'id';

        // Validate sort_order
        $sortOrder = in_array(strtolower($sortOrder), ['asc', 'desc']) ? $sortOrder : 'desc';

        $query = Berita::query();

        // Apply search filter
        if ($search) {
            $query->where('judul', 'like', "%{$search}%")
                  ->orWhere('konten', 'like', "%{$search}%");
        }

        // Apply status filter
        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        // Apply sorting
        $beritas = $query->orderBy($sortBy, $sortOrder)->paginate($perPage);

        // Append query parameters to pagination links
        $beritas->appends([
            'search' => $search,
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder,
            'per_page' => $perPage,
            'status_filter' => $statusFilter,
        ]);

        return view('berita.index', compact('beritas', 'search', 'sortBy', 'sortOrder', 'perPage', 'statusFilter'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('berita.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:berita,slug',
            'konten' => 'required|string',
            'gambar' => 'nullable|image|max:2048',
            'status' => 'required|in:aktif,tidak_aktif',
        ]);

        $data = $request->except('gambar');
        $data['user_id'] = Auth::id();

        // Handle image upload
        if ($request->hasFile('gambar')) {
            $image = $request->file('gambar');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/berita'), $imageName);
            $data['gambar'] = 'uploads/berita/' . $imageName;
        }

        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['judul']);
        }

        // Ensure unique slug
        $originalSlug = $data['slug'];
        $count = 1;
        while (Berita::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $originalSlug . '-' . $count;
            $count++;
        }

        $berita = Berita::create($data);

        // Log activity
        ActivityLog::log(
            'Menambah berita baru',
            $berita,
            'created',
            ['data' => $data],
            'berita'
        );

        return redirect()->route('berita.index')
            ->with('success', 'Berita berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $berita = Berita::with('user')->findOrFail($id);
        return view('berita.show', compact('berita'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $berita = Berita::findOrFail($id);
        return view('berita.edit', compact('berita'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $berita = Berita::findOrFail($id);
        $oldData = $berita->toArray();

        $request->validate([
            'judul' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:berita,slug,' . $id,
            'konten' => 'required|string',
            'gambar' => 'nullable|image|max:2048',
            'status' => 'required|in:aktif,tidak_aktif',
        ]);

        $data = $request->except('gambar');

        // Handle image upload
        if ($request->hasFile('gambar')) {
            // Delete old image
            if ($berita->gambar && file_exists(public_path($berita->gambar))) {
                unlink(public_path($berita->gambar));
            }

            $image = $request->file('gambar');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/berita'), $imageName);
            $data['gambar'] = 'uploads/berita/' . $imageName;
        }

        $berita->update($data);

        // Log activity
        ActivityLog::log(
            'Mengupdate berita',
            $berita,
            'updated',
            [
                'old' => $oldData,
                'new' => $data
            ],
            'berita'
        );

        return redirect()->route('berita.index')
            ->with('success', 'Berita berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $berita = Berita::findOrFail($id);

        // Delete associated image
        if ($berita->gambar && file_exists(public_path($berita->gambar))) {
            unlink(public_path($berita->gambar));
        }

        // Log activity
        ActivityLog::log(
            'Menghapus berita',
            $berita,
            'deleted',
            ['data' => $berita->toArray()],
            'berita'
        );

        $berita->delete();

        return redirect()->route('berita.index')
            ->with('success', 'Berita berhasil dihapus.');
    }

    /**
     * Toggle slider status for the specified news.
     */
    public function toggleSlider(string $id)
    {
        $berita = Berita::findOrFail($id);
        $oldStatus = $berita->is_featured;
        $newStatus = !$oldStatus;
        
        $berita->update(['is_featured' => $newStatus]);

        // Log activity
        ActivityLog::log(
            'Mengubah status slider berita',
            $berita,
            'updated',
            [
                'old' => ['is_featured' => $oldStatus],
                'new' => ['is_featured' => $newStatus]
            ],
            'berita'
        );

        return redirect()->route('berita.index')
            ->with('success', 'Status slider berhasil diubah menjadi ' . ($newStatus ? 'Aktif' : 'Tidak Aktif'));
    }
}
