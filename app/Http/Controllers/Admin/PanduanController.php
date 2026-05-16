<?php

namespace App\Http\Controllers\Admin;

use App\Models\Panduan;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PanduanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $perPage = $request->get('per_page', 10);
        $statusFilter = $request->get('status_filter', 'all');

        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;

        $query = Panduan::query();

        if ($search) {
            $query->where('nama_panduan', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
        }

        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter === 'aktif');
        }

        $panduan = $query->orderBy('created_at', 'desc')->paginate($perPage);
        $panduan->appends([
            'search' => $search,
            'per_page' => $perPage,
            'status_filter' => $statusFilter,
        ]);

        return view('panduan.index', compact('panduan', 'search', 'perPage', 'statusFilter'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('panduan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_panduan' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:panduans,slug',
            'file' => 'required|file|mimes:pdf|max:5120',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:aktif,tidak_aktif',
        ]);

        $data = $request->except('file');
        $data['status'] = $request->status === 'aktif';

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $fileName = time() . '_' . Str::slug($request->nama_panduan) . '.' . $extension;
            
            // Ensure directory exists
            $uploadPath = public_path('uploads/panduan');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            
            $file->move($uploadPath, $fileName);
            $data['file'] = 'uploads/panduan/' . $fileName;
        }

        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($request->nama_panduan);
        }

        // Ensure unique slug
        $originalSlug = $data['slug'];
        $count = 1;
        while (Panduan::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $originalSlug . '-' . $count;
            $count++;
        }

        $panduan = Panduan::create($data);

        // Log activity
        ActivityLog::log(
            'Menambah panduan baru',
            $panduan,
            'created',
            ['data' => $data],
            'panduan'
        );

        return redirect()->route('panduan.index')
            ->with('success', 'Panduan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $panduan = Panduan::findOrFail($id);
        return view('panduan.show', compact('panduan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $panduan = Panduan::findOrFail($id);
        return view('panduan.edit', compact('panduan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $panduan = Panduan::findOrFail($id);
        $oldData = $panduan->toArray();

        $request->validate([
            'nama_panduan' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:panduans,slug,' . $id,
            'file' => 'nullable|file|mimes:pdf|max:5120',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:aktif,tidak_aktif',
        ]);

        $data = $request->except('file');
        $data['status'] = $request->status === 'aktif';

        // Handle file upload
        if ($request->hasFile('file')) {
            // Delete old file
            if ($panduan->file && file_exists(public_path($panduan->file))) {
                unlink(public_path($panduan->file));
            }

            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $fileName = time() . '_' . Str::slug($request->nama_panduan) . '.' . $extension;

            // Ensure directory exists
            $uploadPath = public_path('uploads/panduan');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $file->move($uploadPath, $fileName);
            $data['file'] = 'uploads/panduan/' . $fileName;
        }

        $panduan->update($data);

        // Log activity
        ActivityLog::log(
            'Mengupdate panduan',
            $panduan,
            'updated',
            [
                'old' => $oldData,
                'new' => $data
            ],
            'panduan'
        );

        return redirect()->route('panduan.index')
            ->with('success', 'Panduan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $panduan = Panduan::findOrFail($id);

        // Delete associated file
        if ($panduan->file && file_exists(public_path($panduan->file))) {
            unlink(public_path($panduan->file));
        }

        // Log activity
        ActivityLog::log(
            'Menghapus panduan',
            $panduan,
            'deleted',
            ['data' => $panduan->toArray()],
            'panduan'
        );

        $panduan->delete();

        return redirect()->route('panduan.index')
            ->with('success', 'Panduan berhasil dihapus.');
    }

    /**
     * Preview the panduan file.
     */
    public function preview(string $id)
    {
        $panduan = Panduan::findOrFail($id);

        if (!$panduan->file || !file_exists(public_path($panduan->file))) {
            return redirect()->back()->with('error', 'File tidak ditemukan.');
        }

        return response()->file(public_path($panduan->file), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $panduan->nama_panduan . '.pdf"'
        ]);
    }
}
