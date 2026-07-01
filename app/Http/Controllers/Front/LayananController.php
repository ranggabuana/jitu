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
                $repName = "PENCABUTAN SURAT IZIN PRAKTIK TENAGA MEDIS DAN TENAGA KESEHATAN";
                if (stripos($repName, $search) !== false || $pencabutanMedisItems->count() > 0) {
                    $includePencabutanMedis = true;
                }
            }
        }

        // Fetch operasional pendidikan items separately
        $operasionalQuery = Perijinan::where('jenis_perijinan', 'operasional_pendidikan');
        if ($search !== '') {
            $operasionalQuery->where(function($q) use ($search) {
                $q->where('nama_perijinan', 'like', "%{$search}%")
                  ->orWhere('dasar_hukum', 'like', "%{$search}%");
            });
        }
        $operasionalPendidikanItems = $operasionalQuery->orderBy('nama_perijinan')->get();

        // Determine if we should show the representative card
        $includeOperasionalPendidikan = false;
        if ($operasionalPendidikanItems->count() > 0) {
            if ($search === '') {
                $includeOperasionalPendidikan = true;
            } else {
                $repName = "IZIN OPERASIONAL PROGRAM ATAU SATUAN PENDIDIKAN";
                if (stripos($repName, $search) !== false || $operasionalPendidikanItems->count() > 0) {
                    $includeOperasionalPendidikan = true;
                }
            }
        }

        // Fetch pendirian pendidikan items separately
        $pendirianQuery = Perijinan::where('jenis_perijinan', 'pendirian_pendidikan');
        if ($search !== '') {
            $pendirianQuery->where(function($q) use ($search) {
                $q->where('nama_perijinan', 'like', "%{$search}%")
                  ->orWhere('dasar_hukum', 'like', "%{$search}%");
            });
        }
        $pendirianPendidikanItems = $pendirianQuery->orderBy('nama_perijinan')->get();

        // Determine if we should show the representative card
        $includePendirianPendidikan = false;
        if ($pendirianPendidikanItems->count() > 0) {
            if ($search === '') {
                $includePendirianPendidikan = true;
            } else {
                $repName = "IZIN PENDIRIAN PROGRAM ATAU SATUAN PENDIDIKAN";
                if (stripos($repName, $search) !== false || $pendirianPendidikanItems->count() > 0) {
                    $includePendirianPendidikan = true;
                }
            }
        }

        // Fetch other items
        $query = Perijinan::whereNotIn('jenis_perijinan', ['pencabutan_medis', 'operasional_pendidikan', 'pendirian_pendidikan']);
        if ($search !== '') {
            $query->where(function($q) use ($search) {
                $q->where('nama_perijinan', 'like', "%{$search}%")
                  ->orWhere('dasar_hukum', 'like', "%{$search}%");
            });
        }

        $layanan = $query->orderBy('nama_perijinan')->paginate(12)->withQueryString();

        return view('front.layanan', compact(
            'layanan', 
            'includePencabutanMedis', 
            'pencabutanMedisItems',
            'includeOperasionalPendidikan',
            'operasionalPendidikanItems',
            'includePendirianPendidikan',
            'pendirianPendidikanItems'
        ));
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
