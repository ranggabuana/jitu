<?php

namespace App\Services;

use App\Models\DataPerijinan;
use App\Services\DocumentGenerator;
use Illuminate\Support\Facades\Log;
use setasign\Fpdi\Fpdi;
use Smalot\PdfParser\Parser;

class PdfVariableProcessor
{
    /**
     * Process a PDF file, replacing dynamic variable placeholders with actual values.
     *
     * Strategy:
     *  - ${QRCODE}: Stamp QR code image at bottom-right of pages containing the placeholder.
     *  - Text variables: Detect position via getDataTm(), white-box the placeholder area,
     *    and overlay the replacement text.
     *
     * Falls back to the original PDF path on any failure (safe).
     *
     * @param  string         $pdfAbsPath  Absolute path to the uploaded PDF
     * @param  DataPerijinan  $application
     * @return string         Same absolute path (file modified in-place)
     */
    public static function process(string $pdfAbsPath, DataPerijinan $application): string
    {
        try {
            // 1. Build the replacement map
            $replacements = self::buildReplacements($application);

            // 2. Parse PDF — detect which pages contain which placeholders
            $parser = new Parser();
            $pdf    = $parser->parseFile($pdfAbsPath);
            $pages  = $pdf->getPages();

            $placeholderPages = []; // [pageNum (1-based) => [placeholder, ...]]
            foreach ($pages as $idx => $page) {
                $pageText = $page->getText();
                $found    = [];
                foreach ($replacements as $placeholder => $replacement) {
                    if (str_contains($pageText, $placeholder)) {
                        $found[] = $placeholder;
                    }
                }
                if (!empty($found)) {
                    $placeholderPages[$idx + 1] = $found;
                }
            }

            // No placeholders found → return original unchanged
            if (empty($placeholderPages)) {
                self::cleanupQrTemp($replacements);
                return $pdfAbsPath;
            }

            // 3. Generate processed PDF with overlays
            $result = self::generateProcessedPdf($pdfAbsPath, $pages, $placeholderPages, $replacements);

            self::cleanupQrTemp($replacements);
            return $result;

        } catch (\Exception $e) {
            Log::error('PdfVariableProcessor::process failed: ' . $e->getMessage(), [
                'path'           => $pdfAbsPath,
                'application_id' => $application->id,
            ]);
            return $pdfAbsPath; // Safe fallback: keep original
        }
    }

    // =========================================================================
    // Replacement Map Builder
    // =========================================================================

    private static function buildReplacements(DataPerijinan $application): array
    {
        $replacements = [];

        // ---------------------------------------------------------------
        // 1. Get ALL text variables from DocumentGenerator — this ensures
        //    every variable available in the template editor is also
        //    supported here (global form fields, BO data, rekom, izin, etc.)
        // ---------------------------------------------------------------
        $dynamicMap = DocumentGenerator::getDynamicVariableMap($application);

        foreach ($dynamicMap as $snakeKey => $value) {
            // Convert snake_case → ${SNAKE_CASE}
            $placeholder = '${' . strtoupper($snakeKey) . '}';
            $replacements[$placeholder] = ['type' => 'text', 'value' => (string) $value];
        }

        // ---------------------------------------------------------------
        // 2. QR Code (image — always injected at fixed bottom-right position)
        // ---------------------------------------------------------------
        $scanUrl = route('front.perizinan.scan', ['no_registrasi' => $application->no_registrasi, 'type' => 'izin']);
        $qrPath  = self::generateQrCodeFile($scanUrl);
        $replacements['${QRCODE}'] = [
            'type'   => 'image',
            'path'   => $qrPath,
            'width'  => 70.87, // ~25mm in points
            'height' => 70.87,
        ];

        return $replacements;
    }


    // =========================================================================
    // PDF Processing
    // =========================================================================

    private static function generateProcessedPdf(
        string $pdfAbsPath,
        array  $pages,
        array  $placeholderPages,
        array  $replacements
    ): string {
        // Use points ('pt') as unit so coordinates match smalot/pdfparser output directly
        $fpdi = new Fpdi('P', 'pt');
        $fpdi->setSourceFile($pdfAbsPath);
        $totalPages = count($pages);

        for ($pageNum = 1; $pageNum <= $totalPages; $pageNum++) {
            $tpl  = $fpdi->importPage($pageNum);
            $size = $fpdi->getTemplateSize($tpl); // in points

            $pageWidth  = (float) $size['width'];
            $pageHeight = (float) $size['height'];
            $orientation = ($pageWidth > $pageHeight) ? 'L' : 'P';

            $fpdi->AddPage($orientation, [$pageWidth, $pageHeight]);
            $fpdi->useTemplate($tpl); // Render original page as background

            // Apply overlays if this page has placeholders
            if (isset($placeholderPages[$pageNum])) {
                foreach ($placeholderPages[$pageNum] as $placeholder) {
                    $replacement = $replacements[$placeholder] ?? null;
                    if (!$replacement) {
                        continue;
                    }

                    if ($replacement['type'] === 'image') {
                        self::stampQrCode($fpdi, $pages[$pageNum - 1], $replacement, $pageWidth, $pageHeight);
                    } else {
                        self::stampTextVariable(
                            $fpdi,
                            $pages[$pageNum - 1],
                            $placeholder,
                            $replacement['value'],
                            $pageWidth,
                            $pageHeight
                        );
                    }
                }
            }
        }

        // Write to temporary output, then replace original
        $outputPath = dirname($pdfAbsPath) . '/tmp_proc_' . basename($pdfAbsPath);
        $fpdi->Output('F', $outputPath);

        if (file_exists($outputPath) && filesize($outputPath) > 0) {
            @unlink($pdfAbsPath);
            rename($outputPath, $pdfAbsPath);
        } else {
            @unlink($outputPath);
            Log::warning('PdfVariableProcessor: Output file was empty, keeping original.', [
                'path' => $pdfAbsPath,
            ]);
        }

        return $pdfAbsPath;
    }

    // =========================================================================
    // Stamp Helpers
    // =========================================================================

    /**
     * Stamp QR code at detected coordinates of ${QRCODE} placeholder,
     * with fallback to the bottom-right corner of the page.
     */
    private static function stampQrCode(
        Fpdi   $fpdi,
        \Smalot\PdfParser\Page $page,
        array  $replacement,
        float  $pageWidth,
        float  $pageHeight
    ): void {
        $qrPath = $replacement['path'] ?? null;
        if (!$qrPath || !file_exists($qrPath)) {
            return;
        }

        $qrSize = $replacement['width'] ?? 70.87; // ~25mm in points
        
        // Try to find the exact position of ${QRCODE} placeholder on the page
        $x = null;
        $y = null;
        
        try {
            $dataTm = $page->getDataTm();
            if (!empty($dataTm)) {
                $window      = '';
                $windowItems = [];

                foreach ($dataTm as $item) {
                    if (!isset($item[0]) || !isset($item[1])) {
                        continue;
                    }

                    $window       .= $item[1];
                    $windowItems[] = $item;

                    $charIndex = strpos($window, '${QRCODE}');
                    if ($charIndex !== false) {
                        // Find which window item corresponds to the start of the match
                        $currentLen = 0;
                        $matchItem = null;
                        foreach ($windowItems as $item) {
                            $itemText = $item[1];
                            if ($currentLen <= $charIndex && $charIndex < $currentLen + strlen($itemText)) {
                                $matchItem = $item;
                                break;
                            }
                            $currentLen += strlen($itemText);
                        }

                        if ($matchItem) {
                            $tm       = $matchItem[0]; // [a, b, c, d, x, y]
                            $pdf_x    = (float) ($tm[4] ?? 0);
                            $pdf_y    = (float) ($tm[5] ?? 0);
                            $fontSize = (float) abs($tm[3] ?? 0);
                            if ($fontSize < 6 || $fontSize > 72) {
                                $fontSize = 12;
                            }

                            // Convert Smalot coordinates (origin bottom-left) to FPDI (origin top-left)
                            $x = $pdf_x;
                            
                            // Set y-coordinate such that the QR code top-left corner is aligned
                            // slightly above the placeholder baseline (centered or baseline aligned)
                            // If we want the QR code to fit nicely, we can shift the Y-axis.
                            $y = $pageHeight - $pdf_y - ($qrSize * 0.95);

                            // Estimate placeholder text width to cover it
                            $boxWidth = strlen('${QRCODE}') * $fontSize * 0.55;
                            
                            // White-box to cover the placeholder text
                            $fpdi->SetFillColor(255, 255, 255);
                            $fpdi->Rect($x - 2, $pageHeight - $pdf_y - ($fontSize * 1.25) - 2, max($boxWidth, $qrSize) + 4, max($fontSize * 1.5, $qrSize) + 4, 'F');
                            break;
                        }
                    }

                    if (strlen($window) > 300) {
                        $removed = array_shift($windowItems);
                        $window  = substr($window, strlen($removed[1] ?? ''));
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning('PdfVariableProcessor: Failed to detect QRCODE placeholder coordinates: ' . $e->getMessage());
        }

        // Fallback to bottom-right corner if placeholder coordinates could not be detected
        if ($x === null || $y === null) {
            $margin = 28.35; // ~10mm in points
            $x = $pageWidth  - $qrSize - $margin;
            $y = $pageHeight - $qrSize - $margin;
            
            // Draw fallback white box
            $fpdi->SetFillColor(255, 255, 255);
            $fpdi->Rect($x - 3, $y - 3, $qrSize + 6, $qrSize + 6, 'F');
        }

        // Draw the QR code
        $fpdi->Image($qrPath, $x, $y, $qrSize, $qrSize, 'PNG');
    }

    /**
     * Detect placeholder position via smalot dataTm and overlay replacement text.
     *
     * Coordinate conversion:
     *   smalot dataTm [4],[5] = (x, y) in PDF user units (pt, origin: bottom-left)
     *   FPDI 'pt' unit        = (x, y) in pt, origin: top-left
     *   → fpdi_y = pageHeight_pt − pdf_y_pt − fontSize_pt
     */
    private static function stampTextVariable(
        Fpdi   $fpdi,
        \Smalot\PdfParser\Page $page,
        string $placeholder,
        string $value,
        float  $pageWidth,
        float  $pageHeight
    ): void {
        try {
            $dataTm = $page->getDataTm();
            if (empty($dataTm)) {
                return;
            }

            // Sliding window to handle placeholders split across multiple PDF text chunks
            $window      = '';
            $windowItems = [];

            foreach ($dataTm as $item) {
                if (!isset($item[0]) || !isset($item[1])) {
                    continue;
                }

                $window       .= $item[1];
                $windowItems[] = $item;

                    $charIndex = strpos($window, $placeholder);
                    if ($charIndex !== false) {
                        // Find which window item corresponds to the start of the match
                        $currentLen = 0;
                        $matchItem = null;
                        foreach ($windowItems as $item) {
                            $itemText = $item[1];
                            if ($currentLen <= $charIndex && $charIndex < $currentLen + strlen($itemText)) {
                                $matchItem = $item;
                                break;
                            }
                            $currentLen += strlen($itemText);
                        }

                        if ($matchItem) {
                            $tm       = $matchItem[0]; // [a, b, c, d, x, y]
                            $pdf_x    = (float) ($tm[4] ?? 0);
                            $pdf_y    = (float) ($tm[5] ?? 0);
                            // Matrix element [3] approximates vertical scale (≈ font size in pt)
                            $fontSize = (float) abs($tm[3] ?? 0);
                            if ($fontSize < 6 || $fontSize > 72) {
                                $fontSize = 12; // Fallback for unusual matrices
                            }

                            // Convert to FPDI top-left coordinate
                            $fpdi_x = $pdf_x;
                            $fpdi_y = $pageHeight - $pdf_y - ($fontSize * 1.25); // 1.25 = ascent factor

                            // Estimate replacement box width (~0.55 × fontSize per char)
                            $boxWidth = strlen($placeholder) * $fontSize * 0.55;

                            // White-box over the placeholder
                            $fpdi->SetFillColor(255, 255, 255);
                            $fpdi->Rect($fpdi_x - 1, $fpdi_y - 1, $boxWidth + 2, $fontSize * 1.5, 'F');

                            // Overlay replacement text
                            $printSize = max(8, min(14, (int) round($fontSize)));
                            $fpdi->SetFont('Helvetica', '', $printSize);
                            $fpdi->SetTextColor(0, 0, 0);
                            $fpdi->SetXY($fpdi_x, $fpdi_y);
                            $fpdi->Cell($pageWidth * 0.6, $fontSize * 1.4, $value, 0, 0, 'L');

                            break; // One occurrence per page is enough
                        }
                    }

                    // Keep the window bounded
                    if (strlen($window) > 300) {
                        $removed = array_shift($windowItems);
                        $window  = substr($window, strlen($removed[1] ?? ''));
                    }
            }
        } catch (\Exception $e) {
            Log::warning('PdfVariableProcessor: Text position detection failed for ' . $placeholder . ': ' . $e->getMessage());
        }
    }

    // =========================================================================
    // QR Code Generator
    // =========================================================================

    private static function generateQrCodeFile(string $url): ?string
    {
        try {
            $qrCode  = \BaconQrCode\Encoder\Encoder::encode($url, \BaconQrCode\Common\ErrorCorrectionLevel::H());
            $matrix  = $qrCode->getMatrix();
            $cols    = $matrix->getWidth();
            $rows    = $matrix->getHeight();
            $margin  = 4;
            $modSize = 6;

            $imgW  = ($cols + 2 * $margin) * $modSize;
            $imgH  = ($rows + 2 * $margin) * $modSize;
            $image = imagecreatetruecolor($imgW, $imgH);
            $white = imagecolorallocate($image, 255, 255, 255);
            $black = imagecolorallocate($image, 0, 0, 0);
            imagefill($image, 0, 0, $white);

            for ($r = 0; $r < $rows; $r++) {
                for ($c = 0; $c < $cols; $c++) {
                    if ($matrix->get($c, $r) === 1) {
                        $x1 = ($c + $margin) * $modSize;
                        $y1 = ($r + $margin) * $modSize;
                        imagefilledrectangle($image, $x1, $y1, $x1 + $modSize - 1, $y1 + $modSize - 1, $black);
                    }
                }
            }

            $tmpPath = storage_path('app/pdf_var_qr_' . md5($url) . '_' . uniqid() . '.png');
            imagepng($image, $tmpPath);
            imagedestroy($image);

            return $tmpPath;
        } catch (\Exception $e) {
            Log::error('PdfVariableProcessor: QR generation failed: ' . $e->getMessage());
            return null;
        }
    }

    // =========================================================================
    // Utilities
    // =========================================================================

    private static function cleanupQrTemp(array $replacements): void
    {
        foreach ($replacements as $r) {
            if (is_array($r) && ($r['type'] ?? '') === 'image' && !empty($r['path'])) {
                @unlink($r['path']);
            }
        }
    }

    /**
     * Return all supported placeholders for UI display in the BO upload panel.
     * These reflect the same variables available in the document template editor.
     *
     * @return array<string, string>  placeholder => description
     */
    public static function getSupportedPlaceholders(): array
    {
        return [
            // --- QR Code (image) ---
            '${QRCODE}'           => 'QR Code verifikasi izin (gambar, pojok kanan bawah)',

            // --- Data Pemohon ---
            '${NAMA_PEMOHON}'     => 'Nama lengkap pemohon',
            '${NIK}'              => 'NIK pemohon',
            '${NO_HP}'            => 'Nomor HP pemohon',
            '${EMAIL}'            => 'Email pemohon',
            '${PEKERJAAN}'        => 'Pekerjaan pemohon',
            '${NAMA_PERUSAHAAN}'  => 'Nama perusahaan pemohon',
            '${NPWP}'             => 'NPWP pemohon',
            '${ALAMAT_KTP}'       => 'Alamat KTP pemohon',
            '${ALAMAT_DOMISILI}'  => 'Alamat domisili pemohon',
            '${ALAMAT_LENGKAP}'   => 'Alamat lengkap (dengan kecamatan, kabupaten, dll.)',
            '${KELURAHAN}'        => 'Kelurahan/desa pemohon',
            '${KECAMATAN}'        => 'Kecamatan pemohon',
            '${KABUPATEN}'        => 'Kabupaten/kota pemohon',
            '${PROVINSI}'         => 'Provinsi pemohon',

            // --- Data Perizinan ---
            '${NAMA_LAYANAN}'     => 'Nama jenis perizinan',
            '${NO_REGISTRASI}'    => 'Nomor registrasi pengajuan',
            '${NOMOR_IZIN}'       => 'Nomor surat izin',
            '${NOMOR_REKOM}'      => 'Nomor surat rekomendasi',
            '${TANGGAL}'          => 'Tanggal pengajuan',
            '${TANGGAL_HARI_INI}' => 'Tanggal hari ini (saat upload)',
            '${MASA_AKTIF}'       => 'Masa aktif / berlaku izin',
            '${TANGGAL_REKOM_TTE}' => 'Tanggal rekomendasi ditandatangani secara TTE',

            // --- Variabel Form Dinamis ---
            '${NAMA_FIELD}'       => 'Ganti NAMA_FIELD dengan nama field formulir (global/rekom/BO/izin)',
        ];
    }

}
