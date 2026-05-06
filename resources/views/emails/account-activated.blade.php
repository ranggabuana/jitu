<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aktivasi Akun Berhasil</title>
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
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
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
            color: #d1fae5;
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
            background-color: #059669;
            color: #ffffff !important;
            text-decoration: none;
            padding: 16px 32px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            display: inline-block;
            box-shadow: 0 10px 15px -3px rgba(5, 150, 105, 0.3);
        }

        /* Info Box */
        .info-box {
            background-color: #f0fdf4;
            border-left: 4px solid #059669;
            padding: 20px;
            border-radius: 0 8px 8px 0;
            margin-bottom: 24px;
        }
        .info-box-text {
            font-size: 14px;
            color: #166534;
            margin: 0;
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
    </style>
</head>
<body>
    <div class="wrapper">
        <table class="main" align="center">
            <tr>
                <td>
                    <!-- Header -->
                    <div class="header">
                        <h1>Aktivasi Akun Berhasil</h1>
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
                            <a href="{{ $loginUrl }}" class="button">
                                Masuk ke Dashboard
                            </a>
                        </div>

                        <!-- Info Box -->
                        <div class="info-box">
                            <p class="info-box-text">
                                Silakan gunakan username dan password yang telah Anda daftarkan sebelumnya untuk masuk ke sistem.
                            </p>
                        </div>

                        <div class="text" style="font-size: 14px; margin-top: 32px;">
                            Jika Anda mengalami kendala saat masuk, silakan hubungi tim bantuan kami melalui informasi kontak yang tertera di website.
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="footer">
                        <div class="footer-logo">{{ $appName }}</div>
                        <p>Sistem Informasi Perijinan Terpadu</p>
                        <p>&copy; {{ date('Y') }} Pemerintah Kabupaten Banjarnegara. All rights reserved.</p>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>