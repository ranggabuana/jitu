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
            'permit_approved_subject' => 'required_if:type,templates|string|max:255',
            'permit_approved_content' => 'required_if:type,templates|string',
            'permit_rejected_subject' => 'required_if:type,templates|string|max:255',
            'permit_rejected_content' => 'required_if:type,templates|string',
            'permit_returned_subject' => 'required_if:type,templates|string|max:255',
            'permit_returned_content' => 'required_if:type,templates|string',
            'complaint_status_changed_subject' => 'required_if:type,templates|string|max:255',
            'complaint_status_changed_content' => 'required_if:type,templates|string',
        ]);

        $type = $request->input('type', 'smtp');
        
        if ($type === 'smtp') {
            $keys = ['mail_host', 'mail_port', 'mail_username', 'mail_password', 'mail_encryption', 'mail_from_address', 'mail_from_name'];
            $group = 'email';
            $logMsg = 'Mengupdate konfigurasi server SMTP';
        } else {
            $keys = [
                'forgot_password_subject', 'forgot_password_content', 
                'account_activated_subject', 'account_activated_content',
                'permit_approved_subject', 'permit_approved_content',
                'permit_rejected_subject', 'permit_rejected_content',
                'permit_returned_subject', 'permit_returned_content',
                'complaint_status_changed_subject', 'complaint_status_changed_content',
            ];
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

    /**
     * Preview email template.
     */
    public function preview(Request $request)
    {
        $request->validate([
            'type' => 'required|string|in:forgot_password,account_activated,permit_approved,permit_rejected,permit_returned,complaint_status_changed',
            'content' => 'required|string',
        ]);

        $type = $request->type;
        $content = $request->content;
        $userName = auth()->user()->name ?? 'Budi Santoso';
        $appName = Setting::get('mail_from_name', config('mail.from.name'));
        $regNo = 'REG-' . date('Ymd') . '-001';
        $permitName = 'Izin Mendirikan Bangunan (IMB)';
        $notes = 'Data KTP kurang jelas, mohon upload ulang pindaian asli.';

        // Complaint specific placeholders
        $complaintDetail = 'Lampu penerangan jalan di Jalan Pemuda mati sejak 3 hari yang lalu.';
        $complaintStatus = 'Dalam Proses';
        $complaintResponse = 'Petugas teknis akan segera meluncur ke lokasi untuk perbaikan malam ini.';

        // Replace placeholders
        $bodyContent = str_replace(
            [
                '{{userName}}', '{{appName}}', '{{registrationNumber}}', '{{permitName}}', '{{notes}}',
                '{{complaintDetail}}', '{{complaintStatus}}', '{{complaintResponse}}'
            ],
            [
                $userName, $appName, $regNo, $permitName, $notes,
                $complaintDetail, $complaintStatus, $complaintResponse
            ],
            $content
        );

        if ($type === 'forgot_password') {
            return view('emails.forgot-password', [
                'userName' => $userName,
                'resetUrl' => 'javascript:void(0)',
                'expiryMinutes' => 60,
                'appName' => $appName,
                'bodyContent' => $bodyContent,
            ]);
        } elseif ($type === 'account_activated') {
            return view('emails.account-activated', [
                'userName' => $userName,
                'loginUrl' => 'javascript:void(0)',
                'appName' => $appName,
                'bodyContent' => $bodyContent,
            ]);
        } elseif ($type === 'complaint_status_changed') {
            return view('emails.complaint-status', [
                'userName' => $userName,
                'appName' => $appName,
                'bodyContent' => $bodyContent,
                'status' => 'proses',
                'noPengaduan' => 'PENG-' . date('Ymd') . '-A1B2C',
                'complaintDetail' => $complaintDetail,
                'complaintStatus' => $complaintStatus,
                'complaintResponse' => $complaintResponse,
            ]);
        } else {
            // New template types use a generic view
            return view('emails.application-status', [
                'userName' => $userName,
                'appName' => $appName,
                'bodyContent' => $bodyContent,
                'status' => $type,
                'regNo' => $regNo,
                'permitName' => $permitName,
                'loginUrl' => 'javascript:void(0)',
            ]);
        }
    }
}
