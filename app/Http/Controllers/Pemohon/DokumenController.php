<?php

namespace App\Http\Controllers\Pemohon;

use App\Http\Controllers\Controller;
use App\Models\MasterDokumenPemohon;
use App\Models\UserDokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DokumenController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $masterUmum = MasterDokumenPemohon::where('jenis', 'umum')->get();
        $masterSpesifik = MasterDokumenPemohon::where('jenis', 'spesifik')->get();
        
        $userDokumens = UserDokumen::where('user_id', $user->id)
            ->get()
            ->keyBy('master_dokumen_id');

        return view('pemohon.dokumen.index', compact('masterUmum', 'masterSpesifik', 'userDokumens'));
    }

    public function upload(Request $request, $masterId)
    {
        $master = MasterDokumenPemohon::findOrFail($masterId);
        
        // Prepare validation string based on master settings
        $allowedExtensions = explode(',', str_replace(' ', '', strtolower($master->tipe_data_file)));
        $mimes = implode(',', $allowedExtensions);
        $maxSize = $master->max_size ?? 2048; // fallback if null
        
        $request->validate([
            'file_dokumen' => 'required|file|mimes:' . $mimes . '|max:' . $maxSize,
        ]);

        $user = Auth::user();
        $file = $request->file('file_dokumen');
        
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $filename = $originalName . '_' . time() . '.' . $extension;
        
        $uploadPath = storage_path('app/uploads/dokumen_pemohon/' . $user->id);
        
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }
        
        $file->move($uploadPath, $filename);
        $filePath = 'uploads/dokumen_pemohon/' . $user->id . '/' . $filename;

        // Check if old file exists
        $userDokumen = UserDokumen::where('user_id', $user->id)->where('master_dokumen_id', $master->id)->first();
        if ($userDokumen) {
            // Delete old file
            $oldFilePath = storage_path('app/' . $userDokumen->file_path);
            if (file_exists($oldFilePath)) {
                unlink($oldFilePath);
            } else if (file_exists(public_path($userDokumen->file_path))) {
                unlink(public_path($userDokumen->file_path));
            }
            $userDokumen->update(['file_path' => $filePath]);
        } else {
            UserDokumen::create([
                'user_id' => $user->id,
                'master_dokumen_id' => $master->id,
                'file_path' => $filePath,
            ]);
        }

        return redirect()->back()->with('success', "Dokumen {$master->nama_dokumen} berhasil diunggah.");
    }
}