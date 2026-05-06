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
        $smtpSettings = Setting::where('group', 'email')->get()->pluck('value', 'key');
        $templateSettings = Setting::where('group', 'email_templates')->get()->pluck('value', 'key');
        
        return view('settings.email', compact('smtpSettings', 'templateSettings'));
    }

    /**
     * Update email settings.
     */
    public function update(Request $request)
    {
        $request->validate([
            // SMTP Settings
            'mail_host' => 'required_if:type,smtp|string',
            'mail_port' => 'required_if:type,smtp|numeric',
            'mail_username' => 'nullable|string',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'nullable|string|in:tls,ssl,null',
            'mail_from_address' => 'required_if:type,smtp|email',
            'mail_from_name' => 'required_if:type,smtp|string',

            // Template Settings
            'forgot_password_subject' => 'required_if:type,templates|string|max:255',
            'forgot_password_content' => 'required_if:type,templates|string',
            'account_activated_subject' => 'required_if:type,templates|string|max:255',
            'account_activated_content' => 'required_if:type,templates|string',
        ]);

        $type = $request->input('type', 'smtp');
        
        if ($type === 'smtp') {
            $keys = ['mail_host', 'mail_port', 'mail_username', 'mail_password', 'mail_encryption', 'mail_from_address', 'mail_from_name'];
            $group = 'email';
            $logMsg = 'Mengupdate konfigurasi server SMTP';
        } else {
            $keys = ['forgot_password_subject', 'forgot_password_content', 'account_activated_subject', 'account_activated_content'];
            $group = 'email_templates';
            $logMsg = 'Mengupdate template isi email';
        }

        $oldData = Setting::where('group', $group)->get()->pluck('value', 'key')->toArray();

        foreach ($keys as $key) {
            if ($request->has($key)) {
                $label = str_replace('_', ' ', ucfirst($key));
                Setting::set($key, $request->input($key), 'text', $group, $label);
            }
        }

        $newData = $request->only($keys);

        // Log activity
        ActivityLog::log(
            $logMsg,
            Setting::where('group', $group)->first() ?? new Setting(),
            'updated',
            ['old' => $oldData, 'new' => $newData],
            'settings'
        );

        return redirect()->route('settings.email')
            ->with('success', 'Pengaturan berhasil disimpan.');
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
