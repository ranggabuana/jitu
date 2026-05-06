<?php

namespace App\Mail;

use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountActivatedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    /**
     * Create a new message instance.
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = Setting::get('account_activated_subject', 'Akun Anda Telah Aktif');

        return new Envelope(
            subject: $subject . ' - ' . config('app.name'),
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
        $body = Setting::get('account_activated_content', 'Selamat! Akun Anda telah berhasil diverifikasi dan diaktifkan oleh admin. Sekarang Anda sudah dapat mengakses dashboard pemohon untuk mengajukan perizinan secara online.');
        
        // Replace placeholders
        $body = str_replace(
            ['{{userName}}', '{{appName}}'], 
            [$this->user->name, config('mail.from.name')], 
            $body
        );

        return new Content(
            view: 'emails.account-activated',
            with: [
                'userName' => $this->user->name,
                'loginUrl' => route('login'),
                'appName' => config('mail.from.name'),
                'bodyContent' => $body,
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
