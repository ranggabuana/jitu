<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Perijinan;
use Illuminate\Http\Request;

class LayananController extends Controller
{
    public function index(Request $request)
    {
        $query = Perijinan::query();

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_perijinan', 'like', "%{$search}%")
                  ->orWhere('dasar_hukum', 'like', "%{$search}%");
            });
        }

        $layanan = $query->orderBy('nama_perijinan')->paginate(12)->withQueryString();

        return view('front.layanan', compact('layanan'));
    }

    public function show($id)
    {
        $layanan = Perijinan::with([
            'activeValidationFlows',
            'activeFormFields'
        ])->findOrFail($id);

        return view('front.layanan-detail', compact('layanan'));
    }
}
