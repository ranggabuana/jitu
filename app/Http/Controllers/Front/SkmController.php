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
    public function index()
    {
        $questions = DataSkm::aktif()->orderBy('urutan')->get();
        
        // Generate CAPTCHA
        session([
            'captcha_num1' => rand(1, 10),
            'captcha_num2' => rand(1, 10),
        ]);
        
        return view('front.skm-form', compact('questions'));
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
            return redirect()->back()
                ->withInput()
                ->with('captcha_error', 'Hasil penjumlahan CAPTCHA salah. Silakan coba lagi.');
        }

        $request->validate([
            'responden_nama' => 'required|string|max:255',
            'responden_email' => 'required|email|max:255',
            'nip' => 'nullable|string|max:50',
            'jawaban' => 'required|array',
            'jawaban.*' => 'required|integer|in:1,2,3,4',
            'saran' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $questions = DataSkm::aktif()->get();
            
            foreach ($request->jawaban as $dataSkmId => $jawaban) {
                HasilSkm::create([
                    'data_skm_id' => $dataSkmId,
                    'responden_nama' => $request->responden_nama,
                    'responden_email' => $request->responden_email,
                    'nip' => $request->nip,
                    'jawaban' => $jawaban,
                    'saran' => $request->saran,
                    'ip_address' => $request->ip(),
                ]);
            }

            DB::commit();

            // Clear CAPTCHA from session
            $request->session()->forget(['captcha_num1', 'captcha_num2']);

            return redirect()->route('skm.success')
                ->with('success', 'Terima kasih! Survei Anda telah berhasil disimpan.');
            
        } catch (\Exception $e) {
            DB::rollBack();
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
