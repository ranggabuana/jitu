<?php

namespace App\Http\Controllers\Auth;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

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

        // Generate CAPTCHA
        session([
            'login_num1' => rand(1, 10),
            'login_num2' => rand(1, 10),
        ]);
        
        return view('auth.login');
    }

    /**
     * Refresh CAPTCHA for login.
     */
    public function refreshCaptcha()
    {
        session([
            'login_num1' => rand(1, 10),
            'login_num2' => rand(1, 10),
        ]);

        return response()->json([
            'num1' => session('login_num1'),
            'num2' => session('login_num2'),
        ]);
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
        // Validate CAPTCHA first
        $request->validate([
            'captcha' => 'required|numeric',
        ], [
            'captcha.required' => 'Silakan masukkan hasil penjumlahan.',
            'captcha.numeric' => 'Hasil penjumlahan harus berupa angka.',
        ]);

        // Check CAPTCHA
        $captchaAnswer = ($request->session()->get('login_num1', 0) + $request->session()->get('login_num2', 0));
        if ($request->captcha != $captchaAnswer) {
            return redirect()->back()
                ->withInput()
                ->with('captcha_error', 'Hasil penjumlahan CAPTCHA salah. Silakan coba lagi.');
        }

        // Clear CAPTCHA after verification
        session()->forget(['login_num1', 'login_num2']);

        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $throttleKey = Str::transliterate(Str::lower($request->input('username')).'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'username' => ['Terlalu banyak percobaan login. Silakan coba lagi dalam '.$seconds.' detik.'],
            ]);
        }

        // Find user by username
        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            RateLimiter::hit($throttleKey, 60);

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

        RateLimiter::clear($throttleKey);

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
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
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
