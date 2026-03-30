<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use Illuminate\Http\Request;

class InformasiController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $category = $request->get('category', 'all');
        $perPage = 9;

        $query = Berita::query()->where('status', 'aktif');

        // Apply search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('konten', 'like', "%{$search}%");
            });
        }

        // Get slider berita (is_featured = true)
        $sliderBerita = (clone $query)->where('is_featured', true)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get latest 3 berita for secondary section (excluding slider)
        $sliderIds = $sliderBerita->pluck('id')->toArray();
        $secondaryBerita = (clone $query)
            ->whereNotIn('id', $sliderIds)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        // Get remaining berita for grid (exclude slider and secondary)
        $excludeIds = [...$sliderIds, ...$secondaryBerita->pluck('id')->toArray()];
        $beritaGrid = $query->whereNotIn('id', array_filter($excludeIds))
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        // Get stats
        $totalArtikel = Berita::where('status', 'aktif')->count();
        $beritaBaru = Berita::where('status', 'aktif')
            ->where('created_at', '>=', now()->subMonth())
            ->count();
        $totalViews = Berita::where('status', 'aktif')->sum('views');

        return view('front.informasi', compact(
            'sliderBerita',
            'secondaryBerita',
            'beritaGrid',
            'totalArtikel',
            'beritaBaru',
            'totalViews',
            'search'
        ));
    }

    public function show($slug)
    {
        $berita = Berita::where('slug', $slug)->where('status', 'aktif')->firstOrFail();
        
        // Increment views
        $berita->increment('views');
        
        // Get related berita (same category or latest)
        $relatedBerita = Berita::where('status', 'aktif')
            ->where('id', '!=', $berita->id)
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get();

        return view('front.berita-detail', compact('berita', 'relatedBerita'));
    }
}
