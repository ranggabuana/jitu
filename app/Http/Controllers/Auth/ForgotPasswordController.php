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
        return view('auth.forgot-password');
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        // Find user by email
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['Email tidak ditemukan di sistem kami.'],
            ]);
        }

        // Check if user has email
        if (!$user->email) {
            throw ValidationException::withMessages([
                'email' => ['User ini tidak memiliki alamat email. Silakan hubungi admin.'],
            ]);
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
            throw ValidationException::withMessages([
                'email' => ['Gagal mengirim email reset password. Silakan coba lagi.'],
            ]);
        }

        return back()->with('success', 'Link reset password telah dikirim ke email Anda. Silakan cek inbox email Anda.');
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
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
            'token' => 'required',
        ]);

        // Find reset record
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$resetRecord) {
            throw ValidationException::withMessages([
                'email' => ['Link reset password tidak valid.'],
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
                'email' => ['Link reset password sudah kedaluwarsa. Silakan minta link baru.'],
            ]);
        }

        // Verify token
        if (!Hash::check($request->token, $resetRecord->token)) {
            throw ValidationException::withMessages([
                'email' => ['Token reset password tidak valid.'],
            ]);
        }

        // Find user and update password
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['User tidak ditemukan.'],
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
