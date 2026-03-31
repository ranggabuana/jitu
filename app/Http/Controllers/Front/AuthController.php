<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Show the registration form.
     */
    public function showRegisterForm()
    {
        // Redirect if already authenticated
        if (auth()->check()) {
            return redirect()->intended('/');
        }

        // Generate CAPTCHA
        session([
            'register_num1' => rand(1, 10),
            'register_num2' => rand(1, 10),
        ]);

        return view('auth.register');
    }

    /**
     * Handle registration request.
     */
    public function register(Request $request)
    {
        // Validate CAPTCHA first
        $request->validate([
            'captcha' => 'required|numeric',
        ], [
            'captcha.required' => 'Silakan masukkan hasil penjumlahan.',
            'captcha.numeric' => 'Hasil penjumlahan harus berupa angka.',
        ]);

        // Check CAPTCHA
        $captchaAnswer = ($request->session()->get('register_num1', 0) + $request->session()->get('register_num2', 0));
        if ($request->captcha != $captchaAnswer) {
            return redirect()->back()
                ->withInput()
                ->with('captcha_error', 'Hasil penjumlahan CAPTCHA salah. Silakan coba lagi.');
        }

        // Clear CAPTCHA after verification
        session()->forget(['register_num1', 'register_num2']);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'nik' => [
                'required',
                'string',
                'size:16',
                'regex:/^[0-9]{16}$/',
            ],
            'status_pemohon' => 'required|in:perorangan,badan_usaha',
            'nama_perusahaan' => 'nullable|string|max:255',
            'npwp' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'no_hp' => 'nullable|string|max:20',
        ], [
            'nik.required' => 'NIK harus diisi.',
            'nik.size' => 'NIK harus terdiri dari 16 digit.',
            'nik.regex' => 'NIK hanya boleh berisi angka.',
            'status_pemohon.required' => 'Status pemohon harus dipilih.',
        ]);

        // Check if NIK already exists with the same status_pemohon
        $existingUser = User::where('nip', $request->nik)
            ->where('status_pemohon', $request->status_pemohon)
            ->first();

        if ($existingUser) {
            throw ValidationException::withMessages([
                'nik' => 'NIK dengan status ' . ($request->status_pemohon === 'perorangan' ? 'Perorangan' : 'Badan Usaha') . ' sudah terdaftar. Silakan login atau gunakan NIK yang berbeda.',
            ]);
        }

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'nip' => $request->nik,
            'password' => Hash::make($request->password),
            'role' => 'pemohon',
            'status' => 'tidak_aktif',
            'status_pemohon' => $request->status_pemohon,
            'no_hp' => $request->no_hp,
        ];

        // Add company data if badan usaha
        if ($request->status_pemohon === 'badan_usaha') {
            $userData['nama_perusahaan'] = $request->nama_perusahaan;
            $userData['npwp'] = $request->npwp;
        }

        $user = User::create($userData);

        // Log activity dengan user_id dari user yang baru dibuat
        ActivityLog::log(
            'Pemohon baru mendaftar',
            $user,
            'created',
            [
                'data' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'username' => $user->username,
                    'nip' => $user->nip,
                    'status_pemohon' => $user->status_pemohon,
                ],
                'role' => $user->role,
            ],
            'pemohon',
            $user->id  // Pass user ID explicitly
        );

        return redirect()->route('login')->with('success', 'Registrasi berhasil! Akun Anda menunggu aktivasi dari admin.');
    }

    /**
     * Check if NIK already exists.
     */
    public function checkNik(Request $request)
    {
        $request->validate([
            'nik' => 'required|string|size:16',
            'status_pemohon' => 'required|in:perorangan,badan_usaha',
        ]);

        $exists = User::where('nip', $request->nik)
            ->where('status_pemohon', $request->status_pemohon)
            ->exists();

        return response()->json([
            'exists' => $exists,
        ]);
    }

    /**
     * Refresh CAPTCHA.
     */
    public function refreshCaptcha()
    {
        session([
            'register_num1' => rand(1, 10),
            'register_num2' => rand(1, 10),
        ]);

        return response()->json([
            'num1' => session('register_num1'),
            'num2' => session('register_num2'),
        ]);
    }
}
