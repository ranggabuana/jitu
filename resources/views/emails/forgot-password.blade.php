<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .email-header {
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0 0 10px 0;
            font-size: 28px;
            font-weight: 700;
        }
        .email-header p {
            margin: 0;
            font-size: 16px;
            opacity: 0.9;
        }
        .email-body {
            padding: 40px 30px;
        }
        .email-body p {
            color: #374151;
            line-height: 1.6;
            margin: 0 0 20px 0;
        }
        .email-body .user-name {
            font-weight: 600;
            color: #1f2937;
        }
        .cta-button {
            display: inline-block;
            padding: 16px 40px;
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            margin: 20px 0;
            text-align: center;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
            transition: all 0.3s ease;
        }
        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
        }
        .info-box {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .info-box p {
            margin: 0;
            color: #92400e;
            font-size: 14px;
        }
        .info-box strong {
            color: #78350f;
        }
        .email-footer {
            background-color: #f9fafb;
            padding: 30px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
        }
        .email-footer p {
            color: #6b7280;
            font-size: 13px;
            margin: 0 0 10px 0;
        }
        .email-footer .app-name {
            font-weight: 600;
            color: #374151;
        }
        .warning-text {
            color: #dc2626;
            font-size: 13px;
            margin-top: 20px;
            padding: 15px;
            background-color: #fee2e2;
            border-radius: 8px;
            border-left: 4px solid #dc2626;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Email Header -->
        <div class="email-header">
            <h1>🔐 Reset Password</h1>
            <p>{{ $appName }}</p>
        </div>

        <!-- Email Body -->
        <div class="email-body">
            <p>Halo <span class="user-name">{{ $userName }}</span>,</p>

            <p>Kami menerima permintaan untuk mereset password akun Anda. Jika Anda yang melakukan permintaan ini, klik tombol di bawah untuk mereset password Anda:</p>

            <!-- CTA Button -->
            <div style="text-align: center;">
                <a href="{{ $resetUrl }}" class="cta-button">
                    Reset Password Saya
                </a>
            </div>

            <!-- Info Box -->
            <div class="info-box">
                <p>
                    <strong>⏰ Informasi Penting:</strong><br>
                    Link reset password ini hanya berlaku selama <strong>{{ $expiryMinutes }} menit</strong> 
                    dari permintaan ini. Setelah itu, Anda perlu melakukan permintaan ulang.
                </p>
            </div>

            <p style="margin-top: 20px;">
                Jika tombol tidak berfungsi, Anda dapat menyalin dan menempelkan URL berikut ke browser Anda:
            </p>
            <p style="word-break: break-all; color: #3b82f6; font-size: 13px; background: #f3f4f6; padding: 10px; border-radius: 6px;">
                {{ $resetUrl }}
            </p>

            <!-- Warning -->
            <div class="warning-text">
                <strong>⚠️ Keamanan:</strong> Jika Anda tidak meminta reset password, abaikan email ini. 
                Password Anda akan tetap aman. Jangan pernah membagikan email ini kepada siapapun.
            </div>
        </div>

        <!-- Email Footer -->
        <div class="email-footer">
            <p>Email ini dikirim secara otomatis oleh <span class="app-name">{{ $appName }}</span></p>
            <p>Mohon jangan membalas email ini. Jika Anda membutuhkan bantuan, hubungi tim support kami.</p>
            <p style="margin-top: 15px; font-size: 12px; color: #9ca3af;">
                © {{ date('Y') }} {{ $appName }}. Hak cipta dilindungi undang-undang.
            </p>
        </div>
    </div>
</body>
</html>
