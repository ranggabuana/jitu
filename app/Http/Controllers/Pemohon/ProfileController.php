<?php

namespace App\Http\Controllers\Pemohon;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Show the profile page for pemohon.
     */
    public function show()
    {
        $user = Auth::user();
        return view('pemohon.profile.show', compact('user'));
    }

    /**
     * Show the edit profile form for pemohon.
     */
    public function edit()
    {
        $user = Auth::user();
        return view('pemohon.profile.edit', compact('user'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'nip' => [
                'required',
                'string',
                'size:16',
                'regex:/^[0-9]{16}$/',
                Rule::unique('users')->ignore($user->id),
            ],
            'status_pemohon' => 'required|in:perorangan,badan_usaha',
            'nama_perusahaan' => 'nullable|string|max:255',
            'npwp' => 'nullable|string|max:50',
            'no_hp' => 'nullable|string|max:20',
            'provinsi_id' => 'nullable|exists:provinsis,id',
            'kabupaten_id' => 'nullable|exists:kabupatens,id',
            'kecamatan_id' => 'nullable|exists:kecamatans,id',
            'kelurahan_id' => 'nullable|exists:kelurahans,id',
            'alamat_lengkap' => 'nullable|string|max:500',
            'foto_ktp' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ], [
            'nip.required' => 'NIK harus diisi.',
            'nip.size' => 'NIK harus terdiri dari 16 digit.',
            'nip.regex' => 'NIK hanya boleh berisi angka.',
            'nip.unique' => 'NIK sudah terdaftar.',
        ]);

        $data = $request->except('foto_ktp');

        // Handle KTP file upload
        if ($request->hasFile('foto_ktp')) {
            // Delete old file if exists
            if ($user->foto_ktp && file_exists(public_path($user->foto_ktp))) {
                @unlink(public_path($user->foto_ktp));
            }

            $file = $request->file('foto_ktp');
            $extension = $file->getClientOriginalExtension();
            $filename = 'ktp_' . time() . '_' . $request->nip . '.' . $extension;
            $uploadPath = public_path('uploads/register');

            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $file->move($uploadPath, $filename);
            $data['foto_ktp'] = 'uploads/register/' . $filename;
        }

        $user->update($data);

        return redirect()->route('pemohon.profile.show')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Show the change password form for pemohon.
     */
    public function editPassword()
    {
        return view('pemohon.profile.change-password');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Check current password
        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah.']);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('pemohon.profile.show')
            ->with('success', 'Password berhasil diubah.');
    }
}
