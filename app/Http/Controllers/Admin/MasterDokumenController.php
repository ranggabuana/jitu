<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterDokumenPemohon;
use Illuminate\Http\Request;

class MasterDokumenController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nama_dokumen' => 'required|string|max:255',
            'tipe_data_file' => 'required|string|max:255',
            'jenis' => 'required|in:umum,spesifik',
            'max_size' => 'required|integer|min:1',
        ]);

        MasterDokumenPemohon::create($request->all());

        return redirect()->back()->with('success', 'Dokumen pemohon berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_dokumen' => 'required|string|max:255',
            'tipe_data_file' => 'required|string|max:255',
            'jenis' => 'required|in:umum,spesifik',
            'max_size' => 'required|integer|min:1',
        ]);

        $dokumen = MasterDokumenPemohon::findOrFail($id);
        $dokumen->update($request->all());

        return redirect()->back()->with('success', 'Dokumen pemohon berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $dokumen = MasterDokumenPemohon::findOrFail($id);
        $dokumen->delete();

        return redirect()->back()->with('success', 'Dokumen pemohon berhasil dihapus.');
    }
}