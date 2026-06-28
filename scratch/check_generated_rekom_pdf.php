<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$latestApp = \App\Models\DataPerijinan::findOrFail(57);
$pdfPath = public_path($latestApp->file_rekom);
if (file_exists($pdfPath)) {
    echo "Rekom Draft PDF file exists!\n";
    echo "Path: " . $pdfPath . "\n";
    echo "File Size: " . filesize($pdfPath) . " bytes\n";
    echo "Modified At: " . date("Y-m-d H:i:s", filemtime($pdfPath)) . "\n";
} else {
    echo "Rekom Draft PDF file does NOT exist at " . $pdfPath . "\n";
}

$docxPath = str_replace('.pdf', '.docx', $pdfPath);
if (file_exists($docxPath)) {
    echo "Rekom Draft DOCX file exists!\n";
    echo "Path: " . $docxPath . "\n";
    echo "File Size: " . filesize($docxPath) . " bytes\n";
    echo "Modified At: " . date("Y-m-d H:i:s", filemtime($docxPath)) . "\n";
} else {
    echo "Rekom Draft DOCX file does NOT exist at " . $docxPath . "\n";
}
