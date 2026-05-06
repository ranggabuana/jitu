<?php

namespace App\Mail;

use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class ForgotPasswordRequest extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $resetUrl;
    public $expiryMinutes;

    /**
     * Create a new message instance.
     *
     * @param mixed $user The user requesting password reset
     * @param string $resetUrl The password reset URL
     * @param int $expiryMinutes Token expiry time in minutes
     */
    public function __construct($user, string $resetUrl, int $expiryMinutes = 60)
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
        
        // Replace placeholders
        $body = str_replace(
            ['{{userName}}', '{{appName}}'], 
            [$this->user->name, config('mail.from.name')], 
            $body
        );

        return new Content(
            view: 'emails.forgot-password',
            with: [
                'userName' => $this->user->name,
                'resetUrl' => $this->resetUrl,
                'expiryMinutes' => $this->expiryMinutes,
                'appName' => config('mail.from.name'),
                'bodyContent' => $body,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
