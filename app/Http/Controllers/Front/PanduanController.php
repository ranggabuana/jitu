<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Panduan;
use Illuminate\Http\Request;

class PanduanController extends Controller
{
    /**
     * Display a listing of active guides.
     */
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        
        $query = Panduan::query()->where('status', true);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_panduan', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        $panduan = $query->orderBy('created_at', 'desc')->paginate(12);

        return view('front.panduan', compact('panduan', 'search'));
    }

    /**
     * Preview the guide file for public.
     */
    public function preview($slug)
    {
        $panduan = Panduan::where('slug', $slug)->where('status', true)->firstOrFail();

        if (!$panduan->file || !file_exists(public_path($panduan->file))) {
            abort(404, 'File tidak ditemukan.');
        }

        return response()->file(public_path($panduan->file), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $panduan->nama_panduan . '.pdf"'
        ]);
    }
}
