<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Setting;
use App\Models\DataPerijinan;

class ApplicationStatusNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $application;
    public $status;
    public $notes;
    public $userName;
    public $appName;
    public $regNo;
    public $permitName;
    public $bodyContent;
    public $loginUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(DataPerijinan $application, string $status, ?string $notes = null)
    {
        $this->application = $application;
        $this->status = $status; // 'approved', 'rejected', 'returned'
        $this->notes = $notes ?? $application->catatan_perbaikan ?? $application->catatan_reject ?? '-';
        
        $this->userName = $application->user->name ?? 'Pemohon';
        $this->appName = Setting::get('mail_from_name', config('mail.from.name'));
        $this->regNo = $application->no_registrasi;
        $this->permitName = $application->perijinan->nama_perijinan ?? 'Perizinan';
        $this->loginUrl = route('pemohon.tracking.detail', $application->id);

        // Load template from settings
        $templateKey = "permit_{$status}_content";
        $subjectKey = "permit_{$status}_subject";
        
        $template = Setting::get($templateKey);
        $subject = Setting::get($subjectKey);

        // Fallbacks if not set
        if (!$template) {
            $fallbacks = [
                'approved' => 'Permohonan Anda telah disetujui.',
                'rejected' => 'Permohonan Anda ditolak.',
                'returned' => 'Permohonan Anda perlu perbaikan.',
            ];
            $template = $fallbacks[$status] ?? 'Status permohonan Anda telah berubah.';
        }

        if (!$subject) {
            $subjectFallbacks = [
                'approved' => 'Permohonan Izin Disetujui',
                'rejected' => 'Permohonan Izin Ditolak',
                'returned' => 'Perbaikan Berkas Permohonan Izin',
            ];
            $subject = $subjectFallbacks[$status] ?? 'Update Status Permohonan';
        }

        // Replace placeholders
        $this->bodyContent = str_replace(
            ['{{userName}}', '{{appName}}', '{{registrationNumber}}', '{{permitName}}', '{{notes}}'],
            [$this->userName, $this->appName, $this->regNo, $this->permitName, $this->notes],
            $template
        );

        $this->subject = str_replace(
            ['{{registrationNumber}}', '{{permitName}}'],
            [$this->regNo, $this->permitName],
            $subject
        );
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->view('emails.application-status')
                    ->subject($this->subject);
    }
}
