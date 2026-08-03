<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permintaan Reset Password</title>
    <style>
        /* Base Styles */
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f8fafc;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }
        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #f8fafc;
            padding-top: 40px;
            padding-bottom: 40px;
        }
        .main {
            background-color: #ffffff;
            margin: 0 auto;
            width: 100%;
            max-width: 600px;
            border-spacing: 0;
            color: #334155;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.025em;
        }
        .header p {
            color: #94a3b8;
            margin: 8px 0 0;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* Body */
        .content {
            padding: 40px 30px;
            line-height: 1.6;
        }
        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 16px;
        }
        .text {
            font-size: 16px;
            color: #475569;
            margin-bottom: 24px;
        }

        /* Button */
        .button-container {
            text-align: center;
            margin: 32px 0;
        }
        .button {
            background-color: #2563eb;
            color: #ffffff !important;
            text-decoration: none;
            padding: 16px 32px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            display: inline-block;
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3);
        }

        /* Alert/Info Box */
        .info-box {
            background-color: #f1f5f9;
            border-left: 4px solid #64748b;
            padding: 20px;
            border-radius: 0 8px 8px 0;
            margin-bottom: 24px;
        }
        .info-box-title {
            display: block;
            font-weight: 600;
            color: #334155;
            margin-bottom: 4px;
            font-size: 14px;
        }
        .info-box-text {
            font-size: 14px;
            color: #64748b;
            margin: 0;
        }

        /* Direct Link */
        .direct-link {
            font-size: 12px;
            color: #94a3b8;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid #f1f5f9;
        }
        .url-box {
            word-break: break-all;
            color: #2563eb;
            background-color: #f8fafc;
            padding: 12px;
            border-radius: 6px;
            margin-top: 8px;
            display: block;
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 30px;
            font-size: 13px;
            color: #64748b;
        }
        .footer-logo {
            font-weight: 700;
            color: #334155;
            margin-bottom: 8px;
        }
        .security-note {
            background-color: #fff1f2;
            color: #be123c;
            padding: 12px;
            border-radius: 8px;
            font-size: 12px;
            margin-top: 20px;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <table class="main" align="center">
            <tr>
                <td>
                    <!-- Header -->
                    <div class="header">
                        <h1>Keamanan Akun</h1>
                        <p>{{ $appName }}</p>
                    </div>

                    <!-- Body Content -->
                    <div class="content">
                        <div class="greeting">Halo, {{ $userName }}</div>
                        <div class="text">
                            {!! nl2br(e($bodyContent)) !!}
                        </div>

                        <!-- CTA Button -->
                        <div class="button-container">
                            <a href="{{ $resetUrl }}" class="button">
                                Reset Kata Sandi
                            </a>
                        </div>

                        <!-- Info Box -->
                        <div class="info-box">
                            <span class="info-box-title">⏱️ Batas Waktu</span>
                            <p class="info-box-text">
                                Link ini hanya berlaku selama <strong>{{ $expiryMinutes }} menit</strong>. Jika Anda tidak melakukan perubahan dalam waktu tersebut, Anda harus mengajukan permintaan ulang.
                            </p>
                        </div>

                        <!-- Direct Link -->
                        <div class="direct-link">
                            Jika tombol di atas tidak berfungsi, silakan salin dan tempel URL berikut ke peramban (browser) Anda:
                            <span class="url-box">{{ $resetUrl }}</span>
                        </div>

                        <!-- Security Note -->
                        <div class="security-note">
                            <strong>⚠️ Keamanan:</strong> Jika Anda tidak merasa melakukan permintaan ini, abaikan email ini. Akun Anda tetap aman selama Anda tidak membagikan link di atas kepada siapapun.
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="footer">
                        <div class="footer-logo">{{ $appName }}</div>
                        <p>Jaringan Informasi Terpadu</p>
                        <p>&copy; {{ date('Y') }} Pemerintah Kabupaten Banjarnegara. All rights reserved.</p>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>