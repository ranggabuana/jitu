<?php

namespace App\Mail;

use App\Models\Setting;
use App\Models\DataPerijinan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicationStatusNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $application;
    public $status;
    public $notes;

    /**
     * Create a new message instance.
     */
    public function __construct(DataPerijinan $application, string $status, ?string $notes = null)
    {
        $this->application = $application;
        $this->status = $status; // 'approved', 'rejected', 'returned'
        $this->notes = $notes ?? $application->catatan_perbaikan ?? $application->catatan_reject ?? '-';
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subjectKey = "permit_{$this->status}_subject";
        $subject = Setting::get($subjectKey);

        if (!$subject) {
            $subjectFallbacks = [
                'approved' => 'Permohonan Izin Disetujui',
                'rejected' => 'Permohonan Izin Ditolak',
                'returned' => 'Perbaikan Berkas Permohonan Izin',
            ];
            $subject = $subjectFallbacks[$this->status] ?? 'Update Status Permohonan';
        }

        // Replace placeholders in subject
        $regNo = $this->application->no_registrasi;
        $permitName = $this->application->perijinan->nama_perijinan ?? 'Perizinan';
        
        $finalSubject = str_replace(
            ['{{registrationNumber}}', '{{permitName}}'],
            [$regNo, $permitName],
            $subject
        );

        return new Envelope(
            subject: $finalSubject . ' - ' . config('app.name'),
            from: new Address(
                config('mail.from.address'),
                config('mail.from.name')
            ),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $templateKey = "permit_{$this->status}_content";
        $template = Setting::get($templateKey);

        if (!$template) {
            $fallbacks = [
                'approved' => 'Permohonan Anda telah disetujui.',
                'rejected' => 'Permohonan Anda ditolak.',
                'returned' => 'Permohonan Anda perlu perbaikan.',
            ];
            $template = $fallbacks[$this->status] ?? 'Status permohonan Anda telah berubah.';
        }

        $userName = $this->application->user->name ?? 'Pemohon';
        $appName = config('mail.from.name');
        $regNo = $this->application->no_registrasi;
        $permitName = $this->application->perijinan->nama_perijinan ?? 'Perizinan';

        // Replace placeholders in body
        $bodyContent = str_replace(
            ['{{userName}}', '{{appName}}', '{{registrationNumber}}', '{{permitName}}', '{{notes}}'],
            [$userName, $appName, $regNo, $permitName, $this->notes],
            $template
        );

        return new Content(
            view: 'emails.application-status',
            with: [
                'userName' => $userName,
                'appName' => $appName,
                'bodyContent' => $bodyContent,
                'status' => $this->status,
                'regNo' => $regNo,
                'permitName' => $permitName,
                'loginUrl' => route('pemohon.tracking.detail', $this->application->id),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
