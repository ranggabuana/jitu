<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Mail\Mailable;

class EmailService
{
    /**
     * Send an email immediately (synchronous).
     */
    public static function send(string $to, string $name, Mailable $mailable): bool
    {
        try {
            Mail::to($to, $name)->send($mailable);

            Log::info('Email sent successfully', [
                'to' => $to,
                'name' => $name,
                'subject' => $mailable->envelope()->subject ?? 'N/A',
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send email', [
                'to' => $to,
                'name' => $name,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }

    /**
     * Queue an email for background sending (asynchronous).
     */
    public static function queue(string $to, string $name, Mailable $mailable): bool
    {
        try {
            Mail::to($to, $name)->queue($mailable);

            Log::info('Email queued successfully', [
                'to' => $to,
                'name' => $name,
                'subject' => $mailable->envelope()->subject ?? 'N/A',
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to queue email', [
                'to' => $to,
                'name' => $name,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
