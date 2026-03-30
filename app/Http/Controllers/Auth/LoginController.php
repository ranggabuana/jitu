<?php

namespace App\Http\Controllers\Auth;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show the login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        // Redirect if already authenticated
        if (auth()->check()) {
            return redirect()->intended('/dashboard');
        }
        
        return view('auth.login');
    }

    /**
     * Handle a login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Find user by username
        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'username' => ['Username atau password salah.'],
            ]);
        }

        // Check if user status is not active
        if ($user->status !== 'aktif') {
            throw ValidationException::withMessages([
                'username' => ['Akun Anda belum diaktifkan. Silakan hubungi admin untuk aktivasi.'],
            ]);
        }

        // Attempt to log in the user
        Auth::login($user, $request->filled('remember'));
        $request->session()->regenerate();

        // Log activity
        ActivityLog::log(
            'User login',
            $user,
            'login',
            [
                'username' => $user->username,
                'ip_address' => $request->ip()
            ],
            'auth'
        );

        // Set flash message for successful login
        $request->session()->flash('success', 'Login berhasil! Selamat datang, ' . $user->name . '.');

        // Redirect based on role
        if ($user->role === 'pemohon') {
            return redirect()->intended(route('pemohon.dashboard'));
        }

        return redirect()->intended('/dashboard');
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        // Log activity before logout
        if ($user) {
            ActivityLog::log(
                'User logout',
                $user,
                'logout',
                [
                    'username' => $user->username,
                    'ip_address' => $request->ip()
                ],
                'auth'
            );
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Set flash message for successful logout
        $request->session()->flash('success', 'You have been successfully logged out.');

        return redirect('/login');
    }
}
