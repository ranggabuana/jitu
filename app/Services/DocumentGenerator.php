<?php

namespace App\Services;

use App\Models\Perijinan;
use App\Models\DataPerijinan;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DocumentGenerator
{
    /**
     * Generate the three required documents for an application.
     *
     * @param DataPerijinan $application
     * @return array Array of paths for [pernyataan, permohonan, keabsahan]
     */
    public static function generateDocuments(DataPerijinan $application): array
    {
        $perijinan = $application->perijinan;
        $user = $application->user;

        if (!$perijinan || !$user) {
            \Log::error('DocumentGenerator: Application is missing perijinan or user relations.', ['id' => $application->id]);
            return [
                'file_pernyataan' => null,
                'file_permohonan' => null,
                'file_keabsahan' => null,
            ];
        }

        // 1. Identify/Extract Pekerjaan from form data
        $pekerjaan = self::extractPekerjaan($application, $perijinan);

        // 2. Prepare Replacements Map
        $replacements = [
            '[NAMA PEMOHON]' => $user->name ?? '-',
            '[NIK]' => $user->nip ?? '-',
            '[ALAMAT LENGKAP]' => $user->alamat_ktp ?? $user->alamat_lengkap ?? $user->alamat_domisili ?? '-',
            '[NO HP]' => $user->no_hp ?? '-',
            '[EMAIL]' => $user->email ?? '-',
            '[PEKERJAAN]' => $pekerjaan,
            '[NAMA IZIN]' => $perijinan->nama_perijinan ?? '-',
            '[TANGGAL]' => self::formatDateIndonesian($application->created_at ?? now()),
            '[NO REGISTRASI]' => $application->no_registrasi ?? '-',
        ];

        // 3. Define output directory
        $safeNoRegistrasi = str_replace('-', '_', $application->no_registrasi);
        $folderPath = 'uploads/perijinan/generated_' . $safeNoRegistrasi;
        $absoluteFolder = public_path($folderPath);

        if (!File::exists($absoluteFolder)) {
            File::makeDirectory($absoluteFolder, 0755, true);
        }

        $generatedPaths = [];

        // 4. Generate each document type
        $documentTypes = [
            'pernyataan' => [
                'template_field' => 'template_pernyataan',
                'default_method' => 'getDefaultPernyataanTemplate',
                'filename' => 'Surat_Pernyataan_' . $safeNoRegistrasi,
            ],
            'permohonan' => [
                'template_field' => 'template_permohonan',
                'default_method' => 'getDefaultPermohonanTemplate',
                'filename' => 'Surat_Permohonan_' . $safeNoRegistrasi,
            ],
            'keabsahan' => [
                'template_field' => 'template_keabsahan',
                'default_method' => 'getDefaultKeabsahanTemplate',
                'filename' => 'Surat_Keabsahan_' . $safeNoRegistrasi,
            ],
        ];

        foreach ($documentTypes as $type => $config) {
            // Get raw template from perijinan first, then fallback to global settings, then default
            $rawTemplate = $perijinan->{$config['template_field']} ?? \App\Models\Setting::get('template_' . $type);
            
            if (empty(trim(strip_tags($rawTemplate ?? '')))) {
                $rawTemplate = self::{$config['default_method']}();
            }

            // Global fix for checkmarks [x] or [v] or ✓ to ensure they render correctly
            $checkmarkHtml = '<span class="checkmark">&#10003;</span>';
            $rawTemplate = str_replace(['[x]', '[v]', '[V]', '✓'], $checkmarkHtml, $rawTemplate);

            // Replace placeholders
            $htmlContent = str_replace(
                array_keys($replacements),
                array_values($replacements),
                $rawTemplate
            );

            // Wrap with basic page structure and styles for PDF rendering
            $fullHtml = self::wrapHtmlTemplate($htmlContent, $type, $application->no_registrasi);

            // Path to save
            $relativePath = $folderPath . '/' . $config['filename'];

            try {
                // If Barryvdh DomPDF is loaded, generate PDF, else fallback to HTML
                if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
                    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($fullHtml)
                        ->setPaper('a4', 'portrait')
                        ->setWarnings(false)
                        ->setOptions([
                            'isRemoteEnabled' => true,
                            'isHtml5ParserEnabled' => true,
                            'isFontSubsettingEnabled' => true,
                            'defaultFont' => 'DejaVu Sans',
                        ]);
                    
                    $absolutePdfPath = $absoluteFolder . '/' . $config['filename'] . '.pdf';
                    File::put($absolutePdfPath, $pdf->output());
                    $generatedPaths['file_' . $type] = $relativePath . '.pdf';
                    \Log::info("DocumentGenerator: Generated PDF for $type: " . $relativePath . '.pdf');
                } else {
                    $absoluteHtmlPath = $absoluteFolder . '/' . $config['filename'] . '.html';
                    File::put($absoluteHtmlPath, $fullHtml);
                    $generatedPaths['file_' . $type] = $relativePath . '.html';
                    \Log::warning("DocumentGenerator: DomPDF not loaded. Generated HTML fallback for $type: " . $relativePath . '.html');
                }
            } catch (\Exception $e) {
                \Log::error("DocumentGenerator: Failed to generate $type document: " . $e->getMessage());
                // Fallback to basic HTML if PDF generation crashes
                $absoluteHtmlPath = $absoluteFolder . '/' . $config['filename'] . '.html';
                File::put($absoluteHtmlPath, $fullHtml);
                $generatedPaths['file_' . $type] = $relativePath . '.html';
            }
        }

        return $generatedPaths;
    }

    /**
     * Extract pekerjaan (occupation) from application form fields dynamically.
     */
    private static function extractPekerjaan(DataPerijinan $application, Perijinan $perijinan): string
    {
        $formData = $application->form_data;
        if (empty($formData) || !is_array($formData)) {
            return '-';
        }

        $activeFields = $perijinan->activeFormFields;
        if ($activeFields->isEmpty()) {
            return '-';
        }

        foreach ($formData as $fieldId => $value) {
            $field = $activeFields->firstWhere('id', $fieldId);
            if ($field) {
                $labelLower = strtolower($field->label);
                $nameLower = strtolower($field->name);
                if (str_contains($labelLower, 'pekerjaan') || str_contains($nameLower, 'pekerjaan')) {
                    if (!empty($value)) {
                        return $value;
                    }
                }
            }
        }

        return '-';
    }

    /**
     * Format a date into Indonesian format (e.g. 20 Mei 2026).
     */
    private static function formatDateIndonesian($date): string
    {
        if (!$date) {
            $date = now();
        }

        if (is_string($date)) {
            $date = Carbon::parse($date);
        }

        $months = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        return $date->format('j') . ' ' . $months[$date->format('n')] . ' ' . $date->format('Y');
    }

    /**
     * Wrap the inner template body in a printable HTML format with styling.
     */
    private static function wrapHtmlTemplate(string $content, string $type, string $noRegistrasi = '-'): string
    {
        return '<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>' . Str::title($type) . '</title>
    <style>
        @page {
            margin: 1cm 1.5cm 2cm 1.5cm;
        }
        body {
            font-family: "DejaVu Sans", sans-serif !important;
            font-size: 11pt;
            line-height: 1.5;
            color: #000;
            margin: 0;
            padding: 0;
        }
        footer {
            position: fixed;
            bottom: -1cm;
            left: 0;
            right: 0;
            height: 1.5cm;
            text-align: center;
            border-top: 0.5pt solid #ccc;
            padding-top: 5pt;
        }
        .footer-text {
            font-size: 8pt;
            color: #666;
            font-style: italic;
        }
        h2 {
            font-size: 14pt;
            font-weight: bold;
            margin-top: 0;
            margin-bottom: 15pt;
            text-align: center;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10pt 0;
        }
        table td {
            padding: 2pt 0;
            vertical-align: top;
        }
        p {
            margin-bottom: 8pt;
            text-align: justify;
        }
        .checkmark {
            font-family: "DejaVu Sans", sans-serif !important;
        }
        .signature-table {
            margin-top: 20pt;
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <footer>
        <div class="footer-text">
            Dokumen ini dibuat secara elektronik melalui Sistem Perizinan Online "Dawet Ayu" Banjarnegara.<br/>
            Nomor Dokumen: ' . $noRegistrasi . '
        </div>
    </footer>
    <main>
        ' . $content . '
    </main>
</body>
</html>';
    }

    /**
     * Default Template for Surat Pernyataan.
     */
    public static function getDefaultPernyataanTemplate(): string
    {
        return '<h2>SURAT PERNYATAAN</h2>
<p>Yang bertanda tangan di bawah ini:</p>
<table>
    <tbody>
        <tr>
            <td style="width: 25%;">Nama</td>
            <td style="width: 2%;">:</td>
            <td>[NAMA PEMOHON]</td>
        </tr>
        <tr>
            <td>NIK</td>
            <td>:</td>
            <td>[NIK]</td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td>:</td>
            <td>[ALAMAT LENGKAP]</td>
        </tr>
        <tr>
            <td>No. HP</td>
            <td>:</td>
            <td>[NO HP]</td>
        </tr>
        <tr>
            <td>Pekerjaan</td>
            <td>:</td>
            <td>[PEKERJAAN]</td>
        </tr>
        <tr>
            <td>Email</td>
            <td>:</td>
            <td>[EMAIL]</td>
        </tr>
    </tbody>
</table>
<p>Dengan ini saya menyatakan bersedia mentaati dan tidak melanggar ketentuan peraturan perundang-undangan.</p>
<p>Demikian surat pernyataan ini saya buat dengan sebenarnya untuk dipergunakan sebagaimana mestinya.</p>
<table class="signature-table">
    <tbody>
        <tr>
            <td style="width: 60%;">&nbsp;</td>
            <td>
                <p>Banjarnegara, [TANGGAL]<br />Pemohon,</p>
                <br /><br />
                <p><strong>[NAMA PEMOHON]</strong></p>
            </td>
        </tr>
    </tbody>
</table>';
    }

    /**
     * Default Template for Surat Permohonan.
     */
    public static function getDefaultPermohonanTemplate(): string
    {
        return '<h2>SURAT PERMOHONAN IZIN</h2>
<p>Kepada Yth.<br /><strong>Kepala Dinas Penanaman Modal dan Pelayanan Terpadu Satu Pintu Kabupaten Banjarnegara</strong><br />di - <strong>Tempat</strong></p>
<p>Saya yang bertanda tangan di bawah ini:</p>
<table>
    <tbody>
        <tr>
            <td style="width: 25%;">Nama</td>
            <td style="width: 2%;">:</td>
            <td>[NAMA PEMOHON]</td>
        </tr>
        <tr>
            <td>NIK</td>
            <td>:</td>
            <td>[NIK]</td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td>:</td>
            <td>[ALAMAT LENGKAP]</td>
        </tr>
        <tr>
            <td>No. HP</td>
            <td>:</td>
            <td>[NO HP]</td>
        </tr>
        <tr>
            <td>Email</td>
            <td>:</td>
            <td>[EMAIL]</td>
        </tr>
    </tbody>
</table>
<p>Dengan ini mengajukan permohonan untuk memperoleh :<br />Perizinan: <strong>[NAMA IZIN]</strong></p>
<p>Sebagai bahan pertimbangan, bersama ini kami sampaikan kelengkapan persyaratan melalui Sistem Perizinan Online "Dawet Ayu" Banjarnegara.</p>
<p>Demikian permohonan ini disampaikan, atas perhatian dan perkenannya diucapkan terima kasih.</p>
<table class="signature-table">
    <tbody>
        <tr>
            <td style="width: 60%;">
                <p><strong>Pernyataan Pemohon:</strong><br />[x] Data yang disampaikan adalah benar.<br />[x] Bersedia bertanggung jawab atas data yang diberikan.</p>
            </td>
            <td>
                <p>Banjarnegara, [TANGGAL]<br />Pemohon,</p>
                <br /><br />
                <p><strong>[NAMA PEMOHON]</strong></p>
            </td>
        </tr>
    </tbody>
</table>';
    }

    /**
     * Default Template for Surat Keabsahan.
     */
    public static function getDefaultKeabsahanTemplate(): string
    {
        return '<h2>SURAT PERNYATAAN</h2>
<p>Yang bertanda tangan di bawah ini:</p>
<table>
    <tbody>
        <tr>
            <td style="width: 25%;">Nama</td>
            <td style="width: 2%;">:</td>
            <td>[NAMA PEMOHON]</td>
        </tr>
        <tr>
            <td>NIK</td>
            <td>:</td>
            <td>[NIK]</td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td>:</td>
            <td>[ALAMAT LENGKAP]</td>
        </tr>
        <tr>
            <td>No. HP</td>
            <td>:</td>
            <td>[NO HP]</td>
        </tr>
        <tr>
            <td>Pekerjaan</td>
            <td>:</td>
            <td>[PEKERJAAN]</td>
        </tr>
        <tr>
            <td>Email</td>
            <td>:</td>
            <td>[EMAIL]</td>
        </tr>
    </tbody>
</table>
<p>Dengan ini kami menyatakan dengan sesungguhnya bahwa semua informasi yang disampaikan dalam seluruh dokumen serta lampiran-lampirannya yang kami upload ini adalah benar dan kesatuan yang tidak dapat dipisahkan. Apabila diketemukan dan/atau dibuktikan adanya penipuan/pemalsuan atas informasi yang kami sampaikan, maka kami bersedia dikenakan dan menerima penerapan sanksi.</p>
<p>Demikian surat pernyataan kebenaran dan keabsahan data ini kami buat untuk digunakan secara semestinya dan atas perhatiannya diucapkan terima kasih.</p>
<table class="signature-table">
    <tbody>
        <tr>
            <td style="width: 60%;">&nbsp;</td>
            <td>
                <p>Banjarnegara, [TANGGAL]<br />Pemohon,</p>
                <br /><br />
                <p><strong>[NAMA PEMOHON]</strong></p>
            </td>
        </tr>
    </tbody>
</table>';
    }
}
