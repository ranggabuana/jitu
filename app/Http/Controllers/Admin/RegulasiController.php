<?php

namespace App\Http\Controllers\Admin;

use App\Models\Regulasi;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RegulasiController extends Controller
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

        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;
        $allowedSorts = ['nama_regulasi', 'status', 'created_at', 'updated_at'];
        $sortBy = in_array($sortBy, $allowedSorts) ? $sortBy : 'id';
        $sortOrder = in_array(strtolower($sortOrder), ['asc', 'desc']) ? $sortOrder : 'desc';

        $query = Regulasi::query();

        if ($search) {
            $query->where('nama_regulasi', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
        }

        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        $regulasi = $query->with('user')->orderBy($sortBy, $sortOrder)->paginate($perPage);
        $regulasi->appends([
            'search' => $search,
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder,
            'per_page' => $perPage,
            'status_filter' => $statusFilter,
        ]);

        return view('regulasi.index', compact('regulasi', 'search', 'sortBy', 'sortOrder', 'perPage', 'statusFilter'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('regulasi.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_regulasi' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:regulasi,slug',
            'file_regulasi' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar|max:10240',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:aktif,tidak_aktif',
        ]);

        $data = $request->except('file_regulasi');
        $data['user_id'] = Auth::id();

        // Handle file upload
        if ($request->hasFile('file_regulasi')) {
            $file = $request->file('file_regulasi');
            
            // Get file info before moving
            $fileSize = $file->getSize();
            $fileType = $file->getClientMimeType();
            $extension = $file->getClientOriginalExtension();
            
            $fileName = time() . '_' . Str::slug($request->nama_regulasi) . '.' . $extension;
            $file->move(public_path('uploads/regulasi'), $fileName);

            $data['file_regulasi'] = 'uploads/regulasi/' . $fileName;
            $data['file_type'] = $fileType;
            $data['file_size'] = $fileSize;
        }

        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($request->nama_regulasi);
        }

        // Ensure unique slug
        $originalSlug = $data['slug'];
        $count = 1;
        while (Regulasi::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $originalSlug . '-' . $count;
            $count++;
        }

        $regulasi = Regulasi::create($data);

        // Log activity
        ActivityLog::log(
            'Menambah regulasi baru',
            $regulasi,
            'created',
            ['data' => $data],
            'regulasi'
        );

        return redirect()->route('regulasi.index')
            ->with('success', 'Regulasi berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $regulasi = Regulasi::with('user')->findOrFail($id);
        return view('regulasi.show', compact('regulasi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $regulasi = Regulasi::findOrFail($id);
        return view('regulasi.edit', compact('regulasi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $regulasi = Regulasi::findOrFail($id);
        $oldData = $regulasi->toArray();

        $request->validate([
            'nama_regulasi' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:regulasi,slug,' . $id,
            'file_regulasi' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar|max:10240',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:aktif,tidak_aktif',
        ]);

        $data = $request->except('file_regulasi');

        // Handle file upload
        if ($request->hasFile('file_regulasi')) {
            // Delete old file
            if ($regulasi->file_regulasi && file_exists(public_path($regulasi->file_regulasi))) {
                unlink(public_path($regulasi->file_regulasi));
            }

            $file = $request->file('file_regulasi');
            
            // Get file info before moving
            $fileSize = $file->getSize();
            $fileType = $file->getClientMimeType();
            $extension = $file->getClientOriginalExtension();
            
            $fileName = time() . '_' . Str::slug($request->nama_regulasi) . '.' . $extension;
            $file->move(public_path('uploads/regulasi'), $fileName);

            $data['file_regulasi'] = 'uploads/regulasi/' . $fileName;
            $data['file_type'] = $fileType;
            $data['file_size'] = $fileSize;
        }

        $regulasi->update($data);

        // Log activity
        ActivityLog::log(
            'Mengupdate regulasi',
            $regulasi,
            'updated',
            [
                'old' => $oldData,
                'new' => $data
            ],
            'regulasi'
        );

        return redirect()->route('regulasi.index')
            ->with('success', 'Regulasi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $regulasi = Regulasi::findOrFail($id);

        // Delete associated file
        if ($regulasi->file_regulasi && file_exists(public_path($regulasi->file_regulasi))) {
            unlink(public_path($regulasi->file_regulasi));
        }

        // Log activity
        ActivityLog::log(
            'Menghapus regulasi',
            $regulasi,
            'deleted',
            ['data' => $regulasi->toArray()],
            'regulasi'
        );

        $regulasi->delete();

        return redirect()->route('regulasi.index')
            ->with('success', 'Regulasi berhasil dihapus.');
    }

    /**
     * Download the regulation file.
     */
    public function download(string $id)
    {
        $regulasi = Regulasi::findOrFail($id);

        if (!$regulasi->file_regulasi || !file_exists(public_path($regulasi->file_regulasi))) {
            return redirect()->back()->with('error', 'File tidak ditemukan.');
        }

        // Log activity
        ActivityLog::log(
            'Mengunduh regulasi',
            $regulasi,
            'viewed',
            ['file' => $regulasi->file_regulasi],
            'regulasi'
        );

        return response()->download(public_path($regulasi->file_regulasi), $regulasi->nama_regulasi . '.' . pathinfo($regulasi->file_regulasi, PATHINFO_EXTENSION));
    }
}
