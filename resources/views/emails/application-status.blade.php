<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemberitahuan Status Permohonan</title>
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
        .header.approved { background: linear-gradient(135deg, #059669 0%, #10b981 100%); }
        .header.rejected { background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%); }
        .header.returned { background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%); }

        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.025em;
        }
        .header p {
            color: rgba(255, 255, 255, 0.8);
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

        /* Details Box */
        .details-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 24px;
        }
        .detail-item {
            margin-bottom: 12px;
            display: block;
        }
        .detail-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            font-weight: 700;
            margin-bottom: 4px;
            display: block;
        }
        .detail-value {
            font-size: 15px;
            color: #1e293b;
            font-weight: 600;
            display: block;
        }

        /* Button */
        .button-container {
            text-align: center;
            margin: 32px 0;
        }
        .button {
            background-color: #1e293b;
            color: #ffffff !important;
            text-decoration: none;
            padding: 16px 32px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            display: inline-block;
            box-shadow: 0 10px 15px -3px rgba(30, 41, 59, 0.3);
        }
        .button.approved { background-color: #059669; box-shadow: 0 10px 15px -3px rgba(5, 150, 105, 0.3); }
        .button.rejected { background-color: #dc2626; box-shadow: 0 10px 15px -3px rgba(220, 38, 38, 0.3); }
        .button.returned { background-color: #d97706; box-shadow: 0 10px 15px -3px rgba(217, 119, 6, 0.3); }

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
    @php
        $headerClass = '';
        if (strpos($status, 'approved') !== false) $headerClass = 'approved';
        elseif (strpos($status, 'rejected') !== false) $headerClass = 'rejected';
        elseif (strpos($status, 'returned') !== false) $headerClass = 'returned';
    @endphp
    <div class="wrapper">
        <table class="main" align="center">
            <tr>
                <td>
                    <!-- Header -->
                    <div class="header {{ $headerClass }}">
                        <h1>Status Permohonan</h1>
                        <p>{{ $appName }}</p>
                    </div>

                    <!-- Body Content -->
                    <div class="content">
                        <div class="greeting">Halo, {{ $userName }}</div>
                        <div class="text">
                            {!! nl2br(e($bodyContent)) !!}
                        </div>

                        <!-- Details -->
                        <div class="details-box">
                            <span class="detail-item">
                                <span class="detail-label">Nomor Registrasi</span>
                                <span class="detail-value">{{ $regNo }}</span>
                            </span>
                            <span class="detail-item">
                                <span class="detail-label">Jenis Perizinan</span>
                                <span class="detail-value">{{ $permitName }}</span>
                            </span>
                        </div>

                        <!-- CTA Button -->
                        <div class="button-container">
                            <a href="{{ $loginUrl }}" class="button {{ $headerClass }}">
                                Lihat Detail Pengajuan
                            </a>
                        </div>

                        <div class="text" style="font-size: 14px; margin-top: 32px;">
                            Jika Anda memiliki pertanyaan lebih lanjut, silakan hubungi tim bantuan kami melalui layanan pengaduan yang tersedia di website.
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
