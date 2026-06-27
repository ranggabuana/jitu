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
            'validasiRecords.validationFlow.assignedUser.opd',
            'validasiRecords.validator.opd'
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

            $opdName = null;
            if ($validasi->validationFlow && $validasi->validationFlow->assignedUser && $validasi->validationFlow->assignedUser->opd) {
                $opdName = $validasi->validationFlow->assignedUser->opd->nama_opd;
            } elseif ($validasi->validator && $validasi->validator->opd) {
                $opdName = $validasi->validator->opd->nama_opd;
            }

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
                    'opd_name' => $opdName,
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

    /**
     * Scan QR Code to verify permit.
     */
    public function scanQr($no_registrasi)
    {
        $perizinan = DataPerijinan::with([
            'user',
            'perijinan',
            'validasiRecords.validationFlow.assignedUser.opd'
        ])
        ->where('no_registrasi', $no_registrasi)
        ->firstOrFail();

        $type = request('type');
        $opdId = request('opd_id');
        $isDraftParam = request('is_draft');

        $documentType = 'Dokumen Elektronik';
        $documentStatus = 'Draft';

        if ($type === 'rekom') {
            $documentType = 'Surat Rekomendasi';
        } elseif ($type === 'izin') {
            $documentType = 'Surat Izin';
        } else {
            $documentType = 'Surat Izin';
        }

        if ($isDraftParam == 1) {
            $documentStatus = 'Draft';
        } else {
            if ($type === 'rekom') {
                if ($perizinan->perijinan->is_multi_opd && $opdId) {
                    $documentStatus = !empty($perizinan->file_rekom_multi_tte[$opdId]) ? 'Resmi (TTE)' : 'Draft';
                } else {
                    $documentStatus = !empty($perizinan->file_rekom_tte) ? 'Resmi (TTE)' : 'Draft';
                }
            } elseif ($type === 'izin') {
                $documentStatus = !empty($perizinan->file_izin_tte) ? 'Resmi (TTE)' : 'Draft';
            } else {
                // Default fallback if type is not specified (old QR codes)
                $documentStatus = !empty($perizinan->file_izin_tte) ? 'Resmi (TTE)' : 'Draft';
            }
        }

        $tanggalTerbit = null;
        if ($type === 'rekom') {
            // Find the successful EsignLog for this recommendation
            $rekomTteLog = \App\Models\EsignLog::where('data_perijinan_id', $perizinan->id)
                ->where('document_type', 'rekomendasi')
                ->where('status', 'success')
                ->whereHas('user', function($q) use ($opdId) {
                    if ($opdId) {
                        $q->where('opd_id', $opdId);
                    }
                })
                ->latest()
                ->first();

            if ($rekomTteLog) {
                $tanggalTerbit = $rekomTteLog->created_at;
            }
        } else {
            // Find the successful EsignLog for this license (izin)
            $izinTteLog = \App\Models\EsignLog::where('data_perijinan_id', $perizinan->id)
                ->where('document_type', 'izin')
                ->where('status', 'success')
                ->latest()
                ->first();

            if ($izinTteLog) {
                $tanggalTerbit = $izinTteLog->created_at;
            } else {
                $tanggalTerbit = $perizinan->approved_at;
            }
        }

        return view('front.scan-result', compact('perizinan', 'documentType', 'documentStatus', 'type', 'opdId', 'tanggalTerbit'));
    }
}
