<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Kelurahan;

class WilayahController extends Controller
{
    public function getProvinsi()
    {
        $provinsi = Provinsi::orderBy('name')->get(['id', 'code', 'name']);
        return response()->json(['data' => $provinsi]);
    }

    public function getKabupaten($provinsiId)
    {
        $kabupaten = Kabupaten::where('provinsi_id', $provinsiId)
            ->orderBy('name')
            ->get(['id', 'code', 'name']);
        return response()->json(['data' => $kabupaten]);
    }

    public function getKecamatan($kabupatenId)
    {
        $kecamatan = Kecamatan::where('kabupaten_id', $kabupatenId)
            ->orderBy('name')
            ->get(['id', 'code', 'name']);
        return response()->json(['data' => $kecamatan]);
    }

    public function getKelurahan($kecamatanId)
    {
        $kelurahan = Kelurahan::where('kecamatan_id', $kecamatanId)
            ->orderBy('name')
            ->get(['id', 'code', 'name']);
        return response()->json(['data' => $kelurahan]);
    }
}
