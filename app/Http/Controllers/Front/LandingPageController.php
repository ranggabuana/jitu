<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Perijinan;
use App\Models\Berita;
use App\Models\DataPerijinan;
use Illuminate\Http\Request;

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

    /**
     * Track perizinan by no_registrasi.
     */
    public function trackPerizinan(Request $request)
    {
        $request->validate([
            'no_registrasi' => 'required|string',
        ]);

        $perizinan = DataPerijinan::with([
            'user',
            'perijinan',
            'perijinan.activeValidationFlows',
            'validasiRecords.validationFlow.assignedUser',
            'validasiRecords.validator'
        ])
        ->where('no_registrasi', $request->no_registrasi)
        ->first();

        if (!$perizinan) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor registrasi tidak ditemukan!',
            ], 404);
        }

        // Prepare validation records
        $validasiRecords = $perizinan->validasiRecords->map(function ($validasi) {
            // Determine validator based on role
            $validator = null;
            
            // For assigned roles (operator_opd, kepala_opd), use assigned_user from validation_flow
            if (in_array($validasi->validationFlow->role ?? '', ['operator_opd', 'kepala_opd'])) {
                if ($validasi->validationFlow->assignedUser) {
                    $validator = [
                        'name' => $validasi->validationFlow->assignedUser->name,
                        'role' => $validasi->validationFlow->assignedUser->role,
                        'role_label' => $validasi->validationFlow->assignedUser->role_label,
                    ];
                }
            } 
            // For collective roles, use validator from data_perijinan_validasi
            elseif ($validasi->validator) {
                $validator = [
                    'name' => $validasi->validator->name,
                    'role' => $validasi->validator->role,
                    'role_label' => $validasi->validator->role_label,
                ];
            }
            
            return [
                'status' => $validasi->status,
                'catatan' => $validasi->catatan,
                'validated_at' => $validasi->validated_at,
                'validation_flow' => [
                    'role_label' => $validasi->validationFlow->role_label,
                    'description' => $validasi->validationFlow->description,
                ],
                'validator' => $validator,
            ];
        });

        // Calculate total steps
        $totalSteps = $perizinan->perijinan->activeValidationFlows->count();

        return response()->json([
            'success' => true,
            'data' => [
                'no_registrasi' => $perizinan->no_registrasi,
                'status' => $perizinan->status,
                'current_step' => $perizinan->current_step,
                'progress_percentage' => $perizinan->progress_percentage,
                'catatan_perbaikan' => $perizinan->catatan_perbaikan,
                'catatan_reject' => $perizinan->catatan_reject,
                'created_at' => $perizinan->created_at,
                'perijinan' => [
                    'nama_perijinan' => $perizinan->perijinan->nama_perijinan,
                ],
                'data_pemohon' => $perizinan->data_pemohon,
                'user' => $perizinan->user ? [
                    'name' => $perizinan->user->name,
                    'email' => $perizinan->user->email,
                ] : null,
                'validasi_records' => $validasiRecords,
                'total_steps' => $totalSteps,
            ]
        ]);
    }
}
