<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\DataSkm;
use App\Models\HasilSkm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SkmController extends Controller
{
    /**
     * Show the SKM form page.
     */
    public function index(Request $request)
    {
        $questions = DataSkm::aktif()->orderBy('urutan')->get();
        
        $applicationId = $request->query('application_id');
        $application = null;
        
        if ($applicationId) {
            $application = \App\Models\DataPerijinan::with('user')
                ->where('id', $applicationId)
                ->where('user_id', auth()->id())
                ->first();
        }

        // Generate CAPTCHA
        session([
            'captcha_num1' => rand(1, 10),
            'captcha_num2' => rand(1, 10),
        ]);
        
        return view('front.skm-form', compact('questions', 'application'));
    }

    /**
     * Store SKM response.
     */
    public function store(Request $request)
    {
        // Validate CAPTCHA first
        $request->validate([
            'captcha' => 'required|numeric',
        ], [
            'captcha.required' => 'Silakan masukkan hasil penjumlahan.',
            'captcha.numeric' => 'Hasil penjumlahan harus berupa angka.',
        ]);

        // Check CAPTCHA
        $captchaAnswer = ($request->session()->get('captcha_num1', 0) + $request->session()->get('captcha_num2', 0));
        if ($request->captcha != $captchaAnswer) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hasil penjumlahan CAPTCHA salah. Silakan coba lagi.'
                ], 422);
            }
            return redirect()->back()
                ->withInput()
                ->with('captcha_error', 'Hasil penjumlahan CAPTCHA salah. Silakan coba lagi.');
        }

        $request->validate([
            'responden_nama' => 'required|string|max:255',
            'responden_email' => 'required|email|max:255',
            'nip' => 'required|string|max:50',
            'jenis_kelamin' => 'required|string|in:Laki-laki,Perempuan',
            'pendidikan' => 'required|string|max:100',
            'pekerjaan' => 'required|string|max:100',
            'jawaban' => 'required|array',
            'jawaban.*' => 'required|integer|in:1,2,3,4',
            'saran' => 'required|string|max:1000',
            'data_perijinan_id' => 'nullable|exists:data_perijinan,id',
        ], [
            'nip.required' => 'NIP / NIK wajib diisi.',
            'jenis_kelamin.required' => 'Jenis Kelamin wajib diisi.',
            'pendidikan.required' => 'Pendidikan wajib diisi.',
            'pekerjaan.required' => 'Pekerjaan wajib diisi.',
            'saran.required' => 'Saran & Masukan wajib diisi.',
            'jawaban.*.required' => 'Seluruh pertanyaan penilaian wajib dijawab.',
        ]);

        try {
            DB::beginTransaction();

            $questions = DataSkm::aktif()->get();
            
            foreach ($request->jawaban as $dataSkmId => $jawaban) {
                HasilSkm::create([
                    'data_skm_id' => $dataSkmId,
                    'data_perijinan_id' => $request->data_perijinan_id,
                    'responden_nama' => $request->responden_nama,
                    'responden_email' => $request->responden_email,
                    'nip' => $request->nip,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'pendidikan' => $request->pendidikan,
                    'pekerjaan' => $request->pekerjaan,
                    'jawaban' => $jawaban,
                    'saran' => $request->saran,
                    'ip_address' => $request->ip(),
                    'user_id' => auth()->id(),
                ]);
            }

            DB::commit();

            // Clear CAPTCHA from session
            $request->session()->forget(['captcha_num1', 'captcha_num2']);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Terima kasih! Survei Anda telah berhasil disimpan.'
                ]);
            }

            if ($request->data_perijinan_id) {
                return redirect()->route('pemohon.tracking.detail', $request->data_perijinan_id)
                    ->with('success', 'Terima kasih! Survei Anda telah berhasil disimpan. Anda sekarang dapat mengunduh dokumen izin Anda.');
            }

            return redirect()->route('skm.success')
                ->with('success', 'Terima kasih! Survei Anda telah berhasil disimpan.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('SKM Store Error: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menyimpan survei: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan survei. Silakan coba lagi.');
        }
    }

    /**
     * Refresh CAPTCHA.
     */
    public function refreshCaptcha()
    {
        session([
            'captcha_num1' => rand(1, 10),
            'captcha_num2' => rand(1, 10),
        ]);

        return response()->json([
            'num1' => session('captcha_num1'),
            'num2' => session('captcha_num2'),
        ]);
    }

    /**
     * Show success page after submitting SKM.
     */
    public function success()
    {
        return view('front.skm-success');
    }
}
