<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Perijinan;
use App\Models\Berita;

class LandingPageController extends Controller
{
    public function index()
    {
        $layanan = Perijinan::orderBy('nama_perijinan')->limit(4)->get();
        
        // Get featured berita for slider (max 4 slides)
        $beritaSlider = Berita::where('status', 'aktif')
            ->where('is_featured', true)
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get();

        return view('front.index', compact('layanan', 'beritaSlider'));
    }
}
