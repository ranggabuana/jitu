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

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'nip' => 'nullable|string|max:50',
            'no_hp' => 'nullable|string|max:20',
        ]);

        $user->update($validated);

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
