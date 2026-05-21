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
            // For public tracking, we genericize the validator info to reduce information exposure
            $roleLabel = $validasi->validationFlow->role_label ?? 'Validator';
            
            // Map internal roles to generic public labels if needed
            $publicRoleLabels = [
                'fo' => 'Front Office',
                'bo' => 'Back Office',
                'operator_opd' => 'Tim Teknis OPD',
                'kepala_opd' => 'Kepala OPD',
            ];
            
            $internalRole = $validasi->validationFlow->role ?? '';
            $displayRole = $publicRoleLabels[$internalRole] ?? $roleLabel;

            return [
                'status' => $validasi->status,
                'catatan' => $validasi->catatan,
                'validated_at' => $validasi->validated_at,
                'validation_flow' => [
                    'role_label' => $displayRole,
                    'description' => $validasi->validationFlow->description,
                ],
                'validator' => [
                    'role_label' => $displayRole,
                    // We hide the real name of the validator for public tracking
                ],
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
                // Only return necessary pemohon info
                'user' => $perizinan->user ? [
                    'name' => maskName($perizinan->user->name),
                ] : null,
                'validasi_records' => $validasiRecords,
                'total_steps' => $totalSteps,
            ]
        ]);
    }
}
