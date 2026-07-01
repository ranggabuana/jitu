<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules\Password;

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
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
            'no_hp' => 'nullable|string|max:20',
            'provinsi_id' => 'nullable|exists:provinsis,id',
            'kabupaten_id' => 'nullable|exists:kabupatens,id',
            'kecamatan_id' => 'nullable|exists:kecamatans,id',
            'kelurahan_id' => 'nullable|exists:kelurahans,id',
            'alamat_ktp' => 'nullable|string|max:500',
            'is_alamat_sama' => 'boolean',
            'alamat_domisili' => 'nullable|required_if:is_alamat_sama,0|string|max:500',
            'foto_ktp' => 'nullable|required_without:temp_foto_ktp|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'temp_foto_ktp' => 'nullable|string',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'pendidikan' => 'required|in:SD/MI,SMP/MTS,SMA/MA,SMK/MAK,D1,D2,D3,D4,S1,S2,S3',
            'pekerjaan' => 'required|in:PNS,TNI,POLRI,Swasta,Wirausaha,Lainnya',
            'pekerjaan_lainnya' => 'required_if:pekerjaan,Lainnya|nullable|string|max:255',
        ], [
            'nik.required' => 'NIK harus diisi.',
            'nik.size' => 'NIK harus terdiri dari 16 digit.',
            'nik.regex' => 'NIK hanya boleh berisi angka.',
            'status_pemohon.required' => 'Status pemohon harus dipilih.',
            'alamat_domisili.required_if' => 'Alamat domisili harus diisi jika tidak sama dengan alamat KTP.',
            'foto_ktp.required_without' => 'Foto KTP wajib diunggah.',
            'foto_ktp.file' => 'File KTP tidak valid.',
            'foto_ktp.mimes' => 'Format KTP harus jpeg, png, jpg, atau pdf.',
            'foto_ktp.max' => 'Ukuran file KTP maksimal 2MB.',
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in' => 'Pilihan jenis kelamin tidak valid.',
            'pendidikan.required' => 'Pendidikan wajib dipilih.',
            'pendidikan.in' => 'Pilihan pendidikan tidak valid.',
            'pekerjaan.required' => 'Pekerjaan wajib dipilih.',
            'pekerjaan.in' => 'Pilihan pekerjaan tidak valid.',
            'pekerjaan_lainnya.required_if' => 'Isian pekerjaan manual wajib diisi jika memilih Lainnya.',
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
            'provinsi_id' => $request->provinsi_id,
            'kabupaten_id' => $request->kabupaten_id,
            'kecamatan_id' => $request->kecamatan_id,
            'kelurahan_id' => $request->kelurahan_id,
            'alamat_ktp' => $request->alamat_ktp,
            'is_alamat_sama' => $request->has('is_alamat_sama') ? $request->is_alamat_sama : true,
            'alamat_domisili' => $request->is_alamat_sama ? $request->alamat_ktp : $request->alamat_domisili,
            'jenis_kelamin' => $request->jenis_kelamin,
            'pendidikan' => $request->pendidikan,
            'pekerjaan' => $request->pekerjaan === 'Lainnya' ? $request->pekerjaan_lainnya : $request->pekerjaan,
        ];

        // Handle KTP file upload
        if ($request->hasFile('foto_ktp')) {
            $file = $request->file('foto_ktp');
            $extension = $file->getClientOriginalExtension();
            $filename = 'ktp_' . time() . '_' . $request->nik . '.' . $extension;
            $uploadPath = public_path('uploads/register');

            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $file->move($uploadPath, $filename);
            $userData['foto_ktp'] = 'uploads/register/' . $filename;
        } elseif ($request->temp_foto_ktp) {
            // Use temporary file
            $tempFile = public_path($request->temp_foto_ktp);
            if (file_exists($tempFile)) {
                $filename = basename($tempFile);
                $uploadPath = public_path('uploads/register');

                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                $newPath = $uploadPath . '/' . $filename;
                rename($tempFile, $newPath);
                $userData['foto_ktp'] = 'uploads/register/' . $filename;
            }
        }

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

        return redirect()->route('login')->with('success', 'Registrasi berhasil! Akun Anda menunggu aktivasi dari admin. Jika berhasil, akan ada pemberitahuan melalui email anda');
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
     * Handle temporary KTP upload.
     */
    public function uploadTempKtp(Request $request)
    {
        $request->validate([
            'foto_ktp' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        if ($request->hasFile('foto_ktp')) {
            $file = $request->file('foto_ktp');
            $extension = $file->getClientOriginalExtension();
            $filename = 'temp_ktp_' . time() . '_' . uniqid() . '.' . $extension;
            $uploadPath = public_path('uploads/temp');

            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $file->move($uploadPath, $filename);

            return response()->json([
                'success' => true,
                'path' => 'uploads/temp/' . $filename,
                'filename' => $file->getClientOriginalName()
            ]);
        }

        return response()->json(['success' => false], 400);
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
