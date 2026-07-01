<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Show the profile page.
     */
    public function show()
    {
        $user = Auth::user();
        return view('profile.show', compact('user'));
    }

    /**
     * Show the edit profile form.
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'nip' => 'nullable|string|max:50',
            'no_hp' => 'nullable|string|max:20',
        ];

        if ($user->role === 'pemohon') {
            $rules['jenis_kelamin'] = 'required|in:Laki-laki,Perempuan';
            $rules['pendidikan'] = 'required|in:SD/MI,SMP/MTS,SMA/MA,SMK/MAK,D1,D2,D3,D4,S1,S2,S3';
            $rules['pekerjaan'] = 'required|in:PNS,TNI,POLRI,Swasta,Wirausaha,Lainnya';
            $rules['pekerjaan_lainnya'] = 'required_if:pekerjaan,Lainnya|nullable|string|max:255';
        }

        $validated = $request->validate($rules, [
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in' => 'Pilihan jenis kelamin tidak valid.',
            'pendidikan.required' => 'Pendidikan wajib dipilih.',
            'pendidikan.in' => 'Pilihan pendidikan tidak valid.',
            'pekerjaan.required' => 'Pekerjaan wajib dipilih.',
            'pekerjaan.in' => 'Pilihan pekerjaan tidak valid.',
            'pekerjaan_lainnya.required_if' => 'Isian pekerjaan manual wajib diisi jika memilih Lainnya.',
        ]);

        $data = $validated;
        if ($user->role === 'pemohon') {
            $data['pekerjaan'] = $request->pekerjaan === 'Lainnya' ? $request->pekerjaan_lainnya : $request->pekerjaan;
        }

        $user->update($data);

        return redirect()->route('profile.show')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Show the change password form.
     */
    public function editPassword()
    {
        return view('profile.change-password');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => 'required|string',
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
        ], [
            'password.required' => 'Password baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        // Verify current password
        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors([
                'current_password' => 'Password saat ini tidak sesuai.',
            ]);
        }

        // Update password
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('profile.show')
            ->with('success', 'Password berhasil diubah.');
    }
}
