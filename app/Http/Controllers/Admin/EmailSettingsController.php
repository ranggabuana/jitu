<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ForgotPasswordRequest; // Using an existing mailable for testing
use App\Services\EmailService;
use Exception;

class EmailSettingsController extends Controller
{
    /**
     * Display email settings form.
     */
    public function index()
    {
        $settings = Setting::where('group', 'email')->get()->pluck('value', 'key');

        return view('settings.email', compact('settings'));
    }

    /**
     * Update email settings.
     */
    public function update(Request $request)
    {
        $request->validate([
            'mail_host' => 'required|string',
            'mail_port' => 'required|numeric',
            'mail_username' => 'nullable|string',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'nullable|string|in:tls,ssl,null',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string',
        ]);

        $oldData = Setting::where('group', 'email')->get()->pluck('value', 'key')->toArray();

        $keys = [
            'mail_host',
            'mail_port',
            'mail_username',
            'mail_password',
            'mail_encryption',
            'mail_from_address',
            'mail_from_name'
        ];

        foreach ($keys as $key) {
            $label = str_replace('_', ' ', ucfirst($key));
            Setting::set($key, $request->input($key), 'text', 'email', $label);
        }

        $newData = $request->only($keys);

        // Log activity
        ActivityLog::log(
            'Mengupdate pengaturan email SMTP',
            Setting::where('group', 'email')->first() ?? new Setting(),
            'updated',
            ['old' => $oldData, 'new' => $newData],
            'settings'
        );

        return redirect()->route('settings.email')
            ->with('success', 'Pengaturan email berhasil disimpan.');
    }

    /**
     * Test email connection.
     */
    public function testConnection(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);

        try {
            // Data is already bound in AppServiceProvider, but let's ensure fresh config
            // for the test if it was just saved in the same request (not the case here usually)

            $toEmail = $request->test_email;
            $user = auth()->user();

            // We'll use a simple callback to test sending
            Mail::raw('Ini adalah email uji coba untuk verifikasi konfigurasi SMTP di aplikasi Dawet Ayu Banjarnegara.', function ($message) use ($toEmail) {
                $message->to($toEmail)
                    ->subject('Tes Koneksi Email - ' . config('app.name'));
            });

            return response()->json([
                'success' => true,
                'message' => 'Koneksi berhasil! Email uji coba telah dikirim ke ' . $toEmail
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghubungkan ke server email: ' . $e->getMessage()
            ], 500);
        }
    }
}
