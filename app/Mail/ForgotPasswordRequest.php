<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Setting;

class ForgotPasswordRequest extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $resetUrl;
    public $expiryMinutes;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $resetUrl, $expiryMinutes)
    {
        $this->user = $user;
        $this->resetUrl = $resetUrl;
        $this->expiryMinutes = $expiryMinutes;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = Setting::get('forgot_password_subject', 'Permintaan Reset Password');

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
        $body = Setting::get('forgot_password_content', 'Kami menerima permintaan untuk melakukan pengaturan ulang kata sandi (reset password) pada akun Anda. Klik tombol di bawah ini untuk melanjutkan proses:');
        
        $appName = config('mail.from.name');

        // Replace placeholders
        $body = str_replace(
            ['{{userName}}', '{{appName}}'], 
            [$this->user->name, $appName], 
            $body
        );

        return new Content(
            view: 'emails.forgot-password',
            with: [
                'userName' => $this->user->name,
                'resetUrl' => $this->resetUrl,
                'expiryMinutes' => $this->expiryMinutes,
                'appName' => $appName,
                'bodyContent' => $body,
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
