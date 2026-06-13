<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Mail\ForgotPasswordRequest;
use App\Services\EmailService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ForgotPasswordController extends Controller
{
    /**
     * Show the forgot password form.
     *
     * @return \Illuminate\View\View
     */
    public function showForgotPasswordForm()
    {
        // Generate CAPTCHA
        session([
            'forgot_num1' => rand(1, 10),
            'forgot_num2' => rand(1, 10),
        ]);

        return view('auth.forgot-password');
    }

    /**
     * Refresh CAPTCHA for forgot password.
     */
    public function refreshCaptcha()
    {
        session([
            'forgot_num1' => rand(1, 10),
            'forgot_num2' => rand(1, 10),
        ]);

        return response()->json([
            'num1' => session('forgot_num1'),
            'num2' => session('forgot_num2'),
        ]);
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendResetLink(Request $request)
    {
        // Validate CAPTCHA first
        $request->validate([
            'captcha' => 'required|numeric',
        ], [
            'captcha.required' => 'Silakan masukkan hasil penjumlahan.',
            'captcha.numeric' => 'Hasil penjumlahan harus berupa angka.',
        ]);

        // Check CAPTCHA
        $captchaAnswer = ($request->session()->get('forgot_num1', 0) + $request->session()->get('forgot_num2', 0));
        if ($request->captcha != $captchaAnswer) {
            return redirect()->back()
                ->withInput()
                ->with('captcha_error', 'Hasil penjumlahan CAPTCHA salah. Silakan coba lagi.');
        }

        // Clear CAPTCHA after verification
        session()->forget(['forgot_num1', 'forgot_num2']);

        $request->validate([
            'email' => 'required|email',
        ]);

        // Find user by email
        $user = User::where('email', $request->email)->first();

        // Generic message to prevent user enumeration
        $successMessage = 'Jika alamat email tersebut terdaftar di sistem kami, Anda akan segera menerima link untuk mereset password.';

        if (!$user) {
            return back()->with('success', $successMessage);
        }

        // Check if user has email (safety check, though we searched by email)
        if (!$user->email) {
            return back()->with('success', $successMessage);
        }

        // Generate reset token
        $token = Str::random(64);
        
        // Store token in password_reset_tokens table
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        // Create reset URL
        $resetUrl = route('password.reset.form', ['token' => $token, 'email' => $user->email]);

        // Send email using EmailService
        $emailSent = EmailService::send(
            $user->email,
            $user->name,
            new ForgotPasswordRequest($user, $resetUrl, 60)
        );

        if (!$emailSent) {
            // Keep it generic even on failure if we want to be safe, 
            // but usually email sending failure is okay to report as error 
            // as long as it doesn't confirm existence. 
            // However, if we are here, we know the user exists.
            // Let's keep a generic error if it fails to send.
            return back()->with('error', 'Gagal mengirim email reset password. Silakan coba lagi beberapa saat lagi.');
        }

        return back()->with('success', $successMessage);
    }

    /**
     * Show the reset password form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function showResetForm(Request $request)
    {
        $token = $request->route('token');
        $email = $request->email;

        // Verify token exists and is not expired
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$resetRecord) {
            return redirect()->route('password.request')
                ->with('error', 'Link reset password tidak valid atau sudah kedaluwarsa.');
        }

        // Check if token is expired (60 minutes)
        $createdAt = strtotime($resetRecord->created_at);
        $expiryTime = $createdAt + (60 * 60); // 60 minutes
        $currentTime = time();

        if ($currentTime > $expiryTime) {
            // Delete expired token
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            
            return redirect()->route('password.request')
                ->with('error', 'Link reset password sudah kedaluwarsa. Silakan minta link baru.');
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
            'token' => 'required',
        ]);

        // Find reset record
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$resetRecord) {
            throw ValidationException::withMessages([
                'email' => ['Link reset password tidak valid atau sudah kedaluwarsa.'],
            ]);
        }

        // Check if token is expired
        $createdAt = strtotime($resetRecord->created_at);
        $expiryTime = $createdAt + (60 * 60); // 60 minutes
        $currentTime = time();

        if ($currentTime > $expiryTime) {
            // Delete expired token
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            
            throw ValidationException::withMessages([
                'email' => ['Link reset password tidak valid atau sudah kedaluwarsa.'],
            ]);
        }

        // Verify token
        if (!Hash::check($request->token, $resetRecord->token)) {
            throw ValidationException::withMessages([
                'email' => ['Link reset password tidak valid atau sudah kedaluwarsa.'],
            ]);
        }

        // Find user and update password
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['Gagal memproses permintaan Anda. Silakan hubungi administrator.'],
            ]);
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete used token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')
            ->with('success', 'Password berhasil direset. Silakan login dengan password baru Anda.');
    }
}
