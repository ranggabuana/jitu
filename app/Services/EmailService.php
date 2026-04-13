<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Mail\Mailable;

class EmailService
{
    /**
     * Send an email with error handling and logging.
     *
     * @param string $to Recipient email address
     * @param string $name Recipient name
     * @param Mailable $mailable The mailable instance
     * @param string|null $from Sender email (optional, uses config default)
     * @param string|null $fromName Sender name (optional, uses config default)
     * @return bool Whether the email was sent successfully
     */
    public static function send(
        string $to,
        string $name,
        Mailable $mailable,
        ?string $from = null,
        ?string $fromName = null
    ): bool {
        try {
            $mail = Mail::to($to, $name);

            // Optional: set custom from address
            if ($from) {
                $mail = $mail->from($from, $fromName ?? config('mail.from.name'));
            }

            $mail->send($mailable);

            Log::info('Email sent successfully', [
                'to' => $to,
                'name' => $name,
                'subject' => $mailable->subject ?? 'N/A',
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send email', [
                'to' => $to,
                'name' => $name,
                'subject' => $mailable->subject ?? 'N/A',
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Send email to multiple recipients.
     *
     * @param array $recipients Array of ['email' => 'name'] pairs
     * @param Mailable $mailable The mailable instance
     * @return int Number of successful sends
     */
    public static function sendBulk(array $recipients, Mailable $mailable): int
    {
        $successCount = 0;

        foreach ($recipients as $email => $name) {
            if (self::send($email, is_numeric($email) ? $name : $email, $mailable)) {
                $successCount++;
            }
        }

        return $successCount;
    }

    /**
     * Queue an email for sending (async).
     *
     * @param string $to Recipient email address
     * @param string $name Recipient name
     * @param Mailable $mailable The mailable instance
     * @param string|null $from Sender email (optional)
     * @param string|null $fromName Sender name (optional)
     * @return bool Whether the email was queued successfully
     */
    public static function queue(
        string $to,
        string $name,
        Mailable $mailable,
        ?string $from = null,
        ?string $fromName = null
    ): bool {
        try {
            $mail = Mail::to($to, $name);

            if ($from) {
                $mail = $mail->from($from, $fromName ?? config('mail.from.name'));
            }

            $mail->queue($mailable);

            Log::info('Email queued successfully', [
                'to' => $to,
                'name' => $name,
                'subject' => $mailable->subject ?? 'N/A',
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to queue email', [
                'to' => $to,
                'name' => $name,
                'subject' => $mailable->subject ?? 'N/A',
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
