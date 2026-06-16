<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'kswp' => [
        'email' => env('KSWP_API_EMAIL', 'perijinan_terpadu@banjarnegarakab.go.id'),
        'password' => env('KSWP_API_PASSWORD', '@Kswp2025'),
        'base_url' => env('KSWP_API_BASE_URL', 'https://isismiop.banjarnegarakab.go.id/api'),
        'verify_ssl' => env('KSWP_API_VERIFY_SSL', true),
    ],

    'esign' => [
        'url' => env('ESIGN_API_URL', 'http://103.110.4.69/api/v2'),
        'username' => env('ESIGN_USERNAME', 'jitu2026'),
        'password' => env('ESIGN_PASSWORD', 'jitu2026'),
    ],

];
