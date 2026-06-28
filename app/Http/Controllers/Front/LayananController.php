<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Perijinan;
use Illuminate\Http\Request;

class LayananController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');

        // Fetch pencabutan medis items separately
        $pencabutanQuery = Perijinan::where('jenis_perijinan', 'pencabutan_medis');
        if ($search !== '') {
            $pencabutanQuery->where(function($q) use ($search) {
                $q->where('nama_perijinan', 'like', "%{$search}%")
                  ->orWhere('dasar_hukum', 'like', "%{$search}%");
            });
        }
        $pencabutanMedisItems = $pencabutanQuery->orderBy('nama_perijinan')->get();

        // Determine if we should show the representative card
        $includePencabutanMedis = false;
        if ($pencabutanMedisItems->count() > 0) {
            if ($search === '') {
                $includePencabutanMedis = true;
            } else {
                $repName = "pencabutan Surat Izin Praktik (SIP) tenaga medis dan tenaga kesehatan";
                if (stripos($repName, $search) !== false || $pencabutanMedisItems->count() > 0) {
                    $includePencabutanMedis = true;
                }
            }
        }

        // Fetch other items
        $query = Perijinan::where('jenis_perijinan', '!=', 'pencabutan_medis');
        if ($search !== '') {
            $query->where(function($q) use ($search) {
                $q->where('nama_perijinan', 'like', "%{$search}%")
                  ->orWhere('dasar_hukum', 'like', "%{$search}%");
            });
        }

        $layanan = $query->orderBy('nama_perijinan')->paginate(12)->withQueryString();

        return view('front.layanan', compact('layanan', 'includePencabutanMedis', 'pencabutanMedisItems'));
    }

    public function show($id)
    {
        $layanan = Perijinan::with([
            'activeValidationFlows',
            'activeFormFields' => function ($query) {
                $query->where('form_type', 'global')
                    ->orderBy('order', 'asc')
                    ->orderBy('id', 'asc');
            }
        ])->findOrFail($id);

        return view('front.layanan-detail', compact('layanan'));
    }
}
