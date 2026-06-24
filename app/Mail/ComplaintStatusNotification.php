<?php

namespace App\Mail;

use App\Models\Setting;
use App\Models\Pengaduan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ComplaintStatusNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $pengaduan;
    public $notes;

    /**
     * Create a new message instance.
     */
    public function __construct(Pengaduan $pengaduan)
    {
        $this->pengaduan = $pengaduan;
        $this->notes = $pengaduan->respon ?? '-';
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = Setting::get('complaint_status_changed_subject');

        if (!$subject) {
            $subject = 'Pemberitahuan: Status Pengaduan Anda Telah Diperbarui';
        }

        // Replace placeholders in subject
        $noPengaduan = $this->pengaduan->no_pengaduan;
        
        $finalSubject = str_replace(
            ['{{registrationNumber}}'],
            [$noPengaduan],
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
        $template = Setting::get('complaint_status_changed_content');

        if (!$template) {
            $template = 'Halo {{userName}}, pengaduan Anda dengan rincian berikut: "{{complaintDetail}}" kini memiliki status baru: "{{complaintStatus}}". Berikut tanggapan/catatan dari petugas: "{{complaintResponse}}". Terima kasih.';
        }

        $userName = $this->pengaduan->nama ?? 'Pengadu';
        $appName = config('mail.from.name');
        $noPengaduan = $this->pengaduan->no_pengaduan;
        $complaintDetail = $this->pengaduan->isi_pengaduan ?? '-';
        $complaintStatus = $this->pengaduan->status_label ?? ucfirst($this->pengaduan->status);
        $complaintResponse = $this->pengaduan->respon ?? '-';

        // Replace placeholders in body
        $bodyContent = str_replace(
            ['{{userName}}', '{{appName}}', '{{registrationNumber}}', '{{complaintDetail}}', '{{complaintStatus}}', '{{complaintResponse}}'],
            [$userName, $appName, $noPengaduan, $complaintDetail, $complaintStatus, $complaintResponse],
            $template
        );

        return new Content(
            view: 'emails.complaint-status',
            with: [
                'userName' => $userName,
                'appName' => $appName,
                'bodyContent' => $bodyContent,
                'status' => $this->pengaduan->status,
                'noPengaduan' => $noPengaduan,
                'complaintDetail' => $complaintDetail,
                'complaintStatus' => $complaintStatus,
                'complaintResponse' => $complaintResponse,
                'loginUrl' => url('/'),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
